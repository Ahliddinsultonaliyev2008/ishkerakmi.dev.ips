<script setup>
import { formatSalary, timeAgo } from '../format';
import LikeButton from './LikeButton.vue';
import CompanyLogo from './CompanyLogo.vue';

defineProps({ vacancy: { type: Object, required: true } });
</script>

<template>
    <article class="card group relative overflow-hidden p-5 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-900/5">
        <span class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-gradient-to-r from-brand-500 via-gold-400 to-brand-500 transition-transform duration-300 group-hover:scale-x-100"></span>
        <div class="flex items-start gap-4">
            <CompanyLogo :company="vacancy.company" />
            <div class="min-w-0 flex-1">
                <RouterLink :to="`/vacancies/${vacancy.id}`" class="after:absolute after:inset-0">
                    <h3 class="line-clamp-2 text-base leading-snug font-bold text-slate-900 group-hover:text-brand-700">{{ vacancy.title }}</h3>
                </RouterLink>
                <p class="mt-1 flex items-center gap-1 text-sm text-slate-500">
                    <span class="truncate">{{ vacancy.company?.name }}</span>
                    <svg v-if="vacancy.company?.is_verified" class="h-4 w-4 shrink-0 text-brand-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 111.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd" /></svg>
                </p>
            </div>
            <LikeButton type="vacancies" :item="vacancy" class="relative z-10" />
        </div>
        <div class="mt-4 inline-flex rounded-lg bg-gold-50 px-2.5 py-1 text-base font-extrabold text-gold-700">{{ formatSalary(vacancy.salary_from, vacancy.salary_to, vacancy.currency) }}</div>
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="chip bg-brand-50 text-brand-700">📍 {{ vacancy.city }}</span>
            <span class="chip">{{ vacancy.employment_type.label }}</span>
            <span class="chip">{{ vacancy.experience.label }}</span>
        </div>
        <div class="mt-4 flex items-center justify-between border-t border-dashed border-slate-200 pt-3 text-xs text-slate-400">
            <span>{{ timeAgo(vacancy.created_at) }}</span>
            <span>👁 {{ vacancy.views }} · 💬 {{ vacancy.comments_count ?? 0 }}</span>
        </div>
    </article>
</template>
