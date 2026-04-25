import { useEffect, useMemo, useState } from 'react';

const isBrowser = typeof window !== 'undefined';

const readStorageValue = (key, fallback) => {
    if (!isBrowser || !key) {
        return fallback;
    }

    try {
        const raw = window.sessionStorage.getItem(key);

        if (!raw) {
            return fallback;
        }

        return JSON.parse(raw);
    } catch (error) {
        return fallback;
    }
};

export const useSessionStorageState = (key, initialValue) => {
    const resolvedInitialValue = useMemo(() => {
        const fallback = typeof initialValue === 'function' ? initialValue() : initialValue;
        return readStorageValue(key, fallback);
    }, [key, initialValue]);

    const [value, setValue] = useState(resolvedInitialValue);

    useEffect(() => {
        if (!isBrowser || !key) {
            return;
        }

        try {
            window.sessionStorage.setItem(key, JSON.stringify(value));
        } catch (error) {
            // Ignore storage quota / disabled storage errors.
        }
    }, [key, value]);

    return [value, setValue];
};

export const clearSessionStorageKey = (key) => {
    if (!isBrowser || !key) {
        return;
    }

    try {
        window.sessionStorage.removeItem(key);
    } catch (error) {
        // Ignore storage access errors.
    }
};
