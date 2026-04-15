@extends('layouts.app')

@section('title', 'Offers & Promotions - MoovyMoovy')

@section('content')
<section style="padding:120px 5% 5rem;">

    {{-- Header --}}
    <div class="sec-head" style="margin-bottom:3rem;">
        <div>
            <h1 class="sec-title" style="font-size:3rem;">Offers & <span class="acc">Promotions</span></h1>
            <p style="color:var(--muted);margin-top:0.5rem;">Enter a promo code below or browse current offers.</p>
        </div>
    </div>

    {{-- Promo Code Input --}}
    <div class="offer-redeem-box">
        <div class="offer-redeem-inner">
            <div style="margin-bottom:1rem;">
                <div style="font-family:'Bebas Neue';font-size:1.5rem;letter-spacing:1px;margin-bottom:0.25rem;">
                    Got a promo <span class="acc">code?</span>
                </div>
                <p style="color:var(--muted);font-size:0.875rem;">Enter your code to claim the discount.</p>
            </div>

            @auth
            {{-- Success result --}}
            @if(session('offer_success'))
            @php $s = session('offer_success'); @endphp
            <div class="offer-success-banner">
                <div style="font-size:2rem;margin-bottom:0.5rem;">🎉</div>
                <div style="font-weight:700;font-size:1.1rem;margin-bottom:0.25rem;">Code Applied!</div>
                <div style="color:rgba(255,255,255,0.85);font-size:0.875rem;">
                    <strong>{{ $s['title'] }}</strong> — {{ $s['discount'] }}% off
                </div>
                <div style="margin-top:0.75rem;background:rgba(255,255,255,0.15);border-radius:8px;padding:0.5rem 1rem;font-family:monospace;font-size:1rem;letter-spacing:2px;">
                    {{ $s['code'] }}
                </div>
                <p style="color:rgba(255,255,255,0.7);font-size:0.75rem;margin-top:0.5rem;">
                    Show this code at the counter when purchasing your ticket.
                </p>
            </div>
            @endif

            {{-- Error --}}
            @if(session('offer_error'))
            <div class="offer-error-banner">
                <i class="fas fa-exclamation-circle"></i> {{ session('offer_error') }}
            </div>
            @endif

            <form action="{{ route('offers.redeem') }}" method="POST" class="offer-code-form">
                @csrf
                <div class="offer-code-input-wrap">
                    <input type="text" name="code" class="offer-code-input"
                           placeholder="e.g. STUDENT50"
                           value="{{ old('code') }}"
                           autocomplete="off"
                           style="text-transform:uppercase;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-tag"></i> Apply Code
                    </button>
                </div>
                @error('code')
                <div class="field-err" style="display:block;">{{ $message }}</div>
                @enderror
            </form>
            @else
            <div style="background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:12px;padding:1.25rem;text-align:center;">
                <i class="fas fa-lock" style="color:var(--c1);font-size:1.5rem;margin-bottom:0.5rem;display:block;"></i>
                <p style="color:var(--muted);font-size:0.875rem;margin-bottom:0.75rem;">
                    Please log in to redeem a promo code.
                </p>
                <a href="{{ route('login') }}" class="btn-primary" style="font-size:0.875rem;padding:0.6rem 1.5rem;">
                    <i class="fas fa-sign-in-alt"></i> Login to Redeem
                </a>
            </div>
            @endauth
        </div>
    </div>

    {{-- Offer Cards --}}
    <h2 class="sec-title" style="margin-bottom:1.5rem;margin-top:3rem;">
        Current <span class="acc">Offers</span>
    </h2>

    @if($offers->isEmpty())
    <div class="empty-state">
        <i class="fas fa-ticket-alt"></i>
        <p>No active offers right now. Check back soon!</p>
    </div>
    @else
    <div class="offers-grid">
        @foreach($offers as $offer)
        <div class="offer-card">
            {{-- Discount badge --}}
            <div class="offer-badge">{{ $offer->discount_percent }}% OFF</div>

            <div class="offer-card-body">
                <h3 class="offer-title">{{ $offer->title }}</h3>
                <p class="offer-desc">{{ $offer->description }}</p>

                {{-- Code box --}}
                <div class="offer-code-box" onclick="copyCode('{{ $offer->code }}', this)" title="Click to copy">
                    <span class="offer-code-text">{{ $offer->code }}</span>
                    <span class="offer-copy-hint"><i class="fas fa-copy"></i> Copy</span>
                </div>

                {{-- Validity --}}
                <div class="offer-meta">
                    <span><i class="fas fa-calendar-alt"></i>
                        Valid until {{ $offer->valid_until->format('d M Y') }}
                    </span>
                    @if($offer->max_uses)
                    <span><i class="fas fa-users"></i>
                        {{ $offer->max_uses - $offer->used_count }} uses left
                    </span>
                    @endif
                </div>

                {{-- Terms --}}
                @if($offer->terms)
                <details class="offer-terms">
                    <summary>Terms & Conditions</summary>
                    <p>{{ $offer->terms }}</p>
                </details>
                @endif

                {{-- Redeem button --}}
                @auth
                    @if($offer->isRedeemedBy(auth()->id()))
                    <div class="offer-redeemed-tag">
                        <i class="fas fa-check-circle"></i> Already Redeemed
                    </div>
                    @else
                    <form action="{{ route('offers.redeem') }}" method="POST" style="margin-top:1rem;">
                        @csrf
                        <input type="hidden" name="code" value="{{ $offer->code }}">
                        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                            <i class="fas fa-tag"></i> Redeem This Offer
                        </button>
                    </form>
                    @endif
                @else
                <a href="{{ route('login') }}" class="btn-secondary" style="width:100%;justify-content:center;margin-top:1rem;text-align:center;">
                    Login to Redeem
                </a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

