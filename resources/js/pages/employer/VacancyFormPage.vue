<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { validationErrors } from '../../api';
import { useMetaStore } from '../../stores/meta';
import { toast } from '../../toast';
import SkillsInput from '../../components/SkillsInput.vue';

const props = defineProps({ id: String });
const router = useRouter();
const meta = useMetaStore();
const errors = ref({});
const saving = ref(false);
const form = ref({
    title: '',
    category_id: '',
    description: '',
    requirements: '',
    benefits: '',
    city: 'Toshkent',
    address: '',
    employment_type: 'full_time',
    experience: 'none',
    salary_from: '',
    salary_to: '',
    currency: 'UZS',
    skills: [],
    status: 'active',
});

onMounted(async () => {
    meta.load();
    if (props.id) {
        const v = (await api.get(`/vacancies/${props.id}`)).data.data;
        Object.keys(form.value).forEach((k) => {
            const val = v[k];
            form.value[k] = val && typeof val === 'object' && 'value' in val ? val.value : (val ?? '');
        });
        form.value.category_id = v.category?.id ?? '';
        form.value.skills = v.skills;
    }
});

async function save() {
    saving.value = true;
    errors.value = {};
    const payload = Object.fromEntries(Object.entries(form.value).map(([k, v]) => [k, v === '' ? null : v]));
    try {
        const { data } = props.id ? await api.put(`/vacancies/${props.id}`, payload) : await api.post('/vacancies', payload);
        toast('Vakansiya saqlandi');
        router.push(`/vacancies/${data.data.id}`);
    } catch (e) {
        errors.value = validationErrors(e);
        toast(Object.values(errors.value)[0] || 'Xatolik', 'error');
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900">{{ id ? 'Vakansiyani tahrirlash' : 'Yangi vakansiya' }}</h1>
        <form class="card mt-6 space-y-5 p-6 md:p-8" @submit.prevent="save">
            <div>
                <label class="label">Lavozim nomi *</label>
                <input v-model="form.title" class="input" placeholder="Masalan: Laravel dasturchi" required />
                <p v-if="errors.title" class="err">{{ errors.title }}</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Kategoriya</label>
                    <select v-model="form.category_id" class="input">
                        <option value="">Tanlang</option>
                        <option v-for="c in meta.categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Shahar *</label>
                    <select v-model="form.city" class="input">
                        <option v-for="c in meta.cities" :key="c">{{ c }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Ish turi *</label>
                    <select v-model="form.employment_type" class="input">
                        <option v-for="o in meta.employment_types" :key="o.value" :value="o.value">{{ o.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Tajriba *</label>
                    <select v-model="form.experience" class="input">
                        <option v-for="o in meta.experiences" :key="o.value" :value="o.value">{{ o.label }}</option>
                    </select>
                </div>
                <div><label class="label">Manzil</label><input v-model="form.address" class="input" placeholder="Ko'cha, mo'ljal" /></div>
                <div>
                    <label class="label">Holati</label>
                    <select v-model="form.status" class="input">
                        <option value="active">Faol</option>
                        <option value="closed">Yopilgan</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="label">Maosh</label>
                <div class="grid grid-cols-[1fr_1fr_110px] gap-2">
                    <input v-model.number="form.salary_from" type="number" min="0" class="input" placeholder="dan" />
                    <input v-model.number="form.salary_to" type="number" min="0" class="input" placeholder="gacha" />
                    <select v-model="form.currency" class="input">
                        <option v-for="o in meta.currencies" :key="o.value" :value="o.value">{{ o.label }}</option>
                    </select>
                </div>
                <p v-if="errors.salary_to" class="err">{{ errors.salary_to }}</p>
                <p class="mt-1 text-xs text-slate-400">Bo'sh qoldirsangiz "Kelishiladi" deb ko'rsatiladi.</p>
            </div>
            <div>
                <label class="label">Tavsif *</label>
                <textarea v-model="form.description" rows="5" class="input" required></textarea>
                <p v-if="errors.description" class="err">{{ errors.description }}</p>
            </div>
            <div><label class="label">Talablar</label><textarea v-model="form.requirements" rows="4" class="input"></textarea></div>
            <div><label class="label">Sharoitlar</label><textarea v-model="form.benefits" rows="3" class="input"></textarea></div>
            <div><label class="label">Ko'nikmalar</label><SkillsInput v-model="form.skills" /></div>
            <div class="flex gap-2">
                <button class="btn-primary" :disabled="saving">{{ saving ? 'Saqlanmoqda...' : 'Saqlash' }}</button>
                <RouterLink to="/employer" class="btn-ghost">Bekor qilish</RouterLink>
            </div>
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
