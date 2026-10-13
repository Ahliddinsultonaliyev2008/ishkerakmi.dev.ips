<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { errorMessage } from '../api';

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

function demo(phone) {
    form.value = { phone, password: 'password' };
    submit();
}
</script>

<template>
    <div class="mx-auto max-w-md px-4 py-16">
        <div class="card p-8">
            <h1 class="text-2xl font-bold text-slate-900">Tizimga kirish</h1>
            <p class="mt-1 text-sm text-slate-500">Hisobingiz yo'qmi? <RouterLink to="/register" class="font-medium text-brand-600">Ro'yxatdan o'ting</RouterLink></p>
            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="label">Telefon raqam</label>
                    <input v-model="form.phone" class="input" placeholder="+998 90 123 45 67" required autocomplete="tel" />
                </div>
                <div>
                    <label class="label">Parol</label>
                    <input v-model="form.password" type="password" class="input" required autocomplete="current-password" />
                </div>
                <p v-if="error" class="rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ error }}</p>
                <button class="btn-primary w-full py-3" :disabled="loading">{{ loading ? 'Kirilmoqda...' : 'Kirish' }}</button>
            </form>
            <div class="mt-6 rounded-xl bg-slate-50 p-4 text-sm">
                <div class="font-semibold text-slate-700">Demo hisoblar (parol: password)</div>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button class="btn-ghost py-1.5 text-xs" @click="demo('+998901111111')">Ish izlovchi</button>
                    <button class="btn-ghost py-1.5 text-xs" @click="demo('+998902222222')">Ish beruvchi</button>
                </div>
            </div>
        </div>
    </div>
</template>
