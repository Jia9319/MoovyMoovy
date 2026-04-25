import React, { useEffect, useMemo, useRef, useState } from 'react';
import { BookingGuardFallback, useBookingAuthGuard } from './bookingAuth';
import { useSessionStorageState } from './bookingSession';
import { formatCurrency, formatDurationText, formatLongDate } from './bookingUtils';

const createSeatLookup = (rows, seatsPerRow, coupleRows, couplePairStarts, bookedSeats) => {
    const bookedSet = new Set(bookedSeats || []);
    const coupleRowSet = new Set(coupleRows || []);
    const map = new Map();

    (rows || []).forEach((row) => {
        const rowCouplePairStarts = coupleRowSet.has(row) ? (couplePairStarts || []).map(Number) : [];

        for (let number = 1; number <= Number(seatsPerRow || 0); number += 1) {
            const seatId = `${row}${number}`;
            const pairStart = rowCouplePairStarts.includes(number);
            const pairFollower = rowCouplePairStarts.includes(number - 1);

            if (pairFollower) {
                continue;
            }

            if (pairStart) {
                const secondNumber = Math.min(number + 1, Number(seatsPerRow || 0));
                const seats = secondNumber === number ? [seatId] : [seatId, `${row}${secondNumber}`];
                map.set(seatId, {
                    key: seats.join(','),
                    label: seats.join('-'),
                    seats,
                    isCouple: true,
                    booked: seats.some((s) => bookedSet.has(s)),
                });
                continue;
            }

            map.set(seatId, {
                key: seatId,
                label: seatId,
                seats: [seatId],
                isCouple: false,
                booked: bookedSet.has(seatId),
            });
        }
    });

    return map;
};

const toSeatSet = (value) => new Set(String(value || '').split(',').map((s) => s.trim()).filter(Boolean));

const formatMoney = (value) => `RM ${formatCurrency(value)}`;

