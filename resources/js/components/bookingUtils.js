export const formatCurrency = (value) => Number(value || 0).toFixed(2);

export const formatDateLabel = (value) => {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString('en-GB', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
    });
};

export const formatLongDate = (value) => {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString('en-GB', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

export const formatDurationText = (value) => {
    const raw = String(value || '').trim();

    if (raw === '') {
        return '0h 0m';
    }

    const hourMatch = raw.match(/^(\d+)\s*h(?:\s*(\d+)\s*m)?$/i);
    if (hourMatch) {
        const hours = Number(hourMatch[1] || 0);
        const minutes = Number(hourMatch[2] || 0);
        return `${hours}h ${minutes}m`;
    }

    const minuteMatch = raw.match(/^(\d+)\s*m(?:in(?:ute)?s?)?$/i);
    if (minuteMatch) {
        return `0h ${Number(minuteMatch[1] || 0)}m`;
    }

    const totalMinutes = Number(raw || 0);

    if (!Number.isFinite(totalMinutes) || totalMinutes <= 0) {
        return '0h 0m';
    }

    return `${Math.floor(totalMinutes / 60)}h ${totalMinutes % 60}m`;
};
