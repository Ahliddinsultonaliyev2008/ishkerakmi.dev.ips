<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Qidirish...' },
    delay: { type: Number, default: 700 },
    inputClass: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const text = ref(props.modelValue);
let timer;

watch(
    () => props.modelValue,
    (v) => {
        if (v !== text.value.trim()) text.value = v;
    },
);

function commit() {
    clearTimeout(timer);
    const value = text.value.trim();
    if (value !== props.modelValue) emit('update:modelValue', value);
}

function onInput() {
    clearTimeout(timer);
    timer = setTimeout(commit, props.delay);
}

function clear() {
    text.value = '';
    commit();
}

onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <div class="relative">
        <svg class="pointer-events-none absolute top-1/2 left-3.5 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" stroke-linecap="round" />
        </svg>
        <input
            v-model="text"
            type="search"
            class="input pr-10 pl-11"
            :class="inputClass"
            :placeholder="placeholder"
            enterkeyhint="search"
            @input="onInput"
            @keydown.enter.prevent="commit"
            @blur="commit"
        />
        <button v-if="text" type="button" class="absolute top-1/2 right-2 -translate-y-1/2 rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Tozalash" @click="clear">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round" /></svg>
        </button>
    </div>
</template>

<style scoped>
input[type='search']::-webkit-search-cancel-button {
    display: none;
}
</style>
