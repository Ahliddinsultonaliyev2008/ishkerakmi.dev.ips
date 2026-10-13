import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './stores/auth';

const routes = [
    { path: '/', name: 'home', component: () => import('./pages/HomePage.vue') },
    { path: '/vacancies', name: 'vacancies', component: () => import('./pages/VacanciesPage.vue') },
    { path: '/vacancies/:id', name: 'vacancy', component: () => import('./pages/VacancyPage.vue'), props: true },
    { path: '/companies', name: 'companies', component: () => import('./pages/CompaniesPage.vue') },
    { path: '/companies/:id', name: 'company', component: () => import('./pages/CompanyPage.vue'), props: true },
    { path: '/resumes', name: 'resumes', component: () => import('./pages/ResumesPage.vue'), meta: { auth: true } },
    { path: '/resumes/:id', name: 'resume', component: () => import('./pages/ResumePage.vue'), props: true, meta: { auth: true } },
    { path: '/login', name: 'login', component: () => import('./pages/LoginPage.vue'), meta: { guest: true } },
    { path: '/register', name: 'register', component: () => import('./pages/RegisterPage.vue'), meta: { guest: true } },
    { path: '/profile', name: 'profile', component: () => import('./pages/ProfilePage.vue'), meta: { auth: true } },
    { path: '/saved', name: 'saved', component: () => import('./pages/SavedPage.vue'), meta: { auth: true } },

    { path: '/my/resumes', name: 'my-resumes', component: () => import('./pages/seeker/MyResumesPage.vue'), meta: { role: 'seeker' } },
    { path: '/my/resumes/new', name: 'resume-create', component: () => import('./pages/seeker/ResumeFormPage.vue'), meta: { role: 'seeker' } },
    { path: '/my/resumes/:id/edit', name: 'resume-edit', component: () => import('./pages/seeker/ResumeFormPage.vue'), props: true, meta: { role: 'seeker' } },
    { path: '/my/applications', name: 'my-applications', component: () => import('./pages/seeker/MyApplicationsPage.vue'), meta: { role: 'seeker' } },

    { path: '/employer', name: 'employer', component: () => import('./pages/employer/DashboardPage.vue'), meta: { role: 'employer' } },
    { path: '/employer/company', name: 'employer-company', component: () => import('./pages/employer/CompanyFormPage.vue'), meta: { role: 'employer' } },
    { path: '/employer/vacancies/new', name: 'vacancy-create', component: () => import('./pages/employer/VacancyFormPage.vue'), meta: { role: 'employer' } },
    { path: '/employer/vacancies/:id/edit', name: 'vacancy-edit', component: () => import('./pages/employer/VacancyFormPage.vue'), props: true, meta: { role: 'employer' } },
    { path: '/employer/applications', name: 'employer-applications', component: () => import('./pages/employer/ApplicationsPage.vue'), meta: { role: 'employer' } },

    { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('./pages/NotFoundPage.vue') },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior: (to, from, saved) => saved || (to.path === from.path ? false : { top: 0 }),
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();
    await auth.init();

    if ((to.meta.auth || to.meta.role) && !auth.isLoggedIn) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }
    if (to.meta.role && auth.user?.role?.value !== to.meta.role) {
        return { name: 'home' };
    }
    if (to.meta.guest && auth.isLoggedIn) {
        return { name: auth.isEmployer ? 'employer' : 'vacancies' };
    }
});

export default router;
