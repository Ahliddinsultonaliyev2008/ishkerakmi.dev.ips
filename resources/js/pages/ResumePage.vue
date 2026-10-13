<script setup>
import { onMounted, ref } from 'vue';
import api from '../api';
import { formatSalary, initials, timeAgo } from '../format';
import { useAuthStore } from '../stores/auth';
import LikeButton from '../components/LikeButton.vue';
import CommentsSection from '../components/CommentsSection.vue';
import Spinner from '../components/Spinner.vue';

const props = defineProps({ id: String });
const auth = useAuthStore();
const resume = ref(null);
const error = ref(false);

onMounted(async () => {
    try {
        resume.value = (await api.get(`/resumes/${props.id}`)).data.data;
    } catch {
        error.value = true;
    }
});
</script>

<template>
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div v-if="error" class="card p-10 text-center">Rezyume topilmadi yoki yopiq.</div>
        <Spinner v-else-if="!resume" />
        <div v-else class="space-y-6">
            <section class="card p-6 md:p-8">
                <div class="flex flex-col gap-5 md:flex-row md:items-start">
                    <div class="grid h-20 w-20 shrink-0 place-items-center rounded-full bg-brand-100 text-2xl font-bold text-brand-700">{{ initials(resume.full_name) }}</div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-slate-900">{{ resume.title }}</h1>
                        <p class="text-slate-600">{{ resume.full_name }}<span v-if="resume.age">, {{ resume.age }} yosh</span></p>
                        <div class="mt-2 text-xl font-bold text-brand-600">{{ formatSalary(resume.salary_expected, null, resume.currency) }}</div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="chip">📍 {{ resume.city }}</span>
                            <span class="chip">💼 {{ resume.employment_type.label }}</span>
                            <span class="chip">⏳ {{ resume.experience.label }}</span>
                            <span v-if="resume.category" class="chip">{{ resume.category.icon }} {{ resume.category.name }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <RouterLink v-if="auth.user?.id === resume.user_id" :to="`/my/resumes/${resume.id}/edit`" class="btn-ghost">Tahrirlash</RouterLink>
                        <LikeButton type="resumes" :item="resume" />
                    </div>
                </div>
                <div v-if="resume.phone" class="mt-6 grid gap-3 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-2">
                    <a :href="`tel:${resume.phone}`" class="font-medium text-slate-800">📞 {{ resume.phone }}</a>
                    <a v-if="resume.email" :href="`mailto:${resume.email}`" class="font-medium text-slate-800">✉️ {{ resume.email }}</a>
                </div>
                <p v-else class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Kontaktlar faqat ish beruvchilarga ko'rinadi.</p>
            </section>
            <section class="card space-y-5 p-6 md:p-8">
                <div v-if="resume.about">
                    <h2 class="text-lg font-semibold text-slate-900">O'zim haqimda</h2>
                    <p class="mt-2 whitespace-pre-line text-slate-700">{{ resume.about }}</p>
                </div>
                <div v-if="resume.skills.length">
                    <h2 class="text-lg font-semibold text-slate-900">Ko'nikmalar</h2>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span v-for="s in resume.skills" :key="s" class="chip bg-brand-50 text-brand-700">{{ s }}</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400">{{ timeAgo(resume.updated_at) }} yangilangan</p>
            </section>
            <CommentsSection type="resumes" :id="resume.id" />
        </div>
    </div>
</template>
