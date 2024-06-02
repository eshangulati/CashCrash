<template>
    <div class="app">
      <BudgetPage/>
    </div>
  </template>
  
  <script>
  // @ is an alias to /src
import Budgetpage.vue from '@/components/BudgetPage.vue'
  
  export default {
    name: 'BudgetView',
    components: {
      BudgetPage
    }
  }
  </script>
  