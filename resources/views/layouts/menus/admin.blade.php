{{-- 1. Tổng quan --}}
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2 me-1"></i> Tổng quan
    </a>
</li>

{{-- 2. Quản lý Tài khoản --}}
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
        <i class="bi bi-people me-1"></i> Tài khoản
    </a>
</li>

{{-- 3. Diễn đàn --}}
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/forum*') ? 'active' : '' }}" href="{{ route('admin.forum.index') }}">
        <i class="bi bi-chat-dots me-1"></i> Diễn đàn
    </a>
</li>

{{-- 4. CÔNG TÁC CHUYÊN MÔN (Lấy từ Giáo viên) --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ Request::is('teacher*') ? 'active' : '' }}" href="#" id="profMenu" role="button" data-bs-toggle="dropdown">
        <i class="bi bi-mortarboard me-1"></i> Công tác Chuyên môn
    </a>
    <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="profMenu">
        <li>
            <a class="dropdown-item py-2" href="{{ route('teacher.questions.index') }}">
                <i class="bi bi-database-fill-add text-primary"></i> Ngân hàng câu hỏi
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ route('teacher.exams.index') }}">
                <i class="bi bi-file-earmark-text-fill text-success"></i> Quản lý đề thi
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ route('teacher.sessions.index') }}">
                <i class="bi bi-calendar-event-fill text-warning"></i> Tổ chức kỳ thi
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item py-2" href="{{ route('teacher.documents.index') }}">
                <i class="bi bi-folder-fill text-info"></i> Kho tài liệu
            </a>
        </li>
    </ul>
</li>