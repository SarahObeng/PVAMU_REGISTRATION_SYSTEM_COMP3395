<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PVAMU — Admin Portal Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: #F0EDF8;
    }

    /* Left panel */
    .hero {
      background: linear-gradient(155deg, #2A1250 0%, #4B2E83 50%, #6B47A8 100%);
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      padding: 60px 56px;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      width: 400px; height: 400px;
      background: radial-gradient(circle, rgba(255,209,0,0.15) 0%, transparent 65%);
      bottom: -80px; right: -80px;
      border-radius: 50%;
    }

    .hero::after {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
      top: 40px; left: -40px;
      border-radius: 50%;
    }

    .hero-logo {
      width: 68px; height: 68px;
      background: #FFD100;
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      color: #2A1250;
      margin-bottom: 32px;
      box-shadow: 0 8px 24px rgba(255,209,0,0.35);
      position: relative;
      z-index: 1;
    }

    .hero-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      font-weight: 700;
      color: #fff;
      line-height: 1.15;
      margin-bottom: 16px;
      position: relative;
      z-index: 1;
    }

    .hero-title em {
      color: #FFD100;
      font-style: normal;
    }

    .hero-desc {
      font-size: 1rem;
      color: rgba(255,255,255,0.65);
      max-width: 360px;
      line-height: 1.6;
      position: relative;
      z-index: 1;
    }

    .hero-stats {
      margin-top: 40px;
      display: flex;
      gap: 28px;
      position: relative;
      z-index: 1;
    }

    .hero-stat {
      text-align: left;
    }

    .stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 700;
      color: #FFD100;
      line-height: 1;
    }

    .stat-lbl {
      font-size: 0.78rem;
      color: rgba(255,255,255,0.5);
      margin-top: 4px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    /* Right panel — login form */
    .login-panel {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 48px;
    }

    .login-card {
      width: 100%;
      max-width: 400px;
    }

    .login-header {
      margin-bottom: 32px;
      text-align: center;
    }

    .login-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.75rem;
      font-weight: 700;
      color: #2A1250;
    }

    .login-sub {
      font-size: 0.875rem;
      color: #5A5475;
      margin-top: 6px;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 600;
      color: #2A1250;
      margin-bottom: 7px;
      letter-spacing: 0.03em;
    }

    .form-input {
      width: 100%;
      padding: 13px 16px;
      border: 1.5px solid rgba(75,46,131,0.18);
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      color: #1A1035;
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus {
      outline: none;
      border-color: #4B2E83;
      box-shadow: 0 0 0 3px rgba(75,46,131,0.12);
    }

    .form-input-wrap {
      position: relative;
    }

    .form-input-wrap i {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #9B96B2;
      font-size: 0.9rem;
      cursor: pointer;
    }

    .login-btn {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #4B2E83, #6B47A8);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 6px 20px rgba(75,46,131,0.3);
    }

    .login-btn:hover {
      opacity: 0.92;
      transform: translateY(-1px);
      box-shadow: 0 10px 28px rgba(75,46,131,0.35);
    }

    .login-btn i {
      margin-left: 8px;
    }

    .login-footer {
      margin-top: 28px;
      text-align: center;
      font-size: 0.78rem;
      color: #9B96B2;
    }

    .login-footer a {
      color: #4B2E83;
      text-decoration: none;
      font-weight: 600;
    }

    .demo-hint {
      background: rgba(255,209,0,0.12);
      border: 1px solid rgba(255,209,0,0.4);
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 0.8rem;
      color: #6B4B00;
      margin-bottom: 24px;
      text-align: center;
    }

    .demo-hint strong { color: #4B2E83; }

    @media (max-width: 768px) {
      body { grid-template-columns: 1fr; }
      .hero { display: none; }
      .login-panel { padding: 40px 24px; }
    }
  </style>
</head>
<body>

<!-- Hero Panel -->
<div class="hero">
  <div class="hero-logo">P</div>
  <h1 class="hero-title">
    PVAMU<br>
    <em>Registration</em><br>
    Analytics
  </h1>
  <p class="hero-desc">
    Real-time enrollment intelligence for the Office of the Registrar at
    Prairie View A&amp;M University.
  </p>
  <div class="hero-stats">
    <div class="hero-stat">
      <div class="stat-num">4</div>
      <div class="stat-lbl">BI Charts</div>
    </div>
    <div class="hero-stat">
      <div class="stat-num">10s</div>
      <div class="stat-lbl">Refresh Rate</div>
    </div>
    <div class="hero-stat">
      <div class="stat-num">5</div>
      <div class="stat-lbl">Business Insights</div>
    </div>
  </div>
</div>

<!-- Login Panel -->
<div class="login-panel">
  <div class="login-card">

    <div class="login-header">
      <div class="login-title">Welcome back</div>
      <div class="login-sub">Sign in to the admin analytics portal</div>
    </div>

    <div class="demo-hint">
      <strong>Demo mode:</strong> Click "Sign In" to enter the dashboard.<br>
      No credentials required for local development.
    </div>

    <form action="admin_dashboard.php" method="get" onsubmit="return true">
      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <div class="form-input-wrap">
          <input type="text" id="username" name="username" class="form-input"
                 placeholder="admin@pvamu.edu" value="admin@pvamu.edu">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="form-input-wrap">
          <input type="password" id="password" name="password" class="form-input"
                 placeholder="••••••••" value="••••••••">
          <i class="fas fa-eye" onclick="togglePw(this)"></i>
        </div>
      </div>

      <button type="submit" class="login-btn">
        Sign In <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="login-footer">
      <p>&copy; Prairie View A&amp;M University &mdash; Registrar's Office</p>
    </div>
  </div>
</div>

<script>
  function togglePw(icon) {
    const input = icon.previousElementSibling;
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
  }
</script>
</body>
</html>
