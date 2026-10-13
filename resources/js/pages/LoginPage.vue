<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { errorMessage } from '../api';
import AuthShell from '../components/AuthShell.vue';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const form = ref({ phone: '+998', password: '' });
const error = ref('');
const loading = ref(false);

async function submit() {
    loading.value = true;
    error.value = '';
    try {
        await auth.login(form.value);
        router.push(route.query.redirect || (auth.isEmployer ? '/employer' : '/vacancies'));
    } catch (e) {
        error.value = errorMessage(e);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <AuthShell title="Xush kelibsiz!">
        <template #sub>
            <p class="mt-1 text-sm text-slate-500">
                Hisobingiz yo'qmi?
                <RouterLink to="/register" class="font-semibold text-brand-600 hover:underline">Ro'yxatdan o'ting</RouterLink>
            </p>
        </template>
        <form class="mt-8 space-y-4" @submit.prevent="submit">
            <div>
                <label class="label">Telefon raqam</label>
                <input v-model="form.phone" class="input" placeholder="+998 90 123 45 67" required autocomplete="tel" inputmode="tel" />
            </div>
            <div>
                <label class="label">Parol</label>
                <input v-model="form.password" type="password" class="input" placeholder="Parolingizni kiriting" required autocomplete="current-password" />
            </div>
            <p v-if="error" class="rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ error }}</p>
            <button class="btn-primary w-full py-3" :disabled="loading">{{ loading ? 'Kirilmoqda...' : 'Kirish' }}</button>
        </form>
    </AuthShell>
</template>
