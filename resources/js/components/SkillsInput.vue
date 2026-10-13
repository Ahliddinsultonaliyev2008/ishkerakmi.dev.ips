<script setup>
import { ref } from 'vue';

const model = defineModel({ type: Array, default: () => [] });
const text = ref('');

function add() {
    const value = text.value.trim().replace(/,$/, '');
    if (value && !model.value.includes(value)) model.value = [...model.value, value];
    text.value = '';
}

function remove(skill) {
    model.value = model.value.filter((s) => s !== skill);
}
</script>

<template>
    <div class="input flex flex-wrap items-center gap-2">
        <span v-for="s in model" :key="s" class="chip bg-brand-50 text-brand-700">
            {{ s }} <button type="button" class="ml-1 text-brand-400 hover:text-brand-700" @click="remove(s)">×</button>
        </span>
        <input
            v-model="text"
            class="min-w-32 flex-1 outline-none"
            placeholder="Yozing va Enter bosing"
            @keydown.enter.prevent="add"
            @keydown.,.prevent="add"
            @blur="add"
        />
    </div>
</template>
