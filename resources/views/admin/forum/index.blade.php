<x-layouts.admin title="Kiểm duyệt Diễn đàn">

    <div class="container-fluid px-4 mt-4 mb-5">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Kiểm duyệt Diễn đàn</h2>
                <p class="text-muted mb-0">Quản lý và xử lý nội dung vi phạm tiêu chuẩn cộng đồng.</p>
            </div>
            {{-- Nút xóa hàng loạt --}}
            <button type="button" id="btnDeleteSelected" class="btn btn-danger fw-bold shadow-sm d-none" onclick="confirmBulkDelete()">
                <i class="bi bi-trash3-fill me-2"></i> Xóa mục đã chọn (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-shield-exclamation me-2"></i> Danh sách tin nhắn</h6>
                <div class="form-check small">
                    <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                    <label class="form-check-label fw-bold text-muted" for="selectAll" style="cursor: pointer;">Chọn tất cả</label>
                </div>
            </div>

            {{-- FORM BAO QUANH CẢ BẢNG --}}
            <form id="bulkDeleteForm" action="{{ route('admin.forum.bulkDestroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 40px;"></th>
                                <th>Người gửi</th>
                                <th style="width: 45%;">Nội dung</th>
                                <th>Loại tin</th>
                                <th>Thời gian</th>
                                <th class="text-end pe-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $msg)
                                <tr>
                                    <td class="ps-4">
                                        <input type="checkbox" name="ids[]" value="{{ $msg->id }}" class="form-check-input msg-checkbox" onchange="updateSelectedCount()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2 text-primary fw-bold" 
                                                 style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr($msg->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $msg->user->name ?? 'User đã xóa' }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">ID: {{ $msg->user_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="p-2 bg-light rounded text-break border-0 small">
                                            {{ $msg->message }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($msg->type == 'announcement')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border-0 small">Thông báo</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 small">Thảo luận</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-end pe-4">
                                        {{-- Nút xóa đơn lẻ gọi JS --}}
                                        <button type="button" class="btn btn-sm btn-light text-danger border-0 shadow-none" onclick="deleteSingle({{ $msg->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small">Chưa có tin nhắn nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-center">
                {{ $messages->links() }}
            </div>
        </div>
    </div>

    {{-- Form ẩn để xóa đơn lẻ --}}
    <form id="deleteSingleForm" method="POST" style="display:none">
        @csrf @method('DELETE')
    </form>

    @push('scripts')
    <script>
        // Chọn/Bỏ chọn tất cả
        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.msg-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateSelectedCount();
        }

        // Cập nhật số lượng hiển thị trên nút xóa
        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.msg-checkbox:checked');
            const totalBoxes = document.querySelectorAll('.msg-checkbox').length;
            const btn = document.getElementById('btnDeleteSelected');
            const countDisplay = document.getElementById('selectedCount');
            const selectAllBox = document.getElementById('selectAll');
            
            countDisplay.innerText = checkedBoxes.length;

            if (checkedBoxes.length > 0) {
                btn.classList.remove('d-none');
            } else {
                btn.classList.add('d-none');
            }

            // Tự động tích/hủy tích "Chọn tất cả" dựa trên các checkbox con
            if(selectAllBox) {
                selectAllBox.checked = (checkedBoxes.length === totalBoxes && totalBoxes > 0);
            }
        }

        // Xác nhận xóa hàng loạt
        function confirmBulkDelete() {
            if (confirm('Bạn có chắc chắn muốn xóa các tin nhắn đã chọn? Hành động này không thể hoàn tác.')) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }

        // Xác nhận xóa đơn lẻ
        function deleteSingle(id) {
            if (confirm('Bạn có chắc chắn muốn xóa tin nhắn này?')) {
                const form = document.getElementById('deleteSingleForm');
                // Sử dụng đường dẫn động để tránh lỗi thư mục con
                form.action = "{{ route('admin.forum.index') }}/" + id;
                form.submit();
            }
        }
    </script>
    @endpush

</x-layouts.admin>