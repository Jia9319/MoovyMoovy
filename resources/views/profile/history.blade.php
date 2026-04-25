@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
<style>
	html {
		scroll-behavior: smooth;
	}

	body {
		background: #0a0510;
		color: #fff;
	}

	.history-page {
		min-height: 100vh;
		padding: 100px 4.5% 40px;
		background:
			radial-gradient(circle at top left, rgba(223, 183, 255, 0.16), transparent 35%),
			radial-gradient(circle at top right, rgba(255, 160, 92, 0.12), transparent 30%),
			#0a0510;
	}

	.history-shell {
		max-width: 1000px;
		margin: 0 auto;
	}

	.history-hero {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		gap: 1rem;
		align-items: end;
		margin-bottom: 1.5rem;
	}

	.history-hero h1 {
		font-size: clamp(2rem, 4vw, 3.4rem);
		margin: 0;
		font-family: 'Bebas Neue', sans-serif;
		letter-spacing: 1px;
	}

	.history-hero p {
		margin: 0.35rem 0 0;
		color: rgba(255, 255, 255, 0.66);
		max-width: 640px;
	}

	.history-stats {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 12px;
		margin-bottom: 18px;
	}

	.history-stat {
		background: rgba(22, 13, 33, 0.94);
		border: 1px solid rgba(223, 183, 255, 0.12);
		border-radius: 18px;
		padding: 16px 18px;
	}

	.history-stat span {
		display: block;
		color: rgba(255, 255, 255, 0.5);
		font-size: 0.78rem;
		letter-spacing: 1px;
		text-transform: uppercase;
		margin-bottom: 6px;
	}

	.history-stat strong {
		font-size: 1.5rem;
		font-family: 'Bebas Neue', sans-serif;
		letter-spacing: 1px;
	}

	.history-list {
		display: grid;
		gap: 18px;
	}

	.ticket-link {
		display: block;
		text-decoration: none;
		color: inherit;
	}

	.ticket-link:focus-visible {
		outline: 2px solid #d16aff;
		outline-offset: 4px;
		border-radius: 24px;
	}

	.ticket-card {
		border: 1px solid rgba(255, 255, 255, 0.08);
		border-radius: 22px;
		background: rgba(15, 15, 15, 0.94);
		box-shadow: 0 20px 50px rgba(0, 0, 0, 0.26);
		overflow: hidden;
		transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
	}

	.ticket-card-inner {
		display: grid;
		grid-template-columns: minmax(0, 1.5fr) minmax(230px, 0.66fr);
	}

	.ticket-link:hover .ticket-card {
		transform: translateY(-2px);
		border-color: rgba(227, 192, 255, 0.5);
		background: rgba(23, 10, 37, 0.99);
		box-shadow: 0 28px 70px rgba(61, 15, 91, 0.28);
	}

	.ticket-link:hover .ticket-main {
		padding-left: 1.55rem;
	}

	.ticket-link:hover .ticket-side {
		background: linear-gradient(180deg, rgba(227, 192, 255, 0.16), rgba(255, 255, 255, 0.03));
	}

	.ticket-main {
		padding: 1.15rem 1.2rem 1.15rem 1.2rem;
	}

	.ticket-head {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		gap: 0.85rem;
		margin-bottom: 1.1rem;
	}

	.ticket-head h2 {
		margin: 0;
		font-size: clamp(1.7rem, 2.8vw, 2.35rem);
		line-height: 1.05;
		font-family: 'Bebas Neue', sans-serif;
		letter-spacing: 1px;
	}

	.ticket-meta {
		color: rgba(255, 255, 255, 0.58);
		font-size: 0.9rem;
		margin-top: 0.3rem;
	}

	.ticket-badge {
		align-self: start;
		padding: 0.35rem 0.75rem;
		border-radius: 999px;
		border: 1px solid rgba(227, 192, 255, 0.28);
		color: #e7b8ff;
		background: rgba(227, 192, 255, 0.1);
		font-size: 0.78rem;
		font-weight: 700;
		letter-spacing: 0.5px;
		text-transform: uppercase;
	}

	.ticket-link:hover .ticket-badge {
		border-color: rgba(227, 192, 255, 0.6);
		background: rgba(227, 192, 255, 0.18);
		box-shadow: 0 0 0 1px rgba(227, 192, 255, 0.08), 0 0 24px rgba(227, 192, 255, 0.14);
	}

	.ticket-grid span,
	.ticket-side span {
		display: block;
		color: rgba(255, 255, 255, 0.46);
		font-size: 0.78rem;
		margin-bottom: 0.2rem;
		text-transform: uppercase;
		letter-spacing: 0.8px;
	}

	.ticket-grid strong,
	.ticket-side strong {
		display: block;
		font-size: 1rem;
		line-height: 1.35;
	}

	.ticket-side {
		background: linear-gradient(180deg, rgba(227, 192, 255, 0.08), rgba(255, 255, 255, 0.02));
		border-left: 1px solid rgba(255, 255, 255, 0.08);
		padding: 1.15rem;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		gap: 1rem;
	}

	.ticket-quick {
		display: grid;
		gap: 0.72rem;
	}

	.ticket-quick.left-metadata {
		gap: 0.55rem;
		margin-top: 0.2rem;
	}

	.ticket-quick-item {
		background: rgba(255, 255, 255, 0.03);
		border: 1px solid rgba(255, 255, 255, 0.05);
		border-radius: 16px;
		padding: 0.72rem 0.85rem;
	}

	.qr-box {
		width: 100%;
		aspect-ratio: 1;
		border-radius: 18px;
		border: 1px solid rgba(255, 255, 255, 0.08);
		background: #fff;
		padding: 8px;
		display: flex;
		align-items: center;
		justify-content: center;
		overflow: hidden;
	}

	.qr-box img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		border-radius: 12px;
	}

	.ticket-image {
		width: 100%;
		aspect-ratio: 1;
		border-radius: 18px;
		overflow: hidden;
		border: 1px solid rgba(255, 255, 255, 0.08);
		background: #fff;
		padding: 8px;
		margin-bottom: 0.85rem;
	}

	.ticket-image img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
		border-radius: 12px;
	}

	.ticket-total {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 1rem;
		padding-top: 0.9rem;
		border-top: 1px solid rgba(255, 255, 255, 0.08);
		margin-top: 1rem;
	}

	.ticket-total span {
		color: rgba(255, 255, 255, 0.55);
		font-size: 0.85rem;
	}

	.ticket-total strong {
		font-size: 1.8rem;
		font-family: 'Bebas Neue', sans-serif;
		letter-spacing: 1px;
	}

	.empty-state {
		border: 1px dashed rgba(209, 106, 255, 0.2);
		border-radius: 20px;
		background: rgba(255, 255, 255, 0.03);
		padding: 2.2rem;
		text-align: center;
		color: rgba(255, 255, 255, 0.7);
	}

	.empty-state h2 {
		margin-bottom: 0.4rem;
	}

	.summary-link {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		margin-top: 1rem;
		padding: 0.85rem 1.15rem;
		border-radius: 12px;
		text-decoration: none;
		color: #fff;
		background: linear-gradient(135deg, #d16aff, #ff9b63);
		font-weight: 700;
	}

	@media (max-width: 960px) {
		.history-stats,
		.ticket-card-inner {
			grid-template-columns: 1fr;
		}

		.ticket-side {
			border-left: 0;
			border-top: 1px solid rgba(255, 255, 255, 0.08);
		}

			.ticket-card-inner {
				grid-template-columns: 1fr;
			}
	}

	@media (max-width: 640px) {
		.history-page {
			padding-inline: 16px;
		}
	}
</style>

<section class="history-page">
	<div class="history-shell">
		<div class="history-hero">
			<div>
				<h1>My Tickets</h1>
			</div>
		</div>

		<div class="history-stats">
			<div class="history-stat">
				<span>Total Tickets</span>
				<strong>{{ $stats['totalTickets'] }}</strong>
			</div>
			<div class="history-stat">
				<span>Total Spent</span>
				<strong>RM {{ number_format((float) $stats['spent'], 2) }}</strong>
			</div>
		</div>

		<div class="history-list">
			@forelse($tickets as $ticket)
				<a href="{{ route('bookings.show', $ticket) }}" class="ticket-link" aria-label="View details for {{ optional($ticket->movie)->title ?? $ticket->ticket_code }}">
					<article class="ticket-card">
						<div class="ticket-card-inner">
							<div class="ticket-main">
								<div class="ticket-head">
									<div>
										<h2>{{ optional($ticket->movie)->title ?? $ticket->ticket_code }}</h2>
									</div>
								</div>

								<div class="ticket-quick left-metadata">
									<div class="ticket-quick-item">
										<span>Cinema</span>
										<strong>{{ $ticket->cinema ?: 'N/A' }}</strong>
									</div>
									<div class="ticket-quick-item">
										<span>Hall</span>
										@php
											$hallLabel = trim((string) $ticket->hall);
											$hallLabel = preg_replace('/^\s*hall\s*/i', '', $hallLabel);
										@endphp
										<strong>{{ $hallLabel !== '' ? $hallLabel : 'N/A' }}</strong>
									</div>
									<div class="ticket-quick-item">
										<span>Seats</span>
										<strong>{{ is_array($ticket->seats) ? implode(', ', $ticket->seats) : trim((string) $ticket->seats) }}</strong>
									</div>
									<div class="ticket-quick-item">
										<span>Date</span>
										<strong>{{ $ticket->date->format('D, d M Y') }}</strong>
									</div>
									<div class="ticket-quick-item">
										<span>Time</span>
										<strong>{{ $ticket->time }}</strong>
									</div>
								</div>
							</div>

							<div class="ticket-side">
								<div>
									<span>E-Ticket</span>
									@php
										$ticketQrUrl = $ticket->qr_url ?: 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($ticket->ticket_code);
									@endphp
									<div class="ticket-image">
										<img src="{{ $ticketQrUrl }}" alt="QR code for {{ $ticket->ticket_code }}">
									</div>
									<div class="ticket-quick-item">
										<span>Booked At</span>
										<strong>{{ $ticket->created_at->format('d M Y, h:i A') }}</strong>
									</div>
								</div>
							</div>
						</div>
					</article>
				</a>
			@empty
				<div class="empty-state">
					<h2>No tickets yet</h2>
					<p>Your ticket history will appear here after you complete a booking.</p>
					<a href="{{ route('movies.index') }}" class="summary-link">Browse Movies</a>
				</div>
			@endforelse
		</div>
	</div>
</section>
@endsection
