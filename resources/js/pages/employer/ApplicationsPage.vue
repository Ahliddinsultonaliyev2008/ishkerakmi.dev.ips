<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';
import { formatSalary, initials, timeAgo } from '../../format';
import { toast } from '../../toast';
import { useMetaStore } from '../../stores/meta';
import StatusBadge from '../../components/StatusBadge.vue';
import Spinner from '../../components/Spinner.vue';
import EmptyState from '../../components/EmptyState.vue';

const route = useRoute();
const meta = useMetaStore();
const applications = ref(null);
const status = ref('');
const vacancyId = computed(() => route.query.vacancy);

async function load() {
    const url = vacancyId.value ? `/vacancies/${vacancyId.value}/applications` : '/me/incoming-applications';
    applications.value = (await api.get(url, { params: { status: status.value || undefined, per_page: 50 } })).data.data;
}

async function setStatus(app, value) {
    const { data } = await api.patch(`/applications/${app.id}`, { status: value });
    app.status = data.data.status;
    toast(`Holat: ${data.data.status.label}`);
}

onMounted(() => {
    meta.load();
    load();
});
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Kelgan arizalar</h1>
                <RouterLink v-if="vacancyId" to="/employer/applications" class="text-sm text-brand-600">← Barcha vakansiyalar bo'yicha</RouterLink>
            </div>
            <select v-if="!vacancyId" v-model="status" class="input md:w-56" @change="load">
                <option value="">Barcha holatlar</option>
                <option v-for="s in meta.application_statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
        </div>
        <Spinner v-if="!applications" />
        <EmptyState v-else-if="!applications.length" class="mt-6" icon="📭" title="Hozircha ariza yo'q" />
        <div v-else class="mt-6 space-y-3">
            <div v-for="a in applications" :key="a.id" class="card p-5">
                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-brand-50 font-bold text-brand-700">{{ initials(a.applicant.name) }}</div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-slate-900">{{ a.applicant.name }}</span>
                            <StatusBadge :status="a.status" />
                        </div>
                        <div class="text-sm text-slate-500">
                            <a :href="`tel:${a.applicant.phone}`" class="hover:text-brand-600">{{ a.applicant.phone }}</a>
                            <span v-if="a.vacancy"> · {{ a.vacancy.title }}</span> · {{ timeAgo(a.created_at) }}
                        </div>
                        <p v-if="a.cover_letter" class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-700">"{{ a.cover_letter }}"</p>
                        <RouterLink v-if="a.resume" :to="`/resumes/${a.resume.id}`" class="mt-2 inline-block text-sm font-medium text-brand-600 hover:underline">
                            📄 {{ a.resume.title }} — {{ formatSalary(a.resume.salary_expected, null, a.resume.currency) }}
                        </RouterLink>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button class="btn-ghost text-sky-700" :disabled="a.status.value === 'interview'" @click="setStatus(a, 'interview')">Suhbatga</button>
                        <button class="btn-ghost text-emerald-700" :disabled="a.status.value === 'accepted'" @click="setStatus(a, 'accepted')">Qabul</button>
                        <button class="btn-danger" :disabled="a.status.value === 'rejected'" @click="setStatus(a, 'rejected')">Rad etish</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
