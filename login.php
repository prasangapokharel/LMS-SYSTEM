<?php
session_start();
require_once 'include/connect.php';

use App\Bootstrap;

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$msg = "";
$cache = Bootstrap::cache();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $msg = "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle'></i> Please enter both username and password.</div>";
    } else {
        try {
            // Check cache first for user data
            $cacheKey = "user_login_{$username}";
            $cachedUser = $cache->get($cacheKey);
            
            if (!$cachedUser) {
                // Get user with role information from database
                $stmt = $pdo->prepare("SELECT u.*, r.role_name 
                                      FROM users u 
                                      JOIN user_roles r ON u.role_id = r.id 
                                      WHERE u.username = ? AND u.is_active = 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Cache user data for 5 minutes
                    $cache->set($cacheKey, $user, 300);
                    $cachedUser = $user;
                }
            } else {
                $user = $cachedUser;
            }
            
            if ($user) {
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role_name'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    
                    // Cache user session data
                    $cache->cacheUserData($user['id'], [
                        'username' => $user['username'],
                        'role' => $user['role_name'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'last_login' => date('Y-m-d H:i:s')
                    ]);
                    
                    // Log the login activity
                    try {
                        $stmt = $pdo->prepare("INSERT INTO system_logs 
                                              (user_id, action, ip_address, user_agent) 
                                              VALUES (?, 'user_login', ?, ?)");
                        $stmt->execute([
                            $user['id'],
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                        ]);
                        
                        // Clear login cache after successful login
                        $cache->delete($cacheKey);
                        
                    } catch (Exception $e) {
                        error_log("Login logging error: " . $e->getMessage());
                    }
                    
                    // Redirect based on role
                    switch ($user['role_name']) {
                        case 'principal':
                            header('Location: headoffice/index.php');
                            break;
                        case 'teacher':
                            header('Location: teachers/index.php');
                            break;
                        case 'student':
                            header('Location: students/index.php');
                            break;
                        default:
                            header('Location: dashboard.php');
                            break;
                    }
                    exit;
                } else {
                    $msg = "<div class='alert alert-danger'><i class='bi bi-shield-x'></i> Invalid username or password.</div>";
                }
            } else {
                $msg = "<div class='alert alert-danger'><i class='bi bi-person-x'></i> Invalid username or password.</div>";
            }
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle'></i> Database connection error. Please try again.</div>";
            error_log("Login error: " . $e->getMessage());
        }
    }
}

