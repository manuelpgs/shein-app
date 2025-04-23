/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import OrderList from './components/OrderList.vue';
import ClientList from './components/ClientList.vue';
import OrderForm from './components/OrderForm.vue';
import ClientForm from './components/ClientForm.vue';
import TwoFactorAuth from './components/TwoFactorAuth.vue';
import axios from 'axios';
import '../css/app.css'; // Importa tus estilos CSS globales
import 'bootstrap/dist/css/bootstrap.css'; // Importa Bootstrap CSS

/*
1. Step 1: Fetch the Form Page & Save Cookies
curl -c cookies.txt https://www.system-puntoexpressdo.com/ -o form_page.html


2. Llamar al post
curl -b cookies.txt -c cookies.txt \
     -e https://www.system-puntoexpressdo.com/ \
     -d "codigo=AG-2757" \
     -d "password=40211965757" \
     -d "csrfmiddlewaretoken=DsRa49THlNaVP4l91okQdW5h36kABqp0WWF5quKhbVSTV9ClRYmDwxsDK6FWCtAt" \
     -L \
     https://www.system-puntoexpressdo.com/


3. Download the page: 

// wget -r --load-cookies cookies.txt https://www.system-puntoexpressdo.com/puntoexpress/AppClientes/Home/ -O output.html
wget -r --load-cookies cookies.txt https://www.system-puntoexpressdo.com/puntoexpress/AppClientes/MisPedidos/


*/

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const token = localStorage.getItem('authToken'); // Ejemplo de cómo obtener el token del almacenamiento local

if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    console.log('Token de autenticación establecido:', token);
}else {
    console.log('No se encontró el token de autenticación.');
}

const routes = [
    { path: '/', redirect: '/loginx' },
    { path: '/loginx', component: Login },
    { path: '/orders', component: OrderList },
    { path: '/clients', component: ClientList },
    { path: '/orders/create', component: OrderForm },
    { path: '/orders/:id/edit', component: OrderForm, props: true },
    { path: '/clients/create', component: ClientForm },
    { path: '/clients/:id/edit', component: ClientForm, props: true },
    { path: '/2fa', component: TwoFactorAuth },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});


const app = createApp({});
app.use(router);

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
