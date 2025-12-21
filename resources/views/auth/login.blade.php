<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DeCasa Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --sage-primary: #697565; --sage-bg: #F4F6F6; }
        
        body {
            background-color: var(--sage-bg); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card-login {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 0; 
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .btn-sage {
            background-color: var(--sage-primary);
            color: white;
            border-radius: 0;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }
        .btn-sage:hover { background-color: #4e634d; color: white; }

        .form-control {
            border-radius: 0;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .form-control:focus {
            border-color: var(--sage-primary);
            background-color: #fff;
            box-shadow: none;
        }
        
        .logo-text { color: var(--sage-primary); font-weight: bold; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="card card-login p-5">
        <div class="text-center mb-4">
            <h3 class="logo-text mb-2">DECASA DASHBOARD</h3>
            <p class="text-muted small">Sign in to manage your dashboard</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-0 small mb-3">
                {{ $errors->first() }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success rounded-0 small mb-3">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">EMAIL</label>
                <input type="email" name="email" class="form-control"  required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-sage mb-3">LOGIN</button>

            <div class="text-center">
                <a href="{{ route('register') }}" class="text-muted small text-decoration-none">
                    Create new admin account? <span class="fw-bold" style="color: var(--sage-primary)">Register</span>
                </a>
            </div>
        </form>
    </div>

</body>
</html>