@extends('layouts.app')

@section('title', 'Add Movie - MoovyMoovy')

@section('content')
<section style="padding:120px 5% 4rem;max-width:800px;margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <a href="{{ route('movies.index') }}" style="color:var(--muted);text-decoration:none;font-size:0.875rem;">
            <i class="fas fa-arrow-left"></i> Back to Movies
        </a>
        <h1 class="sec-title" style="margin-top:1rem;">Add <span class="acc">Movie</span></h1>
    </div>

    @if($errors->any())
    <div style="background:rgba(255,68,68,0.15);border:1px solid #ff4444;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.5rem;color:#ff4444;">
        <ul style="margin:0;padding-left:1.25rem;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data"
          style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:2rem;">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

            {{-- Title --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Title <span style="color:#ff4444;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Dune: Part Two"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            {{-- Genre --}}
            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Genre <span style="color:#ff4444;">*</span></label>
                <select name="genre" style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;">
                    <option value="">Select genre</option>
                    @foreach(['Action','Sci-Fi','Drama','Comedy','Horror','Superhero','Thriller','Romance','Animation','Documentary'] as $g)
                        <option value="{{ $g }}" {{ old('genre') == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Duration --}}
            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Duration (minutes) <span style="color:#ff4444;">*</span></label>
                <input type="number" name="duration" value="{{ old('duration') }}" placeholder="e.g. 148" min="1"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            {{-- Release Date --}}
            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Release Date <span style="color:#ff4444;">*</span></label>
                <input type="date" name="release_date" value="{{ old('release_date') }}"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            {{-- Rating --}}
            <div>
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Rating (0–10)</label>
                <input type="number" name="rating" value="{{ old('rating') }}" placeholder="e.g. 8.5" min="0" max="10" step="0.1"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;outline:none;box-sizing:border-box;">
            </div>

            {{-- Description --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Description <span style="color:#ff4444;">*</span></label>
                <textarea name="description" rows="4" placeholder="Movie synopsis..."
                    style="width:100%;background:var(--bg);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:10px;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description') }}</textarea>
            </div>

            {{-- Poster Upload --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;margin-bottom:0.5rem;font-weight:500;">Poster Image</label>
                <div style="border:2px dashed var(--border);border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:border-color 0.3s;"
                     id="dropZone" onclick="document.getElementById('posterInput').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size:2.5rem;color:var(--muted);margin-bottom:0.75rem;display:block;"></i>
                    <div style="color:var(--muted);margin-bottom:0.5rem;">Click to upload or drag & drop</div>
                    <div style="color:var(--muted);font-size:0.75rem;">JPG, PNG, WEBP — max 2MB</div>
                    <img id="posterPreview" src="" alt="" style="display:none;max-height:200px;margin-top:1rem;border-radius:8px;">
                </div>
                <input type="file" name="poster" id="posterInput" accept="image/*" style="display:none;">
            </div>
        </div>

        <div style="display:flex;gap:1rem;margin-top:2rem;">
            <button type="submit"
                style="background:var(--grad-2);border:none;color:white;padding:0.875rem 2rem;border-radius:10px;cursor:pointer;font-weight:600;font-size:0.95rem;">
                <i class="fas fa-plus"></i> Add Movie
            </button>
            <a href="{{ route('movies.index') }}"
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
const drop    = document.getElementById('dropZone');

input.addEventListener('change', () => {
    const file = input.files[0];
    if (file) { preview.src = URL.createObjectURL(file); preview.style.display = 'block'; }
});

drop.addEventListener('dragover', e => { e.preventDefault(); drop.style.borderColor = 'var(--c1)'; });
drop.addEventListener('dragleave', () => drop.style.borderColor = 'var(--border)');
drop.addEventListener('drop', e => {
    e.preventDefault();
    drop.style.borderColor = 'var(--border)';
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>
@endpush
@endsection