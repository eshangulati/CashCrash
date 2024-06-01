<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <h1>DashBoard</h1>
      <nav class="nav-buttons">
        <router-link :to="`/budget/${user_id}`">
          <button class="nav-button">Budget</button>
        </router-link>
        <router-link :to="`/transaction/${user_id}`">
          <button class="nav-button">Transactions</button>
        </router-link>
        <router-link :to="`/savings/${user_id}`">
          <button class="nav-button">Savings</button>
        </router-link>
        <router-link :to="`/reports/${user_id}`">
          <button class="nav-button">Reports</button>
        </router-link>
      </nav>
    </header>
    <main class="dashboard-main">
      <section class="balance-overview">
        <h2>Balance Overview</h2>
        <div class="balance-amount">
          ${{ totalAllowance }}
        </div>
      </section>
      <section class="transaction-history">
        <h2>Transaction History</h2>
        <ul>
          <li v-for="transaction in transactions" :key="transaction.id">
            Date: {{ transaction.date }}, Category: {{ transaction.category }}, Merchant: {{ transaction.merchant }}, Amount: ${{ transaction.amount }}
          </li>
        </ul>
      </section>
      <section class="upcoming-transaction">
        <h2>Upcoming Transaction</h2>
        <div class="transaction-details">
          <p>Date: June 1, 2024, 11:18 AM</p>
          <p>Category: Entertainment</p>
          <p>Merchant: Spotify</p>
          <p>Amount: $196.73</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DashboardView',
  data() {
    return {
      user_id: localStorage.getItem('user_id'),
      transactions: [],
      budgets: [],
      totalAllowance: 0
    };
  },
  mounted() {
    this.fetchTransactions();
    this.fetchBudgets();
  },
  methods: {
    fetchTransactions() {
      axios.get('http://localhost/transactions.php', {
        params: {
          user_id: this.user_id
        }
      })
      .then(response => {
        this.transactions = response.data;
      })
      .catch(error => {
        console.error(error);
        this.$router.push('/');
      });
    },
    fetchBudgets(){
      axios.get('http://localhost/budget.php', {
        params: {
          user_id: this.user_id
        }
      })
      .then(response => {
        this.budgets = response.data;
        this.calculateTotalAllowance();
      })
      .catch(error => {
        console.error('Error fetching budgets:', error);
      })
    },
    calculateTotalAllowance(){
      this.totalAllowance = this.budgets.reduce((sum, budget) => sum + parseFloat(budget.allowance), 0);
    }
  }
};
</script>
  
  <style scoped>
  .dashboard-container {
    padding: 20px;
  }
  
  .dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .nav-buttons button {
    margin: 0 5px;
  }
  
  .dashboard-main {
    display: flex;
    flex-wrap: wrap;
  }
  
  .balance-overview,
  .transaction-history,
  .upcoming-transaction {
    flex: 1 1 45%;
    margin: 10px;
    padding: 20px;
    border-radius: 10px;
    background-color: #e0f7fa;
    text-align: center;
  }
  
  .balance-amount {
    font-size: 2em;
    color: #00796b;
  }
  
  .transaction-history ul {
    list-style: none;
    padding: 0;
  }
  
  .transaction-history li {
    margin: 5px 0;
  }
  
  .transaction-details {
    background-color: #4dd0e1;
    padding: 10px;
    border-radius: 5px;
  }
  .nav-button {
  padding: 10px 20px;
  border: none;
  border-radius: 10px; /* Rounded corners */
  background-color: #4dd0e1; /* Button background color */
  color: white;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
  }

  .nav-button:hover {
  background-color: #26c6da; /* Darker shade on hover */
  }
  </style>
  