<script setup>
import { onMounted, ref } from 'vue';
import api, { errorMessage } from '../api';
import { useAuthStore } from '../stores/auth';
import { initials, timeAgo } from '../format';
import { toast } from '../toast';

const props = defineProps({ type: String, id: [String, Number] });
const auth = useAuthStore();
const comments = ref([]);
const body = ref('');
const sending = ref(false);

async function load() {
    const { data } = await api.get(`/${props.type}/${props.id}/comments`, { params: { per_page: 50 } });
    comments.value = data.data;
}

async function submit() {
    sending.value = true;
    try {
        const { data } = await api.post(`/${props.type}/${props.id}/comments`, { body: body.value });
        comments.value.unshift(data.data);
        body.value = '';
    } catch (e) {
        toast(errorMessage(e), 'error');
    } finally {
        sending.value = false;
    }
}

async function remove(comment) {
    await api.delete(`/comments/${comment.id}`);
    comments.value = comments.value.filter((c) => c.id !== comment.id);
}

onMounted(load);
</script>

<template>
    <section class="card p-6">
        <h2 class="text-lg font-semibold text-slate-900">Izohlar ({{ comments.length }})</h2>
        <form v-if="auth.isLoggedIn" class="mt-4 flex gap-2" @submit.prevent="submit">
            <input v-model="body" class="input" placeholder="Savol yoki izoh yozing..." required minlength="2" />
            <button class="btn-primary" :disabled="sending">Yuborish</button>
        </form>
        <p v-else class="mt-3 text-sm text-slate-500">
            Izoh qoldirish uchun <RouterLink to="/login" class="font-medium text-brand-600">tizimga kiring</RouterLink>.
        </p>
        <ul class="mt-5 space-y-4">
            <li v-for="comment in comments" :key="comment.id" class="flex gap-3">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ initials(comment.author.name) }}</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold text-slate-900">{{ comment.author.name }}</span>
                        <span class="text-xs text-slate-400">{{ timeAgo(comment.created_at) }}</span>
                        <button v-if="comment.can_edit" class="ml-auto text-xs text-rose-500 hover:underline" @click="remove(comment)">O'chirish</button>
                    </div>
                    <p class="mt-0.5 text-sm text-slate-700">{{ comment.body }}</p>
                </div>
            </li>
        </ul>
    </section>
</template>
