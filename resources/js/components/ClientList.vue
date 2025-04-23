<template>
  <div class="container mt-4">
    <h1>Clients</h1>
    <router-link to="/clients/create" class="btn btn-primary mb-3">Create Client</router-link>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td>{{ client.id }}</td>
            <td>{{ client.name }}</td>
            <td>{{ client.email }}</td>
            <td>{{ client.phone }}</td>
            <td>
              <router-link :to="`/clients/${client.id}/edit`" class="btn btn-sm btn-outline-secondary">Edit</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        clients: [],
      };
    },
    mounted() {
      this.fetchClients();
    },
    methods: {
      async fetchClients() {
        try {
          const response = await axios.get('/api/clients');
          this.clients = response.data;
        } catch (error) {
          console.error(error);
        }
      },
    },
  };
  </script>