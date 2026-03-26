<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Hệ thống Luyện thi Tin học</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --bg-gradient: linear-gradient(135deg, #4361ee 0%, #7209b7 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2b2d42;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        
        /* Hero Section */
        .hero-section {
            background: var(--bg-gradient);
            color: white;
            padding: 160px 0 120px;
            position: relative;
            clip-path: ellipse(150% 100% at 50% 0%);
        }
        
        .hero-section::after {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }

        .navbar {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.85);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -1px;
            font-size: 1.5rem;
        }

        .feature-card {
            border: none;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .feature-card:hover {
            transform: translateY(-12px);
            background: #fff;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        }
        
        .icon-box {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-size: 2.2rem;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .btn-login-main {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .btn-login-main:hover {
            background: #f0f0f0;
            transform: scale(1.05);
            color: var(--secondary-color);
        }

        .glass-stats {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            border: 1px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .login-guide-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            border: 2px dashed #dee2e6;
        }

        footer a {
            transition: color 0.3s ease;
        }
        
        footer a:hover {
            color: var(--accent-color) !important;
        }
    </style>
</head>
<body>

    {{-- 1. NAVIGATION BAR --}}
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary d-flex align-items-center" href="#">
                <i class="bi bi-cpu-fill me-2"></i>
                TIN HỌC<span class="text-dark">PRO</span>
            </a>
            <div class="ms-auto">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                        Vào Dashboard <i class="bi bi-arrow-right-short"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-person-lock me-2"></i>Đăng nhập thành viên
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- 2. HERO SECTION --}}
    <header class="hero-section d-flex align-items-center">
        <div class="container position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start">
                    <div class="badge bg-white bg-opacity-20 text-white px-4 py-2 rounded-pill mb-4 fw-semibold border border-white border-opacity-25 shadow-sm">
                        ✨ Hệ thống Quản lý đề thi & Ôn tập trực tuyến
                    </div>
                    <h1 class="display-3 fw-extrabold mb-4 lh-tight">
                        Chinh phục kỳ thi <br>
                        <span class="text-info">Tin học quốc gia</span>
                    </h1>
                    <p class="lead mb-5 text-white text-opacity-75 pe-lg-5">
                        Nền tảng ôn tập dành riêng cho học sinh lớp 12. Ngân hàng câu hỏi bám sát cấu trúc đề minh họa mới nhất. Tài khoản được cung cấp bởi nhà trường để đảm bảo lộ trình học tập tối ưu.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-login-main">
                                TRUY CẬP HỌC TẬP NGAY
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login-main">
                                <i class="bi bi-box-arrow-in-right me-2"></i>ĐĂNG NHẬP ĐỂ BẮT ĐẦU
                            </a>
                            <div class="d-flex align-items-center text-white text-opacity-50 small ms-lg-3">
                                <i class="bi bi-info-circle me-2"></i> Tài khoản đã được cấp sẵn
                            </div>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-5 mt-5 mt-lg-0">
                    <div class="position-relative">
                        {{-- Decorative image mockup --}}
                        <div class="card glass-stats p-4 border-0 shadow-lg" style="transform: perspective(1000px) rotateY(-10deg);">
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="fw-bold mb-0">Tiến độ ôn tập</h5>
                                <i class="bi bi-lightning-charge-fill text-warning"></i>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Lý thuyết cơ bản</span>
                                    <span class="fw-bold">95%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: 95%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Giải quyết vấn đề</span>
                                    <span class="fw-bold">60%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <span class="small text-muted d-block">Điểm trung bình thi thử</span>
                                <h3 class="fw-bold text-primary mb-0">8.75</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- 3. STATS SECTION --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary">2K+</h2>
                    <p class="text-muted small">Câu hỏi chọn lọc</p>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary">50+</h2>
                    <p class="text-muted small">Đề thi thử 2025</p>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary">100%</h2>
                    <p class="text-muted small">Bám sát cấu trúc</p>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary">∞</h2>
                    <p class="text-muted small">Lượt làm bài miễn phí</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. FEATURES SECTION --}}
    <section id="features" class="py-5">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-7">
                    <h2 class="fw-bold mb-3">Chế độ ôn luyện thông minh</h2>
                    <p class="text-muted">Mọi tính năng được thiết kế để tối ưu hóa thời gian và hiệu quả ghi nhớ của học sinh.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 shadow-sm text-center text-md-start">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mx-md-0">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Luyện tập theo chuyên đề</h4>
                        <p class="text-muted small">Ôn luyện sâu từng nhóm kiến thức: Phần cứng, Phần mềm, Mạng máy tính, CS, ICT.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 shadow-sm text-center text-md-start">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mx-md-0">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Kiểm soát thời gian</h4>
                        <p class="text-muted small">Rèn luyện áp lực thời gian với các đề thi 45 phút, 50 phút giống hệt kỳ thi thật.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 shadow-sm text-center text-md-start">
                        <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mx-md-0">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Giải thích chi tiết</h4>
                        <p class="text-muted small">Không chỉ biết đúng sai, hệ thống cung cấp hướng dẫn giải cho từng câu hỏi khó.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. LOGIN INSTRUCTIONS --}}
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="login-guide-card text-center shadow-sm">
                        <h3 class="fw-bold mb-4">Bạn chưa có tài khoản?</h3>
                        <p class="text-muted mb-5">Hệ thống của chúng tôi không mở đăng ký tự do để bảo mật dữ liệu học sinh. Vui lòng liên hệ với **Giảng viên hướng dẫn** hoặc **Quản trị viên nhà trường** để nhận thông tin đăng nhập cá nhân.</p>
                        
                        <div class="row text-start g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center p-3 bg-white rounded-4 border">
                                    <i class="bi bi-shield-lock-fill text-primary fs-3 me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Mật khẩu</h6>
                                        <small class="text-muted">Dễ dàng thay đổi sau lần đầu đăng nhập</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center p-3 bg-white rounded-4 border">
                                    <i class="bi bi-person-badge-fill text-success fs-3 me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Tài khoản</h6>
                                        <small class="text-muted">Cung cấp theo mã số học sinh</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                ĐĂNG NHẬP NGAY
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. FOOTER --}}
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h5 class="fw-bold text-primary mb-3">TIN HỌC PRO</h5>
                    <p class="text-secondary small pe-lg-5">
                        Dự án nghiên cứu và phát triển phần mềm hỗ trợ luyện thi THPT Quốc gia môn Tin học. Nội dung được biên soạn bởi các chuyên gia và giảng viên giàu kinh nghiệm.
                    </p>
                    <div class="d-flex gap-3 fs-5 mt-4">
                        <i class="bi bi-facebook cursor-pointer"></i>
                        <i class="bi bi-youtube cursor-pointer"></i>
                        <i class="bi bi-github cursor-pointer"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Tài nguyên</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Ngân hàng câu hỏi</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Đề thi minh họa 2025</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Tài liệu Tin học 12</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Liên hệ kĩ thuật</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> admin@luyenthi.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> 0123 456 789</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-5">
            <div class="text-center text-secondary small">
                &copy; {{ date('Y') }} TinHocPro - All Rights Reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>