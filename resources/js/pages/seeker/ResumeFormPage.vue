<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { validationErrors } from '../../api';
import { useAuthStore } from '../../stores/auth';
import { useMetaStore } from '../../stores/meta';
import { toast } from '../../toast';
import SkillsInput from '../../components/SkillsInput.vue';

const props = defineProps({ id: String });
const router = useRouter();
const auth = useAuthStore();
const meta = useMetaStore();
const errors = ref({});
const saving = ref(false);
const form = ref({
    title: '',
    category_id: '',
    full_name: auth.user.name,
    phone: auth.user.phone,
    email: auth.user.email || '',
    city: auth.user.city || 'Toshkent',
    birth_date: '',
    gender: '',
    employment_type: 'full_time',
    experience: 'none',
    salary_expected: '',
    currency: 'UZS',
    skills: [],
    about: '',
    is_public: true,
});

onMounted(async () => {
    meta.load();
    if (props.id) {
        const r = (await api.get(`/resumes/${props.id}`)).data.data;
        Object.assign(form.value, {
            ...r,
            category_id: r.category?.id ?? '',
            employment_type: r.employment_type.value,
            experience: r.experience.value,
            currency: r.currency.value,
            birth_date: r.birth_date ?? '',
            gender: r.gender ?? '',
            email: r.email ?? '',
            salary_expected: r.salary_expected ?? '',
        });
    }
});

async function save() {
    saving.value = true;
    errors.value = {};
    const payload = Object.fromEntries(Object.entries(form.value).map(([k, v]) => [k, v === '' ? null : v]));
    try {
        const { data } = props.id ? await api.put(`/resumes/${props.id}`, payload) : await api.post('/resumes', payload);
        toast('Rezyume saqlandi');
        router.push(`/resumes/${data.data.id}`);
    } catch (e) {
        errors.value = validationErrors(e);
        toast("Formada xatolar bor", 'error');
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-10">
        <h1 class="text-3xl font-bold text-slate-900">{{ id ? 'Rezyumeni tahrirlash' : 'Yangi rezyume' }}</h1>
        <form class="card mt-6 space-y-5 p-6 md:p-8" @submit.prevent="save">
            <div>
                <label class="label">Lavozim *</label>
                <input v-model="form.title" class="input" placeholder="Masalan: Frontend dasturchi" required />
                <p v-if="errors.title" class="err">{{ errors.title }}</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="label">Ism familiya *</label><input v-model="form.full_name" class="input" required /></div>
                <div>
                    <label class="label">Kategoriya</label>
                    <select v-model="form.category_id" class="input">
                        <option value="">Tanlang</option>
                        <option v-for="c in meta.categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Telefon *</label><input v-model="form.phone" class="input" required />
                    <p v-if="errors.phone" class="err">{{ errors.phone }}</p>
                </div>
                <div><label class="label">Email</label><input v-model="form.email" type="email" class="input" /></div>
                <div>
                    <label class="label">Shahar *</label>
                    <select v-model="form.city" class="input" required>
                        <option v-for="c in meta.cities" :key="c">{{ c }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">Tug'ilgan sana</label><input v-model="form.birth_date" type="date" class="input" />
                    <p v-if="errors.birth_date" class="err">{{ errors.birth_date }}</p>
                </div>
                <div>
                    <label class="label">Jins</label>
                    <select v-model="form.gender" class="input">
                        <option value="">Ko'rsatilmagan</option>
                        <option value="male">Erkak</option>
                        <option value="female">Ayol</option>
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
                <div>
                    <label class="label">Kutilayotgan maosh</label>
                    <div class="flex gap-2">
                        <input v-model.number="form.salary_expected" type="number" min="0" class="input" />
                        <select v-model="form.currency" class="input w-28">
                            <option v-for="o in meta.currencies" :key="o.value" :value="o.value">{{ o.label }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div><label class="label">Ko'nikmalar</label><SkillsInput v-model="form.skills" /></div>
            <div><label class="label">O'zim haqimda</label><textarea v-model="form.about" rows="5" class="input" placeholder="Tajribangiz, yutuqlaringiz..."></textarea></div>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input v-model="form.is_public" type="checkbox" class="h-4 w-4 rounded" /> Rezyume ish beruvchilarga ko'rinsin
            </label>
            <div class="flex gap-2">
                <button class="btn-primary" :disabled="saving">{{ saving ? 'Saqlanmoqda...' : 'Saqlash' }}</button>
                <RouterLink to="/my/resumes" class="btn-ghost">Bekor qilish</RouterLink>
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
