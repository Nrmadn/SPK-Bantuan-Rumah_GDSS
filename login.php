<?php
session_start();
require_once 'config/database.php';
require_once 'functions/auth.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: dm/dashboard.php");
    }
    exit;
}

// Proses login
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        if (isAdmin()) {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: dm/dashboard.php");
        }
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GDSS Bantuan Rumah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-purple: #8B5CF6;
            --secondary-purple: #A78BFA;
            --dark-purple: #6D28D9;
            --deep-purple: #5B21B6;
            --black-primary: #0A0A0A;
            --black-secondary: #1A1A1A;
            --black-tertiary: #2A2A2A;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(109, 40, 217, 0.1) 0%, transparent 50%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            opacity: 0.1;
            animation: float 15s infinite ease-in-out;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 20%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            animation-delay: 3s;
        }

        .particle:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 10%;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            33% { transform: translateY(-30px) translateX(30px); }
            66% { transform: translateY(30px) translateX(-30px); }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: linear-gradient(135deg, var(--black-secondary) 0%, var(--black-tertiary) 100%);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
                        0 0 40px rgba(139, 92, 246, 0.2);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            border: 2px solid var(--primary-purple);
            position: relative;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-purple) 0%, var(--secondary-purple) 50%, var(--primary-purple) 100%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .login-left {
            background: linear-gradient(135deg, #6D28D9 0%, #5B21B6 50%, #4C1D95 100%);
            color: white;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 1%, transparent 2%);
            background-size: 30px 30px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .login-left > * {
            position: relative;
            z-index: 1;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 100px;
            background: linear-gradient(135deg, #ffffff 0%, var(--secondary-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
            display: inline-block;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .system-title {
            font-size: 2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            color: #E9D5FF;
        }

        .system-subtitle {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            opacity: 0.95;
            color: #DDD6FE;
        }

        .system-description {
            font-size: 0.95rem;
            line-height: 1.7;
            opacity: 0.9;
            margin-bottom: 30px;
            color: #DDD6FE;
        }

        .info-box {
            background: rgba(139, 92, 246, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(167, 139, 250, 0.3);
            margin-top: 30px;
        }

        .info-box h6 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #E9D5FF;
        }

        .info-box small {
            font-size: 0.85rem;
            line-height: 1.8;
            color: #DDD6FE;
        }

        .info-box strong {
            color: #F3E8FF;
        }

        .login-right {
            padding: 60px 50px;
            background: linear-gradient(135deg, var(--black-secondary) 0%, var(--black-tertiary) 100%);
        }

        .login-title {
            color: var(--secondary-purple);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-label {
            color: var(--primary-purple);
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        .form-control {
            background: var(--black-primary);
            border: 2px solid var(--black-tertiary);
            border-radius: 12px;
            padding: 15px 20px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: var(--black-secondary);
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.3rem rgba(139, 92, 246, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-check-input {
            background-color: var(--black-primary);
            border: 2px solid var(--black-tertiary);
            width: 20px;
            height: 20px;
        }

        .form-check-input:checked {
            background-color: var(--primary-purple);
            border-color: var(--primary-purple);
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
            border: 2px solid var(--primary-purple);
            padding: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.5);
            border-color: var(--secondary-purple);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .alert {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(200, 35, 51, 0.2) 100%);
            border: 2px solid #dc3545;
            border-radius: 12px;
            color: #ff6b6b;
            padding: 15px 20px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .divider {
            margin: 30px 0;
            border-top: 1px solid rgba(139, 92, 246, 0.2);
        }

        .security-badge {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .security-badge i {
            font-size: 1.2rem;
            color: var(--primary-purple);
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                margin: 20px;
            }

            .login-left, .login-right {
                padding: 40px 30px;
            }

            .system-title {
                font-size: 1.5rem;
            }

            .login-title {
                font-size: 1.4rem;
            }

            .logo-icon {
                font-size: 70px;
            }

            .info-box {
                font-size: 0.8rem;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Floating Particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <div class="login-card">
                    <div class="row g-0">
                        <!-- Left Side - System Info -->
                        <div class="col-md-6 login-left">
                            <div class="logo-container">
                                <i class="bi bi-house-heart-fill logo-icon"></i>
                            </div>
                            
                            <h3 class="system-title text-center">GDSS</h3>
                            <h5 class="system-subtitle text-center">Bantuan Rumah Keluarga Miskin</h5>
                            
                            <p class="system-description text-center">
                                Group Decision Support System untuk menentukan kelayakan 
                                penerima bantuan rumah menggunakan metode <strong>TOPSIS</strong> dan <strong>Borda</strong>
                            </p>
                            
                            <div class="info-box">
                                <h6>
                                    <i class="bi bi-info-circle-fill"></i>
                                    Informasi Akun Demo
                                </h6>
                                <small>
                                    <strong>👤 Admin:</strong> admin / admin123<br>
                                    <strong>👤 Kepala Desa:</strong> kepaladesa / kades123<br>
                                    <strong>👤 Sekretaris:</strong> sekretaris / sekre123<br>
                                    <strong>👤 Ketua RT:</strong> ketuart / rt123
                                </small>
                            </div>
                        </div>
                        
                        <!-- Right Side - Login Form -->
                        <div class="col-md-6 login-right">
                            <h4 class="login-title">Masuk ke Sistem</h4>
                            
                            <?php if ($error): ?>
                            <div class="alert alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" id="loginForm">
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-person-fill"></i> Username
                                    </label>
                                    <input type="text" 
                                           name="username" 
                                           class="form-control form-control-lg" 
                                           placeholder="Masukkan username Anda" 
                                           required 
                                           autofocus>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-lock-fill"></i> Password
                                    </label>
                                    <input type="password" 
                                           name="password" 
                                           class="form-control form-control-lg" 
                                           placeholder="Masukkan password Anda" 
                                           required>
                                </div>
                                
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Ingat saya di perangkat ini
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-login w-100 btn-lg">
                                    <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                                </button>
                            </form>
                            
                            <div class="divider"></div>
                            
                            <div class="security-badge">
                                <i class="bi bi-shield-check"></i>
                                <span>Sistem aman dengan enkripsi end-to-end</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-text">
                    <small>
                        &copy; <?= date('Y') ?> GDSS Bantuan Rumah | 
                        <strong>Universitas Islam Negeri Malang</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            btn.innerHTML = '<span class="loading"></span> Memproses...';
            btn.disabled = true;
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);

        // Add focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.2s';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>