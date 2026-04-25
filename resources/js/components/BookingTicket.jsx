import React from 'react';
import { BookingGuardFallback, useBookingAuthGuard } from './bookingAuth';
import { formatCurrency, formatLongDate } from './bookingUtils';

export default function BookingTicket({ initialData }) {
    const auth = useBookingAuthGuard(initialData);
    const ticket = initialData.ticket || {};
    const foodLines = ticket.foodLines || [];
    const seats = ticket.seats || [];
    const isCancelled = String(ticket.status || '').toLowerCase() === 'cancelled';

    if (auth.isLoading) {
        return <BookingGuardFallback message="Checking your session..." />;
    }

    if (!auth.isAuthorized) {
        return <BookingGuardFallback message="Please log in to view your ticket." />;
    }

    return (
        <section className="ticket-page">
            <div className="ticket-card">
                <div className="ticket-head">
                    <a href={initialData.homeUrl} className="seat-back" title="Back to home">
                        <i className="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1>Your E-Ticket</h1>
                        <p className="sub">{isCancelled ? 'This ticket has been cancelled. QR code is disabled.' : 'Payment successful. Show this QR code at cinema entry.'}</p>
                    </div>
                </div>

                <div className="ticket-layout">
                    <div className="ticket-details">
                        <div className="row"><span>Ticket Code</span><strong>{ticket.ticketCode}</strong></div>
                        <div className="row"><span>Movie</span><strong>{ticket.title}</strong></div>
                        <div className="row"><span>Cinema</span><strong>{ticket.cinema} • {ticket.hall} • {ticket.format}</strong></div>
                        <div className="row"><span>Date & Time</span><strong>{formatLongDate(ticket.date)} {ticket.time}</strong></div>
                        <div className="row"><span>Seats</span><strong>{seats.join(', ')}</strong></div>
                        <div className="row"><span>Payment</span><strong>{String(ticket.paymentMethod || 'N/A').toUpperCase()}</strong></div>
                        <div className="row"><span>Status</span><strong className={isCancelled ? 'status-pill cancelled' : 'status-pill paid'}>{isCancelled ? 'CANCELLED' : 'PAID'}</strong></div>
                        <div className="row"><span>Seat Total</span><strong>RM {formatCurrency(ticket.seatTotal)}</strong></div>
                        <div className="row"><span>Food Total</span><strong>RM {formatCurrency(ticket.foodTotal)}</strong></div>
                        <div className="addons-block">
                            {foodLines.length ? foodLines.map((line) => (
                                <div className="row small" key={`${line.key || line.name}-${line.qty}`}>
                                    <span>
                                        {line.name} x{line.qty}
                                        {line.category === 'beverage' && line.temperatureLabel ? ` • ${line.temperatureLocked ? 'Cold only' : line.temperatureLabel}` : ''}
                                    </span>
                                    <strong>RM {formatCurrency(line.lineTotal)}</strong>
                                </div>
                            )) : <div className="addons-empty">No food or beverage added.</div>}
                        </div>
                        <div className="row total"><span>Total</span><strong>RM {formatCurrency(ticket.grandTotal)}</strong></div>
                    </div>
                    <div className={`qr-wrap ${isCancelled ? 'cancelled' : ''}`}>
                        {ticket.qrUrl ? <img className={isCancelled ? 'blurred-qr' : ''} src={ticket.qrUrl} alt="Ticket QR Code" /> : null}
                        {isCancelled ? <div className="cancel-overlay">CANCELLED</div> : null}
                    </div>
                </div>

                <div className="actions">
                    <a href={initialData.homeUrl} className="back-btn">Back to Home</a>
                </div>
            </div>
        </section>
    );
}