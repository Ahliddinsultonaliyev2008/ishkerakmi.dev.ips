<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../../api';
import { useAuthStore } from '../../stores/auth';
import { formatSalary, timeAgo } from '../../format';
import { toast } from '../../toast';
import StatusBadge from '../../components/StatusBadge.vue';
import CompanyLogo from '../../components/CompanyLogo.vue';
import Spinner from '../../components/Spinner.vue';
import EmptyState from '../../components/EmptyState.vue';

const auth = useAuthStore();
const vacancies = ref(null);
const stats = computed(() => {
    const list = vacancies.value ?? [];
    return {
        total: list.length,
        active: list.filter((v) => v.status.value === 'active').length,
        applications: list.reduce((s, v) => s + (v.applications_count ?? 0), 0),
        views: list.reduce((s, v) => s + v.views, 0),
    };
});

async function load() {
    vacancies.value = (await api.get('/me/vacancies', { params: { per_page: 50 } })).data.data;
}

async function toggleStatus(v) {
    const status = v.status.value === 'active' ? 'closed' : 'active';
    const payload = { ...v, category_id: v.category?.id ?? null, employment_type: v.employment_type.value, experience: v.experience.value, currency: v.currency.value, status };
    await api.put(`/vacancies/${v.id}`, payload);
    toast(status === 'active' ? 'Vakansiya faollashtirildi' : 'Vakansiya yopildi');
    load();
}

async function remove(v) {
    if (!confirm(`"${v.title}" vakansiyasini o'chirasizmi?`)) return;
    await api.delete(`/vacancies/${v.id}`);
    toast("Vakansiya o'chirildi");
    load();
}

onMounted(load);
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <CompanyLogo :company="auth.user.company" size="h-14 w-14 text-lg" />
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ auth.user.company?.name }}</h1>
                <p class="text-sm text-slate-500">Ish beruvchi kabineti</p>
            </div>
            <div class="flex gap-2">
                <RouterLink to="/employer/company" class="btn-ghost">Kompaniya profili</RouterLink>
                <RouterLink to="/employer/vacancies/new" class="btn-primary">+ Vakansiya</RouterLink>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="card p-5"><div class="text-sm text-slate-500">Jami vakansiya</div><div class="mt-1 text-3xl font-bold">{{ stats.total }}</div></div>
            <div class="card p-5"><div class="text-sm text-slate-500">Faol</div><div class="mt-1 text-3xl font-bold text-emerald-600">{{ stats.active }}</div></div>
            <RouterLink to="/employer/applications" class="card p-5 hover:border-brand-200">
                <div class="text-sm text-slate-500">Arizalar</div><div class="mt-1 text-3xl font-bold text-brand-600">{{ stats.applications }}</div>
            </RouterLink>
            <div class="card p-5"><div class="text-sm text-slate-500">Ko'rishlar</div><div class="mt-1 text-3xl font-bold">{{ stats.views }}</div></div>
        </div>

        <h2 class="mt-10 text-xl font-bold text-slate-900">Vakansiyalarim</h2>
        <Spinner v-if="!vacancies" />
        <EmptyState v-else-if="!vacancies.length" class="mt-4" icon="📢" title="Hali vakansiya joylamagansiz">
            <RouterLink to="/employer/vacancies/new" class="btn-primary">Birinchi vakansiyani joylash</RouterLink>
        </EmptyState>
        <div v-else class="card mt-4 divide-y divide-slate-100">
            <div v-for="v in vacancies" :key="v.id" class="flex flex-col gap-3 p-5 md:flex-row md:items-center">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <RouterLink :to="`/vacancies/${v.id}`" class="font-semibold text-slate-900 hover:text-brand-600">{{ v.title }}</RouterLink>
                        <StatusBadge :status="v.status" />
                    </div>
                    <div class="mt-1 text-sm text-slate-500">
                        {{ formatSalary(v.salary_from, v.salary_to, v.currency) }} · {{ v.city }} · {{ timeAgo(v.created_at) }}
                    </div>
                    <div class="mt-1 text-xs text-slate-400">👁 {{ v.views }} · 📨 {{ v.applications_count }} ariza · ❤️ {{ v.likes_count }} · 💬 {{ v.comments_count }}</div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <RouterLink :to="{ name: 'employer-applications', query: { vacancy: v.id } }" class="btn-ghost">Arizalar</RouterLink>
                    <RouterLink :to="`/employer/vacancies/${v.id}/edit`" class="btn-ghost">Tahrirlash</RouterLink>
                    <button class="btn-ghost" @click="toggleStatus(v)">{{ v.status.value === 'active' ? 'Yopish' : 'Ochish' }}</button>
                    <button class="btn-danger" @click="remove(v)">O'chirish</button>
                </div>
            </div>
        </div>
    </div>
</template>
