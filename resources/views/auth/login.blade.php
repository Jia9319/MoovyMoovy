<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoovyMoovy - Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg: #0d0515;
            --c1: #d16aff;
            --c2: #9614d0;
            --grad-2: linear-gradient(135deg, #9614d0, #660094);
            --border: rgba(255, 255, 255, 0.1);
            --muted: rgba(255, 255, 255, 0.6);
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
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 2rem;
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -2px; left: 50%; transform: translateX(-50%);
            width: 40%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--c1), transparent);
        }

        .bebas { font-family: 'Bebas Neue', sans-serif; }

        .input-group input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            transition: all 0.3s;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--c1);
            box-shadow: 0 0 15px rgba(209, 106, 255, 0.2);
            outline: none;
        }

        .btn-signin {
            background: var(--grad-2);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(150, 20, 208, 0.5);
        }

        .logo-text {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--c1), var(--c2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-10">
            <a href="{{ url('/') }}" class="inline-block mb-2">
                <span class="bebas logo-text">MoovyMoovy</span>
            </a>
            <p class="text-[10px] uppercase tracking-[0.3em] text-purple-300/60 font-black">Ready for your next experience?</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="redirect" value="{{ old('redirect', $redirect ?? request()->query('redirect')) }}">
            
            <div class="input-group">
                <label class="block text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2 ml-1">Account Email</label>
                <input type="email" name="email" required 
                       class="w-full px-5 py-4 rounded-xl text-white text-sm" 
                       placeholder="name@example.com">
                @error('email') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="input-group">
                <label class="block text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2 ml-1">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-5 py-4 rounded-xl text-white text-sm" 
                       placeholder="••••••••">
            </div>

            <div class="flex items-center ml-1">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-800 bg-black text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                <label for="remember" class="ml-2 text-xs text-gray-400">Keep me signed in</label>
            </div>

            <button type="submit" class="w-full py-4 rounded-xl btn-signin text-white text-sm mt-4">
                Sign In
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-sm text-gray-500">
                New to Moovy? 
                <a href="{{ route('register') }}" class="text-white font-bold hover:text-purple-400 transition underline underline-offset-4 decoration-purple-500/30">Create Account</a>
            </p>
        </div>
    </div>

</body>
</html>