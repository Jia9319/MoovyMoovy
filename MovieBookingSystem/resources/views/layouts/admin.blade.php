<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Console | MoovyMoovy</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            background-color: #080112; 
            color: white; 
            font-family: 'Montserrat', sans-serif; 
        }
        .bebas { font-family: 'Bebas Neue', cursive; }
        .sidebar { background-color: #0d0118; border-right: 1px solid #1f0b35; }
        .card-stat { 
            background: linear-gradient(145deg, #160229, #0d0118);
            border: 1px solid #3d0563; 
            border-radius: 12px;
        }
        .text-neon { color: #d000ff; }
        .bg-neon { background-color: #d000ff; }
        .nav-link { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; color: #94a3b8; }
        .nav-link:hover, .nav-link.active { color: white; }
    </style>
</head>
<body class="flex min-h-screen">
    <aside class="w-64 sidebar p-8 flex flex-col gap-10 min-h-screen sticky top-0 h-screen overflow-y-auto">
        <div class="bebas text-4xl tracking-wider text-neon">MOOVYMOOVY</div>

        <nav class="flex flex-col gap-6 uppercase">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin') || request()->is('admin/dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.movies') }}" class="nav-link {{ request()->is('admin/movies') ? 'active' : '' }}">Movies</a>
    <a href="{{ route('admin.bookings') }}" class="nav-link {{ request()->is('admin/bookings') ? 'active' : '' }}">Bookings</a>
    <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->is('admin/reviews') ? 'active' : '' }}">Reviews</a>
    <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->is('admin/reports') ? 'active' : '' }}">Reports</a>
</nav>

        {{-- Logout at bottom of sidebar --}}
        <div class="mt-auto">
            <div class="text-[10px] text-slate-600 uppercase tracking-widest mb-1">Logged in as</div>
            <div class="text-xs font-bold text-slate-300 mb-6 truncate">{{ auth()->user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-[10px] font-black tracking-widest uppercase border border-neon/30 text-neon px-4 py-2 rounded hover:bg-neon hover:text-black transition">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-12">
        @yield('content')
    </main>
</body>
</html>