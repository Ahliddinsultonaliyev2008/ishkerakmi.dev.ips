<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { initials } from '../format';

const auth = useAuthStore();
const router = useRouter();
const open = ref(false);
const menu = ref(false);

async function logout() {
    await auth.logout();
    menu.value = false;
    router.push('/');
}
</script>

<template>
    <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/85 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4">
            <RouterLink to="/" class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-slate-900">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-600 text-white">IK</span>
                Ish kerakmi<span class="text-brand-600">.uz</span>
            </RouterLink>

            <nav class="hidden items-center gap-1 text-sm font-medium md:flex">
                <RouterLink to="/vacancies" class="nav-link">Vakansiyalar</RouterLink>
                <RouterLink to="/companies" class="nav-link">Kompaniyalar</RouterLink>
                <RouterLink v-if="auth.isEmployer" to="/resumes" class="nav-link">Rezyumelar</RouterLink>
            </nav>

            <div class="ml-auto hidden items-center gap-2 md:flex">
                <template v-if="!auth.isLoggedIn">
                    <RouterLink to="/login" class="btn-ghost">Kirish</RouterLink>
                    <RouterLink to="/register" class="btn-primary">Ro'yxatdan o'tish</RouterLink>
                </template>
                <template v-else>
                    <RouterLink v-if="auth.isEmployer" to="/employer/vacancies/new" class="btn-primary">+ Vakansiya joylash</RouterLink>
                    <RouterLink v-else to="/my/resumes/new" class="btn-primary">+ Rezyume yaratish</RouterLink>
                    <div class="relative">
                        <button class="flex items-center gap-2 rounded-xl p-1.5 hover:bg-slate-100" @click="menu = !menu">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                                {{ initials(auth.user.name) }}
                            </span>
                        </button>
                        <div v-if="menu" class="card absolute right-0 mt-2 w-60 p-2 text-sm" @click="menu = false">
                            <div class="px-3 py-2">
                                <div class="font-semibold text-slate-900">{{ auth.user.name }}</div>
                                <div class="text-xs text-slate-500">{{ auth.user.role.label }}</div>
                            </div>
                            <hr class="my-1 border-slate-100" />
                            <template v-if="auth.isEmployer">
                                <RouterLink to="/employer" class="menu-link">Kabinet</RouterLink>
                                <RouterLink to="/employer/applications" class="menu-link">Kelgan arizalar</RouterLink>
                                <RouterLink to="/employer/company" class="menu-link">Kompaniya profili</RouterLink>
                            </template>
                            <template v-else>
                                <RouterLink to="/my/resumes" class="menu-link">Rezyumelarim</RouterLink>
                                <RouterLink to="/my/applications" class="menu-link">Arizalarim</RouterLink>
                            </template>
                            <RouterLink to="/saved" class="menu-link">Saqlanganlar</RouterLink>
                            <RouterLink to="/profile" class="menu-link">Sozlamalar</RouterLink>
                            <button class="menu-link w-full text-left text-rose-600" @click="logout">Chiqish</button>
                        </div>
                    </div>
                </template>
            </div>

            <button class="ml-auto rounded-lg p-2 md:hidden" aria-label="Menyu" @click="open = !open">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
        </div>

        <div v-if="open" class="border-t border-slate-200 bg-white px-4 py-3 md:hidden" @click="open = false">
            <RouterLink to="/vacancies" class="menu-link">Vakansiyalar</RouterLink>
            <RouterLink to="/companies" class="menu-link">Kompaniyalar</RouterLink>
            <template v-if="auth.isLoggedIn">
                <RouterLink v-if="auth.isEmployer" to="/resumes" class="menu-link">Rezyumelar</RouterLink>
                <RouterLink :to="auth.isEmployer ? '/employer' : '/my/applications'" class="menu-link">Kabinet</RouterLink>
                <RouterLink to="/saved" class="menu-link">Saqlanganlar</RouterLink>
                <button class="menu-link w-full text-left text-rose-600" @click="logout">Chiqish</button>
            </template>
            <template v-else>
                <RouterLink to="/login" class="menu-link">Kirish</RouterLink>
                <RouterLink to="/register" class="menu-link">Ro'yxatdan o'tish</RouterLink>
            </template>
        </div>
    </header>
</template>

<style scoped>
@reference '../../css/app.css';
.nav-link {
    @apply rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900;
}
.nav-link.router-link-active {
    @apply text-brand-600;
}
.menu-link {
    @apply block rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-100;
}
</style>
