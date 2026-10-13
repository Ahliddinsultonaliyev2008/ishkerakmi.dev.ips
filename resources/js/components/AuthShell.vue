<script setup>
import AppLogo from './AppLogo.vue';

defineProps({ title: String, subtitle: String });
</script>

<template>
    <div class="mx-auto grid max-w-5xl gap-0 px-4 py-12 md:py-16 lg:grid-cols-[1fr_1.1fr]">
        <aside class="relative hidden overflow-hidden rounded-l-3xl bg-gradient-to-br from-lapis-800 via-brand-800 to-brand-600 p-10 text-white lg:block">
            <div class="bg-girih absolute inset-0 opacity-[0.12]"></div>
            <div class="relative flex h-full flex-col">
                <AppLogo light size="h-12 w-12" />
                <h2 class="font-display mt-12 text-3xl leading-tight font-bold">Har bir mehnatkashga munosib ish, har bir korxonaga ishonchli xodim</h2>
                <ul class="mt-8 mb-10 space-y-3 text-sm text-brand-50">
                    <li class="flex gap-3"><span class="text-gold-300">✦</span> Bir bosishda ariza topshirish</li>
                    <li class="flex gap-3"><span class="text-gold-300">✦</span> Arizangiz holatini kuzatib borish</li>
                    <li class="flex gap-3"><span class="text-gold-300">✦</span> Tasdiqlangan kompaniyalar</li>
                </ul>
                <div class="atlas-strip mt-auto h-2 rounded-full"></div>
            </div>
        </aside>
        <div class="card p-8 sm:p-10 lg:rounded-l-none">
            <div class="mb-6 lg:hidden"><AppLogo /></div>
            <h1 class="font-display text-2xl font-bold text-lapis-900">{{ title }}</h1>
            <p v-if="subtitle" class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>
            <slot name="sub" />
            <slot />
        </div>
    </div>
</template>
