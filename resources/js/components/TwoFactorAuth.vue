<template>
    <div>
      <h1>Two-Factor Authentication</h1>

      <div v-if="!user.two_fa_enabled">
        <p>Enable 2FA for enhanced security.</p>
        <button @click="generateSecret">Generate Secret</button>

        <div v-if="secret">
          <p>Scan this QR code with your authenticator app:</p>
          <img :src="qrCodeImage" v-if="qrCodeImage" alt="QR Code" />
          <p>Or enter this secret key manually: {{ secret }}</p>

          <label>Verification Code:</label>
          <input type="text" v-model="verificationCode" />
          <button @click="enable2FA">Enable</button>
        </div>
      </div>

      <div v-else>
        <p>2FA is currently enabled.</p>
        <button @click="disable2FA">Disable 2FA</button>
      </div>

      <div v-if="showVerificationForm">
        <label>Verification Code:</label>
        <input type="text" v-model="verificationCode" />
        <button @click="verify2FA">Verify</button>
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