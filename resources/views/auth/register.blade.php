<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DeCasa Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --sage-primary: #697565; --sage-bg: #F0F2F0; }
        
        body {
            background-color: var(--sage-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px; 
        }

        .card-login {
            width: 100%;
            max-width: 500px; 
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
            <h3 class="logo-text mb-2">New Account</h3>
            <p class="text-muted small">Register a new account</p>
        </div>

        <form action="{{ route('register.process') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">USERNAME</label>
                <input type="text" name="nm_user" class="form-control"  required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-muted">EMAIL</label>
                    <input type="email" name="email" class="form-control"  required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-muted">PHONE</label>
                    <input type="text" name="no_hp" class="form-control"  required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                <input type="password" name="password" class="form-control"  required>
            </div>

            <div class="mb-4">
            <label class="form-label small fw-bold text-muted">REPEAT PASSWORD</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

            <button type="submit" class="btn btn-sage mb-3">REGISTER</button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                    Already have an account? <span class="fw-bold" style="color: var(--sage-primary)">Login</span>
                </a>
            </div>
        </form>
    </div>

</body>
</html>