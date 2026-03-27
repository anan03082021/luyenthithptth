<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê số lượng tổng quát
        $stats = [
            'users'     => User::count(),
            'teachers'  => User::where('role', 'teacher')->count(),
            'students'  => User::where('role', 'student')->count(),
            'exams'     => Exam::count(),
            'sessions'  => ExamSession::count(),
            'messages'  => ChatMessage::count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
        ];

        // 2. Lấy dữ liệu cho biểu đồ đường (7 ngày gần nhất)
        $chartLabels = collect();
        $chartData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels->push($date->format('d/m'));
            
            // Đếm số ca thi được tạo trong ngày đó
            $count = ExamSession::whereDate('created_at', $date->toDateString())->count();
            $chartData->push($count);
        }

        // 3. Lấy hoạt động gần đây (5 tài khoản mới nhất)
        $recentActivities = User::latest()
            ->take(5)
            ->get()
            ->map(function($user) {
                return (object)[
                    'time' => $user->created_at->diffForHumans(),
                    'user_name' => $user->name,
                    'action' => 'Đã tham gia hệ thống với vai trò ' . ($user->role == 'teacher' ? 'Giáo viên' : 'Học sinh') . '.'
                ];
            });

        return view('admin.dashboard', compact('stats', 'chartLabels', 'chartData', 'recentActivities'));
    }
}