<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoovyMoovy - Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg: #0d0515;
            --c1: #d16aff;
            --c2: #9614d0;
            --grad-2: linear-gradient(135deg, #9614d0, #660094);
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(150, 20, 208, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(209, 106, 255, 0.1) 0%, transparent 40%);
            color: white;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            border: 1px solid var(--border);
            border-radius: 2.5rem;
            width: 100%;
            max-width: 500px;
            padding: 3.5rem;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.7);
        }

        .bebas { font-family: 'Bebas Neue', sans-serif; }

        .input-group input {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            padding: 0.875rem 1.25rem;
            border-radius: 1rem;
            color: white;
            font-size: 0.875rem;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--c1);
            box-shadow: 0 0 20px rgba(209, 106, 255, 0.15);
            outline: none;
        }

        .btn-register {
            background: var(--grad-2);
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
            width: 100%;
            padding: 1rem;
            border-radius: 1rem;
            margin-top: 1rem;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(150, 20, 208, 0.4);
            filter: brightness(1.1);
        }

        .logo-text {
            font-size: 2.8rem;
            background: linear-gradient(135deg, var(--c1), var(--c2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-block mb-1">
                <span class="bebas logo-text">MoovyMoovy</span>
            </a>
            <p class="text-[9px] uppercase tracking-[0.4em] text-purple-300/50 font-black">Create your cinema account</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="redirect" value="{{ old('redirect', $redirect ?? '') }}">
            
            <div class="input-group">
                <label class="block text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2 ml-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Doe">
                @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="input-group">
                <label class="block text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2 ml-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com">
                @error('email') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="input-group">
                <label class="block text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2 ml-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <div class="input-group">
                <label class="block text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2 ml-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••">
            </div>
            @error('password') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror

            <button type="submit" class="btn-register">
                Register Now
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                Already have an account? 
                <a href="{{ $redirect ? route('login', ['redirect' => $redirect]) : route('login') }}" class="text-white font-bold hover:text-purple-400 transition underline underline-offset-4 decoration-purple-500/30">Sign In</a>
            </p>
        </div>
    </div>

</body>
</html>