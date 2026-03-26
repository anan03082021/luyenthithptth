<x-layouts.shared title="Giám sát kỳ thi">
    
    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root { --primary-color: #4f46e5; --text-secondary: #64748b; }

        /* Card & Tabs */
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02); }
        .nav-tabs { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs .nav-link { border: none; color: var(--text-secondary); font-weight: 600; padding: 1rem 1.5rem; transition: all 0.2s; }
        .nav-tabs .nav-link:hover { color: var(--primary-color); background-color: #f8fafc; }
        .nav-tabs .nav-link.active { color: var(--primary-color); border-bottom: 3px solid var(--primary-color); background: transparent; }

        /* Stat Cards Small */
        .stat-card-sm { padding: 1.5rem; border-radius: 12px; text-align: center; transition: transform 0.2s; }
        .stat-card-sm:hover { transform: translateY(-3px); }
        
        /* Progress Bar */
        .progress { height: 8px; border-radius: 4px; background-color: #f1f5f9; }

        /* Table Modern */
        .table-modern thead th { background-color: #f8fafc; color: var(--text-secondary); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .table-modern td { padding: 1rem 1.5rem; vertical-align: middle; }
    </style>
    @endpush

    {{-- HEADER (ĐÃ XÓA NÚT QUAY LẠI) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                {{-- Đã xóa link Quay lại ở đây --}}
                <span class="badge bg-light text-dark border">
                    Mã kỳ thi: #{{ $session->id }}
                </span>
            </div>
            
            <h4 class="fw-bold text-dark mb-1">{{ $session->title }}</h4>
            
            <p class="text-muted small mb-0">
                Đề thi gốc: 
                @if($session->exam)
                    <strong class="text-primary">{{ $session->exam->title }}</strong>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger">Đã bị xóa</span>
                @endif
            </p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('teacher.sessions.edit', $session->id) }}" class="btn btn-white border shadow-sm fw-bold text-secondary">
                <i class="bi bi-gear-fill me-1"></i> Cài đặt
            </a>
            <a href="{{ route('teacher.sessions.export', $session->id) }}" class="btn btn-success fw-bold shadow-sm text-white">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Xuất Excel
            </a>
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-4" id="monitorTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">
                <i class="bi bi-speedometer2 me-1"></i> Tổng quan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#students">
                <i class="bi bi-people me-1"></i> Danh sách thí sinh
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#analysis">
                <i class="bi bi-pie-chart me-1"></i> Phân tích câu hỏi
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        {{-- TAB 1: TỔNG QUAN --}}
        <div class="tab-pane fade show active" id="overview">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-primary bg-opacity-10 text-primary border-0">
                        <h3 class="fw-bold mb-1">{{ $session->attempts->count() }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Thí sinh tham gia</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-success bg-opacity-10 text-success border-0">
                        <h3 class="fw-bold mb-1">{{ $session->attempts->whereNotNull('submitted_at')->count() }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Đã nộp bài</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-warning bg-opacity-10 text-warning border-0">
                        <h3 class="fw-bold mb-1">{{ $session->attempts->whereNull('submitted_at')->count() }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Đang làm bài</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-sm bg-info bg-opacity-10 text-info border-0">
                        {{-- Tính thời gian còn lại --}}
                        @php
                            $remaining = \Carbon\Carbon::parse($session->end_at)->diffForHumans(null, true);
                            $isExpired = \Carbon\Carbon::now()->gt($session->end_at);
                        @endphp
                        <h3 class="fw-bold mb-1">{{ $isExpired ? 'Đã kết thúc' : $remaining }}</h3>
                        <div class="small fw-bold text-uppercase ls-1">Thời gian còn lại</div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2"></i> Thông tin chi tiết</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block text-muted small text-uppercase">Bắt đầu</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block text-muted small text-uppercase">Kết thúc</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="d-block text-muted small text-uppercase">Mật khẩu</span>
                            @if($session->password)
                                <span class="badge bg-dark">{{ $session->password }}</span>
                            @else
                                <span class="text-muted fst-italic">Không có</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{-- TAB 2: DANH SÁCH THÍ SINH --}}
<div class="tab-pane fade" id="students">
    <div class="card border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-modern mb-0">
                <thead>
                    <tr>
                        <th>Học sinh</th>
                        <th>Email</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Điểm số</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if($session->password)
                        {{-- TRƯỜNG HỢP 1: CÓ MẬT KHẨU - HIỂN THỊ NHỮNG NGƯỜI ĐÃ VÀO THI --}}
                        @forelse($session->attempts as $attempt)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $attempt->user->name }}</div>
                                <div class="text-muted small">Vào thi: {{ $attempt->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="text-muted">{{ $attempt->user->email }}</td>
                            <td class="text-center">
                                @if($attempt->submitted_at)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Đã nộp</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Đang làm</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-primary fs-6">{{ $attempt->total_score ?? '--' }}</td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-white border shadow-sm text-primary" title="Xem chi tiết">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có thí sinh nào vào thi.</td></tr>
                        @endforelse

                    @else
                        {{-- TRƯỜNG HỢP 2: KHÔNG MẬT KHẨU - HIỂN THỊ THEO DANH SÁCH GÁN (CSV) --}}
                        @forelse($session->students as $assignedStudent)
                            @php
                                // Tìm bài làm của học sinh này trong ca thi
                                $attempt = $session->attempts->where('user_id', $assignedStudent->user_id)->first();
                            @endphp
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $assignedStudent->student_name ?? ($assignedStudent->user->name ?? 'N/A') }}</div>
                                @if($attempt)
                                    <div class="text-muted small">Vào thi: {{ $attempt->created_at->format('H:i:s') }}</div>
                                @endif
                            </td>
                            <td class="text-muted">{{ $assignedStudent->student_email ?? ($assignedStudent->user->email ?? 'N/A') }}</td>
                            <td class="text-center">
                                @if($attempt && $attempt->submitted_at)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Đã nộp</span>
                                @elseif($attempt)
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Đang làm</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Chưa thi</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-primary fs-6">
                                {{ $attempt ? ($attempt->total_score ?? '0') : '--' }}
                            </td>
                            <td class="text-end pe-4">
                                @if($attempt)
                                    <a href="#" class="btn btn-sm btn-white border shadow-sm text-primary" title="Xem bài làm">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-light border disabled" title="Không có dữ liệu">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">Danh sách thí sinh trống (Chưa upload file CSV).</td></tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- TAB 3: PHÂN TÍCH CÂU HỎI --}}
<div class="tab-pane fade" id="analysis">
    <div class="card p-4 border-0 shadow-sm">
        @if(empty($questionStats))
            <div class="text-center py-5 text-muted small">Chưa có dữ liệu bài làm.</div>
        @else
            {{-- 1. Nhận xét tổng quát --}}
            <div class="alert alert-{{ $overallSuggestion['color'] }} border-0 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-2"></i>TỔNG KẾT CA THI</h6>
                        <p class="mb-0 small">{{ $overallSuggestion['text'] }}</p>
                    </div>
                    <div class="text-center border-start ps-4" style="min-width: 140px;">
                        <div class="h3 fw-bold mb-0">{{ round($averageSessionRatio, 1) }}%</div>
                        <div class="small text-uppercase fw-bold opacity-75" style="font-size: 0.65rem;">Đúng trung bình</div>
                    </div>
                </div>
            </div>

            {{-- 2. Nội dung yếu gom nhóm theo Khối lớp --}}
            @if(!empty($weakTopics))
                <div class="mb-4">
                    <h6 class="fw-bold text-danger small mb-3 text-uppercase">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Nội dung cần ôn tập theo khối lớp
                    </h6>
                    
                    @foreach($weakTopics as $grade => $items)
                        <div class="mb-4 shadow-sm border rounded overflow-hidden">
                            <div class="bg-dark text-white px-3 py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">KHỐI LỚP {{ $grade }}</span>
                                <span class="badge bg-light text-dark" style="font-size: 0.6rem;">{{ count($items) }} YCCĐ yếu</span>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($items as $item)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 bg-white">
                                        <div class="small text-dark flex-grow-1 pe-3">
                                            <i class="bi bi-dot text-danger fs-4"></i>{{ $item['yccd'] }}
                                        </div>
                                        <div class="text-end" style="min-width: 85px;">
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                {{ $item['ratio'] }}% đúng
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 3. Lưới thống kê chi tiết từng câu --}}
            <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Tỷ lệ đúng theo từng câu</h6>
            <div class="row g-2">
                @foreach($questionStats as $stat)
                    <div class="col-xl-2 col-lg-3 col-6">
                        <div class="p-2 border rounded bg-white shadow-none">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.7rem;">
                                <span class="fw-bold text-muted">Câu {{ $loop->iteration }}</span>
                                <span class="fw-bold {{ $stat['ratio'] < 50 ? 'text-danger' : 'text-success' }}">{{ $stat['ratio'] }}%</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $stat['ratio'] }}%"></div>
                                <div class="progress-bar bg-danger" style="width: {{ 100 - $stat['ratio'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

    </div>
</x-layouts.shared>