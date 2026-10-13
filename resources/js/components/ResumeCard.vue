<script setup>
import { formatSalary, initials, timeAgo } from '../format';

defineProps({ resume: { type: Object, required: true } });
</script>

<template>
    <article class="card group relative p-5 transition hover:border-brand-200 hover:shadow-md">
        <div class="flex items-start gap-4">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-brand-50 font-bold text-brand-700">{{ initials(resume.full_name) }}</div>
            <div class="min-w-0 flex-1">
                <RouterLink :to="`/resumes/${resume.id}`" class="after:absolute after:inset-0">
                    <h3 class="truncate font-semibold text-slate-900 group-hover:text-brand-600">{{ resume.title }}</h3>
                </RouterLink>
                <p class="text-sm text-slate-500">{{ resume.full_name }}<span v-if="resume.age">, {{ resume.age }} yosh</span></p>
            </div>
        </div>
        <div class="mt-3 font-semibold text-slate-900">{{ formatSalary(resume.salary_expected, null, resume.currency) }}</div>
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="chip">📍 {{ resume.city }}</span>
            <span class="chip">{{ resume.experience.label }}</span>
            <span v-for="skill in resume.skills.slice(0, 3)" :key="skill" class="chip bg-brand-50 text-brand-700">{{ skill }}</span>
        </div>
        <div class="mt-3 text-xs text-slate-400">{{ timeAgo(resume.updated_at) }} yangilangan</div>
    </article>
</template>
