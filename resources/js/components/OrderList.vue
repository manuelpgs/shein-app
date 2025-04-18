<template>
    <div>
        <h1>Orders</h1>
        <router-link to="/orders/create">Create Order</router-link>
        <table>
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
            <td>{{ order.status }}</td>
            <td>
                <router-link :to="`/orders/${order.id}/edit`">Edit</router-link>
                <button @click="processPayment(order.id)" v-if="order.status === 'pending'">Process Payment</button>
                <button @click="addTrackingNumber(order.id)" v-if="order.status === 'paid'">Add Tracking</button>
                <button @click="markForPickup(order.id)" v-if="order.status === 'shipped'">Ready for pickup</button>
            </td>
            </tr>
        </tbody>
        </table>
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