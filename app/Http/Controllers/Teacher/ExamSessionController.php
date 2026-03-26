<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamSessionController extends Controller
{
    // 1. Danh sách kỳ thi
    public function index()
    {
        $sessions = ExamSession::where('teacher_id', Auth::id())
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('teacher.sessions.index', compact('sessions'));
    }

    // 2. Form tạo mới
public function create(Request $request)
{
    // Lấy đề thi
    $exams = Exam::with('creator')->orderBy('title', 'asc')->get();
    $selectedExamId = $request->query('exam_id');

    // [THÊM DÒNG NÀY] Lấy danh sách học sinh
    $students = \App\Models\User::where('role', 'student')->orderBy('name', 'asc')->get();

    // Truyền thêm biến students vào compact
    return view('teacher.sessions.create', compact('exams', 'selectedExamId', 'students'));
}

public function store(Request $request)
{
    // 1. Tạo kỳ thi
    $session = \App\Models\ExamSession::create([
        'title'      => $request->title,
        'exam_id'    => $request->exam_id,
        'teacher_id' => \Illuminate\Support\Facades\Auth::id(),
        'start_at'   => $request->start_at,
        'end_at'     => $request->end_at,
        'password'   => $request->password,
    ]);

    if ($request->hasFile('student_file')) {
        $file = $request->file('student_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Nếu dùng dấu phẩy không ra dữ liệu, thử dùng dấu chấm phẩy
            if (count($data) == 1 && strpos($data[0], ';') !== false) {
                $data = explode(';', $data[0]);
            }

            // Lấy email ở cột đầu tiên
            $email = isset($data[0]) ? trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data[0])) : null;

            // Bỏ qua dòng tiêu đề nếu chữ đó là "email" hoặc không phải định dạng email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue; 
            }

            $user = \App\Models\User::where('email', $email)->first();

            \App\Models\ExamSessionStudent::create([
                'exam_session_id' => $session->id,
                'student_email'   => $email,
                'user_id'         => $user ? $user->id : null,
                'student_name'    => $user ? $user->name : 'Thí sinh',
            ]);
            $count++;
        }
        fclose($handle);

        if ($count == 0) {
            dd("Lỗi: Đã đọc file nhưng không lấy được Email. Nội dung dòng đầu tiên đọc được là: ", $data);
        }
    }

    return redirect()->route('teacher.sessions.index')->with('success', "Đã tạo kỳ thi và thêm $count thí sinh.");
}
    /**
     * 3. MÀN HÌNH GIÁM SÁT (MONITOR)
     * Đã sửa lỗi crash khi đề thi gốc bị xóa
     */
public function show($id)
{
    $session = \App\Models\ExamSession::with([
        'exam.questions.learningObjective', 
        'attempts.user',
        'students.user' // <-- Nạp thêm danh sách thí sinh đã gán từ file
    ])->findOrFail($id);
    
    // Lấy các bài thi đã nộp
    $attempts = $session->attempts->whereNotNull('submitted_at');
    $attemptIds = $attempts->pluck('id')->toArray();
    
    // --- GIỮ NGUYÊN LOGIC TÍNH TOÁN CỦA BẠN ---
    $questionStats = [];
    $analysisData = []; 
    $totalRatio = 0;

    if ($session->exam && !empty($attemptIds)) {
        $allAnswers = \App\Models\AttemptAnswer::whereIn('attempt_id', $attemptIds)
            ->get()
            ->groupBy('question_id');

        foreach ($session->exam->questions as $question) {
            $totalParticipants = count($attemptIds);
            $correctCount = 0;

            if ($question->type === 'single_choice') {
                $correctCount = isset($allAnswers[$question->id]) 
                    ? $allAnswers[$question->id]->where('is_correct', 1)->count() 
                    : 0;
            } else {
                $childIds = \App\Models\Question::where('parent_id', $question->id)->pluck('id');
                $totalChildCorrect = 0;
                foreach ($childIds as $cId) {
                    if (isset($allAnswers[$cId])) {
                        $totalChildCorrect += $allAnswers[$cId]->where('is_correct', 1)->count();
                    }
                }
                $correctCount = $childIds->count() > 0 ? ($totalChildCorrect / $childIds->count()) : 0;
            }

            $ratio = $totalParticipants > 0 ? ($correctCount / $totalParticipants) * 100 : 0;
            $totalRatio += $ratio;

            $questionStats[$question->id] = [
                'content' => $question->content,
                'ratio' => round($ratio, 1),
                'correct' => round($correctCount, 1),
                'wrong' => round($totalParticipants - $correctCount, 1),
            ];

            $yccdContent = $question->learningObjective->content ?? 'Chưa xác định YCCĐ';
            $grade = $question->grade ?? 'Khác';
            $key = $question->learning_objective_id ?? 'none';

            if (!isset($analysisData[$grade][$key])) {
                $analysisData[$grade][$key] = ['yccd' => $yccdContent, 'correct' => 0, 'total' => 0];
            }
            $analysisData[$grade][$key]['correct'] += $correctCount;
            $analysisData[$grade][$key]['total'] += $totalParticipants;
        }
    }

    $averageSessionRatio = count($questionStats) > 0 ? ($totalRatio / count($questionStats)) : 0;
    
    $weakTopics = [];
    foreach ($analysisData as $grade => $yccds) {
        foreach ($yccds as $item) {
            $ratio = $item['total'] > 0 ? round(($item['correct'] / $item['total']) * 100, 1) : 0;
            if ($ratio < 70) {
                $weakTopics[$grade][] = ['yccd' => $item['yccd'], 'ratio' => $ratio];
            }
        }
    }
    krsort($weakTopics);
    
    $overallSuggestion = $this->generateOverallSuggestion($averageSessionRatio);

    // Trả về view kèm theo các biến thống kê
    return view('teacher.sessions.show', compact(
        'session', 
        'questionStats', 
        'overallSuggestion', 
        'averageSessionRatio', 
        'weakTopics'
    ));
}

