<x-layouts.shared title="Quản lý đề thi">

    @push('styles')
    <style>
        /* --- STYLE HIỆN ĐẠI (CLEAN UI) --- */
        :root { --primary-color: #4f46e5; --primary-bg: #eef2ff; --text-secondary: #64748b; }

        .card-custom {
            border: none;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 16px;
        }

        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern thead th {
            background-color: #f8fafc; color: var(--text-secondary);
            font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
            padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;
        }
        .table-modern tbody tr { transition: background-color 0.2s; }
        .table-modern tbody tr:hover { background-color: #fcfcfc; }
        .table-modern td {
            padding: 1.2rem 1.5rem; border-bottom: 1px solid #f1f5f9;
            vertical-align: middle; color: #334155; font-size: 0.95rem;
        }

        .exam-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: var(--primary-bg); color: var(--primary-color); font-size: 1.4rem;
        }
        .exam-title {
            font-weight: 700; color: #1e293b; text-decoration: none; font-size: 1rem;
            display: block; margin-bottom: 2px; transition: color 0.2s;
        }
        .exam-title:hover { color: var(--primary-color); }
        .exam-desc { color: #64748b; font-size: 0.85rem; max-width: 400px; }
        
        .meta-text { font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; gap: 15px; margin-top: 6px; }

        .badge-soft { padding: 0.5em 1em; font-size: 0.75rem; font-weight: 600; border-radius: 8px; }
        .badge-soft-success { background-color: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .badge-soft-secondary { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none; color: white; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: all 0.2s;
        }
        .btn-gradient:hover { transform: translateY(-2px); color: white; }
        
        .btn-icon {
            width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background: white; transition: all 0.2s;
        }
        .btn-icon:hover { border-color: var(--primary-color); color: var(--primary-color); background: #f8fafc; }
    </style>
    @endpush

    <div class="card card-custom bg-white mt-3" style="overflow: visible;">
        
        {{-- Header & Toolbar --}}
        <div class="p-4 border-bottom">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-indigo-50 text-primary rounded-3 p-3 me-3 d-none d-md-block">
                        <i class="bi bi-folder2-open fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">Ngân hàng đề thi</h4>
                        <div class="text-muted small">
                            <span class="fw-bold text-dark">{{ $exams->total() }}</span> đề thi đang quản lý
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    {{-- Form Tìm kiếm --}}
                    {{-- Thay thế đoạn Form tìm kiếm cũ của bạn bằng đoạn này --}}
<form action="{{ route('teacher.exams.index') }}" method="GET" class="d-flex align-items-center">
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" 
               name="search" 
               class="form-control border-start-0 ps-0" 
               placeholder="Tìm tên đề thi..." 
               value="{{ request('search') }}" 
               style="min-width: 250px;">
        
        {{-- Nút xóa tìm kiếm (chỉ hiện khi đang có từ khóa) --}}
        @if(request('search'))
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-light border border-start-0 text-muted">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
        
        <button type="submit" class="btn btn-primary px-3">
            Tìm
        </button>
    </div>
</form>

                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-gradient fw-bold px-4 rounded-3 d-flex align-items-center">
                        <i class="bi bi-plus-lg me-2"></i> <span>Tạo mới</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Danh sách --}}
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="35%">Thông tin đề thi</th>
                        <th width="15%" class="text-center">Người tạo</th>
                        <th width="12%" class="text-center">Số câu hỏi</th>
                        <th width="12%" class="text-center">Thời lượng</th>
                        <th width="13%" class="text-center">Trạng thái</th>
                        <th width="13%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            {{-- Cột 1: Thông tin chính --}}
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="exam-icon me-3 shadow-sm">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        @php 
                                            $routeEdit = (auth()->user()->role == 'admin') ? 'admin.exams.edit' : 'teacher.exams.edit'; 
                                        @endphp
                                        {{-- Chỉ cho phép vào trang sửa nếu là đề của mình --}}
                                        @if($exam->creator_id == auth()->id() || auth()->user()->role == 'admin')
                                            <a href="{{ route($routeEdit, $exam->id) }}" class="exam-title text-truncate" style="max-width: 350px;">
                                                {{ $exam->title }}
                                            </a>
                                        @else
                                            <span class="exam-title text-truncate" style="max-width: 350px;">{{ $exam->title }}</span>
                                        @endif
                                        
                                        <div class="exam-desc text-truncate small mb-1">
                                            {{ $exam->description ?? 'Chưa có mô tả' }}
                                        </div>
                                        <div class="meta-text">
                                            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.7rem;">#{{ $exam->id }}</span>
                                            <span><i class="bi bi-calendar3 me-1"></i> {{ $exam->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Cột 2: Người tạo --}}
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-medium text-dark small">{{ $exam->creator->name ?? 'Hệ thống' }}</span>
                                    @if($exam->creator_id == auth()->id())
                                        <span class="badge bg-indigo-50 text-primary border border-primary-subtle mt-1" style="font-size: 0.6rem;">Của tôi</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Cột 3: Số câu --}}
                            <td class="text-center">
                                <span class="fw-bold text-dark fs-6">{{ $exam->total_questions ?? 0 }}</span>
                                <span class="text-muted small d-block">câu</span>
                            </td>

                            {{-- Cột 4: Thời gian --}}
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fw-normal px-3 py-2 rounded-pill">
                                    <i class="bi bi-clock me-1 text-primary"></i> {{ $exam->duration }}'
                                </span>
                            </td>

                            {{-- Cột 5: Trạng thái --}}
                            <td class="text-center">
                                @if($exam->is_public)
                                    <span class="badge badge-soft badge-soft-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Công khai
                                    </span>
                                @else
                                    <span class="badge badge-soft badge-soft-secondary">
                                        <i class="bi bi-eye-slash-fill me-1"></i> Bản nháp
                                    </span>
                                @endif
                            </td>

                            {{-- Cột 6: Menu hành động --}}
                            <td class="text-end pe-4">
                                <div class="btn-group dropstart">
                                    <button class="btn btn-icon shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-1" style="min-width: 220px;">
                                        @php 
                                            $routeEdit = (auth()->user()->role == 'admin') ? 'admin.exams.edit' : 'teacher.exams.edit'; 
                                        @endphp

                                        {{-- 2. CHỈ CHỦ SỞ HỮU CÓ QUYỀN: Sửa, Xóa, Xem kết quả --}}
                                        @if($exam->creator_id == auth()->id() || auth()->user()->role == 'admin')
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li class="dropdown-header small text-uppercase fw-bold text-muted">Quản lý đề của tôi</li>
                                            <li>
                                                <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.exams.edit', $exam->id) }}">
                                                    <span class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2"><i class="bi bi-pencil-square"></i></span>
                                                    Chỉnh sửa đề
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 rounded-2 d-flex align-items-center" href="{{ route('teacher.exams.results', $exam->id) }}">
                                                    <span class="bg-success bg-opacity-10 text-success rounded p-1 me-2"><i class="bi bi-bar-chart-line"></i></span>
                                                    Xem kết quả thi
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề thi này không?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 rounded-2 text-danger d-flex align-items-center">
                                                        <span class="bg-danger bg-opacity-10 text-danger rounded p-1 me-2"><i class="bi bi-trash"></i></span>
                                                        Xóa đề thi
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <span class="dropdown-item py-2 rounded-2 text-muted small italic">
                                                    <i class="bi bi-lock-fill me-1"></i> Quyền quản lý hạn chế
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-5">
                                    <i class="bi bi-inbox text-muted opacity-50 display-4 d-block mb-3"></i>
                                    <h6 class="fw-bold text-dark">Chưa có đề thi nào</h6>
                                    <p class="text-muted small mb-4">Không tìm thấy kết quả phù hợp với tìm kiếm của bạn.</p>
                                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bold">
                                        <i class="bi bi-plus-lg me-1"></i> Tạo đề ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if($exams->hasPages())
            <div class="card-footer bg-white border-top py-4 d-flex justify-content-center">
                {{ $exams->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</x-layouts.shared>