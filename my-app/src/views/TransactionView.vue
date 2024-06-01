<template>
    <div class="app">
      <TransactionPage/>
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
import TransactionPage from '@/components/TransactionPage.vue'
  
  export default {
    name: 'TransactionView',
    components: {
      TransactionPage
    }
  }
  </script>
  