private function generateOverallSuggestion($averageRatio) {
    if ($averageRatio < 50) {
        return [
            'status' => 'Nguy cấp',
            'color' => 'danger',
            'text' => 'Kết quả chung dưới trung bình. Đa số học sinh gặp khó khăn với nội dung đề thi này. Cần tổ chức ôn tập lại toàn bộ kiến thức trọng tâm.'
        ];
    } elseif ($averageRatio < 75) {
        return [
            'status' => 'Khá',
            'color' => 'warning',
            'text' => 'Kết quả ở mức trung bình khá. Học sinh đã nắm được kiến thức cơ bản nhưng vẫn còn sai sót ở các câu vận dụng. Cần tăng cường luyện tập thêm.'
        ];
    } else {
        return [
            'status' => 'Tốt',
            'color' => 'success',
            'text' => 'Kết quả thi rất tốt. Học sinh nắm vững kiến thức bài học. Giáo viên có thể đẩy nhanh tiến độ hoặc đưa thêm các chuyên đề nâng cao.'
        ];
    }
}

    // 4. Chỉnh sửa
    public function edit($id)
    {
        $session = ExamSession::findOrFail($id);
        $exams = Exam::where('creator_id', Auth::id())->orderBy('created_at', 'desc')->get();
        
        return view('teacher.sessions.edit', compact('session', 'exams'));
    }

    public function update(Request $request, $id)
    {
        $session = ExamSession::findOrFail($id);
        $session->update($request->all());
        return redirect()->route('teacher.sessions.show', $id)->with('success', 'Cập nhật thành công');
    }

    /**
     * 5. XUẤT EXCEL (CSV)
     */
    public function export($id)
    {
        $session = ExamSession::with(['attempts.user', 'exam'])->findOrFail($id);
        $fileName = 'ket_qua_thi_' . $session->id . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($session) {
            $file = fopen('php://output', 'w');
            
            // Add BOM để Excel đọc được Tiếng Việt
            fputs($file, "\xEF\xBB\xBF");

            // Header cột
            fputcsv($file, ['ID', 'Họ tên', 'Email', 'Thời gian bắt đầu', 'Thời gian nộp', 'Điểm số', 'Trạng thái']);

            // Dữ liệu
            foreach ($session->attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user->id,
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->created_at->format('H:i d/m/Y'),
                    $attempt->submitted_at ? $attempt->submitted_at->format('H:i d/m/Y') : 'Chưa nộp',
                    $attempt->total_score,
                    $attempt->submitted_at ? 'Đã xong' : 'Đang làm'
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Xóa (Hủy) ca thi
     */
public function destroy($id)
    {
        try {
            // 1. Tìm ca thi
            $session = ExamSession::findOrFail($id);

            // 2. [THAY ĐỔI QUAN TRỌNG] 
            // Thay vì kiểm tra và chặn, ta thực hiện XÓA HẾT BÀI LÀM liên quan trước.
            // Điều này giúp tránh lỗi khóa ngoại trong Database.
            $session->attempts()->delete(); 

            // 3. Xóa danh sách học sinh được gán (nếu có dùng bảng trung gian session_student)
            // Nếu dùng Eloquent relationship (Many-to-Many):
            // $session->students()->detach(); 
            // Hoặc nếu quan hệ 1-n:
            // $session->students()->delete();

            // 4. Cuối cùng mới xóa Ca thi
            $session->delete();

            return redirect()->route('teacher.sessions.index')
                ->with('success', 'Đã xóa ca thi và toàn bộ dữ liệu bài làm liên quan.');

        } catch (\Exception $e) {
            return redirect()->route('teacher.sessions.index')
                ->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function recalculateAllScores($id)
{
    // Lấy tất cả các lượt làm bài của ca thi này
    $attempts = \App\Models\ExamAttempt::where('exam_session_id', $id)->get();
    $count = 0;

    foreach ($attempts as $attempt) {
        // Gọi lại Service tính điểm (Lúc này Service đã có logic mới của bạn)
        $this->examService->calculateScore($attempt->id);
        $count++;
    }

    return back()->with('success', "Đã tính toán lại điểm cho $count bài thi.");
}
}

