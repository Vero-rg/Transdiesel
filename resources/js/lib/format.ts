export function formatCurrency(
    value: number | string | null | undefined,
): string {
    const amount = typeof value === 'string' ? Number(value) : (value ?? 0);
    const safe = Number.isFinite(amount) ? amount : 0;

    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(safe);
}

export function formatDate(value: string): string {
    return new Intl.DateTimeFormat('es-MX', { dateStyle: 'long' }).format(
        new Date(value),
    );
}

export function formatRating(value: number | null | undefined): string {
    return (value ?? 0).toFixed(1);
}
