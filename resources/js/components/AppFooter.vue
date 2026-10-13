<template>
    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
            <div>
                <span class="font-bold text-slate-800">Ish kerakmi.uz</span> — O'zbekistondagi ish va xodim qidirish platformasi.
            </div>
            <div class="flex gap-4">
                <RouterLink to="/vacancies" class="hover:text-slate-800">Vakansiyalar</RouterLink>
                <RouterLink to="/companies" class="hover:text-slate-800">Kompaniyalar</RouterLink>
                <RouterLink to="/register" class="hover:text-slate-800">Ish beruvchilarga</RouterLink>
            </div>
            <div>© {{ new Date().getFullYear() }}</div>
        </div>
    </footer>
</template>
