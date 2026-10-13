<script setup>
import { ref } from 'vue';
import api, { validationErrors } from '../api';
import { useAuthStore } from '../stores/auth';
import { useMetaStore } from '../stores/meta';
import { toast } from '../toast';

const auth = useAuthStore();
const meta = useMetaStore();
meta.load();
const form = ref({ name: auth.user.name, email: auth.user.email || '', city: auth.user.city || '', current_password: '', password: '', password_confirmation: '' });
const errors = ref({});

async function save() {
    errors.value = {};
    const payload = { ...form.value, email: form.value.email || null };
    if (!payload.password) ['password', 'password_confirmation', 'current_password'].forEach((k) => delete payload[k]);
    try {
        const { data } = await api.put('/auth/profile', payload);
        auth.user = data.data;
        form.value.current_password = form.value.password = form.value.password_confirmation = '';
        toast('Profil saqlandi');
    } catch (e) {
        errors.value = validationErrors(e);
    }
}
</script>

<template>
    <div class="mx-auto max-w-2xl px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900">Sozlamalar</h1>
        <form class="card mt-6 space-y-4 p-6" @submit.prevent="save">
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="label">Ism</label><input v-model="form.name" class="input" required /></div>
                <div><label class="label">Telefon</label><input :value="auth.user.phone" class="input bg-slate-50" disabled /></div>
                <div>
                    <label class="label">Email</label><input v-model="form.email" type="email" class="input" />
                    <p v-if="errors.email" class="mt-1 text-xs text-rose-600">{{ errors.email }}</p>
                </div>
                <div>
                    <label class="label">Shahar</label>
                    <select v-model="form.city" class="input">
                        <option value="">Tanlanmagan</option>
                        <option v-for="c in meta.cities" :key="c">{{ c }}</option>
                    </select>
                </div>
            </div>
            <hr class="border-slate-100" />
            <h2 class="font-semibold text-slate-900">Parolni o'zgartirish</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="label">Joriy parol</label><input v-model="form.current_password" type="password" class="input" />
                    <p v-if="errors.current_password" class="mt-1 text-xs text-rose-600">{{ errors.current_password }}</p>
                </div>
                <div>
                    <label class="label">Yangi parol</label><input v-model="form.password" type="password" class="input" />
                    <p v-if="errors.password" class="mt-1 text-xs text-rose-600">{{ errors.password }}</p>
                </div>
                <div><label class="label">Takrorlang</label><input v-model="form.password_confirmation" type="password" class="input" /></div>
            </div>
            <button class="btn-primary">Saqlash</button>
        </form>
    </div>
</template>
