<template>
    <div class="budget-container">
      <header class="budget-header">
        <h1>Budget</h1>
        <nav class="nav-buttons">
            <router-link :to="`/dashboard/${user_id}`">
                <button class="nav-button">Dashboard</button>
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
      <button @click="showAddModal = true" class="nav-button">Add Budget</button>
      <main class="budget-main">
        <div class="budget-list">
          <div v-for="budget in budgets" :key="budget.id" class="budget-item">
            <div class="budget-circle" :class="{'alert': budget.amountSpent >= budget.limit}">
              <p>{{ budget.category }}</p>
              <p>${{ budget.amountSpent }} out of ${{ budget.allowance }}</p>
            </div>
            <br>
            <div>
                <button @click="editBudget(budget)" class="function_button">Edit</button>
                <button @click="deletebudget(budget.id)" class="function_button">Delete</button>
            </div>
          </div>
        </div>
      </main>
  
      <div v-if="showAddModal" class="modal">
        <div class="modal-content">
          <h2>Add Budget</h2>
          <form @submit.prevent="createBudget">
            <select v-model="newBudget.category" required>
              <option disabled value="">Select Category</option>
              <option value="Grocery">Grocery</option>
              <option value="Entertainment">Entertainment</option>
              <option value="Shopping">Shopping</option>
              <option value="Education">Education</option>
            </select>
            <input v-model="newBudget.allowance" type="number" step="0.01" placeholder="Budget Allowance" required />
            <button type="submit">Add</button>
            <button type="button" @click="showAddModal = false">Cancel</button>
          </form>
        </div>
      </div>

      <div v-if="showEditModal" class="modal">
        <div class="modal-content">
            <h2>Edit Buddget</h2>
            <form @submit.prevent="updateBudget">
                <input v-model="currentBudget.allowance" type="number" step="0.01" placeholder="Budget Allowance" required />
                <button @click="updateBudget()">Update</button>
                <button type="button" @click="showEditModal = false">Cancel</button>
            </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'BudgetView',
    data() {
      return {
        user_id: localStorage.getItem('user_id'),
        budgets: [],
        showAddModal: false,
        showEditModal: false,
        newBudget: {
          user_id: localStorage.getItem('user_id'),
          category: '',
          allowance: 0,
          amountSpent: 0
        },
        currentBudget: {}
      };
    },
    created() {
      this.fetchBudgets();
    },
    methods: {
      fetchBudgets() {
        axios.get('http://localhost/budget.php', {
          params: {
            user_id: this.user_id
          }
        })
        .then(response => {
          this.budgets = response.data;
        })
        .catch(error => {
          console.error(error);
        });
      },
      createBudget() {
        axios.post('http://localhost/budget.php', this.newBudget)
        .then(() => {
          this.fetchBudgets();
          this.showAddModal = false;
          this.resetNewBudget();
        })
        .catch(error => {
          console.error(error);
        });
      },
      editBudget(budget){
        this.currentBudget = {...budget}
        this.showEditModal = true
      },
      deletebudget(budgetId) {
        return axios.delete('http://localhost/budget.php', { data: { id: budgetId } })
          .then(() => {
            this.fetchBudgets();
          })
          .catch(error => {
            console.error(error);
          });
      },
      updateTransaction() {
        return axios.put('http://localhost/budget.php', this.currentBudget)
          .then(() => {
            this.fetchBudgets();
            this.showEditModal = false;
          })
          .catch(error => {
            console.error(error);
          });
      },
      resetNewBudget() {
        this.newBudget = {
          user_id: localStorage.getItem('user_id'),
          category: '',
          limit: 0,
          amountSpent: 0
        };
      }
    }
  };
  </script>
  
  <style scoped>
  .budget-container {
    padding: 20px;
  }
  
  .budget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .nav-buttons button {
    margin: 0 5px;
  }
  
  .budget-main {
    display: flex;
    justify-content: center;
    align-items: flex-start;
  }
  
  .budget-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
  }
  
  .budget-item {
    margin: 20px;
  }
  
  .budget-circle {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background-color: #4dd0e1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 16px;
    transition: background-color 0.3s;
  }
  
  .budget-circle.alert {
    background-color: #ff6f61;
  }
  
  .add-budget-button {
    width: 150px; /* Ensure the button has a fixed width */
    height: 150px; /* Ensure the button has a fixed height */
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    background-color: #4dd0e1;
    color: white;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
    margin: 20px; /* Add some margin to separate it from the other items */
  }
  
  .add-budget-button:hover {
    background-color: #26c6da;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .modal-content {
    background: white;
    padding: 20px;
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
  .function_button{
    margin: 0 5px;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    background-color: #4dd0e1;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  </style>
  