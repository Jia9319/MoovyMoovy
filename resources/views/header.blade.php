<nav id="navbar">
    <a href="{{ url('/') }}" class="logo">
        <div>
            <div class="logo-text">MoovyMoovy</div>
        </div>
    </a>

    <ul class="nav-links" id="navLinks">
        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
        <li><a href="{{ route('movies.index') }}" class="{{ request()->routeIs('movies.index') ? 'active' : '' }}">Now Showing</a></li>
        <li><a href="{{ route('movies.coming-soon') }}" class="{{ request()->routeIs('movies.coming-soon') ? 'active' : '' }}">Coming Soon</a></li>
        <li><a href="{{ route('cinemas.index') }}" class="{{ request()->routeIs('cinemas.*') ? 'active' : '' }}">Cinemas</a></li>
        <li><a href="{{ route('offers.index') }}" class="{{ request()->routeIs('offers.*') ? 'active' : '' }}">Offers</a></li>
    </ul>

    <div class="nav-right">
        <div class="nav-search">
            <i class="fas fa-search" style="color: #b494cc; font-size: 13px;"></i>
            <input type="text" placeholder="Search movies...">
        </div>
        
        <div class="profile-dropdown">
            <div class="profile-avatar">
                @auth
                    <span style="font-size: 10px; font-weight: bold; color: #d16aff;">{{ substr(Auth::user()->name, 0, 1) }}</span>
                @else
                    <i class="fas fa-user" style="color: rgba(209,106,255,0.5);"></i>
                @endauth
            </div>

            <div class="dropdown-content">
                @auth
                    <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 0.25rem;">
                        <p style="font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 0.25rem;">Account</p>
                        <p style="font-size: 0.85rem; font-weight: 600; color: white; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Auth::user()->name }}</p>
                    </div>
                @endauth

                <a href="{{ auth()->check() ? route('profile.show') : route('login') }}" class="dropdown-item">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                
                <a href="{{ auth()->check() ? route('profile.reviews') : route('login') }}" class="dropdown-item">
                    <i class="fas fa-star"></i> My Reviews
                </a>

                <a href="{{ auth()->check() ? route('bookings.history') : route('login') }}" class="dropdown-item">
                    <i class="fas fa-ticket-alt"></i> My Tickets
                </a>

                <div style="height: 1px; background: rgba(255,255,255,0.05); margin: 0.25rem 0;"></div>

                @auth
                    <a href="#" class="dropdown-item" style="color: #ef4444;" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="dropdown-item" style="color: #d16aff; font-weight: 600;">
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