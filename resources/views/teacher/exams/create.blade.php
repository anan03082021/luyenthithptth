<x-layouts.shared title="Tạo đề thi & Chọn câu hỏi">
    @push('styles')
    <style>
        :root { --primary-color: #4f46e5; --primary-bg: #eef2ff; }
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02), 0 4px 10px rgba(0,0,0,0.03); border-radius: 12px; }
        .form-control, .form-select { border-color: #e2e8f0; padding: 0.65rem 1rem; border-radius: 8px; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .form-label { font-weight: 600; color: #334155; font-size: 0.8rem; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .badge { padding: 0.5em 0.8em; font-weight: 600; }
        .badge-source-thpt { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .stats-card { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; transition: all 0.3s; }
        .stats-card:hover { border-color: var(--primary-color); background: #fff; }
        .question-checkbox { width: 1.3em; height: 1.3em; cursor: pointer; border: 2px solid #cbd5e1; border-radius: 4px; }
        .question-checkbox:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
        .cursor-pointer { cursor: pointer; }
        .sticky-header { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,0.98); backdrop-filter: blur(8px); box-shadow: 0 4px 15px -5px rgba(0, 0, 0, 0.1); border-bottom: 1px solid #f1f5f9; }
    </style>
    @endpush

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Vui lòng kiểm tra lại:</strong>
            <ul class="mb-0 mt-1 small">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container-fluid p-0" 
         x-data="{ 
            bankQuestions: JSON.parse(localStorage.getItem('exam_cart_v2') || '[]'),

            inBank(id) { return this.bankQuestions.some(q => q.id == id); },

            toggleBank(id, type, source) {
                id = String(id);
                if (this.inBank(id)) {
                    this.bankQuestions = this.bankQuestions.filter(q => q.id != id);
                } else {
                    this.bankQuestions.push({ id: id, type: type.toLowerCase(), source: source });
                }
            },

            toggleAllPage(isChecked) {
                const checkboxes = document.querySelectorAll('.question-checkbox-item');
                checkboxes.forEach(cb => {
                    let id = cb.value;
                    let type = cb.dataset.type;
                    let source = cb.dataset.source;
                    if (isChecked && !this.inBank(id)) {
                        this.bankQuestions.push({ id: id, type: type, source: source });
                    } else if (!isChecked && this.inBank(id)) {
                        this.bankQuestions = this.bankQuestions.filter(q => q.id != id);
                    }
                });
            },

            get totalCount() { return this.bankQuestions.length; },
            get countChung() { return this.bankQuestions.filter(q => q.type == 'chung' || !q.type).length; },
            get countCS() { return this.bankQuestions.filter(q => q.type == 'cs').length; },
            get countICT() { return this.bankQuestions.filter(q => q.type == 'ict').length; },
            get countTHPT() { return this.bankQuestions.filter(q => q.source == 'thpt_2025').length; },

            submitForm() {
                let titleInput = document.querySelector('input[name=title]');
                if (!titleInput.value.trim()) {
                    alert('Vui lòng nhập Tên đề thi!');
                    titleInput.focus();
                    return;
                }
                let ids = this.bankQuestions.map(q => q.id);
                document.getElementById('finalQuestionIds').value = ids.join(',');
                localStorage.removeItem('exam_cart_v2'); 
                document.getElementById('createExamForm').requestSubmit();
            }
         }"
         x-init="$watch('bankQuestions', val => localStorage.setItem('exam_cart_v2', JSON.stringify(val)));">
        
            {{-- HEADER THÔNG TIN ĐỀ THI --}}
            <div class="card mb-4 border-0 sticky-header rounded-0 rounded-bottom-4">
                <div class="card-body p-4">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-9">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                                    <input type="text" name="title" form="createExamForm" class="form-control fw-bold text-primary" placeholder="VD: Kiểm tra Giữa kỳ 1 Tin học 12..." required>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label">Thời gian</label>
                                    <div class="input-group">
                                        <input type="number" name="duration" form="createExamForm" class="form-control fw-bold" value="45" min="5" required>
                                        <span class="input-group-text bg-light text-muted small">phút</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <label class="form-label">Hiển thị</label>
                                    <select name="is_public" form="createExamForm" class="form-select fw-bold text-success bg-success bg-opacity-10 border-success border-opacity-25">
                                        <option value="1">Công khai</option>
                                        <option value="0">Nháp</option>
                                    </select>
                                </div>
                                <div class="col-12" x-data="{ desc: '' }">
                                    <label class="form-label d-flex justify-content-between">
                                        Mô tả / Ghi chú đề thi
                                        <span class="fw-normal text-muted" style="font-size: 0.75rem" x-text="desc.length + '/255 ký tự'"></span>
                                    </label>
                                    <textarea name="description" form="createExamForm" class="form-control text-secondary" rows="1" maxlength="255" x-model="desc" placeholder="Nhập mô tả ngắn về đề thi (tùy chọn)..." style="font-size: 0.9rem;"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 text-end border-start ps-lg-4">
                            <div class="d-flex flex-column gap-3 h-100 justify-content-center">
                                <div class="text-dark text-center text-lg-end">
                                    <span class="text-secondary small text-uppercase d-block mb-1">Tổng câu hỏi</span>
                                    <span class="display-6 fw-bold text-primary" x-text="totalCount">0</span>
                                </div>
                                <button type="button" @click="submitForm" class="btn btn-primary fw-bold w-100 py-2 shadow-sm text-uppercase ls-1">
                                    <i class="bi bi-cloud-arrow-up-fill me-2 fs-5"></i> LƯU ĐỀ THI
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top g-2">
                        <div class="col-md-3 col-6"><div class="stats-card p-2 d-flex justify-content-between px-3"><span class="text-danger fw-bold small">THPT 2025</span><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" x-text="countTHPT">0</span></div></div>
                        <div class="col-md-3 col-6"><div class="stats-card p-2 d-flex justify-content-between px-3"><span class="text-primary fw-bold small">CS (Khoa học)</span><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" x-text="countCS">0</span></div></div>
                        <div class="col-md-3 col-6"><div class="stats-card p-2 d-flex justify-content-between px-3"><span class="text-success fw-bold small">ICT (Ứng dụng)</span><span class="badge bg-success bg-opacity-10 text-success rounded-pill" x-text="countICT">0</span></div></div>
                        <div class="col-md-3 col-6"><div class="stats-card p-2 d-flex justify-content-between px-3"><span class="text-secondary fw-bold small">Chung</span><span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill" x-text="countChung">0</span></div></div>
                    </div>
                </div>
            </div>

            {{-- KHU VỰC CHỌN CÂU HỎI --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5" style="min-height: 600px;">
                <div class="card-header bg-white border-bottom px-4 pt-3 pb-2">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-database me-2 text-primary"></i> Ngân hàng câu hỏi</h5>
                </div>

                <div class="card-body bg-white p-0">
                    {{-- BỘ LỌC CẢI TIẾN --}}
                    <div class="p-4 bg-light border-bottom">
                        <form action="{{ route('teacher.exams.create') }}" method="GET">
                            <div class="row g-2">
                                <div class="col-md-1">
                                    <select name="grade" class="form-select bg-white" title="Lớp">
                                        <option value="">Lớp</option>
                                        @foreach([10,11,12] as $g) <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="source" class="form-select bg-white fw-bold text-danger">
                                        <option value="">-- Nguồn --</option>
                                        <option value="thpt_2025" {{ request('source') == 'thpt_2025' ? 'selected' : '' }}>⭐ THPT 2025</option>
                                        <option value="user" {{ request('source') == 'user' ? 'selected' : '' }}>Giáo viên</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="topic_id" class="form-select bg-white">
                                        <option value="">-- Chủ đề --</option>
                                        @foreach($topics as $t) <option value="{{ $t->id }}" {{ request('topic_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="competency_id" class="form-select bg-white" title="Năng lực">
                                        <option value="">-- Năng lực --</option>
                                        @foreach($competencies as $c) 
                                            <option value="{{ $c->id }}" {{ request('competency_id') == $c->id ? 'selected' : '' }}>{{ $c->code }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="orientation" class="form-select bg-white">
                                        <option value="">-- Định hướng --</option>
                                        <option value="chung" {{ request('orientation') == 'chung' ? 'selected' : '' }}>Chung</option>
                                        <option value="cs" {{ request('orientation') == 'cs' ? 'selected' : '' }}>CS</option>
                                        <option value="ict" {{ request('orientation') == 'ict' ? 'selected' : '' }}>ICT</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <select name="type" class="form-select bg-white"><option value="">Dạng</option><option value="single_choice">TN</option><option value="true_false_group">Đ/S</option></select>
                                </div>
                                <div class="col-md-1"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button></div>
                            </div>
                        </form>
                    </div>

                    {{-- DANH SÁCH CÂU HỎI --}}
                    <div class="d-flex justify-content-between align-items-center p-3 px-4 border-bottom bg-white">
                        <span class="text-muted small fw-bold">Tìm thấy {{ $questions->total() }} kết quả</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkAll" @change="toggleAllPage($event.target.checked)">
                            <label class="form-check-label small fw-bold text-dark cursor-pointer" for="checkAll">Chọn tất cả trang này</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr><th width="60" class="text-center">Chọn</th><th>Nội dung câu hỏi</th><th width="200">Thông tin</th><th width="80" class="text-center">Xem</th></tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $q)
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input question-checkbox question-checkbox-item" type="checkbox" value="{{ $q->id }}" data-type="{{ $q->orientation ?? 'chung' }}" data-source="{{ $q->source ?? 'user' }}" :checked="inBank({{ $q->id }})" @change="toggleBank('{{ $q->id }}', '{{ $q->orientation ?? 'chung' }}', '{{ $q->source ?? 'user' }}')">
                                        </td>
                                        <td class="py-3">
                                            <div class="mb-2">
                                                @if($q->source == 'thpt_2025') <span class="badge badge-source-thpt rounded-pill me-1">THPT 2025</span> @endif
                                                @if($q->competency) <span class="text-muted small"><i class="bi bi-lightning-charge-fill text-warning"></i> {{ $q->competency->code }}</span> @endif
                                            </div>
                                            <div class="fw-bold text-dark">{{ Str::limit(strip_tags($q->content), 200) }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge bg-light text-dark border">Lớp {{ $q->grade }}</span>
                                                <span class="badge bg-secondary text-white">{{ strtoupper($q->orientation ?? 'chung') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-light text-primary rounded-circle border shadow-sm" data-question='@json($q)' onclick="showQuestionPreview(this)"><i class="bi bi-eye"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Không tìm thấy câu hỏi nào.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">{{ $questions->withQueryString()->links() }}</div>
                </div>
            </div>
    </div>

    {{-- FORM ẨN CHÍNH --}}
    <form id="createExamForm" action="{{ route('teacher.exams.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="question_ids" id="finalQuestionIds">
    </form>

    @push('scripts')
    <div class="modal fade" id="questionPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4" id="previewContent"></div>
                <div class="modal-footer bg-light border-0 py-2"><button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button></div>
            </div>
        </div>
    </div>
    <script>
        function showQuestionPreview(button) {
            const q = JSON.parse(button.getAttribute('data-question'));
            const modalBody = document.getElementById('previewContent');
            let html = `<div class="p-4 bg-light rounded-3 border mb-4"><h6 class="fw-bold text-primary mb-3"><i class="bi bi-question-circle-fill me-2"></i> Nội dung câu hỏi:</h6><div class="fs-5 text-dark" style="line-height: 1.6;">${q.content}</div></div>`;
            if (q.type === 'single_choice' && q.answers) {
                html += '<div class="list-group shadow-sm">';
                q.answers.forEach((ans) => {
                    let itemClass = ans.is_correct == 1 ? 'list-group-item-success fw-bold border-success' : 'list-group-item-light border-0 mb-1';
                    let icon = ans.is_correct == 1 ? '<i class="bi bi-check-circle-fill text-success me-2"></i>' : '<i class="bi bi-circle text-muted me-2"></i>';
                    html += `<div class="list-group-item ${itemClass} p-3 d-flex align-items-center">${icon} ${ans.content}</div>`;
                });
                html += '</div>';
            } else if (q.type === 'true_false_group' && q.children) {
                html += '<div class="table-responsive rounded-3 shadow-sm border"><table class="table table-striped mb-0 align-middle"><thead class="bg-primary text-white"><tr><th class="p-3">Ý nhận định</th><th class="text-center p-3" width="120">Đáp án</th></tr></thead><tbody>';
                q.children.forEach(child => {
                    let correctAns = child.answers.find(a => a.is_correct == 1);
                    let resultText = correctAns ? correctAns.content : 'N/A';
                    let badgeClass = resultText === 'Đúng' ? 'bg-success' : 'bg-danger';
                    html += `<tr><td class="p-3">${child.content}</td><td class="text-center p-3"><span class="badge ${badgeClass} rounded-pill px-3 py-2 w-75">${resultText}</span></td></tr>`;
                });
                html += '</tbody></table></div>';
            }
            modalBody.innerHTML = html;
            new bootstrap.Modal(document.getElementById('questionPreviewModal')).show();
        }
    </script>
    @endpush
</x-layouts.shared>