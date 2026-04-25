import { useEffect, useState } from 'react';

const STATUS_LOADING = 'loading';
const STATUS_AUTHORIZED = 'authorized';
const STATUS_UNAUTHORIZED = 'unauthorized';

const fallbackAuth = { isAuthenticated: false, userId: null };

const resolveInitialAuthenticated = (initialData) => {
    if (typeof initialData?.auth?.isAuthenticated === 'boolean') {
        return initialData.auth.isAuthenticated;
    }

    if (typeof initialData?.isAuthenticated === 'boolean') {
        return initialData.isAuthenticated;
    }

    return false;
};

export const useBookingAuthGuard = (initialData) => {
    const [status, setStatus] = useState(() => {
        const isAuthenticated = resolveInitialAuthenticated(initialData);
        return isAuthenticated ? STATUS_AUTHORIZED : STATUS_LOADING;
    });

    useEffect(() => {
        const authCheckUrl = initialData?.authCheckUrl;
        const initialAuthenticated = resolveInitialAuthenticated(initialData);

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

        const buildLoginUrl = () => {
            try {
                const loginUrl = new URL(initialData.loginUrl, window.location.origin);
                if (!loginUrl.searchParams.has('redirect')) {
                    const returnTo = `${window.location.pathname}${window.location.search}`;
                    loginUrl.searchParams.set('redirect', returnTo);
                }
                return loginUrl.toString();
            } catch (error) {
                return initialData.loginUrl;
            }
        };

        const timer = window.setTimeout(() => {
            window.location.assign(buildLoginUrl());
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
