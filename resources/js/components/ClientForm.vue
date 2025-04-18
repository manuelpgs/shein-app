<template>
    <div>
        <h1>{{ client.id ? 'Edit Client' : 'Create Client' }}</h1>
        <form @submit.prevent="saveClient">
        <label>Name:</label>
        <input type="text" v-model="client.name" />
        <label>Email:</label>
        <input type="email" v-model="client.email" />
        <label>Phone:</label>
        <input type="text" v-model="client.phone" />
        <button type="submit">Save</button>
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