<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h1>{{ twoFaRequired ? 'Verify 2FA' : 'Login' }}</h1>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitLogin">
              <div v-if="!twoFaRequired" class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" v-model="email" class="form-control" required>
              </div>
              <div v-if="!twoFaRequired" class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" v-model="password" class="form-control" required>
              </div>
              <div v-if="twoFaRequired" class="mb-3">
                <label for="code" class="form-label">Verification Code:</label>
                <input type="text" id="code" v-model="twoFaCode" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">{{ twoFaRequired ? 'Verify Code' : 'Login' }}</button>
              <p v-if="error" class="mt-3 text-danger">{{ error }}</p>
            </form>
          </div>
        </div>
      </div>
    </div>
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