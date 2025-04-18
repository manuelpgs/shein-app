<template>
    <div>
      <h1>Login</h1>
      <form @submit.prevent="submitLogin">
        <div v-if="!twoFaRequired">
          <label for="email">Email:</label>
          <input type="email" id="email" v-model="email" required>
        </div>
        <div v-if="!twoFaRequired">
          <label for="password">Password:</label>
          <input type="password" id="password" v-model="password" required>
        </div>
        <div v-if="twoFaRequired">
          <h2>Verify 2FA</h2>
          <label for="code">Verification Code:</label>
          <input type="text" id="code" v-model="twoFaCode" required>
        </div>
        <button type="submit">{{ twoFaRequired ? 'Verify Code' : 'Login' }}</button>
        <p v-if="error" style="color: red;">{{ error }}</p>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
        error: null,
        twoFaRequired: false,
        userId: null,
        twoFaCode: '',
      };
    },
    methods: {
      async submitLogin() {
        this.error = null;
        if (!this.twoFaRequired) {
          await this.loginWithCredentials();
        } else {
          await this.verifyTwoFaCode();
        }
      },
      async loginWithCredentials() {
        try {
          const response = await axios.post('/api/login', {
            email: this.email,
            password: this.password,
          });
  
          if (response.data.two_fa_required) {
            this.twoFaRequired = true;
            this.userId = response.data.user_id;
          } else if (response.data.token) {
            this.handleSuccessfulLogin(response.data.token);
          } else {
            this.error = 'Login failed';
          }
        } catch (error) {
          this.error = 'Invalid credentials';
          console.error('Login error:', error);
        }
      },
      async verifyTwoFaCode() {
        try {
          const response = await axios.post('/api/login/2fa/verify', {
            user_id: this.userId,
            code: this.twoFaCode,
          });
  
          if (response.data.token) {
            this.handleSuccessfulLogin(response.data.token);
          } else {
            this.error = 'Invalid 2FA code';
          }
        } catch (error) {
          this.error = 'Invalid 2FA code';
          console.error('2FA verification error:', error);
        }
      },
      handleSuccessfulLogin(token) {
        localStorage.setItem('authToken', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        this.$router.push('/orders');
      },
    },
  };
  </script>