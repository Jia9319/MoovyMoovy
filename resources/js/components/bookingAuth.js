import { useEffect, useState } from 'react';

const STATUS_LOADING = 'loading';
const STATUS_AUTHORIZED = 'authorized';
const STATUS_UNAUTHORIZED = 'unauthorized';

const fallbackAuth = { isAuthenticated: false, userId: null };

export const useBookingAuthGuard = (initialData) => {
    const [status, setStatus] = useState(() => {
        const isAuthenticated = Boolean(initialData?.auth?.isAuthenticated);
        return isAuthenticated ? STATUS_AUTHORIZED : STATUS_LOADING;
    });

    useEffect(() => {
        const authCheckUrl = initialData?.authCheckUrl;
        const initialAuthenticated = Boolean(initialData?.auth?.isAuthenticated);

        if (!authCheckUrl) {
            setStatus(initialAuthenticated ? STATUS_AUTHORIZED : STATUS_UNAUTHORIZED);
            return;
        }

        let cancelled = false;

        const validateSession = async () => {
            try {
                const response = await fetch(authCheckUrl, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (cancelled) {
                    return;
                }

                if (!response.ok) {
                    setStatus(STATUS_UNAUTHORIZED);
                    return;
                }

                const payload = await response.json();
                const isAuthenticated = Boolean(payload?.authenticated);
                setStatus(isAuthenticated ? STATUS_AUTHORIZED : STATUS_UNAUTHORIZED);
            } catch (error) {
                if (!cancelled) {
                    setStatus(initialAuthenticated ? STATUS_AUTHORIZED : STATUS_UNAUTHORIZED);
                }
            }
        };

        validateSession();

        return () => {
            cancelled = true;
        };
    }, [initialData]);

    useEffect(() => {
        if (status !== STATUS_UNAUTHORIZED || !initialData?.loginUrl) {
            return;
        }

        const timer = window.setTimeout(() => {
            window.location.assign(initialData.loginUrl);
        }, 600);

        return () => {
            window.clearTimeout(timer);
        };
    }, [status, initialData]);

    return {
        status,
        isAuthorized: status === STATUS_AUTHORIZED,
        isLoading: status === STATUS_LOADING,
        auth: initialData?.auth || fallbackAuth,
    };
};

export const BookingGuardFallback = ({ message }) => (
    <section className="booking-select-page">
        <div className="booking-select-card" style={{ textAlign: 'center' }}>
            <h1>Session Required</h1>
            <p style={{ color: 'var(--muted)' }}>{message || 'Please log in to view your bookings.'}</p>
        </div>
    </section>
);
