<!DOCTYPE html>
<html>
<head>
    <title>Super Admin Access</title>
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

    button {
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
    <h4>Super Admin Access</h4>

    @if ($errors->any())
        <div class="alert danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if($lockoutRemaining)
        <div class="alert warning">
            Try again in 
            <span class="timer" id="lockoutTimer">
                {{ sprintf('%02d:%02d', $lockoutRemaining->i, $lockoutRemaining->s) }}
            </span>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.login') }}">
        @csrf

        <div class="form-group">
           <input type="email" name="email" placeholder="Email">
          </div>

          <div class="form-group">
              <input type="password" name="password" placeholder="Password">
          </div>

          <div class="form-group">
              <input type="password" name="secret_key" placeholder="Secret Key">
          </div>
          
          <div class="form-group">
              <button type="submit">Login</button>
          </div>
              </form>
          </div>

@if($lockoutRemaining)
<script>
let totalSeconds = {{ $lockoutRemaining->i * 60 + $lockoutRemaining->s }};
const timerEl = document.getElementById('lockoutTimer');

const countdown = setInterval(() => {
    if (totalSeconds <= 0) {
        clearInterval(countdown);
        location.reload();
        return;
    }

    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;

    timerEl.textContent =
        String(minutes).padStart(2,'0') + ':' +
        String(seconds).padStart(2,'0');

    totalSeconds--;
}, 1000);
</script>
@endif

</body>
</html>