<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - DENR DQIMS</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --denr-green: #1a5f2a;
            --denr-green-light: #2e7d32;
            --denr-green-dark: #0d3d16;
            --denr-blue: #1565c0;
            --denr-blue-light: #42a5f5;
            --denr-blue-dark: #0d47a1;
            --white: #ffffff;
            --gray-light: #f5f7fa;
            --gray-medium: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Background video */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        /* Video overlay for better text readability */
        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(26, 95, 42, 0.7) 0%, rgba(13, 61, 22, 0.7) 50%, rgba(21, 101, 192, 0.7) 100%);
            z-index: -1;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-container {
            width: 100%;
            max-width: 1100px;
            display: flex;
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.12), 0 10px 30px rgba(46, 125, 50, 0.1);
            overflow: hidden;
            min-height: 600px;
        }

        /* Left Panel - Branding */
        .login-branding {
            flex: 1;
            background: linear-gradient(160deg, var(--denr-green) 0%, var(--denr-green-dark) 50%, var(--denr-blue-dark) 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .login-branding::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 150%;
            background: radial-gradient(ellipse, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-branding::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(to top, rgba(0,0,0,0.2), transparent);
            pointer-events: none;
        }

        .branding-header {
            position: relative;
            z-index: 1;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            flex-shrink: 0;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            padding: 0;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-text h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .logo-text .region-text {
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 1px;
            opacity: 0.95;
            margin-top: 2px;
        }

        .logo-text .country-text {
            font-size: 0.85rem;
            opacity: 0.85;
            font-weight: 400;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .system-title {
            margin-top: 10px;
        }

        .system-title p {
            font-size: 1rem;
            opacity: 0.85;
            line-height: 1.7;
            font-weight: 300;
        }

        .features-list {
            margin-top: 40px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon i {
            font-size: 20px;
        }

        .feature-text {
            font-size: 0.95rem;
            font-weight: 500;
        }

        .branding-footer {
            position: relative;
            z-index: 1;
            font-size: 0.85rem;
            opacity: 0.7;
            text-align: center;
        }

        /* Right Panel - Login Form */
        .login-form-section {
            flex: 0 0 420px;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .form-header {
            margin-bottom: 35px;
            text-align: center;
        }

        .form-header h3 {
            color: var(--denr-green-dark);
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .role-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background: var(--gray-light);
            padding: 5px;
            border-radius: 12px;
        }

        .role-tab {
            flex: 1;
            padding: 12px 15px;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .role-tab.active {
            background: var(--white);
            color: var(--denr-green);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .role-tab:hover:not(.active) {
            color: var(--denr-green);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--denr-green-light);
            font-size: 1.1rem;
        }

        .form-control-custom {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid var(--gray-medium);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--denr-green-light);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }

        .form-control-custom.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--denr-green);
            cursor: pointer;
        }

        .remember-me label {
            font-size: 0.9rem;
            color: #555;
            cursor: pointer;
        }

        .forgot-password {
            font-size: 0.9rem;
            color: var(--denr-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--denr-blue-dark);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-green-light) 100%);
            border: none;
            border-radius: 12px;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 125, 50, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #999;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-medium);
        }

        .divider span {
            padding: 0 15px;
        }

        /* System Info Section */
        .system-info {
            background: linear-gradient(135deg, rgba(26, 95, 42, 0.03) 0%, rgba(21, 101, 192, 0.03) 100%);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(46, 125, 50, 0.15);
            margin-top: 20px;
        }

        .info-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(46, 125, 50, 0.1);
        }

        .info-header i {
            font-size: 1.4rem;
            color: var(--denr-green);
        }

        .info-header span {
            font-size: 0.95rem;
            color: var(--denr-green-dark);
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .info-features {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .info-item:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(46, 125, 50, 0.2);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.08);
            transform: translateX(4px);
        }

        .info-item i {
            font-size: 1.3rem;
            color: var(--denr-green);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-item strong {
            display: block;
            font-size: 0.85rem;
            color: var(--denr-green-dark);
            margin-bottom: 3px;
            font-weight: 600;
        }

        .info-item small {
            font-size: 0.75rem;
            color: #6c757d;
            line-height: 1.3;
        }

        .demo-account-item .role-badge.admin {
            background: var(--denr-blue);
        }

        .demo-account-item .role-badge.section {
            background: #6c757d;
        }

        .alert-custom {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
        }

        .alert-danger-custom {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            color: #c53030;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }

            .login-branding {
                padding: 40px 30px;
                min-height: auto;
            }

            .login-form-section {
                flex: 1;
                padding: 40px 30px;
            }

            .features-list {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 10px;
            }

            .login-container {
                border-radius: 16px;
            }

            .login-branding,
            .login-form-section {
                padding: 30px 20px;
            }

            .logo-section {
                flex-direction: column;
                text-align: center;
            }

            .role-tabs {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Background Video -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="{{ asset('images/216134_medium.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    
    <!-- Video Overlay -->
    <div class="video-overlay"></div>
    
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Left Panel - Branding -->
            <div class="login-branding">
                <div class="branding-header">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <img src="{{ asset('images/denrlogo.webp') }}" alt="DENR Logo">
                        </div>
                        <div class="logo-text">
                            <h1>DENR REGION 4A CALABARZON</h1>
                            <span class="country-text">Republic of the Philippines</span>
                        </div>
                    </div>
                    
                    <div class="system-title">
                        <p>Streamlined queue management for efficient public service delivery. Manage inquiries, track queue status, and serve the public better.</p>
                    </div>

                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bi bi-reception-4"></i>
                            </div>
                            <span class="feature-text">Front Desk Queue Management</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <span class="feature-text">Section Staff Processing</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <span class="feature-text">Real-time Reports & Analytics</span>
                        </div>
                    </div>
                </div>

                <div class="branding-footer">
                    <p>&copy; {{ date('Y') }} Department of Environment and Natural Resources. All rights reserved.</p>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="login-form-section">
                <div class="form-header">
                    <h3>Welcome Back</h3>
                    <p>Sign in to access your dashboard</p>
                </div>

                @if(session('error'))
                    <div class="alert-custom alert-danger-custom">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" 
                                   class="form-control-custom @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Enter your username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" 
                                   class="form-control-custom @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password" 
                                   required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In
                    </button>
                </form>

                <div class="system-info">
                    <div class="info-header">
                        <i class="bi bi-shield-check"></i>
                        <span>Secure Login System</span>
                    </div>
                    
                    <div class="info-features">
                        <div class="info-item">
                            <i class="bi bi-clock-history"></i>
                            <div>
                                <strong>24/7 Access</strong>
                                <small>Available round the clock</small>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-speedometer2"></i>
                            <div>
                                <strong>Fast Queue Management</strong>
                                <small>Efficient service delivery</small>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-graph-up-arrow"></i>
                            <div>
                                <strong>Real-time Monitoring</strong>
                                <small>Track your queue status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Add subtle animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.login-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
