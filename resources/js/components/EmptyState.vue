<script setup>
defineProps({ title: String, text: String, icon: { type: String, default: '🔍' } });
</script>

<template>
    <div class="card flex flex-col items-center px-6 py-14 text-center">
        <div class="text-4xl">{{ icon }}</div>
        <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ title }}</h3>
        <p v-if="text" class="mt-1 max-w-md text-sm text-slate-500">{{ text }}</p>
        <div class="mt-5"><slot /></div>
    </div>
</template>
