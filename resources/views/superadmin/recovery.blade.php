<!DOCTYPE html>
<html>
<head>
    <title>Admin Recovery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <style>
    * {
        box-sizing: border-box; /* VERY IMPORTANT */
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #141e30, #243b55);
    }

    .card {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
        padding: 35px;
        width: 380px; /* fixed consistent width */
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.4);
        color: white;
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity:0; transform:translateY(15px); }
        to { opacity:1; transform:translateY(0); }
    }

    h4 {
        text-align: center;
        margin-bottom: 25px;
    }

    .form-group {
        width: 100%;
        margin-bottom: 15px;
    }

    input {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: none;
        outline: none;
        font-size: 14px;
    }

    input:focus {
        box-shadow: 0 0 8px #00c6ff;
    }

    .btn-primary {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        font-size: 14px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        color: white;
    }


    .btn-success {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        font-size: 14px;
        background: linear-gradient(45deg, #00b09b, #96c93d);
        color: white;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .alert {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
        text-align: center;
    }

    .danger { background: #ff4d4d; }
    .success { background: #28a745; }
    .warning { background: #ffc107; color: black; }

</style>
</head>
<body>

<div class="card">
    <h4>Admin Recovery Panel</h4>

    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.recovery.submit') }}">
        @csrf

    <div class="form-group">
        <input type="email" name="new_email"
            placeholder="New Admin Email" required>
    </div>
            
    <div class="form-group">
        <input type="password" name="new_password"
            placeholder="New Admin Password" required>
    </div>
            
    <div class="form-group">
        <button type="submit" class="btn btn-success">
            Recreate Admin
        </button>
    </div>
    </form>

    <a href="{{ route('login') }}">
        <button class="btn btn-primary">
            Login as Admin
        </button>
    </a>
</div>

</body>
</html>