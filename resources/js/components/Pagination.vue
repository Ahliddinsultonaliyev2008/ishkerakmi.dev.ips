<script setup>
const props = defineProps({ meta: Object });
const emit = defineEmits(['change']);
const go = (page) => page >= 1 && page <= props.meta.last_page && emit('change', page);
</script>

<template>
    <div v-if="meta && meta.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
        <button class="btn-ghost" :disabled="meta.current_page === 1" @click="go(meta.current_page - 1)">← Oldingi</button>
        <span class="px-3 text-sm text-slate-500">{{ meta.current_page }} / {{ meta.last_page }}</span>
        <button class="btn-ghost" :disabled="meta.current_page === meta.last_page" @click="go(meta.current_page + 1)">Keyingi →</button>
    </div>
</template>
