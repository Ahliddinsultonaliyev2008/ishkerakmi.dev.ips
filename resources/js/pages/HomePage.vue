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
const popular = ['Dasturchi', 'Buxgalter', 'Sotuv menejeri', "O'qituvchi", 'Haydovchi', 'Dizayner'];
const steps = {
    seeker: [
        { icon: '📝', title: "Ro'yxatdan o'ting", text: 'Telefon raqamingiz bilan bir daqiqada hisob oching.' },
        { icon: '📄', title: 'Rezyume yarating', text: "Tajriba va ko'nikmalaringizni qulay shaklda yozing." },
        { icon: '🤝', title: 'Ariza yuboring', text: 'Yoqqan vakansiyaga bir bosishda ariza topshiring va javobni kuzating.' },
    ],
    employer: [
        { icon: '🏢', title: 'Kompaniyani qo\'shing', text: "Kompaniya profilini to'ldiring va ishonch qozoning." },
        { icon: '📢', title: 'Vakansiya joylang', text: "Talablar va maoshni ko'rsating — e'lon darhol chiqadi." },
        { icon: '✅', title: 'Nomzodni tanlang', text: 'Arizalarni ko\'ring, suhbatga chaqiring yoki qabul qiling.' },
    ],
};
const tab = ref('seeker');

