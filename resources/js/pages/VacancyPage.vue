<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { errorMessage } from '../api';
import { useAuthStore } from '../stores/auth';
import { formatSalary, timeAgo } from '../format';
import { toast } from '../toast';
import CompanyLogo from '../components/CompanyLogo.vue';
import LikeButton from '../components/LikeButton.vue';
import CommentsSection from '../components/CommentsSection.vue';
import Spinner from '../components/Spinner.vue';
import StatusBadge from '../components/StatusBadge.vue';

const props = defineProps({ id: String });
const auth = useAuthStore();
const router = useRouter();
const vacancy = ref(null);
const notFound = ref(false);
const resumes = ref([]);
const applyOpen = ref(false);
const form = ref({ resume_id: '', cover_letter: '' });
const sending = ref(false);

const isOwner = computed(() => auth.user?.company?.id === vacancy.value?.company?.id);

async function openApply() {
    if (!auth.isLoggedIn) return router.push({ name: 'login', query: { redirect: `/vacancies/${props.id}` } });
    const { data } = await api.get('/me/resumes');
    resumes.value = data.data;
    form.value.resume_id = resumes.value[0]?.id ?? '';
    applyOpen.value = true;
}

async function apply() {
    sending.value = true;
    try {
        await api.post(`/vacancies/${props.id}/apply`, { ...form.value, resume_id: form.value.resume_id || null });
        vacancy.value.has_applied = true;
        applyOpen.value = false;
        toast('Arizangiz yuborildi!');
    } catch (e) {
        toast(errorMessage(e), 'error');
    } finally {
        sending.value = false;
    }
}

onMounted(async () => {
    try {
        const { data } = await api.get(`/vacancies/${props.id}`);
        vacancy.value = data.data;
    } catch {
        notFound.value = true;
    }
});
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div v-if="notFound" class="card p-10 text-center">Vakansiya topilmadi. <RouterLink to="/vacancies" class="text-brand-600">Ortga</RouterLink></div>
        <Spinner v-else-if="!vacancy" />
        <div v-else class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <div class="space-y-6">
                <section class="card p-6 md:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span v-if="vacancy.category" class="chip">{{ vacancy.category.icon }} {{ vacancy.category.name }}</span>
                                <StatusBadge v-if="vacancy.status.value !== 'active'" :status="vacancy.status" />
                            </div>
                            <h1 class="mt-3 text-2xl font-bold text-slate-900 md:text-3xl">{{ vacancy.title }}</h1>
                            <div class="mt-3 text-2xl font-bold text-brand-600">{{ formatSalary(vacancy.salary_from, vacancy.salary_to, vacancy.currency) }}</div>
                        </div>
                        <LikeButton type="vacancies" :item="vacancy" />
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="chip">📍 {{ vacancy.city }}<template v-if="vacancy.address">, {{ vacancy.address }}</template></span>
                        <span class="chip">💼 {{ vacancy.employment_type.label }}</span>
                        <span class="chip">⏳ {{ vacancy.experience.label }}</span>
                        <span class="chip">👁 {{ vacancy.views }} marta ko'rildi</span>
                        <span class="chip">🕒 {{ timeAgo(vacancy.created_at) }}</span>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <RouterLink v-if="isOwner" :to="`/employer/vacancies/${vacancy.id}/edit`" class="btn-primary">Tahrirlash</RouterLink>
                        <template v-else-if="!auth.isEmployer">
                            <button v-if="vacancy.has_applied" class="btn-ghost" disabled>✓ Ariza topshirilgan</button>
                            <button v-else-if="vacancy.status.value === 'active'" class="btn-primary px-8" @click="openApply">Ariza topshirish</button>
                        </template>
                    </div>

                    <form v-if="applyOpen" class="mt-6 space-y-4 rounded-xl bg-slate-50 p-5" @submit.prevent="apply">
                        <div>
                            <label class="label">Rezyume</label>
                            <select v-model="form.resume_id" class="input">
                                <option value="">Rezyumesiz yuborish</option>
                                <option v-for="r in resumes" :key="r.id" :value="r.id">{{ r.title }}</option>
                            </select>
                            <p v-if="!resumes.length" class="mt-1 text-xs text-slate-500">
                                Sizda rezyume yo'q. <RouterLink to="/my/resumes/new" class="text-brand-600">Yaratish</RouterLink>
                            </p>
                        </div>
                        <div>
                            <label class="label">Qo'shimcha xat (ixtiyoriy)</label>
                            <textarea v-model="form.cover_letter" rows="4" class="input" placeholder="Nega aynan siz bu lavozimga mossiz?"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn-primary" :disabled="sending">Yuborish</button>
                            <button type="button" class="btn-ghost" @click="applyOpen = false">Bekor qilish</button>
                        </div>
                    </form>
                </section>

                <section class="card space-y-6 p-6 md:p-8">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Vakansiya haqida</h2>
                        <p class="mt-2 whitespace-pre-line text-slate-700">{{ vacancy.description }}</p>
                    </div>
                    <div v-if="vacancy.requirements">
                        <h2 class="text-lg font-semibold text-slate-900">Talablar</h2>
                        <p class="mt-2 whitespace-pre-line text-slate-700">{{ vacancy.requirements }}</p>
                    </div>
                    <div v-if="vacancy.benefits">
                        <h2 class="text-lg font-semibold text-slate-900">Biz taklif qilamiz</h2>
                        <p class="mt-2 whitespace-pre-line text-slate-700">{{ vacancy.benefits }}</p>
                    </div>
                    <div v-if="vacancy.skills.length">
                        <h2 class="text-lg font-semibold text-slate-900">Ko'nikmalar</h2>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span v-for="s in vacancy.skills" :key="s" class="chip bg-brand-50 text-brand-700">{{ s }}</span>
                        </div>
                    </div>
                </section>

                <CommentsSection type="vacancies" :id="vacancy.id" />
            </div>

            <aside class="space-y-4">
                <RouterLink :to="`/companies/${vacancy.company.id}`" class="card block p-6 hover:border-brand-200">
                    <CompanyLogo :company="vacancy.company" size="h-16 w-16 text-xl" />
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ vacancy.company.name }}</h3>
                    <p class="text-sm text-slate-500">{{ vacancy.company.industry }} · {{ vacancy.company.city }}</p>
                    <p class="mt-3 line-clamp-4 text-sm text-slate-600">{{ vacancy.company.description }}</p>
                    <div class="mt-4 text-sm font-semibold text-brand-600">{{ vacancy.company.vacancies_count }} ta vakansiya →</div>
                </RouterLink>
            </aside>
        </div>
    </div>
</template>
