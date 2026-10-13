const number = new Intl.NumberFormat('uz-UZ');

export function formatSalary(from, to, currency) {
    const unit = currency?.value === 'USD' ? '$' : "so'm";
    const fmt = (v) => number.format(v).replace(/,/g, ' ');
    if (from && to) return `${fmt(from)} – ${fmt(to)} ${unit}`;
    if (from) return `${fmt(from)} ${unit} dan`;
    if (to) return `${fmt(to)} ${unit} gacha`;
    return 'Kelishiladi';
}

export function timeAgo(date) {
    const seconds = Math.floor((Date.now() - new Date(date).getTime()) / 1000);
    const units = [
        [31536000, 'yil'],
        [2592000, 'oy'],
        [86400, 'kun'],
        [3600, 'soat'],
        [60, 'daqiqa'],
    ];
    for (const [size, label] of units) {
        const value = Math.floor(seconds / size);
        if (value >= 1) return `${value} ${label} oldin`;
    }
    return 'hozirgina';
}

export function initials(name = '') {
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join('');
}

export const statusColors = {
    pending: 'bg-amber-50 text-amber-700 ring-amber-200',
    interview: 'bg-sky-50 text-sky-700 ring-sky-200',
    accepted: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    rejected: 'bg-rose-50 text-rose-700 ring-rose-200',
    active: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    closed: 'bg-slate-100 text-slate-600 ring-slate-200',
};
