<script setup>
import { statusColors } from '../format';

defineProps({ status: Object });
</script>

<template>
    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset" :class="statusColors[status?.value]">
        {{ status?.label }}
    </span>
</template>
