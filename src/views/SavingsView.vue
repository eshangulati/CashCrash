<template>
    <div class="app">
      <SavingsPage/>
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
import SavingsPage from '@/componenets/SavingsPage.vue'
  
  export default {
    name: 'SavingsView',
    components: {
      SavingsPage
    }
  }
  </script>
  