export default function BookingSeat({ initialData }) {
    const auth = useBookingAuthGuard(initialData);
    const storageKey = initialData.storageKey || 'booking.seat.default';
    const rows = initialData.rows || [];
    const seatsPerRow = Number(initialData.seatsPerRow || 0);
    const aisleAfterColumns = (initialData.aisleAfterColumns || []).map(Number);
    const showtime = initialData.showtime || {};
    const movie = showtime.movie || {};
    const seatLookup = useMemo(() => createSeatLookup(rows, seatsPerRow, initialData.coupleRows || [], initialData.couplePairStarts || [], initialData.bookedSeats || []), [rows, seatsPerRow, initialData.coupleRows, initialData.couplePairStarts, initialData.bookedSeats]);

    const [persistedSeatIds, setPersistedSeatIds] = useSessionStorageState(storageKey, String(initialData.selectedSeats || '').split(',').map((s) => s.trim()).filter(Boolean));

    const [selectedSeatSet, setSelectedSeatSet] = useState(() => {
        const initial = (persistedSeatIds || []).length ? new Set(persistedSeatIds) : toSeatSet(initialData.selectedSeats);
        return initial;
    });

    const seatGridRef = useRef(null);
    const screenWrapRef = useRef(null);

    useEffect(() => {
        setPersistedSeatIds(Array.from(selectedSeatSet));
    }, [selectedSeatSet, setPersistedSeatIds]);

    useEffect(() => {
        const syncScreenWidth = () => {
            if (!seatGridRef.current || !screenWrapRef.current) {
                return;
            }

            const seatRows = Array.from(seatGridRef.current.querySelectorAll('.seat-row'));
            if (!seatRows.length) {
                return;
            }

            const widths = seatRows.map((rowEl) => {
                const seats = Array.from(rowEl.querySelectorAll('.seat'));
                if (!seats.length) {
                    return 0;
                }
                const firstRect = seats[0].getBoundingClientRect();
                const lastRect = seats[seats.length - 1].getBoundingClientRect();
                return Math.ceil(lastRect.right - firstRect.left);
            }).filter((value) => value > 0);

            if (!widths.length) {
                return;
            }

            const targetWidth = Math.max(Math.ceil(Math.max(...widths) * 1.2), 220);
            screenWrapRef.current.style.setProperty('--screen-width', `${targetWidth}px`);
        };

        const observer = typeof ResizeObserver !== 'undefined' && seatGridRef.current ? new ResizeObserver(syncScreenWidth) : null;
        window.addEventListener('resize', syncScreenWidth);
        if (observer) {
            observer.observe(seatGridRef.current);
        }
        syncScreenWidth();

        return () => {
            window.removeEventListener('resize', syncScreenWidth);
            if (observer) {
                observer.disconnect();
            }
        };
    }, []);

    if (auth.isLoading) {
        return <BookingGuardFallback message="Checking your session..." />;
    }

    if (!auth.isAuthorized) {
        return <BookingGuardFallback message="Please log in to continue seat selection." />;
    }

    const selectedGroups = Array.from(seatLookup.values()).filter((group) => group.seats.every((seat) => selectedSeatSet.has(seat)));
    const selectedSeatIds = selectedGroups.flatMap((group) => group.seats).sort((a, b) => a.localeCompare(b));
    const totalPrice = selectedGroups.reduce((sum, group) => {
        const unitPrice = Number(initialData.basePrice || 0) + (group.isCouple ? Number(initialData.coupleExtra || 0) : 0);
        return sum + (unitPrice * group.seats.length);
    }, 0);

    const toggleSeatGroup = (group) => {
        if (!group || group.booked) {
            return;
        }

        setSelectedSeatSet((current) => {
            const next = new Set(current);
            const isSelected = group.seats.every((seat) => next.has(seat));

            if (isSelected) {
                group.seats.forEach((seat) => next.delete(seat));
            } else {
                group.seats.forEach((seat) => next.add(seat));
            }

            return next;
        });
    };

    return (
        <section className="seat-page">
            <div className="seat-shell">
                <div className="seat-main">
                    <div className="seat-head">
                        <a href={initialData.backUrl} className="seat-back" title="Back to choose cinema and time">
                            <i className="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 className="seat-title">{movie.title}</h1>
                            <p className="seat-sub">
                                {showtime.cinema}{showtime.hall ? `, ${showtime.hall}` : ''}{showtime.format ? `, ${showtime.format}` : ''} • {formatLongDate(showtime.date)} {showtime.time}
                            </p>
                        </div>
                    </div>

                    <div className="screen-wrap" ref={screenWrapRef}>
                        <div className="screen-glow"></div>
                        <div className="screen-bar"></div>
                        <span>SCREEN</span>
                    </div>

                    <div className="seat-map-wrap">
                        <div className="seat-grid" id="seatGrid" ref={seatGridRef}>
                            {rows.map((row) => (
                                <div className="seat-row" key={row}>
                                    <span className="row-label">{row}</span>
                                    {Array.from({ length: seatsPerRow }).map((_, seatIndex) => {
                                        const number = seatIndex + 1;
                                        const seatId = `${row}${number}`;
                                        const showAisle = aisleAfterColumns.includes(number - 1);
                                        const group = seatLookup.get(seatId);
                                        const isSelected = group ? group.seats.every((seat) => selectedSeatSet.has(seat)) : false;

                                        return (
                                            <React.Fragment key={`${row}-${number}`}>
                                                {showAisle ? <span className="aisle" aria-hidden="true"></span> : null}
                                                {group ? (
                                                    <button
                                                        type="button"
                                                        className={`seat ${group.isCouple ? 'couple couple-pair' : ''} ${group.booked ? 'booked' : ''} ${isSelected ? 'selected' : ''}`}
                                                        disabled={group.booked}
                                                        onClick={() => toggleSeatGroup(group)}
                                                        title={`Seat ${group.label}`}
                                                    >
                                                        <i className="fas fa-couch"></i>
                                                    </button>
                                                ) : null}
                                            </React.Fragment>
                                        );
                                    })}
                                    <span className="row-label">{row}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="seat-legend">
                        <span><i className="seat-dot available"></i> Available</span>
                        <span><i className="seat-dot selected"></i> Selected</span>
                        {initialData.hasCoupleSeats ? (
                            <span><i className="seat-dot couple"></i> {showtime.format === 'BEANIE' ? 'Beanie Sofa (2 seats)' : `Couple (2 seats, +RM ${formatCurrency(initialData.coupleExtra || 0)} / seat)`}</span>
                        ) : null}
                        <span><i className="seat-dot booked"></i> Booked</span>
                    </div>
                </div>

                <aside className="booking-side">
                    <h2>Booking Summary</h2>

                    <div className="movie-mini">
                        {movie.poster ? <img src={movie.poster} alt={`${movie.title} poster`} /> : <div className="movie-mini-placeholder">{String(movie.title || '').slice(0, 2).toUpperCase()}</div>}
                        <div>
                            <small>NOW SHOWING</small>
                            <strong>{movie.title}</strong>
                            <p>{movie.genre} • {formatDurationText(movie.durationText)}</p>
                        </div>
                    </div>

                    <div className="meta-line"><span>Cinema</span><strong>{showtime.cinema}</strong></div>
                    <div className="meta-line"><span>Date & Time</span><strong>{formatLongDate(showtime.date)} {showtime.time}</strong></div>
                    <div className="meta-line"><span>Base Price</span><strong>{formatMoney(showtime.price)}</strong></div>
                    {initialData.hasCoupleSeats && showtime.format !== 'BEANIE' ? <div className="meta-line"><span>Couple Seat Add-on</span><strong>+{formatMoney(initialData.coupleExtra || 0)} / seat</strong></div> : null}

                    <div className="selected-block">
                        <span>Selected Seats</span>
                        <div id="selectedSeats" className="selected-list">
                            {selectedSeatIds.length ? selectedGroups.map((group) => (
                                <span key={group.key} className={`seat-chip ${group.isCouple ? 'couple' : ''}`}>
                                    {group.label}{group.isCouple ? (showtime.format === 'BEANIE' ? ' Beanie' : ' Couple') : ''}
                                </span>
                            )) : 'None'}
                        </div>
                    </div>

                    <div className="total-wrap"><span>Total Price</span><strong id="totalPrice">{formatMoney(totalPrice)}</strong></div>

                    <form method="GET" action={initialData.nextUrl}>
                        <input type="hidden" name="movie_id" value={initialData.bookingState.movie_id || ''} />
                        <input type="hidden" name="showtime_id" value={initialData.bookingState.showtime_id || ''} />
                        <input type="hidden" name="title" value={initialData.bookingState.title || movie.title || ''} />
                        <input type="hidden" name="genre" value={initialData.bookingState.genre || movie.genre || ''} />
                        <input type="hidden" name="duration" value={initialData.bookingState.duration || movie.durationText || ''} />
                        <input type="hidden" name="poster" value={initialData.bookingState.poster || movie.poster || ''} />
                        <input type="hidden" name="cinema" value={showtime.cinema || ''} />
                        <input type="hidden" name="hall" value={showtime.hall || ''} />
                        <input type="hidden" name="format" value={showtime.format || ''} />
                        <input type="hidden" name="price" value={showtime.price || 0} />
                        <input type="hidden" name="date" value={showtime.date || ''} />
                        <input type="hidden" name="time" value={showtime.time || ''} />
                        {initialData.returnTo ? <input type="hidden" name="return_to" value={initialData.returnTo} /> : null}
                        {Object.entries(initialData.addonSelections || {}).map(([key, selection]) => (
                            <React.Fragment key={key}>
                                <input type="hidden" name={`${key}_qty`} value={selection?.qty ?? 0} />
                                {Object.prototype.hasOwnProperty.call(selection || {}, 'temp') ? (
                                    <input type="hidden" name={`${key}_temp`} value={selection?.temp || ''} />
                                ) : null}
                            </React.Fragment>
                        ))}
                        <input type="hidden" name="seats" value={selectedSeatIds.join(',')} />
                        <button type="submit" className="proceed-btn" disabled={selectedSeatIds.length === 0}>
                            {initialData.nextLabel || 'Continue to Food & Beverage'}
                            <i className="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </aside>
            </div>
        </section>
    );
}
