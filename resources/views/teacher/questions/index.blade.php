<x-layouts.shared title="Ngân hàng câu hỏi">

    {{-- Custom CSS nhỏ để làm đẹp thêm --}}
    @push('styles')
    <style>
        .table-hover tbody tr:hover { background-color: #f8f9fa; transition: all 0.2s; }
        .question-preview { font-size: 0.95rem; line-height: 1.5; color: #2c3e50; }
        .badge-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .badge-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .badge-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #997404; }
        .badge-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0aa2c0; }
        .badge-soft-secondary { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
        .col-id { font-family: 'Courier New', Courier, monospace; letter-spacing: -0.5px; }
    </style>
    @endpush

    {{-- Hiển thị thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Ngân hàng câu hỏi</h5>
                <p class="text-muted small mb-0">Quản lý kho dữ liệu câu hỏi và đề thi</p>
            </div>
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tạo câu hỏi mới
            </a>
        </div>

        {{-- Bộ lọc (Giữ nguyên vị trí chờ phát triển) --}}
{{-- --- ĐOẠN CODE BỘ LỌC VÀ TÌM KIẾM --- --}}
<div class="card-body border-bottom bg-light py-3 px-4">
    <div class="row align-items-center g-3">
        {{-- Nhóm nút Tab lọc --}}
        <div class="col-lg-7">
            <div class="btn-group p-1 bg-white rounded-3 shadow-sm border">
                <a href="{{ route('teacher.questions.index', ['filter' => 'all', 'search' => request('search')]) }}" 
                   class="btn {{ request('filter', 'all') == 'all' ? 'btn-primary text-white' : 'btn-light text-secondary' }} btn-sm px-3 fw-bold border-0">
                    <i class="bi bi-grid-fill me-1"></i> Tất cả
                </a>
                <a href="{{ route('teacher.questions.index', ['filter' => 'mine', 'search' => request('search')]) }}" 
                   class="btn {{ request('filter') == 'mine' ? 'btn-primary text-white' : 'btn-light text-secondary' }} btn-sm px-3 fw-bold border-0">
                    <i class="bi bi-person-fill-check me-1"></i> Của tôi
                </a>
                <a href="{{ route('teacher.questions.index', ['filter' => 'others', 'search' => request('search')]) }}" 
                   class="btn {{ request('filter') == 'others' ? 'btn-primary text-white' : 'btn-light text-secondary' }} btn-sm px-3 fw-bold border-0">
                    <i class="bi bi-people-fill me-1"></i> GV khác
                </a>
                <a href="{{ route('teacher.questions.index', ['filter' => 'thpt', 'search' => request('search')]) }}" 
                   class="btn {{ request('filter') == 'thpt' ? 'btn-primary text-white' : 'btn-light text-secondary' }} btn-sm px-3 fw-bold border-0">
                    <i class="bi bi-mortarboard-fill me-1"></i> Đề THPT
                </a>
            </div>
        </div>

        {{-- Ô tìm kiếm --}}
        <div class="col-lg-5">
            <form action="{{ route('teacher.questions.index') }}" method="GET">
                {{-- Quan trọng: Giữ lại filter khi tìm kiếm --}}
                <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-end-0 rounded-start-pill ps-4" 
                           placeholder="Tìm nội dung câu hỏi..." value="{{ request('search') }}">
                    <button class="btn btn-white border border-start-0 rounded-end-pill px-3 text-primary shadow-sm" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- --- HẾT ĐOẠN CODE BỘ LỌC --- --}}

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold" width="5%">ID</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold" width="40%">Nội dung câu hỏi</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold" width="15%">Phân loại</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold" width="15%">Chủ đề & Mức độ</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold" width="10%">Ngày tạo</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold text-end pe-4" width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            {{-- ID: Style kiểu mã code --}}
                            <td class="ps-4">
                                <span class="badge bg-light text-secondary border col-id">#{{ $q->id }}</span>
                            </td>
                            
                            {{-- Nội dung câu hỏi --}}
                            {{-- Nội dung câu hỏi --}}
                            <td>
                                <div class="d-flex flex-column py-2">
                                    {{-- [MỚI] Phần hiển thị Nhãn Nguồn gốc --}}
                                    <div class="mb-2">
                                        @if($q->source == 'thpt_2025')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2">
                                                <i class="bi bi-star-fill me-1"></i> Đề THPT 2025
                                            </span>
                                        @elseif($q->user_id == Auth::id())
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">
                                                <i class="bi bi-person-check-fill me-1"></i> Của bạn
                                            </span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2">
                                                <i class="bi bi-person-fill me-1"></i> GV khác
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Nội dung chính --}}
                                    <div class="question-preview mb-2 text-truncate" style="max-width: 450px;">
                                        {{ Str::limit(strip_tags($q->content), 90) }}
                                    </div>
                                    
                                    {{-- Phần đáp án gợi ý (Giữ nguyên) --}}
                                    <div class="d-flex align-items-center">
                                        @if($q->type == 'single_choice')
                                            @php $correct = $q->answers->where('is_correct', true)->first(); @endphp
                                            <span class="badge badge-soft-success rounded-pill px-2 py-1 fw-normal border border-success border-opacity-25">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                {{ $correct ? Str::limit($correct->content, 40) : '(Chưa chọn đáp án đúng)' }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-primary rounded-pill px-2 py-1 fw-normal border border-primary border-opacity-25">
                                                <i class="bi bi-layers-fill me-1"></i>
                                                Câu hỏi đúng sai ({{ $q->children->count() }} ý)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Loại & Lớp --}}
                            <td>
                                <div class="d-flex flex-column gap-2 align-items-start">
                                    {{-- Badge Loại câu hỏi --}}
                                    @if($q->type == 'single_choice')
                                        <span class="badge badge-soft-info border border-info border-opacity-25 rounded-pill">
                                            Trắc nghiệm
                                        </span>
                                    @else
                                        <span class="badge badge-soft-warning border border-warning border-opacity-25 rounded-pill">
                                            Đúng/Sai
                                        </span>
                                    @endif

                                    {{-- Badge Lớp & Định hướng --}}
                                    <div class="d-flex align-items-center small text-muted">
                                        <i class="bi bi-mortarboard me-1"></i> 
                                        Lớp {{ $q->grade }}
                                        @if($q->orientation)
                                            <span class="mx-1">&bull;</span>
                                            <span class="fw-bold text-dark">{{ strtoupper($q->orientation) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Chủ đề & Mức độ --}}
                            <td>
                                <div class="mb-1 text-dark small fw-semibold text-truncate" style="max-width: 180px;" title="{{ $q->topic->name ?? '' }}">
                                    {{ $q->topic->name ?? '---' }}
                                </div>
                                @if($q->cognitiveLevel)
                                    <span class="badge badge-soft-secondary border rounded-1">
                                        {{ $q->cognitiveLevel->name }}
                                    </span>
                                @endif
                            </td>

                            {{-- Ngày tạo --}}
                            <td class="text-muted small">
                                <div><i class="bi bi-calendar3 me-1"></i>{{ $q->created_at?->format('d/m/Y') ?? 'Chưa cập nhật' }}</div>
                                <div class="text-xs opacity-75 mt-1">{{ $q->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Hành động --}}
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-white border shadow-sm rounded-circle" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-1">
                                        <li>
                                            <button class="dropdown-item rounded-2 py-2" onclick="showQuestionDetails({{ $q->id }})">
                                                <i class="bi bi-eye text-primary me-2"></i> Xem chi tiết
                                            </button>
                                        </li>
                                        @if($q->user_id == Auth::id())
                                            {{-- Nếu là chủ sở hữu: Cho phép sửa và xóa --}}
                                            <li>
                                                <a class="dropdown-item rounded-2 py-2" href="{{ route('teacher.questions.edit', $q->id) }}">
                                                    <i class="bi bi-pencil-square text-warning me-2"></i> Chỉnh sửa
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('teacher.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                        <i class="bi bi-trash me-2"></i> Xóa bỏ
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            {{-- Nếu không phải chủ sở hữu: Hiển thị trạng thái bị khóa --}}
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <button class="dropdown-item rounded-2 py-2 text-muted" disabled title="Bạn không thể sửa câu hỏi của người khác">
                                                    <i class="bi bi-lock-fill me-2"></i> Chỉ xem (Read-only)
                                                </button>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider my-1"></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 bg-light">
                                <div class="py-4">
                                    <div class="mb-3 text-secondary opacity-25">
                                        <i class="bi bi-clipboard-data" style="font-size: 4rem;"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">Chưa có dữ liệu câu hỏi</h6>
                                    <p class="text-muted small mb-4">Hãy bắt đầu xây dựng ngân hàng câu hỏi của bạn ngay hôm nay.</p>
                                    <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary px-4 rounded-pill">
                                        <i class="bi bi-plus-lg me-1"></i> Tạo câu hỏi đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $questions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- MODAL XEM CHI TIẾT (Giữ nguyên Logic, chỉ Clean lại UI một chút) --}}
    <div class="modal fade" id="questionDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i> 
                        Chi tiết câu hỏi <span class="badge bg-secondary ms-2 rounded-1 col-id" style="font-size: 0.9rem;">#<span id="modalQId"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Nội dung câu hỏi --}}
                    <div class="mb-4">
                        <label class="fw-bold text-secondary small text-uppercase mb-2"><i class="bi bi-question-circle me-1"></i> Nội dung câu hỏi</label>
                        <div class="p-3 bg-light rounded-3 border">
                            <div id="modalQContent" class="text-dark" style="font-size: 1.05rem;"></div>
                        </div>
                    </div>

                    {{-- Danh sách đáp án --}}
                    <div>
                        <label class="fw-bold text-secondary small text-uppercase mb-2"><i class="bi bi-list-check me-1"></i> Phương án trả lời</label>
                        <div id="modalAnswers" class="d-flex flex-column gap-2">
                            {{-- JS render here --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-white border fw-bold" data-bs-dismiss="modal">Đóng lại</button>
                    <a href="#" id="modalEditBtn" class="btn btn-primary px-4 fw-bold"><i class="bi bi-pencil-square me-1"></i> Chỉnh sửa ngay</a>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        // 1. Dữ liệu từ Laravel truyền sang JS
        const questionsData = @json($questions->items()); 
        // THÊM DÒNG NÀY: Lấy ID người dùng hiện tại từ Auth
        const currentUserId = {{ Auth::id() }}; 

        function showQuestionDetails(id) {
            // 2. Tìm câu hỏi trong mảng (Sửa thành == để so sánh linh hoạt)
            const question = questionsData.find(q => q.id == id);
            if (!question) {
                console.error("Không tìm thấy dữ liệu cho câu hỏi ID:", id);
                return;
            }

            // 3. Lấy element container (Phải đảm bảo các ID này có trong HTML Modal của bạn)
            const answersDiv = document.getElementById('modalAnswers');
            const editBtn = document.getElementById('modalEditBtn');
            const modalQId = document.getElementById('modalQId');
            const modalQContent = document.getElementById('modalQContent');

            // 4. Đổ thông tin cơ bản
            if(modalQId) modalQId.innerText = question.id;
            if(modalQContent) modalQContent.innerHTML = question.content;

            // 5. Kiểm tra quyền sửa nút (Dùng currentUserId đã định nghĩa ở trên)
            if (editBtn) {
                if (question.user_id == currentUserId) {
                    editBtn.href = `/teacher/questions/${id}/edit`;
                    editBtn.style.display = 'inline-block';
                } else {
                    editBtn.style.display = 'none';
                }
            }

            // 6. Xử lý hiển thị đáp án
            if (answersDiv) {
                answersDiv.innerHTML = ''; // Xóa nội dung cũ

                if (question.type === 'single_choice') {
    if (question.answers && question.answers.length > 0) {
        // 1. Định nghĩa mảng nhãn
        // 1. Định nghĩa mảng nhãn
// 1. Định nghĩa mảng nhãn
const labels = ['A', 'B', 'C', 'D', 'E', 'F'];

question.answers.forEach((ans, index) => {
    const isCorrect = ans.is_correct == 1;
    const bgClass = isCorrect ? 'bg-success bg-opacity-10 border-success' : 'bg-white border-light-subtle';
    const textClass = isCorrect ? 'text-success fw-bold' : 'text-dark';
    const borderClass = isCorrect ? 'border border-success border-opacity-25' : 'border';

    // 2. Lấy nhãn chữ cái (A., B., ...)
    const labelText = labels[index] ? labels[index] + '.' : '';

    // 3. Cấu trúc phần bên trái (Nhãn + Icon nếu có)
// 3. Cấu trúc phần bên trái (Đã thu nhỏ width để sát nội dung)
    let leftSideHtml = '';
    if (isCorrect) {
        // Giảm width từ 55px xuống 30px
        leftSideHtml = `
            <div class="d-flex align-items-center me-2" style="width: 30px; min-width: 30px;">
                <i class="bi bi-check-circle-fill text-success fs-6 me-1"></i>
                <span class="fw-bold text-success" style="font-size: 1rem;">${labelText}</span>
            </div>`;
    } else {
        // Giảm width từ 55px xuống 30px và bỏ khoảng trống thừa
        leftSideHtml = `
            <div class="d-flex align-items-center me-2" style="width: 30px; min-width: 30px;">
                <div style="width: 18px;"></div> 
                <span class="fw-bold text-secondary" style="font-size: 1rem;">${labelText}</span>
            </div>`;
    }

let displayContent = ans.content;

    // 1. Regex mạnh hơn: Xóa nhãn A. hoặc A. (có hoặc không có khoảng trắng)
    // ^[A-F]\. : Bắt đầu bằng A-F và dấu chấm
    // \s* : Có thể có hoặc không có khoảng trắng theo sau
    const prefixRegex = /^[A-F]\.\s*/i; 
    displayContent = displayContent.replace(prefixRegex, '');

    // 2. Chuyển đổi ký tự đặc biệt để hiện thẻ HTML < > an toàn
    const safeContent = displayContent
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    // 3. Render nội dung
answersDiv.innerHTML += `
    <div class="p-3 rounded-3 ${borderClass} ${bgClass} d-flex align-items-start mb-2 shadow-sm">
        ${leftSideHtml} 
        <div class="flex-grow-1 ${textClass}" style="padding-top: 1px;">
            <code style="color: inherit; background: none; padding: 0; font-family: inherit;">${safeContent}</code>
        </div>
    </div>
`;
});
    } else {
        answersDiv.innerHTML = '<p class="text-muted small">Chưa có dữ liệu đáp án.</p>';
    }
    
}               else {
                    // Trường hợp câu hỏi Đúng/Sai (True/False Group)
                    if (question.children && question.children.length > 0) {
                        question.children.forEach((child, index) => {
                            let trueAns = child.answers ? child.answers.find(a => a.is_correct == 1) : null;
                            let resultText = trueAns ? trueAns.content : 'Chưa thiết lập';
                            let badgeClass = resultText === 'Đúng' ? 'bg-success' : 'bg-danger';

                            answersDiv.innerHTML += `
                                <div class="p-3 rounded-3 border bg-white mb-2 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1 pe-3">
                                            <span class="badge bg-light text-dark border me-2">Ý ${index + 1}</span>
                                            <span class="fw-medium">${child.content}</span>
                                        </div>
                                        <span class="badge ${badgeClass} rounded-pill px-3 py-2">${resultText}</span>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        answersDiv.innerHTML = '<p class="text-muted small">Chưa có dữ liệu câu hỏi con.</p>';
                    }
                }
            }

            // 7. Hiển thị Modal
            const modalEl = document.getElementById('questionDetailModal');
            if (modalEl) {
                let myModal = bootstrap.Modal.getInstance(modalEl);
                if (!myModal) {
                    myModal = new bootstrap.Modal(modalEl);
                }
                myModal.show();
            }
        }
    </script>
    @endpush

</x-layouts.shared>