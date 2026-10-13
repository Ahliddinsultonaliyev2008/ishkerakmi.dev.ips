<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { validationErrors, errorMessage } from '../api';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const form = ref({
    role: route.query.role === 'employer' ? 'employer' : 'seeker',
    name: '',
    company_name: '',
    phone: '+998',
    email: '',
    password: '',
    password_confirmation: '',
});
const errors = ref({});
const error = ref('');
const loading = ref(false);

async function submit() {
    loading.value = true;
    errors.value = {};
    error.value = '';
    try {
        await auth.register({ ...form.value, email: form.value.email || null });
        router.push(auth.isEmployer ? '/employer' : '/my/resumes/new');
    } catch (e) {
        errors.value = validationErrors(e);
        if (!Object.keys(errors.value).length) error.value = errorMessage(e);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-lg px-4 py-16">
        <div class="card p-8">
            <h1 class="text-2xl font-bold text-slate-900">Ro'yxatdan o'tish</h1>
            <p class="mt-1 text-sm text-slate-500">Hisobingiz bormi? <RouterLink to="/login" class="font-medium text-brand-600">Kirish</RouterLink></p>
            <div class="mt-6 grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1">
                <button
                    v-for="r in [{ v: 'seeker', l: '👤 Ish izlayman' }, { v: 'employer', l: '🏢 Xodim izlayman' }]"
                    :key="r.v"
                    type="button"
                    class="rounded-lg py-2 text-sm font-semibold transition"
                    :class="form.role === r.v ? 'bg-white text-brand-700 shadow' : 'text-slate-500'"
                    @click="form.role = r.v"
                >
                    {{ r.l }}
                </button>
            </div>
            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="label">Ism familiya</label>
                    <input v-model="form.name" class="input" required />
                    <p v-if="errors.name" class="error">{{ errors.name }}</p>
                </div>
                <div v-if="form.role === 'employer'">
                    <label class="label">Kompaniya nomi</label>
                    <input v-model="form.company_name" class="input" required />
                    <p v-if="errors.company_name" class="error">{{ errors.company_name }}</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Telefon raqam</label>
                        <input v-model="form.phone" class="input" required />
                        <p v-if="errors.phone" class="error">{{ errors.phone }}</p>
                    </div>
                    <div>
                        <label class="label">Email (ixtiyoriy)</label>
                        <input v-model="form.email" type="email" class="input" />
                        <p v-if="errors.email" class="error">{{ errors.email }}</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Parol</label>
                        <input v-model="form.password" type="password" class="input" required minlength="8" autocomplete="new-password" />
                        <p v-if="errors.password" class="error">{{ errors.password }}</p>
                    </div>
                    <div>
                        <label class="label">Parolni takrorlang</label>
                        <input v-model="form.password_confirmation" type="password" class="input" required autocomplete="new-password" />
                    </div>
                </div>
                <p v-if="error" class="rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ error }}</p>
                <button class="btn-primary w-full py-3" :disabled="loading">{{ loading ? 'Yuborilmoqda...' : "Ro'yxatdan o'tish" }}</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
.error {
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: rgb(225 29 72);
}
</style>
