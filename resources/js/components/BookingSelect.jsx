import React from 'react';
import { BookingGuardFallback, useBookingAuthGuard } from './bookingAuth';
import { useSessionStorageState } from './bookingSession';
import { formatCurrency, formatDateLabel } from './bookingUtils';

export default function BookingSelect({ initialData }) {
    const movie = initialData.movie;
    const cinemas = initialData.cinemas || [];
    const types = initialData.types || [];
    const dates = initialData.dates || [];
    const times = initialData.times || [];
    const selection = initialData.selection || {};
    const auth = useBookingAuthGuard(initialData);
    const storageKey = initialData.storageKey || 'booking.select.default';

    const initialCinema = selection.cinema
        ? cinemas.find((cinema) => cinema.name === selection.cinema) || { name: selection.cinema, hall: selection.hall || '' }
        : { name: '', hall: '' };
    const initialType = selection.format
        ? types.find((type) => type.label === selection.format) || { label: selection.format, price: selection.price || 0 }
        : { label: '', price: '' };
    const initialDate = selection.date || '';
    const initialTime = selection.time || '';

    const [draft, setDraft] = useSessionStorageState(storageKey, {
        selectedCinema: initialCinema.name,
        selectedHall: initialCinema.hall,
        selectedType: initialType.label,
        selectedPrice: initialType.price,
        selectedDate: initialDate,
        selectedTime: initialTime,
    });

    const selectedCinema = draft.selectedCinema || initialCinema.name;
    const selectedHall = draft.selectedHall || initialCinema.hall;
    const selectedType = draft.selectedType || initialType.label;
    const selectedPrice = draft.selectedPrice || initialType.price;
    const selectedDate = draft.selectedDate || initialDate;
    const selectedTime = draft.selectedTime || initialTime;
    const canProceed = Boolean(selectedCinema && selectedType && selectedDate && selectedTime);

    if (auth.isLoading) {
        return <BookingGuardFallback message="Checking your session..." />;
    }

    if (!auth.isAuthorized) {
        return <BookingGuardFallback message="Please log in to start your booking." />;
    }

    return (
        <section className="booking-select-page">
            <div className="booking-select-card">
                <div className="booking-select-head">
                    <a href={initialData.homeUrl} className="seat-back" title="Back to home">
                        <i className="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1>{movie.title}</h1>
                        <p>{movie.genre} • {movie.durationText}</p>
                    </div>
                </div>

                <form method="GET" action={initialData.seatUrl} className="booking-form" id="bookingForm">
                    <input type="hidden" name="movie_id" value={movie.id} />
                    <input type="hidden" name="title" value={movie.title} />
                    <input type="hidden" name="genre" value={movie.genre} />
                    <input type="hidden" name="duration" value={movie.duration} />
                    <input type="hidden" name="poster" value={movie.poster || ''} />

                    <input type="hidden" name="hall" id="hallInput" value={selectedHall} />
                    <input type="hidden" name="format" id="formatInput" value={selectedType} />
                    <input type="hidden" name="price" id="priceInput" value={selectedPrice} />

                    <div className="booking-grid">
                        <div>
                            <label>Cinema</label>
                            <div className="options" id="cinemaOptions">
                                {cinemas.map((cinema) => (
                                    <button
                                        key={`${cinema.name}-${cinema.hall}`}
                                        type="button"
                                        className={`option-btn cinema-option ${selectedCinema === cinema.name ? 'active' : ''}`}
                                        data-cinema={cinema.name}
                                        data-hall={cinema.hall}
                                        onClick={() => {
                                            setDraft((current) => ({
                                                ...current,
                                                selectedCinema: cinema.name,
                                                selectedHall: cinema.hall,
                                            }));
                                        }}
                                    >
                                        <strong>{cinema.name}</strong>
                                        <span>{cinema.hall}</span>
                                    </button>
                                ))}
                            </div>
                            <input type="hidden" name="cinema" id="cinemaInput" value={selectedCinema} />
                        </div>

                        <div>
                            <label>Experience</label>
                            <div className="chips" id="typeOptions">
                                {types.map((type) => (
                                    <button
                                        key={type.label}
                                        type="button"
                                        className={`chip-btn type-option ${selectedType === type.label ? 'active' : ''}`}
                                        data-format={type.label}
                                        data-price={type.price}
                                        onClick={() => {
                                            setDraft((current) => ({
                                                ...current,
                                                selectedType: type.label,
                                                selectedPrice: type.price,
                                            }));
                                        }}
                                    >
                                        {type.label} • RM {formatCurrency(type.price)}
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div>
                            <label>Select Date</label>
                            <div className="chips">
                                {dates.map((date) => (
                                    <label
                                        className="chip"
                                        key={date}
                                        onClick={() => setDraft((current) => ({ ...current, selectedDate: date }))}
                                    >
                                        <input
                                            type="radio"
                                            name="date"
                                            value={date}
                                            checked={selectedDate === date}
                                            onChange={() => setDraft((current) => ({ ...current, selectedDate: date }))}
                                        />
                                        <span
                                            className={selectedDate === date ? 'active' : ''}
                                            style={selectedDate === date ? { borderColor: 'var(--c1)', color: '#ffffff', background: 'rgba(209, 106, 255, 0.2)', fontWeight: 600 } : undefined}
                                        >
                                            {formatDateLabel(date)}
                                        </span>
                                    </label>
                                ))}
                            </div>
                        </div>

                        <div>
                            <label>Available Showtimes</label>
                            <div className="chips">
                                {times.map((time) => (
                                    <label
                                        className="chip"
                                        key={time}
                                        onClick={() => setDraft((current) => ({ ...current, selectedTime: time }))}
                                    >
                                        <input
                                            type="radio"
                                            name="time"
                                            value={time}
                                            checked={selectedTime === time}
                                            onChange={() => setDraft((current) => ({ ...current, selectedTime: time }))}
                                        />
                                        <span
                                            className={selectedTime === time ? 'active' : ''}
                                            style={selectedTime === time ? { borderColor: 'var(--c1)', color: '#ffffff', background: 'rgba(209, 106, 255, 0.2)', fontWeight: 600 } : undefined}
                                        >
                                            {time}
                                        </span>
                                    </label>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="booking-footer">
                        <div>
                            <small>Selection Summary</small>
                            <p id="selectedCinemaText">Cinema: {selectedCinema || 'Not selected'}{selectedHall ? ` • ${selectedHall}` : ''}</p>
                            <p id="selectedTypeText" style={{ color: 'var(--muted)', marginTop: '0.15rem' }}>
                                Type: {selectedType || 'Not selected'}{selectedType ? ` • RM ${formatCurrency(selectedPrice)}` : ''}
                            </p>
                        </div>
                        <button type="submit" className="next-btn" disabled={!canProceed}>
                            Pick Your Seats
                            <i className="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    );
}