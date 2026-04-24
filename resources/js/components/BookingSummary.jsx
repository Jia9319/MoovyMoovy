import React from 'react';
import { BookingGuardFallback, useBookingAuthGuard } from './bookingAuth';
import { formatCurrency, formatLongDate } from './bookingUtils';

export default function BookingSummary({ initialData }) {
    const auth = useBookingAuthGuard(initialData);
    const summary = initialData.summary || {};

    if (auth.isLoading) {
        return <BookingGuardFallback message="Checking your session..." />;
    }

    if (!auth.isAuthorized) {
        return <BookingGuardFallback message="Please log in to view your booking summary." />;
    }

    return (
        <section className="booking-summary-page">
            <div className="booking-summary-card">
                <h1>Booking Confirmed</h1>
                <p className="sub">Your seats are reserved in this booking flow.</p>

                <div className="summary-grid">
                    <div>
                        <span>Movie</span>
                        <strong>{summary.movieTitle}</strong>
                    </div>
                    <div>
                        <span>Cinema</span>
                        <strong>{summary.cinema} • {summary.hall} • {summary.format}</strong>
                    </div>
                    <div>
                        <span>Date & Time</span>
                        <strong>{formatLongDate(summary.date)} {summary.time}</strong>
                    </div>
                    <div>
                        <span>Seats</span>
                        <strong>{(summary.seats || []).join(', ')}</strong>
                    </div>
                </div>

                <div className="total-row">
                    <span>Total Paid</span>
                    <strong>RM {formatCurrency(summary.total)}</strong>
                </div>

                <div style={{ display: 'flex', gap: '0.75rem', flexWrap: 'wrap', marginTop: '1rem' }}>
                    <a href={initialData.homeUrl} className="summary-btn">Back to Movies</a>
                    {initialData.ticketUrl ? <a href={initialData.ticketUrl} className="summary-btn">View Ticket</a> : null}
                </div>
            </div>
        </section>
    );
}