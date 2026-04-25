import React, { useMemo, useRef, useState } from 'react';
import { BookingGuardFallback, useBookingAuthGuard } from './bookingAuth';
import { useSessionStorageState } from './bookingSession';
import { formatCurrency } from './bookingUtils';

const buildInitialQuantityMap = (entries, initialValues) => {
    const map = {};

    entries.forEach(([key]) => {
        map[key] = Number(initialValues?.[key] ?? 0);
    });

    return map;
};

export default function BookingFood({ initialData }) {
    const auth = useBookingAuthGuard(initialData);
    const storageKey = initialData.storageKey || 'booking.food.default';
    const foodEntries = Object.entries(initialData.foodItems || {});
    const beverageEntries = Object.entries(initialData.beverageItems || {});
    const allEntries = [...foodEntries, ...beverageEntries];
    const formRef = useRef(null);

    const [persistedFoodState, setPersistedFoodState] = useSessionStorageState(storageKey, {
        quantities: buildInitialQuantityMap(allEntries, initialData.foodSelections?.quantities),
        temperatures: (() => {
            const map = {};
            const initialTemperatures = initialData.foodSelections?.temperatures || {};

            beverageEntries.forEach(([key, item]) => {
                const options = item.temperature_options || ['cold'];
                map[key] = options.length === 1
                    ? (options[0] || 'cold')
                    : (initialTemperatures[key] || '');
            });

            return map;
        })(),
    });

    const [quantities, setQuantities] = useState(() => persistedFoodState.quantities || buildInitialQuantityMap(allEntries, initialData.foodSelections?.quantities));
    const [temperatures, setTemperatures] = useState(() => {
        if (persistedFoodState.temperatures) {
            return persistedFoodState.temperatures;
        }

        const map = {};
        const initialTemperatures = initialData.foodSelections?.temperatures || {};

        beverageEntries.forEach(([key, item]) => {
            const options = item.temperature_options || ['cold'];
            map[key] = options.length === 1
                ? (options[0] || 'cold')
                : (initialTemperatures[key] || '');
        });

        return map;
    });

    const addonTotal = useMemo(() => allEntries.reduce((sum, [key, item]) => sum + (Number(quantities[key] || 0) * Number(item.price || 0)), 0), [allEntries, quantities]);
    const grandTotal = Number(initialData.seatTotal || 0) + addonTotal;
    const [temperatureErrors, setTemperatureErrors] = useState({});

    React.useEffect(() => {
        setPersistedFoodState({ quantities, temperatures });
    }, [quantities, temperatures, setPersistedFoodState]);

    React.useEffect(() => {
        setTemperatures((current) => {
            const next = { ...current };
            let changed = false;

            beverageEntries.forEach(([key, item]) => {
                const options = item.temperature_options || ['cold'];
                const qty = Number(quantities[key] || 0);

                if (options.length > 1 && qty === 0 && String(next[key] || '') !== '') {
                    next[key] = '';
                    changed = true;
                }
            });

            return changed ? next : current;
        });
    }, [beverageEntries, quantities]);

    const updateQuantity = (key, delta) => {
        const beverageItem = (initialData.beverageItems || {})[key];
        const temperatureOptions = beverageItem?.temperature_options || ['cold'];

        setQuantities((current) => {
            const nextQty = Math.max(0, Number(current[key] || 0) + delta);

            if (temperatureOptions.length > 1 && nextQty === 0) {
                setTemperatures((currentTemps) => ({
                    ...currentTemps,
                    [key]: '',
                }));
            }

            return {
                ...current,
                [key]: nextQty,
            };
        });

        setTemperatureErrors((current) => {
            if (!current[key]) {
                return current;
            }

            const next = { ...current };
            delete next[key];
            return next;
        });
    };

    const handleSubmit = (event) => {
        const nextErrors = {};

        beverageEntries.forEach(([key, item]) => {
            const qty = Number(quantities[key] || 0);
            const options = item.temperature_options || ['cold'];
            const selectedTemperature = String(temperatures[key] || '').trim();

            if (qty > 0 && options.length > 1 && selectedTemperature === '') {
                nextErrors[key] = 'Please choose Hot or Cold';
            }
        });

        setTemperatureErrors(nextErrors);

        if (Object.keys(nextErrors).length > 0) {
            event.preventDefault();
        }
    };

    const skipFood = () => {
        const resetQuantities = {};

        allEntries.forEach(([key]) => {
            resetQuantities[key] = 0;
        });

        setQuantities(resetQuantities);
        window.requestAnimationFrame(() => {
            if (formRef.current) {
                formRef.current.submit();
            }
        });
    };

    if (auth.isLoading) {
        return <BookingGuardFallback message="Checking your session..." />;
    }

    if (!auth.isAuthorized) {
        return <BookingGuardFallback message="Please log in to continue food selection." />;
    }

    return (
        <section className="food-page">
            <div className="food-shell">
                <div className="food-main">
                    <div className="food-head">
                        <a href={initialData.backUrl} className="seat-back" title="Back to seat selection">
                            <i className="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1>Add Food & Beverages</h1>
                            <p>Optional step. You can skip and continue to payment.</p>
                        </div>
                    </div>

                    <form method="GET" action={initialData.nextUrl} id="foodForm" ref={formRef} onSubmit={handleSubmit}>
                        {Object.entries(initialData.bookingState || {}).map(([key, value]) => (
                            <input key={key} type="hidden" name={key} value={Array.isArray(value) ? value.join(',') : (value ?? '')} />
                        ))}

                        <div className="menu-block">
                            <h2 className="menu-title">Food</h2>
                            <div className="food-grid">
                                {foodEntries.map(([key, item]) => (
                                    <div className="food-card beverage-card" key={key}>
                                        <h3>{item.name}</h3>
                                        <p>RM {formatCurrency(item.price)}</p>
                                        <div className="qty-row">
                                            <label htmlFor={`${key}_qty`}>Qty</label>
                                            <div className="qty-control">
                                                <button type="button" className="qty-btn" onClick={() => updateQuantity(key, -1)}>-</button>
                                                <input id={`${key}_qty`} name={`${key}_qty`} type="number" min="0" step="1" value={quantities[key] ?? 0} data-price={item.price} className="food-qty" readOnly />
                                                <button type="button" className="qty-btn" onClick={() => updateQuantity(key, 1)}>+</button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="menu-block">
                            <h2 className="menu-title">Beverages</h2>
                            <div className="food-grid">
                                {beverageEntries.map(([key, item]) => {
                                    const temperatureOptions = item.temperature_options || ['cold'];
                                    const singleTemperature = temperatureOptions.length === 1;
                                    const qty = Number(quantities[key] || 0);
                                    const selectedTemperature = singleTemperature
                                        ? String(temperatureOptions[0] || 'cold')
                                        : (qty > 0 ? String(temperatures[key] || '') : '');
                                    const hasError = Boolean(temperatureErrors[key]);

                                    return (
                                        <div className="food-card" key={key}>
                                            <h3>{item.name}</h3>
                                            <p>RM {formatCurrency(item.price)}</p>
                                            <div className="qty-row">
                                                <label htmlFor={`${key}_qty`}>Qty</label>
                                                <div className="qty-control">
                                                    <button type="button" className="qty-btn" onClick={() => updateQuantity(key, -1)}>-</button>
                                                    <input id={`${key}_qty`} name={`${key}_qty`} type="number" min="0" step="1" value={quantities[key] ?? 0} data-price={item.price} className="food-qty" readOnly />
                                                    <button type="button" className="qty-btn" onClick={() => updateQuantity(key, 1)}>+</button>
                                                </div>
                                            </div>
                                            <div className="temp-row" style={hasError ? { border: '1px solid #ef4444', borderRadius: '10px', padding: '0.45rem 0.55rem', background: 'rgba(239,68,68,0.08)' } : undefined}>
                                                <label htmlFor={`${key}_temp`}>Serve</label>
                                                {singleTemperature ? (
                                                    <>
                                                        <span className="temp-badge">{String(temperatureOptions[0]).charAt(0).toUpperCase() + String(temperatureOptions[0]).slice(1)} only</span>
                                                    </>
                                                ) : (
                                                    <div className="temp-toggle" role="group" aria-label={`Serve temperature for ${item.name}`}>
                                                        {temperatureOptions.map((tempOption) => (
                                                            <React.Fragment key={tempOption}>
                                                                <input
                                                                    type="radio"
                                                                    id={`${key}_temp_${tempOption}`}
                                                                    name={`${key}_temp`}
                                                                    value={tempOption}
                                                                    className="temp-radio"
                                                                    checked={selectedTemperature === tempOption}
                                                                    onChange={() => {
                                                                        setTemperatures((current) => ({ ...current, [key]: tempOption }));
                                                                        setTemperatureErrors((current) => {
                                                                            if (!current[key]) {
                                                                                return current;
                                                                            }

                                                                            const next = { ...current };
                                                                            delete next[key];
                                                                            return next;
                                                                        });
                                                                    }}
                                                                />
                                                                <label htmlFor={`${key}_temp_${tempOption}`} className="temp-pill">{String(tempOption).charAt(0).toUpperCase() + String(tempOption).slice(1)}</label>
                                                            </React.Fragment>
                                                        ))}
                                                    </div>
                                                )}
                                            </div>
                                            {hasError ? <small style={{ color: '#ef4444', marginTop: '0.35rem', display: 'block', fontSize: '0.78rem' }}>{temperatureErrors[key]}</small> : null}
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        <div className="food-footer">
                            <div>
                                <small>Seats: {(initialData.seats || []).join(', ')}</small>
                                <div className="totals">
                                    <span>Seat Total: RM {formatCurrency(initialData.seatTotal)}</span>
                                    <span id="foodTotalText">Food Total: RM {formatCurrency(addonTotal)}</span>
                                    <strong id="grandTotalText">Grand Total: RM {formatCurrency(grandTotal)}</strong>
                                </div>
                            </div>
                            <div className="actions">
                                <button type="submit" className="next-btn">Proceed to Payment <i className="fas fa-arrow-right"></i></button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </section>
    );
}