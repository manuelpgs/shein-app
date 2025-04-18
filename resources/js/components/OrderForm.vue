<template>
    <div>
        <h1>{{ order.id ? 'Edit Order' : 'Create Order' }}</h1>
        <form @submit.prevent="saveOrder">
            <label>Client:</label>
            <select v-model="order.client_id">
            <option v-for="client in clients" :value="client.id" :key="client.id">
                {{ client.name }}
            </option>
            </select>
            <label>Shein Order #:</label>
            <input type="text" v-model="order.shein_order_number" />
            <label>Amount:</label>
            <input type="number" v-model="order.amount" />
            <label>Status:</label>
            <select v-model="order.status">
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="shipped">Shipped</option>
            <option value="ready_for_pickup">Ready for Pickup</option>
            </select>
            <label>Tracking Number:</label>
            <input type="text" v-model="order.tracking_number" />
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