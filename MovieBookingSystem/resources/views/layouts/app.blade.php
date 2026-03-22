<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MoovyMoovy — Book Your Experience')</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300&family=Cormorant+Garamond:ital,wght@1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>

<div class="stars" id="stars"></div>
<button class="scroll-top" id="scrollTop"><i class="fas fa-arrow-up"></i></button>

{{-- ✅ FIXED: was @include('header') and @include('footer') --}}
@include('header')

<main>
    @if(session('success'))
    <div id="flash-msg" style="position:fixed;top:80px;right:20px;z-index:9999;background:#22c55e;color:white;padding:0.875rem 1.5rem;border-radius:12px;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,0.3);">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>{ const el=document.getElementById('flash-msg'); if(el) el.remove(); },3000);</script>
    @endif
    @if(session('error'))
    <div id="flash-err" style="position:fixed;top:80px;right:20px;z-index:9999;background:#ef4444;color:white;padding:0.875rem 1.5rem;border-radius:12px;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,0.3);">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    <script>setTimeout(()=>{ const el=document.getElementById('flash-err'); if(el) el.remove(); },3000);</script>
    @endif

    @yield('content')
</main>

@include('footer')

<script>
const starsEl = document.getElementById('stars');
for (let i = 0; i < 200; i++) {
    const s = document.createElement('div');
    s.className = 'star';
    const size = Math.random() * 3 + 0.5;
    s.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--dur:${Math.random()*5+3}s;--lo:${Math.random()*0.3+0.05};--hi:${Math.random()*0.6+0.4};animation-delay:${Math.random()*6}s;`;
    starsEl.appendChild(s);
}
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 50));
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navLinks = document.getElementById('navLinks');
if (mobileMenuBtn && navLinks) {
    mobileMenuBtn.addEventListener('click', e => { e.stopPropagation(); navLinks.classList.toggle('active'); });
    navLinks.querySelectorAll('a').forEach(l => l.addEventListener('click', () => navLinks.classList.remove('active')));
    document.addEventListener('click', e => { if (!navLinks.contains(e.target) && !mobileMenuBtn.contains(e.target)) navLinks.classList.remove('active'); });
}
const scrollBtn = document.getElementById('scrollTop');
window.addEventListener('scroll', () => scrollBtn.classList.toggle('show', window.scrollY > 300));
scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>
@stack('scripts')
</body>
</html>