<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\CurriculumController;
use App\Http\Controllers\Admin\AdminDashboardController;

// 1. Controller chung
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;

// 2. Controller cho Học sinh
use App\Http\Controllers\ExamController as StudentExamController; 
use App\Http\Controllers\Student\HistoryController;

// 3. Controller cho Giáo viên
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController; 
use App\Http\Controllers\Teacher\ExamSessionController;
use App\Http\Controllers\Teacher\DocumentController;

// 4. Controller cho Admin
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ForumController as AdminForumController;

/*
|--------------------------------------------------------------------------
| TRANG CHỦ & ĐIỀU HƯỚNG ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'teacher') return redirect()->route('teacher.dashboard');
        return redirect()->route('dashboard');
    }
    return view('welcome'); 
});

require __DIR__.'/auth.php'; 

/*
|--------------------------------------------------------------------------
| CÁC ROUTE DÙNG CHUNG (Hồ sơ cá nhân)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| KHU VỰC CHUYÊN MÔN (GIÁO VIÊN & ADMIN DÙNG CHUNG)
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'role:teacher,admin']) 
    ->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');

        // Ngân hàng câu hỏi
        Route::resource('questions', QuestionController::class);
        Route::post('/questions/store-quick', [QuestionController::class, 'storeQuick'])->name('questions.store_quick');
        Route::post('/questions/upload-image', [QuestionController::class, 'uploadImage'])->name('questions.upload_image');

        // Quản lý Đề thi
        Route::get('/exams', [TeacherExamController::class, 'index'])->name('exams.index'); 
        Route::get('/exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
        Route::post('/exams/store', [TeacherExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{id}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{id}', [TeacherExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{id}', [TeacherExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/exams/{id}/results', [TeacherExamController::class, 'results'])->name('exams.results');

        // Tổ chức Kỳ thi (Sessions)
        Route::resource('sessions', ExamSessionController::class)->except(['store', 'update']);
        // Định nghĩa lại các route thủ công để khớp với logic cũ của bạn nếu cần
        Route::post('/sessions/store', [ExamSessionController::class, 'store'])->name('sessions.store');
        Route::put('/sessions/{id}', [ExamSessionController::class, 'update'])->name('sessions.update');
        Route::get('/sessions/{id}/export', [ExamSessionController::class, 'export'])->name('sessions.export');
        
        // SỬA LỖI TẠI ĐÂY: Loại bỏ tiền tố thừa /teacher/ trong URL vì đã có prefix
        Route::get('/sessions/{id}/recalculate', [ExamSessionController::class, 'recalculateScores'])->name('sessions.recalculate');

        // Quản lý tài liệu
        Route::resource('documents', DocumentController::class); 
    });

/*
|--------------------------------------------------------------------------
| KHU VỰC HỌC SINH
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/practice', [DashboardController::class, 'practiceList'])->name('student.practice');
    Route::get('/documents', [DocumentController::class, 'library'])->name('student.documents');
    Route::get('/history', [HistoryController::class, 'index'])->name('student.history');

    Route::get('/exam/take/{sessionId}', [StudentExamController::class, 'takeExam'])->name('exam.take');
    Route::post('/exam/join/{sessionId}', [StudentExamController::class, 'joinWithPassword'])->name('exam.join_password');
    Route::post('/exam/submit/{sessionId}', [StudentExamController::class, 'submitExam'])->name('exam.submit');
    Route::post('/exam/save-elective/{sessionId}', [StudentExamController::class, 'saveElective'])->name('exam.saveElective');
    Route::get('/practice/start/{examId}', [StudentExamController::class, 'startPractice'])->name('exam.practice');

    Route::get('/exam/result/official/{id}', [StudentExamController::class, 'showOfficialResult'])->name('student.exam.result.official');
    Route::get('/exam/result/practice/{id}', [StudentExamController::class, 'showResult'])->name('student.exam.result.practice');
});

/*
|--------------------------------------------------------------------------
| KHU VỰC ADMIN (QUẢN TRỊ HỆ THỐNG)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::post('/users/import', [UserController::class, 'importExcel'])->name('users.import');

        // Quản lý Diễn đàn
        Route::delete('/forum/bulk-delete', [AdminForumController::class, 'bulkDestroy'])->name('forum.bulkDestroy');
        Route::get('/forum', [AdminForumController::class, 'index'])->name('forum.index');
        Route::delete('/forum/{id}', [AdminForumController::class, 'destroy'])->name('forum.destroy');

        // --- QUẢN LÝ ĐỀ THI DÀNH CHO ADMIN ---
        // 1. Route vào trang sửa (Đổi {id} thành {exam} cho chuẩn Laravel)
        Route::get('/exams/{exam}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
        
        // 2. Route thực hiện lưu dữ liệu (Bắt buộc phải có để nút "Cập nhật" hoạt động)
        Route::put('/exams/{exam}', [TeacherExamController::class, 'update'])->name('exams.update');
        
        // 3. Route xem kết quả (Nếu muốn Admin xem kết quả từ route admin.exams.results)
        Route::get('/exams/{exam}/results', [TeacherExamController::class, 'results'])->name('exams.results');
        // Trong Route::prefix('admin')->group(...)
        Route::get('/exams/{exam}/edit', [App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('exams.edit');

        Route::get('/sessions', [ExamSessionController::class, 'index'])->name('sessions.index');
        Route::delete('/sessions/{id}', [ExamSessionController::class, 'destroy'])->name('sessions.destroy');
        
    });

/*
|--------------------------------------------------------------------------
| DIỄN ĐÀN CHUNG
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forum', [ChatController::class, 'index'])->name('forum.index');
    Route::get('/forum/messages', [ChatController::class, 'fetchMessages'])->name('forum.fetch');
    Route::post('/forum/send', [ChatController::class, 'sendMessage'])->name('forum.send');
});

// Công cụ sửa lỗi nhanh
Route::get('/fix-avatar', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    return "✅ Đã xóa Cache. <a href='/dashboard'>Quay về</a>";
});

/*
|--------------------------------------------------------------------------
| API Dropdown 3 cấp
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/topics', [CurriculumController::class, 'getTopics']);
    Route::get('/core-contents', [CurriculumController::class, 'getCoreContents']);
    Route::get('/learning-objectives', [CurriculumController::class, 'getLearningObjectives']);
});