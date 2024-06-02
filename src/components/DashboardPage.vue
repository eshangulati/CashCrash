<template>
  <div class="dashboard-container">
    <button @click="logout" class="nav-button logout-button">Logout</button>
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
          <li v-for="transaction in lastThreeTransactions" :key="transaction.id">
            Date: {{ transaction.date }}, Category: {{ transaction.category }}, Merchant: {{ transaction.merchant }}, Amount: ${{ transaction.amount }}
          </li>
        </ul>
      </section>
      <section class="upcoming-transaction">
        <h2>Savings</h2>
        <div>
          <div v-for="goal in savingsGoals" :key="goal.id" class="savings-goal">
            <div class="progress-bar-container" :class="{'alert': getBudgetInfo(goal.category).remaining < 0}">
            <div class="progress-bar" :style="{ width: progressBarWidth(goal) }"></div>
            </div>
                <p>{{ goal.category }}</p>
                <p>${{ getBudgetInfo(goal.category).remaining }} saved of ${{ goal.goal }}</p>
            </div>
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
      savingsGoals: [],
      budgets: [],
      totalAllowance: 0
    };
  },
  mounted() {
    this.fetchTransactions();
    this.fetchBudgets();
    this.fetchSavingsGoals();
  },
  methods: {
    fetchTransactions() {
      axios.get('https://mercury.swin.edu.au/cos30043/s103491209/transactions.php', {
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
      axios.get('https://mercury.swin.edu.au/cos30043/s103491209/budget.php', {
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
    fetchSavingsGoals() {
        axios.get('https://mercury.swin.edu.au/cos30043/s103491209/savings.php', { params: { user_id: this.user_id }})
          .then(response => {
            this.savingsGoals = response.data;
          })
          .catch(error => {
            console.error('Error fetching savings goals:', error);
          });
    },
    getBudgetInfo(category) {
        const budget = this.budgets.find(b => b.category === category);
        return budget ? { 
        allowance: budget.allowance, 
        amountSpent: budget.amountSpent,
        remaining: budget.allowance - budget.amountSpent} : { allowance: 0, amountSpent: 0, remaining: 0 };
    },
    getSpent(category) {
      const budget = this.budgets.find(b => b.category === category);
      return budget ? budget.amountSpent : 0;
    },
    progressBarWidth(goal) {
      const budgetInfo = this.getBudgetInfo(goal.category);
      let percentage = (budgetInfo.remaining / goal.goal) * 100;
      percentage = Math.max(0, Math.min(percentage, 100)); // Clamp percentage to 0-100%
      return `${percentage}%`;
    },
    calculateTotalAllowance(){
      this.totalAllowance = this.budgets.reduce((sum, budget) => sum + parseFloat(budget.allowance), 0);
    },
    logout() {
      localStorage.removeItem('user_id'); // Clear user session
      this.$router.push('/').then(() => {
      history.replaceState(null, null, '/'); // Replace the history state
      });
  }
  },
  computed: {
    lastThreeTransactions(){
      return this.transactions.slice(-3);
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
  flex-wrap: wrap;
}

.logout-button {
  position: absolute;
  top: 10px; /* Adjust based on desired spacing */
  right: 10px; /* Adjust based on desired spacing */
  background-color: #ff4444; /* Red color for logout to indicate action */
}

.nav-buttons button {
  margin: 5px;
  flex-grow: 1;
}

.dashboard-main {
  display: flex;
  flex-direction: column;
}
.nav-button.logout-button {
  background-color: #ff4444; /* Red color for logout to indicate action */
}

.nav-button.logout-button:hover {
  background-color: #cc0000; /* Darker red on hover */
}

.balance-overview,
.transaction-history {
  margin: 10px 0;
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

.progress-bar-container {
  width: 100%;
  height: 20px;
  background-color: #ddd;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
}

.progress-bar {
  height: 100%;
  background-color: #4dd0e1;
  border-radius: 10px;
  transition: width 0.5s ease;
}

.savings-goal .alert .progress-bar {
  background-color: #ff6f61;
}

/* Media Queries for smaller devices */
@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
  }

  .nav-buttons button {
    width: 100%; /* Full width for better accessibility */
    margin: 5px 0; /* Spacing adjustment */
  }

  .dashboard-main {
    flex-direction: column;
  }

  .nav-buttons button {
    width: 100%;
  }

  .balance-overview,
  .transaction-history,
  .upcoming-transaction {
    flex: 1 1 100%; /* Full width on small screens */
  }

  .balance-amount {
    font-size: 1.5em; /* Smaller font size for balance */
  }
}

@media (max-width: 480px) {
  .nav-button {
    padding: 8px 10px;
    font-size: 14px;
  }
}
</style>
