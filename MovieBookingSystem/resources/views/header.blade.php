<nav id="navbar">
    <a href="{{ url('/') }}" class="logo">
        
        <div>
            <div class="logo-text">MoovyMoovy</div>
        </div>
    </a>

    <ul class="nav-links" id="navLinks">
        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
        <li><a href="{{ url('/movies') }}" class="{{ request()->is('movies*') ? 'active' : '' }}">Now Showing</a></li>
        <li><a href="#" class="{{ request()->is('coming-soon') ? 'active' : '' }}">Coming Soon</a></li>
        <li><a href="{{ url('/cinemas') }}" class="{{ request()->is('cinemas*') ? 'active' : '' }}">Cinemas</a></li>
        <li><a href="#" class="{{ request()->is('offers') ? 'active' : '' }}">Offers</a></li>
    </ul>

    <div class="nav-right">
        <div class="nav-search">
            <i class="fas fa-search" style="color: #b494cc; font-size: 13px;"></i>
            <input type="text" placeholder="Search movies...">
        </div>
        
        <div class="profile-dropdown">
            <div class="profile-avatar">
                @auth
                    <span class="text-[10px] font-bold text-purple-300">{{ substr(Auth::user()->name, 0, 1) }}</span>
                @else
                    <i class="fas fa-user text-purple-300/50"></i>
                @endauth
            </div>

            <div class="dropdown-content">
                @auth
                    <div class="px-4 py-2 border-b border-white/5 mb-1">
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Account</p>
                        <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    </div>
                @endauth

                <a href="{{ auth()->check() ? route('profile.show') : route('login') }}" class="dropdown-item">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                
                <a href="{{ auth()->check() ? route('profile.reviews') : route('login') }}" class="dropdown-item">
                    <i class="fas fa-star"></i> My Reviews
                </a>

                <a href="{{ auth()->check() ? url('/bookings') : route('login') }}" class="dropdown-item">
                    <i class="fas fa-ticket-alt"></i> My Bookings
                </a>

                <div class="dropdown-divider"></div>

                @auth
                    <a href="#" class="dropdown-item text-red-400" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="dropdown-item font-bold text-purple-400">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                    <a href="{{ route('register') }}" class="dropdown-item">
                        <i class="fas fa-user-plus"></i> Join Moovy
                    </a>
                @endauth
            </div>
        </div>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>