@push('scripts')
<script>
function copyCode(code, el) {
    navigator.clipboard.writeText(code).then(function() {
        var hint = el.querySelector('.offer-copy-hint');
        var orig = hint.innerHTML;
        hint.innerHTML = '<i class="fas fa-check"></i> Copied!';
        hint.style.color = '#22c55e';
        setTimeout(function() {
            hint.innerHTML = orig;
            hint.style.color = '';
        }, 2000);
    });
}

// Auto-uppercase the code input
var codeInput = document.querySelector('.offer-code-input');
if (codeInput) {
    codeInput.addEventListener('input', function() {
        var pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
}
</script>
@endpush

@push('styles')
<style>
/* ── Redeem Box ── */
.offer-redeem-box {
    background: linear-gradient(135deg, rgba(150,20,208,0.25), rgba(102,0,148,0.1));
    border: 1px solid rgba(209,106,255,0.35);
    border-radius: 20px;
    padding: 2rem;
    max-width: 600px;
}
.offer-redeem-inner { width: 100%; }

.offer-code-form { margin-top: 1rem; }
.offer-code-input-wrap {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.offer-code-input {
    flex: 1;
    min-width: 200px;
    background: var(--bg);
    border: 1px solid var(--border);
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-size: 1rem;
    font-family: monospace;
    letter-spacing: 2px;
    outline: none;
    transition: border-color 0.2s;
}
.offer-code-input:focus { border-color: var(--c1); box-shadow: 0 0 0 3px rgba(209,106,255,0.15); }
.offer-code-input::placeholder { letter-spacing: 1px; font-family: 'DM Sans', sans-serif; color: var(--muted); }

/* Success / Error banners */
.offer-success-banner {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    margin-bottom: 1rem;
    color: white;
}
.offer-error-banner {
    background: rgba(239,68,68,0.15);
    border: 1px solid #ef4444;
    color: #ef4444;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

/* ── Offer Cards Grid ── */
.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}
.offer-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    transition: border-color 0.3s, transform 0.3s;
    position: relative;
}
.offer-card:hover {
    border-color: var(--c1);
    transform: translateY(-4px);
}

/* Discount badge (top-right ribbon) */
.offer-badge {
    position: absolute;
    top: 1rem; right: 1rem;
    background: var(--grad-2);
    color: white;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.1rem;
    letter-spacing: 1px;
    padding: 0.3rem 0.85rem;
    border-radius: 20px;
}

.offer-card-body { padding: 1.5rem; }

.offer-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    padding-right: 4rem; /* avoid overlap with badge */
    line-height: 1.3;
}
.offer-desc {
    color: var(--muted);
    font-size: 0.875rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

/* Code box — click to copy */
.offer-code-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(209,106,255,0.1);
    border: 1.5px dashed rgba(209,106,255,0.5);
    border-radius: 10px;
    padding: 0.6rem 1rem;
    cursor: pointer;
    transition: background 0.2s;
    margin-bottom: 1rem;
}
.offer-code-box:hover { background: rgba(209,106,255,0.18); }
.offer-code-text {
    font-family: monospace;
    font-size: 1.1rem;
    letter-spacing: 3px;
    color: var(--c1);
    font-weight: 700;
}
.offer-copy-hint {
    font-size: 0.75rem;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: color 0.2s;
}

/* Validity & meta */
.offer-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: 0.78rem;
    color: var(--muted);
    margin-bottom: 0.75rem;
}
.offer-meta i { color: var(--c1); margin-right: 0.25rem; }

/* Terms accordion */
.offer-terms {
    font-size: 0.78rem;
    color: var(--muted);
    margin-bottom: 0.75rem;
}
.offer-terms summary {
    cursor: pointer;
    color: var(--c1);
    font-size: 0.8rem;
    margin-bottom: 0.4rem;
    list-style: none;
}
.offer-terms summary::before { content: '▸ '; }
.offer-terms[open] summary::before { content: '▾ '; }
.offer-terms p { margin-top: 0.4rem; line-height: 1.5; }

/* Already redeemed tag */
.offer-redeemed-tag {
    margin-top: 1rem;
    text-align: center;
    color: #22c55e;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.6rem;
    background: rgba(34,197,94,0.1);
    border: 1px solid rgba(34,197,94,0.3);
    border-radius: 8px;
}

@media (max-width: 600px) {
    .offer-code-input-wrap { flex-direction: column; }
    .offer-code-input-wrap .btn-primary { width: 100%; justify-content: center; }
}
</style>
@endpush
@endsection