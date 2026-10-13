<script setup>
import { formatSalary, timeAgo } from '../format';
import LikeButton from './LikeButton.vue';
import CompanyLogo from './CompanyLogo.vue';

defineProps({ vacancy: { type: Object, required: true } });
</script>

<template>
    <article class="card group relative p-5 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md">
        <div class="flex items-start gap-4">
            <CompanyLogo :company="vacancy.company" />
            <div class="min-w-0 flex-1">
                <RouterLink :to="`/vacancies/${vacancy.id}`" class="after:absolute after:inset-0">
                    <h3 class="truncate text-base font-semibold text-slate-900 group-hover:text-brand-600">{{ vacancy.title }}</h3>
                </RouterLink>
                <p class="mt-0.5 flex items-center gap-1 text-sm text-slate-500">
                    {{ vacancy.company?.name }}
                    <svg v-if="vacancy.company?.is_verified" class="h-4 w-4 text-brand-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 111.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd" /></svg>
                </p>
            </div>
            <LikeButton type="vacancies" :item="vacancy" class="relative z-10" />
        </div>
        <div class="mt-4 text-lg font-bold text-slate-900">{{ formatSalary(vacancy.salary_from, vacancy.salary_to, vacancy.currency) }}</div>
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="chip">📍 {{ vacancy.city }}</span>
            <span class="chip">{{ vacancy.employment_type.label }}</span>
            <span class="chip">{{ vacancy.experience.label }}</span>
        </div>
        <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
            <span>{{ timeAgo(vacancy.created_at) }}</span>
            <span>👁 {{ vacancy.views }} · 💬 {{ vacancy.comments_count ?? 0 }}</span>
        </div>
    </article>
</template>
