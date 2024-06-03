<template>
  <div class="register-container">
    <div class="register-box">
      <h1>CashCrafter</h1>
      <form @submit.prevent="handleRegister">
    <div class="input-container">
      <label for="email">Email ID</label>
      <input type="email" id="email" v-model="email" placeholder="Email ID" :class="{'input-error': !validEmail && formSubmitted}">
      <div v-if="!validEmail && formSubmitted" class="error-message">Please enter a valid email address.</div>
    </div>
    <div class="input-container">
      <label for="username">Username</label>
      <input type="text" id="username" v-model="username" placeholder="Username" :class="{'input-error': !validUsername && formSubmitted}">
      <div v-if="!validUsername && formSubmitted" class="error-message">Username must be at least 3 characters.</div>
    </div>
    <div class="input-container">
      <label for="password">Password</label>
      <input type="password" id="password" v-model="password" placeholder="Password" :class="{'input-error': !validPassword && formSubmitted}">
      <div v-if="!validPassword && formSubmitted" class="error-message">Password must be at least 6 characters.</div>
    </div>
    <div class="input-container">
      <label for="confirmPassword">Confirm Password</label>
      <input type="password" id="confirmPassword" v-model="confirmPassword" placeholder="Confirm Password" :class="{'input-error': !passwordsMatch && formSubmitted}">
      <div v-if="!passwordsMatch && formSubmitted" class="error-message">Passwords do not match.</div>
    </div>
    <button type="submit" class="register-button">Register</button>
  </form>
      <div class="additional-options">
        <router-link to="/">Back to Login</router-link>
      </div>
    </div>
  </div>
</template>


<script>
import axios from 'axios';

export default {
  name: 'RegisterView',
  data() {
    return {
      email: '',
      username: '',
      password: '',
      confirmPassword: '',
      formSubmitted: false
    };
  },
  computed: {
    validEmail() {
      return /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}]))$/;
    },
    validUsername() {
      return this.username.length >= 3;
    },
    validPassword() {
      return this.password.length >= 6;
    },
    passwordsMatch() {
      return this.password === this.confirmPassword;
    }
  },
  methods: {
    handleRegister() {
  this.formSubmitted = true; // Set the form as submitted

  // Check if all validations are true
  if (this.validEmail && this.validUsername && this.validPassword && this.passwordsMatch) {
    axios.post('http://localhost/api_login.php/register', {
      username: this.username,
      password: this.password,
      email: this.email
    })
    .then(response => {
      if (response.data.auth) {
        localStorage.setItem('user_id', response.data.user_id);
        this.$router.replace(`/dashboard/${response.data.user_id}`);
      } else {
        alert(response.data.message || 'Registration failed');
      }
    })
    .catch(error => {
      console.error(error);
      alert('Error registering.');
    });
  } else {
    console.log("Validation failed, not submitting form");
  }
}
  }
};
</script>
  
  <style scoped>
  .register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f0f0f0;
  }
  
  .register-box {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  
  .register-box h1 {
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
  
  .register-button {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color: #3b82f6;
    color: white;
    font-size: 16px;
    cursor: pointer;
  }
  
  .register-button:hover {
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

  .input-error {
    border: 1px solid red;
  }

  .error-message {
    color: red;
    font-size: 12px;
    margin-top: 5px;
  }
  </style>
  