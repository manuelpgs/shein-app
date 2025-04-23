<template>
    <div class="container mt-4">
      <h1>{{ order.id ? 'Edit Order' : 'Create Order' }}</h1>
      <form @submit.prevent="saveOrder" class="mt-3">
        <div class="mb-3">
          <label for="client_id" class="form-label">Client:</label>
          <select id="client_id" v-model="order.client_id" class="form-select" required>
            <option value="" disabled>Select Client</option>
            <option v-for="client in clients" :value="client.id" :key="client.id">
              {{ client.name }}
            </option>
          </select>
        </div>
        <div class="mb-3">
          <label for="shein_order_number" class="form-label">Shein Order #:</label>
          <input type="text" id="shein_order_number" v-model="order.shein_order_number" class="form-control">
        </div>
        <div class="mb-3">
          <label for="amount" class="form-label">Amount:</label>
          <input type="number" id="amount" v-model="order.amount" class="form-control">
        </div>
        <div class="mb-3">
          <label for="status" class="form-label">Status:</label>
          <select id="status" v-model="order.status" class="form-select">
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="shipped">Shipped</option>
            <option value="ready_for_pickup">Ready for Pickup</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="tracking_number" class="form-label">Tracking Number:</label>
          <input type="text" id="tracking_number" v-model="order.tracking_number" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">{{ order.id ? 'Update Order' : 'Save Order' }}</button>
        <router-link v-if="order.id" :to="`/orders/${order.id}`" class="btn btn-secondary ms-2">Cancel</router-link>
        <router-link v-else to="/orders" class="btn btn-secondary ms-2">Cancel</router-link>
      </form>
    </div>
  </template>
  
  <style scoped>
  /* Puedes agregar estilos específicos del componente aquí si lo deseas */
  </style>
    
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
            order: {
            client_id: null,
            shein_order_number: '',
            amount: 0,
            status: 'pending',
            tracking_number: '',
            },
            clients: [],
        };
        },
        mounted() {
        this.fetchClients();
        if (this.id) {
            this.fetchOrder();
        }
        },
        methods: {
        async fetchOrder() {
            try {
            const response = await axios.get(`/api/orders/${this.id}`);
            this.order = response.data;
            } catch (error) {
            console.error(error);
            }
        },
        async fetchClients() {
            try {
            const response = await axios.get('/api/clients');
            this.clients = response.data;
            } catch (error) {
            console.error(error);
            }
        },
        async saveOrder() {
            try {
            if (this.id) {
                await axios.put(`/api/orders/${this.id}`, this.order);
            } else {
                await axios.post('/api/orders', this.order);
            }
            this.$router.push('/orders');
            } catch (error) {
            console.error(error);
            }
        },
        },
    };
    </script>