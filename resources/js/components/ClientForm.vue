<template>
    <div class="container mt-4">
      <h1>{{ client.id ? 'Edit Client' : 'Create Client' }}</h1>
      <form @submit.prevent="saveClient" class="mt-3">
        <div class="mb-3">
          <label for="name" class="form-label">Name:</label>
          <input type="text" id="name" v-model="client.name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" id="email" v-model="client.email" class="form-control">
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone:</label>
          <input type="text" id="phone" v-model="client.phone" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">{{ client.id ? 'Update Client' : 'Save Client' }}</button>
        <router-link to="/clients" class="btn btn-secondary ms-2">Cancel</router-link>
      </form>
    </div>
  </template>
    
    <script>
    import axios from 'axios';
    
    export default {
        props: {
        id: {
            type: Number,
            default: null,
        },
        },
        data() {
        return {
            client: {
            name: '',
            email: '',
            phone: '',
            },
        };
        },
        mounted() {
        if (this.id) {
            this.fetchClient();
        }
        },
        methods: {
        async fetchClient() {
            try {
            const response = await axios.get(`/api/clients/${this.id}`);
            this.client = response.data;
            } catch (error) {
            console.error(error);
            }
        },
        async saveClient() {
            try {
            if (this.id) {
                await axios.put(`/api/clients/${this.id}`, this.client);
            } else {
                await axios.post('/api/clients', this.client);
            }
            this.$router.push('/clients');
            } catch (error) {
            console.error(error);
            }
        },
        },
    };
    </script>