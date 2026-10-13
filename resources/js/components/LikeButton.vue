<script setup>
import { useRouter } from 'vue-router';
import api from '../api';
import { useAuthStore } from '../stores/auth';
import { toast } from '../toast';

const props = defineProps({ type: String, item: Object });
const auth = useAuthStore();
const router = useRouter();

async function toggle() {
    if (!auth.isLoggedIn) return router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } });
    const { data } = await api.post(`/${props.type}/${props.item.id}/like`);
    props.item.liked = data.liked;
    props.item.likes_count = data.likes_count;
    toast(data.liked ? 'Saqlanganlarga qo\'shildi' : 'Saqlanganlardan olib tashlandi');
}
</script>

<template>
    <button
        class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border transition"
        :class="item.liked ? 'border-rose-200 bg-rose-50 text-rose-600' : 'border-slate-200 text-slate-400 hover:text-rose-500'"
        :title="item.liked ? 'Saqlangan' : 'Saqlash'"
        @click.prevent="toggle"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" :fill="item.liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
            <path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 00-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 000-7.8z" />
        </svg>
    </button>
</template>
