<script setup>
import AppLogo from './AppLogo.vue';
</script>

<template>
    <footer class="relative mt-20 overflow-hidden bg-lapis-900 text-slate-300">
        <div class="atlas-strip h-2"></div>
        <div class="bg-girih absolute inset-0 top-2 opacity-[0.05]"></div>
        <div class="relative mx-auto grid max-w-6xl gap-10 px-4 py-12 md:grid-cols-[1.4fr_1fr_1fr]">
            <div>
                <AppLogo light />
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-400">
                    O'zbekiston bo'ylab ish izlovchilar va ish beruvchilarni bog'laydigan zamonaviy maydon. Halol mehnat — farovon hayot.
                </p>
            </div>
            <div>
                <div class="text-sm font-semibold text-gold-300">Ish izlovchilarga</div>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><RouterLink to="/vacancies" class="hover:text-white">Vakansiyalar</RouterLink></li>
                    <li><RouterLink to="/companies" class="hover:text-white">Kompaniyalar</RouterLink></li>
                    <li><RouterLink :to="{ name: 'register', query: { role: 'seeker' } }" class="hover:text-white">Rezyume yaratish</RouterLink></li>
                </ul>
            </div>
            <div>
                <div class="text-sm font-semibold text-gold-300">Ish beruvchilarga</div>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><RouterLink :to="{ name: 'register', query: { role: 'employer' } }" class="hover:text-white">Vakansiya joylash</RouterLink></li>
                    <li><RouterLink to="/login" class="hover:text-white">Kabinetga kirish</RouterLink></li>
                </ul>
            </div>
        </div>
        <div class="relative border-t border-white/10">
            <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-5 text-xs text-slate-500 md:flex-row md:justify-between">
                <span>© {{ new Date().getFullYear() }} IshKerakmi. Barcha huquqlar himoyalangan.</span>
                <span>O'zbekistonda mehr bilan yaratildi</span>
            </div>
        </div>
    </footer>
</template>
