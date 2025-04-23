<template>
    <div>
      <nav v-if="$route.path !== '/loginx'" class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
          <a class="navbar-brand" href="/">Shein App</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <router-link to="/orders" class="nav-link">Orders</router-link>
              </li>
              <li class="nav-item">
                <router-link to="/clients" class="nav-link">Clients</router-link>
              </li>
              <li class="nav-item">
                <router-link to="/2fa" class="nav-link">2FA</router-link>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <div class="container mt-4">
        <router-view></router-view>
      </div>
    </div>
  </template>
  
  <script>
  import { RouterLink, RouterView } from 'vue-router';
  
  export default {
    components: {
      RouterLink,
      RouterView,
    },
  };
  </script>