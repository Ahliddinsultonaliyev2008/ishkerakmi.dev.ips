<script setup>
import { computed } from 'vue';
import { initials } from '../format';

const props = defineProps({ company: Object, size: { type: String, default: 'h-12 w-12 text-base' } });
const colors = ['bg-sky-100 text-sky-700', 'bg-violet-100 text-violet-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700'];
const color = computed(() => colors[(props.company?.id ?? 0) % colors.length]);
</script>

<template>
    <img v-if="company?.logo" :src="company.logo" :alt="company.name" class="shrink-0 rounded-xl object-cover" :class="size" />
    <div v-else class="grid shrink-0 place-items-center rounded-xl font-bold" :class="[size, color]">{{ initials(company?.name) }}</div>
</template>
