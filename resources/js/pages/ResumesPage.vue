<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import api from '../api';
import { useMetaStore } from '../stores/meta';
import ResumeCard from '../components/ResumeCard.vue';
import Pagination from '../components/Pagination.vue';
import Spinner from '../components/Spinner.vue';
import EmptyState from '../components/EmptyState.vue';
import SearchInput from '../components/SearchInput.vue';

const meta = useMetaStore();
const filters = reactive({ q: '', city: '', category: '', experience: '' });
const resumes = ref([]);
const pagination = ref(null);
const loading = ref(true);

async function load(page = 1) {
    loading.value = true;
    const params = Object.fromEntries(Object.entries(filters).filter(([, v]) => v));
    const { data } = await api.get('/resumes', { params: { ...params, page } });
    resumes.value = data.data;
    pagination.value = data.meta;
    loading.value = false;
}

let timer;
watch(filters, () => {
    clearTimeout(timer);
    timer = setTimeout(() => load(1), 150);
});
onMounted(() => {
    meta.load();
    load();
});
</script>

<template>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <h1 class="font-display text-3xl font-bold text-lapis-900">Rezyumelar</h1>
        <p class="mt-1 text-slate-500">{{ pagination?.total ?? 0 }} ta nomzod</p>
        <div class="card mt-6 grid gap-3 p-4 md:grid-cols-4">
            <SearchInput v-model="filters.q" placeholder="Lavozim yoki ko'nikma" />
            <select v-model="filters.city" class="input">
                <option value="">Barcha shaharlar</option>
                <option v-for="c in meta.cities" :key="c" :value="c">{{ c }}</option>
            </select>
            <select v-model="filters.category" class="input">
                <option value="">Barcha kategoriyalar</option>
                <option v-for="c in meta.categories" :key="c.id" :value="c.slug">{{ c.name }}</option>
            </select>
            <select v-model="filters.experience" class="input">
                <option value="">Har qanday tajriba</option>
                <option v-for="o in meta.experiences" :key="o.value" :value="o.value">{{ o.label }}</option>
            </select>
        </div>
        <Spinner v-if="loading" />
        <EmptyState v-else-if="!resumes.length" class="mt-6" title="Rezyume topilmadi" />
        <template v-else>
            <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <ResumeCard v-for="r in resumes" :key="r.id" :resume="r" />
            </div>
            <Pagination :meta="pagination" @change="load" />
        </template>
    </div>
</template>
