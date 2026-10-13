<script setup>
defineProps({
    size: { type: String, default: 'h-10 w-10' },
    light: { type: Boolean, default: false },
    tagline: { type: Boolean, default: true },
});
</script>

<template>
    <span class="flex items-center gap-2.5">
        <svg :class="size" class="shrink-0 drop-shadow-sm" viewBox="0 0 64 64" aria-hidden="true">
            <defs>
                <linearGradient id="ik-logo-g" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#14a3c0" />
                    <stop offset="1" stop-color="#0b3a6b" />
                </linearGradient>
            </defs>
            <path d="M32 2l8.8 8.8H53.2v12.4L62 32l-8.8 8.8v12.4H40.8L32 62l-8.8-8.8H10.8V40.8L2 32l8.8-8.8V10.8h12.4z" fill="url(#ik-logo-g)" />
            <path d="M32 9l6 6h11v11l6 6-6 6v11H38l-6 6-6-6H15V38l-6-6 6-6V15h11z" fill="none" stroke="#f2c14e" stroke-width="1.6" />
            <rect x="19" y="25" width="26" height="18" rx="3" fill="#fff" />
            <path d="M27 25v-3a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3" fill="none" stroke="#fff" stroke-width="2.6" />
            <path d="M19 32h26" stroke="#0b5a85" stroke-width="2" />
            <rect x="29.5" y="30" width="5" height="4" rx="1" fill="#e0a526" />
        </svg>
        <span class="flex flex-col leading-none">
            <span class="font-display text-[1.15rem] font-bold tracking-tight" :class="light ? 'text-white' : 'text-lapis-900'">
                Ish<span :class="light ? 'text-gold-300' : 'text-brand-600'">Kerakmi</span>
            </span>
            <span v-if="tagline" class="mt-1 text-[0.65rem] font-semibold tracking-[0.18em] uppercase" :class="light ? 'text-brand-100' : 'text-gold-600'">
                ish va xodim maskani
            </span>
        </span>
    </span>
</template>
