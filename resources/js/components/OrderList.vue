<template>
    <div class="container mt-4">
      <h1>Orders</h1>
      <router-link to="/orders/create" class="btn btn-primary mb-3">Create Order</router-link>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Client</th>
              <th>Shein Order #</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.client.name }}</td>
              <td>{{ order.shein_order_number }}</td>
              <td>{{ order.amount }}</td>
              <td>
                <span :class="{'badge bg-warning': order.status === 'pending',
                               'badge bg-success': order.status === 'paid',
                               'badge bg-info': order.status === 'shipped',
                               'badge bg-secondary': order.status === 'delivered',
                               'badge bg-danger': order.status === 'canceled'}">
                  {{ order.status }}
                </span>
              </td>
              <td>
                <router-link :to="`/orders/${order.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</router-link>
                <button @click="processPayment(order.id)" v-if="order.status === 'pending'" class="btn btn-sm btn-success me-2">Process Payment</button>
                <button @click="addTrackingNumber(order.id)" v-if="order.status === 'paid'" class="btn btn-sm btn-info me-2">Add Tracking</button>
                <button @click="markForPickup(order.id)" v-if="order.status === 'shipped'" class="btn btn-sm btn-primary">Ready for pickup</button>
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
            orders: [],
            trackingNumber: '',
        };
        },
        mounted() {
        this.fetchOrders();
        },
        methods: {
        async fetchOrders() {
            try {
            const response = await axios.get('/api/orders');
            this.orders = response.data;
            } catch (error) {
            console.error(error);
            }
        },
        async processPayment(orderId) {
            try {
            await axios.post(`/api/orders/${orderId}/payment`);
            this.fetchOrders();
            } catch (error) {
            console.error(error);
            }
        },
        async addTrackingNumber(orderId) {
            this.trackingNumber = prompt('Enter tracking number:');
            if (this.trackingNumber) {
            try {
                await axios.post(`/api/orders/${orderId}/tracking`, { tracking_number: this.trackingNumber });
                this.fetchOrders();
            } catch (error) {
                console.error(error);
            }
            }
        },
        async markForPickup(orderId) {
            try {
                await axios.post(`/api/orders/${orderId}/pickup`);
                this.fetchOrders();
            } catch (error) {
                console.error(error);
            }
    
        }
        },
    };
    </script>