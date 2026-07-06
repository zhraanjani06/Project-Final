<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Global Supply Chain Risk Intelligence</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-gradient-start: #0f172a; /* Slate 900 */
            --bg-gradient-end: #1e293b; /* Slate 800 */
            --accent-glow: rgba(59, 130, 246, 0.15); /* Blue glow */
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-muted: #94a3b8;
            --primary-accent: #3b82f6; /* Premium Blue */
            --secondary-accent: #60a5fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract glowing blobs for premium feel */
        .glowing-blob-1 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(0,0,0,0) 70%);
            top: -10%;
            left: -10%;
            z-index: 0;
            pointer-events: none;
        }

        .glowing-blob-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.1) 0%, rgba(0,0,0,0) 70%);
            bottom: -10%;
            right: -10%;
            z-index: 0;
            pointer-events: none;
        }

        .auth-container {
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }

        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            padding: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4), 0 0 30px var(--accent-glow);
        }

        .brand-logo {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #3b82f6, #9333ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 5px;
            letter-spacing: -1px;
        }

        .brand-subtitle {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 35px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #cbd5e1;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-group-text {
            background-color: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            color: #94a3b8;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            transition: border-color 0.3s ease;
        }

        .form-control {
            background-color: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            color: #f1f5f9;
            padding: 12px 16px;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
            color: #fff;
        }

        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-accent);
            color: var(--primary-accent);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--primary-accent), #2563eb);
            border: none;
            color: #fff;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-accent:hover {
            background: linear-gradient(135deg, #60a5fa, var(--primary-accent));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .btn-accent:active {
            transform: translateY(1px);
        }

        .auth-footer {
            margin-top: 25px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--secondary-accent);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        /* Checkbox styling */
        .form-check-input {
            background-color: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            width: 1.1em;
            height: 1.1em;
            margin-top: 0.2em;
        }

        .form-check-input:checked {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #cbd5e1;
            cursor: pointer;
        }
        
        .invalid-feedback {
            font-size: 0.8rem;
            color: #f87171;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="glowing-blob-1"></div>
    <div class="glowing-blob-2"></div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="brand-logo"><i class="fa-solid fa-earth-americas me-2"></i>SC Risk Intel</div>
            <div class="brand-subtitle">Supply Chain Monitoring</div>
            
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
