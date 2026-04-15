@extends('layouts.app')

@section('title', 'Add Showtime - MoovyMoovy')

@section('content')

<div class="showtime-create-container">
    <div class="showtime-create-card">
        <div class="create-header">
            <div class="header-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="header-text">
                <h1>Add New Showtime</h1>
                <p>Fill in the details to add a new showtime</p>
            </div>
        </div>

        <form action="{{ route('showtimes.store') }}" method="POST" class="showtime-form">
            @csrf

            <div class="form-group">
                <label for="movie_id" class="form-label">
                    <i class="fas fa-film"></i>
                    Select Movie <span class="required">*</span>
                </label>
                <select name="movie_id" id="movie_id" class="form-select" required>
                    <option value="">-- Choose a movie --</option>
                    @foreach($movies ?? [] as $movieItem)
                        <option value="{{ $movieItem->id }}" {{ (request('movie_id') == $movieItem->id) ? 'selected' : '' }}>
                            {{ $movieItem->title }} ({{ $movieItem->duration }} min) - {{ $movieItem->genre }}
                        </option>
                    @endforeach
                </select>
                @error('movie_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cinema" class="form-label">
                    <i class="fas fa-building"></i>
                    Cinema Name <span class="required">*</span>
                </label>
                <input type="text" name="cinema" id="cinema" class="form-input" 
                       placeholder="e.g., GSC Mid Valley, TGV Sunway"
                       value="{{ old('cinema') }}" required>
                @error('cinema')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="date" class="form-label">
                        <i class="fas fa-calendar-alt"></i>
                        Show Date <span class="required">*</span>
                    </label>
                    <input type="date" name="date" id="date" class="form-input" 
                           value="{{ old('date') }}" required>
                    @error('date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group half">
                    <label for="time" class="form-label">
                        <i class="fas fa-clock"></i>
                        Show Time <span class="required">*</span>
                    </label>
                    <input type="time" name="time" id="time" class="form-input" 
                           value="{{ old('time') }}" required>
                    @error('time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="hall" class="form-label">
                        <i class="fas fa-door-open"></i>
                        Hall Number
                    </label>
                    <input type="text" name="hall" id="hall" class="form-input" 
                           placeholder="e.g., Hall 1"
                           value="{{ old('hall') }}">
                </div>

                <div class="form-group half">
                    <label for="format" class="form-label">
                        <i class="fas fa-video"></i>
                        Format
                    </label>
                    <select name="format" id="format" class="form-select">
                        <option value="">-- Select format --</option>
                        <option value="2D">2D</option>
                        <option value="3D">3D</option>
                        <option value="IMAX">IMAX</option>
                        <option value="4DX">4DX</option>
                        <option value="Dolby Atmos">Dolby Atmos</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="price" class="form-label">
                    <i class="fas fa-tag"></i>
                    Ticket Price (RM) <span class="required">*</span>
                </label>
                <div class="price-input-wrapper">
                    <span class="currency">RM</span>
                    <input type="number" name="price" id="price" class="form-input price-input" 
                           placeholder="0.00" step="0.01" min="0" 
                           value="{{ old('price') }}" required>
                </div>
                @error('price')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="available_seats" class="form-label">
                    <i class="fas fa-chair"></i>
                    Available Seats
                </label>
                <input type="number" name="available_seats" id="available_seats" class="form-input" 
                       placeholder="Total seats available"
                       value="{{ old('available_seats', 200) }}" min="1">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Add Showtime
                </button>
                <a href="{{ url()->previous() }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.showtime-create-container {
    min-height: 100vh;
    padding: 120px 5% 80px;
    background: linear-gradient(135deg, #0a0010 0%, #120020 100%);
}

.showtime-create-card {
    max-width: 700px;
    margin: 0 auto;
    background: #120020;
    border-radius: 28px;
    border: 1px solid rgba(255,255,255,0.1);
    overflow: hidden;
}

.create-header {
    padding: 2rem;
    background: linear-gradient(135deg, rgba(209,106,255,0.08), rgba(150,20,208,0.05));
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #9614d0, #660094);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
}

.header-text h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    margin-bottom: 0.25rem;
    background: linear-gradient(135deg, #d16aff, #9614d0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-text p {
    color: rgba(255,255,255,0.5);
    font-size: 0.875rem;
}

.showtime-form {
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: flex;
    gap: 1.5rem;
}

.form-group.half {
    flex: 1;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    color: rgba(255,255,255,0.85);
}

.form-label i {
    color: #d16aff;
}

.required {
    color: #ef4444;
}

.form-input,
.form-select {
    width: 100%;
    background: #0a0010;
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    padding: 0.875rem 1rem;
    border-radius: 12px;
    outline: none;
    transition: all 0.2s;
    font-size: 0.9rem;
}

.form-input:focus,
.form-select:focus {
    border-color: #d16aff;
    box-shadow: 0 0 0 3px rgba(209,106,255,0.1);
}

.price-input-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.currency {
    background: linear-gradient(135deg, #9614d0, #660094);
    padding: 0.875rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    color: white;
}

.price-input {
    flex: 1;
}

.error-message {
    display: block;
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.375rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.btn-submit {
    background: linear-gradient(135deg, #9614d0, #660094);
    color: white;
    border: none;
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    justify-content: center;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(150,20,208,0.4);
}

.btn-cancel {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.5);
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
    flex: 1;
}

.btn-cancel:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    border-color: #d16aff;
}

input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .create-header {
        flex-direction: column;
        text-align: center;
    }
    
    .showtime-form {
        padding: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }
});
</script>
@endsection