<x-layouts.admin title="Tổng quan hệ thống">

    @push('styles')
    <style>
        /* --- STYLE DASHBOARD ADMIN NÂNG CẤP (REMOVED QUICK ACTIONS) --- */
        .stat-card {
            border: none; border-radius: 20px; padding: 1.5rem;
            background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.02);
            transition: all 0.3s ease; height: 100%; position: relative;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        
        .icon-box {
            width: 54px; height: 54px; border-radius: 14px; display: flex; 
            align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.25rem;
        }
        
        /* Bảng màu Soft UI */
        .bg-indigo-soft { background: #eef2ff; color: #4f46e5; border: 1px solid #e0e7ff; }
        .bg-green-soft { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .bg-amber-soft { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
        .bg-blue-soft { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .bg-teal-soft { background: #f0fdfa; color: #0d9488; border: 1px solid #ccfbf1; }
        .bg-rose-soft { background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6; }

        .card-header-custom {
            background: transparent; border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;
        }
        
        /* Hoạt động gần đây Timeline */
        .activity-feed { border-left: 2px solid #f1f5f9; padding-left: 20px; margin-left: 10px; }
        .activity-item { position: relative; padding-bottom: 1.5rem; }
        .activity-item::before {
            content: ""; position: absolute; left: -27px; top: 0;
            width: 12px; height: 12px; border-radius: 50%; background: #4f46e5; border: 3px solid white;
        }
    </style>
    @endpush

    <div class="container-fluid px-4 mt-4 mb-5">
        
        {{-- WELCOME BANNER --}}
        <div class="row mb-4 align-items-center">
            <div class="col-md-7">
                <h2 class="fw-bold text-dark mb-1">Dashboard Quản Trị</h2>
                <p class="text-muted mb-0">Hệ thống đang hoạt động ổn định. Chào mừng, {{ Auth::user()->name }}!</p>
            </div>
            <div class="col-md-5 text-md-end">
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill me-2">
                    <i class="bi bi-calendar3 me-1"></i> {{ date('d/m/Y') }}
                </span>
                <span class="badge bg-dark px-3 py-2 rounded-pill shadow-sm">Phiên bản 2.0</span>
            </div>
        </div>

        {{-- 1. THỐNG KÊ TỔNG QUÁT --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-indigo-soft"><i class="bi bi-people-fill"></i></div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['users'] ?? 0) }}</h3>
                    <span class="text-muted small">Tổng tài khoản người dùng</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-green-soft"><i class="bi bi-person-video3"></i></div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['teachers'] ?? 0) }}</h3>
                    <span class="text-muted small">Giáo viên trên hệ thống</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-amber-soft"><i class="bi bi-backpack2-fill"></i></div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['students'] ?? 0) }}</h3>
                    <span class="text-muted small">Học sinh đăng ký</span>
                </div>
            </div>
        </div>

        {{-- 2. BIỂU ĐỒ & CHỈ SỐ HỆ THỐNG --}}
        <div class="row g-4 mb-4">
            {{-- Biểu đồ chính --}}
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">Xu hướng hoạt động hệ thống</h6>
                        <span class="small text-muted">7 ngày gần nhất</span>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="mainChart" height="320"></canvas>
                    </div>
                </div>
            </div>
            
            {{-- Các chỉ số phụ --}}
            <div class="col-lg-3">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="stat-card">
                        <div class="icon-box bg-blue-soft mb-2" style="width:40px; height:40px; font-size:1.1rem;">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-0">{{ number_format($stats['exams'] ?? 0) }}</h4>
                        <span class="text-muted small">Đề thi trong kho</span>
                    </div>
                    <div class="stat-card">
                        <div class="icon-box bg-teal-soft mb-2" style="width:40px; height:40px; font-size:1.1rem;">
                            <i class="bi bi-broadcast"></i>
                        </div>
                        <h4 class="fw-bold mb-0">{{ number_format($stats['sessions'] ?? 0) }}</h4>
                        <span class="text-muted small">Ca thi đã tổ chức</span>
                    </div>
                    <div class="stat-card border-bottom-0">
                        <div class="icon-box bg-rose-soft mb-2" style="width:40px; height:40px; font-size:1.1rem;">
                            <i class="bi bi-chat-dots-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-0">{{ number_format($stats['messages'] ?? 0) }}</h4>
                        <span class="text-muted small">Thảo luận diễn đàn</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. HOẠT ĐỘNG MỚI NHẤT TRÊN HỆ THỐNG --}}
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Hoạt động mới nhất từ người dùng</h6>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold small">Xem tất cả</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            {{-- Cột hoạt động Timeline --}}
                            <div class="col-md-12">
                                <div class="activity-feed">
                                    @forelse($recentActivities ?? [] as $activity)
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <span class="fw-bold text-dark">{{ $activity->user_name }}</span>
                                                    <span class="mx-2 text-muted small">|</span>
                                                    <span class="text-primary small fw-semibold">{{ $activity->action }}</span>
                                                </div>
                                                <span class="badge bg-light text-muted border-0 fw-normal" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock me-1"></i> {{ $activity->time }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="bi bi-inboxes text-secondary opacity-25" style="font-size: 3rem;"></i>
                                            <p class="text-muted small mt-2">Dữ liệu hoạt động đang được cập nhật...</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            
            // Dữ liệu mẫu nếu Controller chưa truyền sang, nếu đã có dữ liệu thật sẽ tự động cập nhật
            const labels = {!! json_encode($chartLabels ?? ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']) !!};
            const dataValues = {!! json_encode($chartData ?? [10, 25, 15, 40, 35, 50, 65]) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Lượt tương tác/Thi',
                        data: dataValues,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>