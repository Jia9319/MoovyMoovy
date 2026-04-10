@extends('layouts.app')

@section('title', 'Edit ' . $movie->title . ' - MoovyMoovy')

@section('content')
<section style="padding:120px 5% 4rem;max-width:800px;margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <a href="{{ route('movies.show', $movie->id) }}" style="color:var(--muted);text-decoration:none;font-size:0.875rem;">
            <i class="fas fa-arrow-left"></i> Back to Movie
        </a>
        <h1 class="sec-title" style="margin-top:1rem;">Edit <span class="acc">Movie</span></h1>
    </div>

    @if($errors->any())
    <div style="background:rgba(255,68,68,0.15);border:1px solid #ff4444;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.5rem;color:#ff4444;">
        <ul style="margin:0;padding-left:1.25rem;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data"
          style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:2rem;">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

            <div style="grid-column:1/-1;">
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Title <span style="color:#ff4444;">*</span></label>
                <input type="text" name="title" value="{{ old('title', $movie->title) }}"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Genre <span style="color:#ff4444;">*</span></label>
                <select name="genre" style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;">
                    @foreach(['Action','Sci-Fi','Drama','Comedy','Horror','Superhero','Thriller','Romance','Animation','Documentary'] as $g)
                        <option value="{{ $g }}" {{ old('genre', $movie->genre) == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Duration (minutes) <span style="color:#ff4444;">*</span></label>
                <input type="number" name="duration" value="{{ old('duration', $movie->duration) }}" min="1"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Release Date <span style="color:#ff4444;">*</span></label>
                <input type="date" name="release_date" value="{{ old('release_date', $movie->release_date->format('Y-m-d')) }}"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Rating (0–10)</label>
                <input type="number" name="rating" value="{{ old('rating', $movie->rating) }}" min="0" max="10" step="0.1"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            <div style="grid-column:1/-1;">
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Description <span style="color:#ff4444;">*</span></label>
                <textarea name="description" rows="4"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description', $movie->description) }}</textarea>
            </div>

            {{-- Current Poster --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Poster</label>
                @if($movie->poster)
                <div style="margin-bottom:1rem;">
                    <img src="{{ asset('storage/'.$movie->poster) }}" alt="Current poster"
                         style="height:160px;border-radius:8px;object-fit:cover;">
                    <p style="color:var(--muted);font-size:0.75rem;margin-top:0.5rem;">Current poster. Upload new to replace.</p>
                </div>
                @endif
                <div style="border:2px dashed var(--border);border-radius:12px;padding:1.5rem;text-align:center;cursor:pointer;"
                     onclick="document.getElementById('posterInput').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:var(--muted);margin-bottom:0.5rem;display:block;"></i>
                    <div style="color:var(--muted);font-size:0.875rem;">Click to upload new poster (optional)</div>
                    <img id="posterPreview" src="" alt="" style="display:none;max-height:160px;margin-top:1rem;border-radius:8px;">
                </div>
                <input type="file" name="poster" id="posterInput" accept="image/*" style="display:none;">
            </div>
        </div>

        <div style="display:flex;gap:1rem;margin-top:2rem;">
            <button type="submit"
                style="background:var(--grad-2);border:none;color:white;padding:0.875rem 2rem;border-radius:10px;cursor:pointer;font-weight:600;">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('movies.show', $movie->id) }}"
               style="background:transparent;border:1px solid var(--border);color:var(--muted);padding:0.875rem 1.5rem;border-radius:10px;text-decoration:none;">
                Cancel
            </a>
        </div>
    </form>
</section>

@push('scripts')
<script>
const input   = document.getElementById('posterInput');
const preview = document.getElementById('posterPreview');
input.addEventListener('change', () => {
    if (input.files[0]) { preview.src = URL.createObjectURL(input.files[0]); preview.style.display = 'block'; }
});
</script>
@endpush
@endsection