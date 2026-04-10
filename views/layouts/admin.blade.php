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
    <aside class="w-64 sidebar p-8 flex flex-col gap-10">
        <div class="bebas text-4xl tracking-wider text-neon">MOOVYMOOVY</div>
        <nav class="flex flex-col gap-6 uppercase">
            <a href="/admin" class="nav-link active">Dashboard</a>
            <a href="#" class="nav-link">Movies</a>
            <a href="#" class="nav-link">Reports</a>
        </nav>
    </aside>

    <main class="flex-1 p-12">
        @yield('content')
    </main>
</body>
</html>