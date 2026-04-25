import React, { useState } from 'react';
import { BookingGuardFallback, useBookingAuthGuard } from './bookingAuth';
import { useSessionStorageState } from './bookingSession';
import { formatCurrency, formatLongDate } from './bookingUtils';

export default function BookingPayment({ initialData }) {
    const auth = useBookingAuthGuard(initialData);
    const storageKey = initialData.storageKey || 'booking.payment.default';
    const bookingState = initialData.bookingState || {};
    const seats = initialData.seats || [];
    const foodLines = initialData.foodLines || [];
    const [paymentDraft, setPaymentDraft] = useSessionStorageState(storageKey, {
        paymentMethod: initialData.paymentMethod || 'tng',
    });
    const [paymentMethod, setPaymentMethod] = useState(paymentDraft.paymentMethod || initialData.paymentMethod || 'tng');

    React.useEffect(() => {
        setPaymentDraft({ paymentMethod });
    }, [paymentMethod, setPaymentDraft]);

    if (auth.isLoading) {
        return <BookingGuardFallback message="Checking your session..." />;
    }

    if (!auth.isAuthorized) {
        return <BookingGuardFallback message="Please log in to continue payment." />;
    }

    return (
        <section className="payment-page">
            <div className="payment-shell">
                <div className="payment-main">
                    <div className="payment-head">
                        <a href={initialData.backUrl} className="seat-back" title="Back to food and beverage">
                            <i className="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1>Payment</h1>
                            <p>Review details and choose payment method.</p>
                        </div>
                    </div>

                    <div className="payment-grid">
                        <div className="card-block">
                            <h3>Booking Details</h3>
                            <div className="line"><span>Movie</span><strong>{bookingState.title}</strong></div>
                            <div className="line"><span>Cinema</span><strong>{bookingState.cinema} • {bookingState.hall} • {bookingState.format}</strong></div>
                            <div className="line"><span>Date & Time</span><strong>{formatLongDate(bookingState.date)} {bookingState.time}</strong></div>
                            <div className="line"><span>Seats</span><strong>{seats.join(', ')}</strong></div>
                            <div className="edit-actions">
                                <a href={initialData.editSeatUrl || initialData.backUrl} className="edit-link">Edit Seats</a>
                                <a href={initialData.editFoodUrl || initialData.backUrl} className="edit-link">Edit Food & Beverage</a>
                            </div>
                        </div>

                        <div className="card-block">
                            <h3>Price Breakdown</h3>
                            <div className="line"><span>Seat Total</span><strong>RM {formatCurrency(initialData.seatTotal)}</strong></div>
                            <div className="line"><span>Food Total</span><strong>RM {formatCurrency(initialData.foodTotal)}</strong></div>
                            {foodLines.map((line) => (
                                <div className="line small" key={`${line.key || line.name}-${line.qty}`}>
                                    <span>
                                        {line.name} x{line.qty}
                                        {line.category === 'beverage' && line.temperatureLabel ? ` • ${line.temperatureLocked ? 'Cold only' : line.temperatureLabel}` : ''}
                                    </span>
                                    <strong>RM {formatCurrency(line.lineTotal)}</strong>
                                </div>
                            ))}
                            <div className="line total"><span>Grand Total</span><strong>RM {formatCurrency(initialData.grandTotal)}</strong></div>
                        </div>
                    </div>

                    <form method="POST" action={initialData.nextUrl} className="pay-form">
                        <input type="hidden" name="_token" value={initialData.csrfToken} />
                        {Object.entries(bookingState).map(([key, value]) => (
                            <input key={key} type="hidden" name={key} value={Array.isArray(value) ? value.join(',') : (value ?? '')} />
                        ))}
                        <input type="hidden" name="seat_total" value={initialData.seatTotal} />
                        <input type="hidden" name="food_total" value={initialData.foodTotal} />
                        <input type="hidden" name="payment_method" value={paymentMethod} />

                        {(initialData.foodSelections?.allEntries || []).map(([key, item]) => (
                            <React.Fragment key={key}>
                                <input type="hidden" name={`${key}_qty`} value={initialData.foodSelections.quantities?.[key] ?? 0} />
                                {item.temperature_options && item.temperature_options.length > 0 ? (
                                    <input type="hidden" name={`${key}_temp`} value={initialData.foodSelections.temperatures?.[key] || item.default_temperature || item.temperature_options[0]} />
                                ) : null}
                            </React.Fragment>
                        ))}

                        <h3>Payment Method</h3>
                        <label className="method"><input type="radio" name="payment_method_option" checked={paymentMethod === 'tng'} onChange={() => setPaymentMethod('tng')} /><span>Touch 'n Go eWallet</span></label>
                        <label className="method"><input type="radio" name="payment_method_option" checked={paymentMethod === 'debit'} onChange={() => setPaymentMethod('debit')} /><span>Debit Card</span></label>
                        <label className="method"><input type="radio" name="payment_method_option" checked={paymentMethod === 'credit'} onChange={() => setPaymentMethod('credit')} /><span>Credit Card</span></label>

                        <button type="submit" className="pay-btn">Pay Now</button>
                    </form>
                </div>
            </div>
        </section>
    );
}