function search(text = q.value) {
    router.push({ name: 'vacancies', query: { ...(text && { q: text }), ...(city.value && { city: city.value }) } });
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
        <!-- Hero -->
        <section class="relative overflow-hidden bg-gradient-to-br from-lapis-900 via-lapis-800 to-brand-700 text-white">
            <div class="bg-girih absolute inset-0 opacity-[0.09]"></div>
            <div class="absolute -top-32 -right-32 h-96 w-96 rounded-full bg-gold-400/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-20 h-96 w-96 rounded-full bg-brand-400/25 blur-3xl"></div>

            <div class="relative mx-auto grid max-w-6xl gap-12 px-4 py-16 md:py-24 lg:grid-cols-[1.35fr_1fr] lg:items-center">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-gold-300/40 bg-white/10 px-3 py-1 text-xs font-semibold tracking-wide text-gold-100">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-gold-300"></span>
                        Bugun {{ meta.stats.vacancies ?? '...' }} ta faol vakansiya
                    </span>
                    <h1 class="font-display mt-6 text-4xl leading-[1.1] font-bold md:text-5xl">
                        Mehnatingizga munosib <span class="text-gold-300">ishni</span> toping
                    </h1>
                    <p class="mt-5 max-w-xl text-lg text-brand-100">
                        Toshkentdan Xorazmgacha — O'zbekistonning ishonchli kompaniyalaridagi vakansiyalar bir joyda. Rezyume yarating va bir bosishda ariza topshiring.
                    </p>

                    <form class="mt-8 grid gap-2 rounded-2xl bg-white p-2 shadow-2xl shadow-lapis-900/40 md:grid-cols-[1fr_190px_auto]" @submit.prevent="search()">
                        <div class="relative">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" stroke-linecap="round" /></svg>
                            <input v-model="q" class="input border-0 py-3 pl-11 text-slate-800 focus:ring-0" placeholder="Lavozim, kasb yoki kompaniya" />
                        </div>
                        <select v-model="city" class="input border-0 bg-slate-50 py-3 text-slate-800 focus:ring-0">
                            <option value="">📍 Barcha shaharlar</option>
                            <option v-for="c in meta.cities" :key="c" :value="c">{{ c }}</option>
                        </select>
                        <button class="btn-gold px-8 py-3 text-base">Ish izlash</button>
                    </form>

                    <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                        <span class="text-brand-200">Ko'p izlanadi:</span>
                        <button v-for="p in popular" :key="p" type="button" class="rounded-full border border-white/20 bg-white/5 px-3 py-1 text-white/90 transition hover:border-gold-300 hover:text-gold-200" @click="search(p)">
                            {{ p }}
                        </button>
                    </div>
                </div>

                <div class="grid gap-4">
                    <RouterLink
                        :to="auth.isLoggedIn ? (auth.isEmployer ? '/vacancies' : '/my/resumes/new') : { name: 'register', query: { role: 'seeker' } }"
                        class="group flex items-center gap-4 rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur transition hover:border-gold-300/60 hover:bg-white/15"
                    >
                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-brand-400/30 text-3xl">👷</span>
                        <span class="flex-1">
                            <span class="block font-bold">Men ish izlayapman</span>
                            <span class="text-sm text-brand-100">Rezyume yarating, kompaniyalar sizni topsin</span>
                        </span>
                        <span class="text-xl text-gold-300 transition group-hover:translate-x-1">→</span>
                    </RouterLink>
                    <RouterLink
                        :to="auth.isEmployer ? '/employer/vacancies/new' : { name: 'register', query: { role: 'employer' } }"
                        class="group flex items-center gap-4 rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur transition hover:border-gold-300/60 hover:bg-white/15"
                    >
                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gold-400/30 text-3xl">🏢</span>
                        <span class="flex-1">
                            <span class="block font-bold">Menga xodim kerak</span>
                            <span class="text-sm text-brand-100">Vakansiya joylang, eng yaxshi nomzodlarni tanlang</span>
                        </span>
                        <span class="text-xl text-gold-300 transition group-hover:translate-x-1">→</span>
                    </RouterLink>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div v-for="s in [
                            { n: meta.stats.vacancies, l: 'Vakansiya' },
                            { n: meta.stats.companies, l: 'Kompaniya' },
                            { n: meta.stats.resumes, l: 'Rezyume' },
                            { n: meta.stats.seekers, l: 'Ish izlovchi' },
                        ]" :key="s.l" class="rounded-2xl bg-lapis-900/40 px-4 py-3 ring-1 ring-white/10">
                            <div class="font-display text-2xl font-bold text-gold-300">{{ s.n ?? 0 }}</div>
                            <div class="text-xs text-brand-100">{{ s.l }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="atlas-strip relative h-2"></div>
        </section>

        <!-- Kategoriyalar -->
        <section class="mx-auto max-w-6xl px-4 py-16">
            <div class="text-center">
                <span class="text-xs font-bold tracking-[0.2em] text-gold-600 uppercase">Sohalar</span>
                <h2 class="font-display mt-2 text-3xl font-bold text-lapis-900">O'zingizga mos sohani tanlang</h2>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                <RouterLink
                    v-for="cat in meta.categories"
                    :key="cat.id"
                    :to="{ name: 'vacancies', query: { category: cat.slug } }"
                    class="card group p-4 text-center transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg hover:shadow-brand-900/5"
                >
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-brand-50 to-gold-50 text-2xl ring-1 ring-brand-100 transition group-hover:scale-110">{{ cat.icon }}</div>
                    <div class="mt-3 text-sm font-bold text-slate-900">{{ cat.name }}</div>
                    <div class="text-xs text-slate-500">{{ cat.vacancies_count }} vakansiya</div>
                </RouterLink>
            </div>
        </section>

        <!-- Yangi vakansiyalar -->
        <section class="mx-auto max-w-6xl px-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold tracking-[0.2em] text-gold-600 uppercase">Yangi</span>
                    <h2 class="font-display mt-2 text-3xl font-bold text-lapis-900">So'nggi vakansiyalar</h2>
                </div>
                <RouterLink to="/vacancies" class="btn-ghost shrink-0">Barchasi →</RouterLink>
            </div>
            <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <VacancyCard v-for="v in latest" :key="v.id" :vacancy="v" />
            </div>
        </section>

        <!-- Qanday ishlaydi -->
        <section class="mx-auto max-w-6xl px-4 py-20">
            <div class="relative overflow-hidden rounded-3xl bg-white p-8 ring-1 ring-slate-200 md:p-12">
                <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span class="text-xs font-bold tracking-[0.2em] text-gold-600 uppercase">Oddiy va qulay</span>
                        <h2 class="font-display mt-2 text-3xl font-bold text-lapis-900">Qanday ishlaydi?</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1 text-sm font-semibold">
                        <button class="rounded-lg px-4 py-2 transition" :class="tab === 'seeker' ? 'bg-white text-brand-700 shadow' : 'text-slate-500'" @click="tab = 'seeker'">Ish izlovchi</button>
                        <button class="rounded-lg px-4 py-2 transition" :class="tab === 'employer' ? 'bg-white text-brand-700 shadow' : 'text-slate-500'" @click="tab = 'employer'">Ish beruvchi</button>
                    </div>
                </div>
                <div class="relative mt-10 grid gap-6 md:grid-cols-3">
                    <div v-for="(s, i) in steps[tab]" :key="tab + i" class="relative rounded-2xl border border-slate-200 bg-[#fbfaf7] p-6">
                        <span class="font-display absolute top-4 right-5 text-4xl font-bold text-gold-100">0{{ i + 1 }}</span>
                        <div class="grid h-12 w-12 place-items-center rounded-xl bg-brand-600 text-2xl shadow-lg shadow-brand-600/30">{{ s.icon }}</div>
                        <h3 class="mt-4 font-bold text-slate-900">{{ s.title }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ s.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kompaniyalar -->
        <section class="mx-auto max-w-6xl px-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold tracking-[0.2em] text-gold-600 uppercase">Ishonchli</span>
                    <h2 class="font-display mt-2 text-3xl font-bold text-lapis-900">Top ish beruvchilar</h2>
                </div>
                <RouterLink to="/companies" class="btn-ghost shrink-0">Barchasi →</RouterLink>
            </div>
            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <RouterLink v-for="c in companies" :key="c.id" :to="`/companies/${c.id}`" class="card flex items-center gap-4 p-4 transition hover:border-brand-300 hover:shadow-md">
                    <CompanyLogo :company="c" />
                    <div class="min-w-0">
                        <div class="truncate font-bold text-slate-900">{{ c.name }}</div>
                        <div class="text-sm text-slate-500">{{ c.industry }} · <span class="font-semibold text-brand-600">{{ c.vacancies_count }} vakansiya</span></div>
                    </div>
                </RouterLink>
            </div>
        </section>

        <!-- CTA -->
        <section v-if="!auth.isLoggedIn" class="mx-auto max-w-6xl px-4 pt-20">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-700 to-lapis-800 p-10 text-white md:p-14">
                <div class="bg-girih absolute inset-0 opacity-[0.1]"></div>
                <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="font-display text-2xl font-bold md:text-3xl">Xodim kerakmi? Bugunoq e'lon bering</h3>
                        <p class="mt-2 max-w-xl text-brand-100">Minglab ish izlovchilar sizning vakansiyangizni kutmoqda. Joylash bepul.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <RouterLink :to="{ name: 'register', query: { role: 'employer' } }" class="btn-gold px-6 py-3">Vakansiya joylash</RouterLink>
                        <RouterLink :to="{ name: 'register', query: { role: 'seeker' } }" class="btn border border-white/30 px-6 py-3 text-white hover:bg-white/10">Rezyume yaratish</RouterLink>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
