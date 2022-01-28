import {createWebHistory, createRouter} from "vue-router";
// App Pages
import CommentSectionPage from '../pages/CommentSectionPage';
import RegisterPage from '../pages/RegisterPage';
import LoginPage from '../pages/LoginPage';

export const routes = [
    { path: '/', component: CommentSectionPage },
    { path: '/register', component: RegisterPage },
    { path: '/login', component: LoginPage },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router;
