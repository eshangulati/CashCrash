<template>
  <div class="login-container">
    <div class="login-box">
      <h1>CashCrafter</h1>
      <form @submit.prevent="handleLogin">
        <div class="input-container">
          <input type="text" v-model="username" placeholder="Username" />
        </div>
        <div class="input-container">
          <input type="password" v-model="password" placeholder="Password" />
        </div>
        <button type="submit" class="login-button">Login</button>
      </form>
      <div class="additional-options">
        <router-link to="/register">Register</router-link> /
        <a href="#">Forgot Password</a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginView',
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    handleLogin() {
      axios.post('http://localhost/api_login.php/login', {
        username: this.username,
        password: this.password
      })
      .then(response => {
        const { user_id } = response.data;
        localStorage.setItem('user_id', user_id);
        this.$router.push(`/dashboard/${user_id}`); // Redirect to dashboard page
      })
      .catch(error => {
        console.error(error);
        alert('Error logging in.');
      });
    }
  }
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f0f0f0;
}

.login-box {
  background: white;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.login-box h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #3b82f6;
}

.input-container {
  margin-bottom: 20px;
}

.input-container input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 16px;
}

.login-button {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 5px;
  background-color: #3b82f6;
  color: white;
  font-size: 16px;
  cursor: pointer;
}

.login-button:hover {
  background-color: #2563eb;
}

.additional-options {
  margin-top: 10px;
  font-size: 14px;
}

.additional-options a {
  color: #3b82f6;
  text-decoration: none;
}

.additional-options a:hover {
  text-decoration: underline;
}
</style>
