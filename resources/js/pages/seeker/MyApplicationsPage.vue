<script setup>
import { onMounted, ref } from 'vue';
import api from '../../api';
import { formatSalary, timeAgo } from '../../format';
import { toast } from '../../toast';
import { useMetaStore } from '../../stores/meta';
import StatusBadge from '../../components/StatusBadge.vue';
import CompanyLogo from '../../components/CompanyLogo.vue';
import Spinner from '../../components/Spinner.vue';
import EmptyState from '../../components/EmptyState.vue';

const meta = useMetaStore();
const applications = ref(null);
const status = ref('');

async function load() {
    applications.value = (await api.get('/me/applications', { params: { status: status.value || undefined, per_page: 50 } })).data.data;
}

async function withdraw(app) {
    if (!confirm('Arizani qaytarib olasizmi?')) return;
    await api.delete(`/applications/${app.id}`);
    toast('Ariza qaytarib olindi');
    load();
}

onMounted(() => {
    meta.load();
    load();
});
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h1 class="text-3xl font-bold text-slate-900">Arizalarim</h1>
            <div class="flex flex-wrap gap-2">
                <button class="chip px-3 py-1.5" :class="!status && 'bg-brand-600 text-white'" @click="status = ''; load()">Barchasi</button>
                <button
                    v-for="s in meta.application_statuses"
                    :key="s.value"
                    class="chip px-3 py-1.5"
                    :class="status === s.value && 'bg-brand-600 text-white'"
                    @click="status = s.value; load()"
                >
                    {{ s.label }}
                </button>
            </div>
        </div>
        <Spinner v-if="!applications" />
        <EmptyState v-else-if="!applications.length" class="mt-6" icon="📨" title="Arizalar yo'q" text="Yoqqan vakansiyaga ariza topshiring.">
            <RouterLink to="/vacancies" class="btn-primary">Vakansiyalar</RouterLink>
        </EmptyState>
        <div v-else class="mt-6 space-y-3">
            <div v-for="a in applications" :key="a.id" class="card flex flex-col gap-4 p-5 md:flex-row md:items-center">
                <CompanyLogo :company="a.vacancy.company" />
                <div class="flex-1">
                    <RouterLink :to="`/vacancies/${a.vacancy.id}`" class="font-semibold text-slate-900 hover:text-brand-600">{{ a.vacancy.title }}</RouterLink>
                    <div class="text-sm text-slate-500">
                        {{ a.vacancy.company.name }} · {{ formatSalary(a.vacancy.salary_from, a.vacancy.salary_to, a.vacancy.currency) }}
                    </div>
                    <div class="mt-1 text-xs text-slate-400">{{ timeAgo(a.created_at) }} yuborilgan<span v-if="a.resume"> · Rezyume: {{ a.resume.title }}</span></div>
                </div>
                <StatusBadge :status="a.status" />
                <button v-if="a.status.value === 'pending'" class="btn-ghost text-xs" @click="withdraw(a)">Qaytarib olish</button>
            </div>
        </div>
    </div>
</template>
