<script setup>
import { toasts } from '../toast';
</script>

<template>
    <div class="pointer-events-none fixed right-4 bottom-4 z-50 flex w-80 flex-col gap-2">
        <TransitionGroup name="fade">
            <div
                v-for="t in toasts"
                :key="t.id"
                class="pointer-events-auto rounded-xl px-4 py-3 text-sm font-medium shadow-lg"
                :class="t.type === 'error' ? 'bg-rose-600 text-white' : 'bg-slate-900 text-white'"
            >
                {{ t.message }}
            </div>
        </TransitionGroup>
    </div>
</template>
