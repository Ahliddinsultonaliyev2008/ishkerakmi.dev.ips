<script setup>
import { onMounted, ref } from 'vue';
import api from '../../api';
import { formatSalary, timeAgo } from '../../format';
import { toast } from '../../toast';
import Spinner from '../../components/Spinner.vue';
import EmptyState from '../../components/EmptyState.vue';

const resumes = ref(null);

async function load() {
    resumes.value = (await api.get('/me/resumes')).data.data;
}

async function remove(resume) {
    if (!confirm(`"${resume.title}" rezyumesini o'chirasizmi?`)) return;
    await api.delete(`/resumes/${resume.id}`);
    toast("Rezyume o'chirildi");
    load();
}

onMounted(load);
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-slate-900">Rezyumelarim</h1>
            <RouterLink to="/my/resumes/new" class="btn-primary">+ Yangi rezyume</RouterLink>
        </div>
        <Spinner v-if="!resumes" />
        <EmptyState v-else-if="!resumes.length" class="mt-6" icon="📄" title="Sizda hali rezyume yo'q" text="Rezyume yarating — ish beruvchilar sizni topa olishadi.">
            <RouterLink to="/my/resumes/new" class="btn-primary">Rezyume yaratish</RouterLink>
        </EmptyState>
        <div v-else class="mt-6 space-y-3">
            <div v-for="r in resumes" :key="r.id" class="card flex flex-col gap-4 p-5 md:flex-row md:items-center">
                <div class="flex-1">
                    <RouterLink :to="`/resumes/${r.id}`" class="text-lg font-semibold text-slate-900 hover:text-brand-600">{{ r.title }}</RouterLink>
                    <div class="mt-1 text-sm text-slate-500">
                        {{ formatSalary(r.salary_expected, null, r.currency) }} · {{ r.city }} · {{ timeAgo(r.updated_at) }} yangilangan
                    </div>
                    <div class="mt-2 flex gap-2 text-xs">
                        <span class="chip" :class="r.is_public ? 'bg-emerald-50 text-emerald-700' : ''">{{ r.is_public ? '🌐 Ommaviy' : '🔒 Yopiq' }}</span>
                        <span class="chip">❤️ {{ r.likes_count }}</span>
                        <span class="chip">💬 {{ r.comments_count }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <RouterLink :to="`/my/resumes/${r.id}/edit`" class="btn-ghost">Tahrirlash</RouterLink>
                    <button class="btn-danger" @click="remove(r)">O'chirish</button>
                </div>
            </div>
        </div>
    </div>
</template>
