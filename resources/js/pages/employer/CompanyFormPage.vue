<script setup>
import { onMounted, ref } from 'vue';
import api, { validationErrors } from '../../api';
import { useAuthStore } from '../../stores/auth';
import { useMetaStore } from '../../stores/meta';
import { toast } from '../../toast';

const auth = useAuthStore();
const meta = useMetaStore();
const errors = ref({});
const form = ref({ name: '', industry: '', city: '', website: '', email: '', phone: '', employees_count: '', description: '', logo: '' });

onMounted(async () => {
    meta.load();
    const c = (await api.get('/me/company')).data.data;
    Object.keys(form.value).forEach((k) => (form.value[k] = c[k] ?? ''));
});

async function save() {
    errors.value = {};
    const payload = Object.fromEntries(Object.entries(form.value).map(([k, v]) => [k, v === '' ? null : v]));
    try {
        const { data } = await api.put('/me/company', payload);
        auth.user.company = data.data;
        toast('Kompaniya profili saqlandi');
    } catch (e) {
        errors.value = validationErrors(e);
    }
}
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900">Kompaniya profili</h1>
        <form class="card mt-6 space-y-4 p-6 md:p-8" @submit.prevent="save">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2"><label class="label">Nomi *</label><input v-model="form.name" class="input" required /></div>
                <div><label class="label">Soha</label><input v-model="form.industry" class="input" placeholder="IT, Savdo..." /></div>
                <div>
                    <label class="label">Shahar</label>
                    <select v-model="form.city" class="input">
                        <option value="">Tanlang</option>
                        <option v-for="c in meta.cities" :key="c">{{ c }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Veb-sayt</label><input v-model="form.website" class="input" placeholder="https://" />
                    <p v-if="errors.website" class="err">{{ errors.website }}</p>
                </div>
                <div><label class="label">Email</label><input v-model="form.email" type="email" class="input" /></div>
                <div>
                    <label class="label">Telefon</label><input v-model="form.phone" class="input" />
                    <p v-if="errors.phone" class="err">{{ errors.phone }}</p>
                </div>
                <div><label class="label">Xodimlar soni</label><input v-model.number="form.employees_count" type="number" min="1" class="input" /></div>
                <div class="sm:col-span-2">
                    <label class="label">Logo (rasm havolasi)</label><input v-model="form.logo" class="input" placeholder="https://.../logo.png" />
                    <p v-if="errors.logo" class="err">{{ errors.logo }}</p>
                </div>
            </div>
            <div><label class="label">Kompaniya haqida</label><textarea v-model="form.description" rows="5" class="input"></textarea></div>
            <button class="btn-primary">Saqlash</button>
        </form>
    </div>
</template>

<style scoped>
.err {
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: rgb(225 29 72);
}
</style>
