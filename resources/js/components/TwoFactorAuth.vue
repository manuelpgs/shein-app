<template>
  <div class="container mt-4">
    <h1>Two-Factor Authentication</h1>

    <div v-if="!user.two_fa_enabled" class="card mb-3">
      <div class="card-body">
        <p class="card-text">Enable 2FA for enhanced security.</p>
        <button @click="generateSecret" class="btn btn-primary">Generate Secret</button>

        <div v-if="secret" class="mt-3">
          <p class="card-text">Scan this QR code with your authenticator app:</p>
          <img :src="qrCodeImage" v-if="qrCodeImage" alt="QR Code" class="img-fluid mb-2" />
          <p class="card-text">Or enter this secret key manually: <strong class="text-monospace">{{ secret }}</strong></p>

          <div class="mb-3">
            <label for="verificationCode" class="form-label">Verification Code:</label>
            <input type="text" id="verificationCode" v-model="verificationCode" class="form-control">
          </div>
          <button @click="enable2FA" class="btn btn-success">Enable</button>
        </div>
      </div>
    </div>

    <div v-else class="card mb-3">
      <div class="card-body">
        <p class="card-text">2FA is currently enabled.</p>
        <button @click="disable2FA" class="btn btn-danger">Disable 2FA</button>
      </div>
    </div>

    <div v-if="showVerificationForm" class="card">
      <div class="card-body">
        <h5 class="card-title">Verify 2FA</h5>
        <div class="mb-3">
          <label for="verificationCode" class="form-label">Verification Code:</label>
          <input type="text" id="verificationCode" v-model="verificationCode" class="form-control">
        </div>
        <button @click="verify2FA" class="btn btn-primary">Verify</button>
      </div>
    </div>
  </div>
</template>

  <script>


  import axios from 'axios';
  import { toDataURL } from 'qrcode';

  export default {
    data() {
      return {
        user: {
          two_fa_enabled: false,
        },
        secret: null,
        qrCodeUrl: null,
        qrCodeImage : null,
        verificationCode: '',
        showVerificationForm: false,
      };
    },
    mounted() {
      this.fetchUser();
    },
    methods: {
      async fetchUser() {
        try {
          const response = await axios.get('/api/user'); // Asumiendo que tienes una ruta para obtener los datos del usuario autenticado
          this.user = response.data;
        } catch (error) {
          console.error(error);
        }
      },
      async generateSecret() {
        try {
          const response = await axios.post('/api/2fa/generate');
          this.secret = response.data.secret;
          this.qrCodeUrl = response.data.otpauth_url;

          // this.qrCodeUrl = await toDataURL(this.secret);
          // } catch (error) {
          //   console.error(error);
          // }

          toDataURL(this.qrCodeUrl)
          .then(url => {
            this.qrCodeImage = url
          })
          .catch(err => {
            console.error(err)
          })
        } catch (error) {
          console.error(error);
        }
      },
      async enable2FA() {
        try {
          const response = await axios.post('/api/2fa/enable', { secret: this.secret, code: this.verificationCode });        
          const newToken = response.data.token;

          // 🔁 Replace old token
          localStorage.setItem('auth_token', newToken);

          // ✅ Set default Authorization header
          axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;

          this.user.two_fa_enabled = true;

        } catch (error) {
          console.error('Error enabling 2FA:', error);
        }
      },
      async disable2FA() {
        try {
          await axios.post('/api/2fa/disable');
          this.user.two_fa_enabled = false;
        } catch (error) {
          console.error(error);
        }
      },
      async verify2FA() {
        try {
          await axios.post('/api/2fa/verify', { code: this.verificationCode });
          this.showVerificationForm = false;
        } catch (error) {
          console.error(error);
        }
      },
    },
  };
  </script>