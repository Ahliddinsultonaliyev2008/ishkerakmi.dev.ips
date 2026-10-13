<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { useMetaStore } from '../stores/meta';
import { useAuthStore } from '../stores/auth';
import VacancyCard from '../components/VacancyCard.vue';
import CompanyLogo from '../components/CompanyLogo.vue';

const meta = useMetaStore();
const auth = useAuthStore();
const router = useRouter();
const q = ref('');
const city = ref('');
const latest = ref([]);
const companies = ref([]);

function search() {
    router.push({ name: 'vacancies', query: { ...(q.value && { q: q.value }), ...(city.value && { city: city.value }) } });
}

onMounted(async () => {
    meta.load();
    const [vacancies, comps] = await Promise.all([api.get('/vacancies', { params: { per_page: 6 } }), api.get('/companies', { params: { per_page: 6 } })]);
    latest.value = vacancies.data.data;
    companies.value = comps.data.data;
});
</script>

<template>
    <div>
        <section class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-indigo-600 text-white">
            <div class="absolute inset-0 opacity-20 [background:radial-gradient(circle_at_20%_20%,white_0,transparent_35%),radial-gradient(circle_at_80%_0,white_0,transparent_25%)]"></div>
            <div class="relative mx-auto max-w-6xl px-4 py-20 md:py-28">
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide uppercase">{{ meta.stats.vacancies ?? '...' }} ta faol vakansiya</span>
                <h1 class="mt-5 max-w-3xl text-4xl font-extrabold tracking-tight md:text-6xl">Orzuingizdagi ishni bugun toping</h1>
                <p class="mt-4 max-w-2xl text-lg text-brand-100">O'zbekistonning eng yaxshi kompaniyalaridagi vakansiyalar. Rezyume yarating, bir bosishda ariza topshiring.</p>

                <form class="mt-8 grid gap-2 rounded-2xl bg-white p-2 shadow-2xl md:grid-cols-[1fr_220px_auto]" @submit.prevent="search">
                    <input v-model="q" class="input border-0 text-slate-800 focus:ring-0" placeholder="Lavozim, kasb yoki kompaniya nomi" />
                    <select v-model="city" class="input border-0 text-slate-800 focus:ring-0">
                        <option value="">Barcha shaharlar</option>
                        <option v-for="c in meta.cities" :key="c" :value="c">{{ c }}</option>
                    </select>
                    <button class="btn-primary px-8 py-3">Qidirish</button>
                </form>

                <div class="mt-10 grid max-w-2xl grid-cols-2 gap-6 md:grid-cols-4">
                    <div><div class="text-3xl font-bold">{{ meta.stats.vacancies ?? 0 }}</div><div class="text-sm text-brand-100">Vakansiya</div></div>
                    <div><div class="text-3xl font-bold">{{ meta.stats.companies ?? 0 }}</div><div class="text-sm text-brand-100">Kompaniya</div></div>
                    <div><div class="text-3xl font-bold">{{ meta.stats.resumes ?? 0 }}</div><div class="text-sm text-brand-100">Rezyume</div></div>
                    <div><div class="text-3xl font-bold">{{ meta.stats.seekers ?? 0 }}</div><div class="text-sm text-brand-100">Ish izlovchi</div></div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-14">
            <h2 class="text-2xl font-bold text-slate-900">Kategoriyalar</h2>
            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-5">
                <RouterLink
                    v-for="cat in meta.categories"
                    :key="cat.id"
                    :to="{ name: 'vacancies', query: { category: cat.slug } }"
                    class="card p-4 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md"
                >
                    <div class="text-2xl">{{ cat.icon }}</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ cat.name }}</div>
                    <div class="text-xs text-slate-500">{{ cat.vacancies_count }} vakansiya</div>
                </RouterLink>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Yangi vakansiyalar</h2>
                <RouterLink to="/vacancies" class="text-sm font-semibold text-brand-600 hover:underline">Barchasi →</RouterLink>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <VacancyCard v-for="v in latest" :key="v.id" :vacancy="v" />
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-14">
            <h2 class="text-2xl font-bold text-slate-900">Top kompaniyalar</h2>
            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <RouterLink v-for="c in companies" :key="c.id" :to="`/companies/${c.id}`" class="card flex items-center gap-4 p-4 hover:border-brand-200 hover:shadow-md">
                    <CompanyLogo :company="c" />
                    <div class="min-w-0">
                        <div class="truncate font-semibold text-slate-900">{{ c.name }}</div>
                        <div class="text-sm text-slate-500">{{ c.industry }} · {{ c.vacancies_count }} vakansiya</div>
                    </div>
                </RouterLink>
            </div>
        </section>

        <section v-if="!auth.isLoggedIn" class="mx-auto max-w-6xl px-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="card bg-gradient-to-br from-emerald-50 to-white p-8">
                    <h3 class="text-xl font-bold text-slate-900">Ish izlayapsizmi?</h3>
                    <p class="mt-2 text-slate-600">Rezyume yarating va kompaniyalar sizni o'zi topsin.</p>
                    <RouterLink :to="{ name: 'register', query: { role: 'seeker' } }" class="btn-primary mt-5">Rezyume yaratish</RouterLink>
                </div>
                <div class="card bg-gradient-to-br from-violet-50 to-white p-8">
                    <h3 class="text-xl font-bold text-slate-900">Xodim kerakmi?</h3>
                    <p class="mt-2 text-slate-600">Vakansiya joylang va eng yaxshi nomzodlardan ariza oling.</p>
                    <RouterLink :to="{ name: 'register', query: { role: 'employer' } }" class="btn-primary mt-5">Vakansiya joylash</RouterLink>
                </div>
            </div>
        </section>
    </div>
</template>
