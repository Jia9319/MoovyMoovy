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
                <i class="fas fa-user"></i>
            </div>
            <div class="dropdown-content">
                <a href="#" class="dropdown-item"><i class="fas fa-user-circle"></i> My Profile</a>
                <a href="#" class="dropdown-item"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                <a href="#" class="dropdown-item"><i class="fas fa-heart"></i> Watchlist</a>
                <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Settings</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>