// Get system status from cache
$dbStatus = $cache->get('db_status', 'unknown');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School LMS - Login</title>
    <!-- Bootstrap CSS from Composer -->
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(45deg, #667eea, #764ba2);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --shadow-primary: 0 20px 40px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--shadow-primary);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            transition: transform 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
        }
        
        .login-left {
            background: var(--secondary-gradient);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
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
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        .login-right {
            padding: 3rem;
            position: relative;
        }
        
        .school-logo {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
            animation: bounce 2s ease-in-out infinite;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        
        .btn-login {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
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
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: white;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
            transition: color 0.3s ease;
        }
        
        .input-group .form-control:focus + i {
            color: #667eea;
        }
        
        .input-group .form-control {
            padding-left: 45px;
        }
        
        .demo-credentials {
            background: linear-gradient(145deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .credential-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(5px);
        }
        
        .credential-item:last-child {
            margin-bottom: 0;
        }
        
        .badge-role {
            font-size: 0.75rem;
            padding: 0.35rem 0.7rem;
            border-radius: 20px;
        }
        
        .system-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.8rem;
        }
        
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-connected {
            background: #28a745;
            animation: pulse-green 2s infinite;
        }
        
        .status-failed {
            background: #dc3545;
            animation: pulse-red 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }
        
        @keyframes pulse-green {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes pulse-red {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        @media (max-width: 768px) {
            .login-left, .login-right {
                padding: 2rem;
            }
            .school-logo {
                font-size: 3rem;
            }
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 40%;
            left: 80%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-container">
                    <!-- System Status Indicator -->
                    <div class="system-status">
                        <span class="status-indicator <?= $dbStatus === 'connected' ? 'status-connected' : 'status-failed' ?>"></span>
                        System <?= ucfirst($dbStatus) ?>
                    </div>
                    
                    <div class="row g-0">
                        <!-- Left Side - Branding -->
                        <div class="col-lg-5">
                            <div class="login-left">
                                <div class="school-logo">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <h2 class="mb-3">Welcome to School LMS</h2>
                                <p class="mb-4">Your comprehensive learning management system powered by modern technology.</p>
                                <div class="features">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <span>Smart Caching System</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <span>Real-time Notifications</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <span>Advanced Analytics</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <span>Secure Authentication</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side - Login Form -->
                        <div class="col-lg-7">
                            <div class="login-right">
                                <div class="text-center mb-4">
                                    <h3 class="fw-bold text-dark">Sign In</h3>
                                    <p class="text-muted">Enter your credentials to access your account</p>
                                </div>
                                
                                <?= $msg ?>
                                
                                <form method="post" id="loginForm">
                                    <div class="input-group">
                                        <input type="text" 
                                               name="username" 
                                               class="form-control" 
                                               placeholder="Username"
                                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                               required>
                                        <i class="bi bi-person"></i>
                                    </div>
                                    
                                    <div class="input-group">
                                        <input type="password" 
                                               name="password" 
                                               class="form-control" 
                                               placeholder="Password"
                                               required>
                                        <i class="bi bi-lock"></i>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-login">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Sign In
                                    </button>
                                </form>
                                
                                <!-- Demo Credentials -->
                                <div class="demo-credentials">
                                    <h6 class="fw-bold mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Demo Credentials
                                    </h6>
                                    
                                    <div class="credential-item" data-username="principal" data-password="password">
                                        <div>
                                            <span class="badge bg-danger badge-role">Principal</span>
                                            <strong class="ms-2">principal</strong>
                                        </div>
                                        <code>password</code>
                                    </div>
                                    
                                    <div class="credential-item" data-username="teacher001" data-password="teacher123">
                                        <div>
                                            <span class="badge bg-success badge-role">Teacher</span>
                                            <strong class="ms-2">teacher001</strong>
                                        </div>
                                        <code>teacher123</code>
                                    </div>
                                    
                                    <div class="credential-item" data-username="student001" data-password="student123">
                                        <div>
                                            <span class="badge bg-primary badge-role">Student</span>
                                            <strong class="ms-2">student001</strong>
                                        </div>
                                        <code>student123</code>
                                    </div>
                                    
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-cursor-fill me-1"></i>
                                            Click on any credential to auto-fill
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS from Composer -->
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced auto-fill credentials with animation
        document.querySelectorAll('.credential-item').forEach(item => {
            item.addEventListener('click', function() {
                const username = this.dataset.username;
                const password = this.dataset.password;
                
                const usernameInput = document.querySelector('input[name="username"]');
                const passwordInput = document.querySelector('input[name="password"]');
                
                // Clear inputs first
                usernameInput.value = '';
                passwordInput.value = '';
                
                // Animate typing effect
                typeText(usernameInput, username, () => {
                    typeText(passwordInput, password);
                });
                
                // Visual feedback
                this.style.background = 'rgba(102, 126, 234, 0.2)';
                this.style.transform = 'translateX(10px)';
                
                setTimeout(() => {
                    this.style.background = '';
                    this.style.transform = '';
                }, 500);
            });
        });
        
        // Typing animation function
        function typeText(element, text, callback) {
            let i = 0;
            const timer = setInterval(() => {
                element.value += text[i];
                i++;
                if (i >= text.length) {
                    clearInterval(timer);
                    if (callback) callback();
                }
            }, 50);
        }
        
        // Enhanced form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;
            
            if (!username || !password) {
                e.preventDefault();
                showAlert('Please enter both username and password.', 'warning');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Signing In...';
            submitBtn.disabled = true;
            
            // Re-enable button after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
        
        // Custom alert function
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('loginForm');
            form.insertBefore(alertDiv, form.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Focus management
        window.addEventListener('load', function() {
            document.querySelector('input[name="username"]').focus();
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + 1, 2, 3 for quick credential selection
            if (e.altKey) {
                const credentials = document.querySelectorAll('.credential-item');
                if (e.key >= '1' && e.key <= '3') {
                    const index = parseInt(e.key) - 1;
                    if (credentials[index]) {
                        credentials[index].click();
                    }
                }
            }
        });
        
        // System status check
        setInterval(function() {
            fetch('api/system-status.php')
                .then(response => response.json())
                .then(data => {
                    const indicator = document.querySelector('.status-indicator');
                    const statusText = document.querySelector('.system-status');
                    
                    if (data.status === 'connected') {
                        indicator.className = 'status-indicator status-connected';
                        statusText.innerHTML = '<span class="status-indicator status-connected"></span>System Connected';
                    } else {
                        indicator.className = 'status-indicator status-failed';
                        statusText.innerHTML = '<span class="status-indicator status-failed"></span>System Error';
                    }
                })
                .catch(() => {
                    // Handle error silently
                });
        }, 30000); // Check every 30 seconds
    </script>
</body>
</html>
