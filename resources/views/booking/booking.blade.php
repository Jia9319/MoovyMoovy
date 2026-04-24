@extends('layouts.app')

@section('title', 'Choose Cinema & Time - ' . $movie['title'])

@section('content')
<div id="booking-select-root"></div>

<style>
.booking-select-page { min-height: 100vh; padding: 108px 5% 3rem; }
.booking-select-card { max-width: 980px; margin: 0 auto; background: rgba(15, 15, 15, 0.92); border: 1px solid var(--border); border-radius: 18px; padding: 1.5rem; }
.booking-select-head { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.2rem; }
.seat-back { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; color: #fff !important; border-radius: 12px; border: 1px solid var(--border); text-decoration: none; transition: all 0.25s; }
.seat-back:hover { border-color: var(--c1); color: #fff !important; box-shadow: 0 0 0 1px rgba(209, 106, 255, 0.35); }
.booking-select-head h1 { margin: 0; font-size: clamp(1.4rem, 3vw, 2rem); }
.booking-select-head p { margin-top: 0.2rem; color: var(--muted); }
.booking-grid { display: grid; gap: 1.25rem; }
.booking-form label { display: inline-block; margin-bottom: 0.55rem; color: var(--muted); font-size: 0.9rem; }
.options { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.7rem; }
.option-btn { text-align: left; border: 1px solid var(--border); border-radius: 12px; padding: 0.8rem 0.9rem; background: rgba(255, 255, 255, 0.02); color: var(--white); cursor: pointer; }
.option-btn strong { display: block; margin-bottom: 0.2rem; }
.option-btn span { color: var(--muted); font-size: 0.85rem; }
.option-btn.active { border-color: var(--c1); box-shadow: 0 0 0 1px rgba(209, 106, 255, 0.3); }
.chips { display: flex; flex-wrap: wrap; gap: 0.55rem; }
.chip input { display: none; }
.chip-btn { border: 1px solid var(--border); background: transparent; color: var(--muted); padding: 0.5rem 0.75rem; border-radius: 999px; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
.chip-btn.active { border-color: var(--c1); color: var(--white); background: rgba(209, 106, 255, 0.2); }
.chip span { display: inline-block; padding: 0.5rem 0.75rem; border-radius: 999px; border: 1px solid var(--border); color: var(--muted); font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
.chip input:checked + span { border-color: var(--c1); color: var(--white); background: rgba(209, 106, 255, 0.2); }
.booking-footer { margin-top: 1.5rem; border-top: 1px solid var(--border); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.booking-footer small { color: var(--muted); }
.booking-footer p { margin-top: 0.2rem; }
.next-btn { border: none; border-radius: 12px; padding: 0.8rem 1.1rem; font-weight: 700; color: #fff; background: var(--grad-2); cursor: pointer; }

@media (max-width: 720px) {
    .booking-select-page { padding: 95px 4% 2rem; }
    .booking-footer { flex-direction: column; align-items: flex-start; }
}
</style>

@push('scripts')
<script>
    window.MoovyBookingSelectData = @json($bookingSelectData);
</script>
@endpush
@endsection