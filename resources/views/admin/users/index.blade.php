<x-layouts.admin title="Quản lý Tài khoản">

    @push('styles')
    <style>
        /* --- NÂNG CẤP GIAO DIỆN QUẢN LÝ TÀI KHOẢN PRO --- */
        :root { --primary-indigo: #4f46e5; }

        /* Stat Group - 3 khối thống kê */
        .stat-group { display: flex; gap: 1rem; flex-wrap: wrap; }
        .stat-bar-wrapper {
            background: white; border-radius: 16px; padding: 0.75rem 1.25rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
            display: flex; align-items: center; min-width: 170px;
        }
        .stat-icon-box {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; margin-right: 12px;
        }

        .card-main {
            border: none; border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.03);
            background: white; overflow: hidden;
        }

        /* Avatar & Badge */
        .user-avatar-circle {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.95rem; margin-right: 15px;
        }
        .role-pill { 
            font-size: 0.7rem; padding: 0.45rem 0.9rem; border-radius: 8px; 
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            display: inline-block; min-width: 95px; text-align: center;
        }
        .pill-admin   { background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6; }
        .pill-teacher { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .pill-student { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }

        /* Nút hành động Modern */
        .btn-modern {
            padding: 0.5rem 0.9rem; border-radius: 10px; font-weight: 600; 
            font-size: 0.8rem; transition: all 0.2s; border: 1px solid transparent;
        }
        .btn-edit-soft { background: #fdfaf6; color: #c2410c; border: 1px solid #ffedd5; }
        .btn-edit-soft:hover { background: #ffedd5; border-color: #fb923c; }
        .btn-delete-soft { background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6; }
        .btn-delete-soft:hover { background: #ffe4e6; border-color: #f43f5e; }

        /* Khung tìm kiếm DÀI 500px */
        .search-wrapper {
            background: #f1f5f9; border-radius: 12px; padding: 4px;
            display: flex; align-items: center; width: 500px; 
            transition: 0.3s; border: 1px solid transparent;
        }
        .search-wrapper:focus-within { background: white; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); border: 1px solid #c7d2fe; }
        .search-wrapper input { border: none; background: transparent; padding: 6px 15px; outline: none; width: 100%; font-size: 0.9rem; }
        .search-wrapper button { 
            border-radius: 9px; padding: 8px 25px; font-weight: 700; font-size: 0.85rem; 
            white-space: nowrap; min-width: fit-content; flex-shrink: 0;
        }

        .bg-indigo-soft { background: #eef2ff; color: #4f46e5; }
        .bg-blue-soft { background: #eff6ff; color: #2563eb; }
        .bg-green-soft { background: #f0fdf4; color: #16a34a; }
    </style>
    @endpush

    <div class="container-fluid px-4 mt-4 mb-5">
        
        {{-- THÀNH PHẦN TRÊN CÙNG: 3 CỘT STATS & NÚT THÊM --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="stat-group">
                <div class="stat-bar-wrapper">
                    <div class="stat-icon-box bg-indigo-soft"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="lh-1 fw-bold text-dark fs-5">{{ number_format($users->total()) }}</div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Người dùng</div>
                    </div>
                </div>
                <div class="stat-bar-wrapper">
                    <div class="stat-icon-box bg-blue-soft"><i class="bi bi-person-workspace"></i></div>
                    <div>
                        <div class="lh-1 fw-bold text-dark fs-5">{{ \App\Models\User::where('role', 'teacher')->count() }}</div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Giáo viên</div>
                    </div>
                </div>
                <div class="stat-bar-wrapper">
                    <div class="stat-icon-box bg-green-soft"><i class="bi bi-mortarboard"></i></div>
                    <div>
                        <div class="lh-1 fw-bold text-dark fs-5">{{ \App\Models\User::where('role', 'student')->count() }}</div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Học sinh</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-success fw-bold px-4 py-2 rounded-3 d-flex align-items-center shadow-sm" onclick="document.getElementById('importFile').click()">
                    <i class="bi bi-file-earmark-excel-fill me-2 fs-5"></i> Nhập Excel
                </button>
                <button class="btn btn-dark fw-bold px-4 py-2 rounded-3 shadow-sm d-flex align-items-center" onclick="openCreateModal()">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i> Thêm mới
                </button>
            </div>
        </div>

        {{-- Form ẩn để upload Excel --}}
        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" id="importForm" style="display:none">
            @csrf
            <input type="file" name="file_excel" id="importFile" accept=".xlsx, .xls, .csv" onchange="document.getElementById('importForm').submit()">
        </form>

        <div class="card card-main">
            {{-- HEADER: TIÊU ĐỀ & TÌM KIẾM DÀI --}}
            <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">Danh sách tài khoản</h5>
                    <p class="text-muted small mb-0">Quản lý thông tin thành viên trong hệ thống.</p>
                </div>
                
                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="search-wrapper shadow-sm">
                        <i class="bi bi-search text-muted ms-2"></i>
                        <input type="text" name="search" placeholder="Nhập tên hoặc địa chỉ email để tìm..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 py-3 text-muted small fw-bold">THÔNG TIN TÀI KHOẢN</th>
                            <th class="border-0 py-3 text-muted small fw-bold text-center">VAI TRÒ</th>
                            <th class="border-0 py-3 text-muted small fw-bold text-center">NGÀY ĐĂNG KÝ</th>
                            <th class="text-end pe-4 border-0 py-3 text-muted small fw-bold">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-circle">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $user->name }}</div>
                                            <div class="text-muted" style="font-size: 0.8rem;">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($user->role == 'admin')
                                        <span class="role-pill pill-admin">Admin</span>
                                    @elseif($user->role == 'teacher')
                                        <span class="role-pill pill-teacher">Giáo viên</span>
                                    @else
                                        <span class="role-pill pill-student">Học sinh</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark small">{{ $user->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn-modern btn-edit-soft me-1" 
                                            onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')">
                                        <i class="bi bi-pencil-square me-1"></i> Sửa
                                    </button>
                                    @if(Auth::id() != $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-modern btn-delete-soft">
                                                <i class="bi bi-trash me-1"></i> Xóa
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-search text-muted opacity-25" style="font-size: 3rem;"></i>
                                    <p class="text-muted fw-bold mt-3">Không tìm thấy kết quả phù hợp</p>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Quay lại danh sách</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-4 d-flex justify-content-center">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL PHẲNG HIỆN ĐẠI --}}
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold text-dark" id="modalTitle">Tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="userForm" action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div id="methodField"></div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="userName" class="form-control border-0 bg-light py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="userEmail" class="form-control border-0 bg-light py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" name="password" class="form-control border-0 bg-light py-2">
                            <div class="form-text small" id="passHelp"></div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Vai trò</label>
                            <select name="role" id="userRole" class="form-select border-0 bg-light py-2">
                                <option value="student">Học sinh</option>
                                <option value="teacher">Giáo viên</option>
                                <option value="admin">Quản trị viên</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">Lưu dữ liệu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var userModal = new bootstrap.Modal(document.getElementById('userModal'));
        function openCreateModal() {
            document.getElementById('modalTitle').innerText = 'Tạo tài khoản mới';
            document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('userForm').reset();
            document.getElementById('userEmail').readOnly = false;
            document.getElementById('passHelp').innerText = 'Mật khẩu mặc định: 123456';
            userModal.show();
        }
        function openEditModal(id, name, email, role) {
            document.getElementById('modalTitle').innerText = 'Chỉnh sửa tài khoản';
            document.getElementById('userForm').action = "{{ route('admin.users.update', ':id') }}".replace(':id', id);
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('userName').value = name;
            document.getElementById('userEmail').value = email;
            document.getElementById('userEmail').readOnly = true;
            document.getElementById('userRole').value = role;
            document.getElementById('passHelp').innerText = 'Để trống nếu không muốn đổi mật khẩu.';
            userModal.show();
        }
    </script>
    @endpush
</x-layouts.admin>