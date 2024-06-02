<template>
    <div class="budget-container">
      <button @click="logout" class="nav-button logout-button">Logout</button>
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
      <main>
        <div>
          <div v-for="budget in budgets" :key="budget.id" class="budget-item">
            <h3>{{ budget.category }}</h3>
            <div class="progress-container" :class="{'alert': budget.amountSpent >= budget.limit}">
            <div class="progress-bar"  :style="progressStyle(budget)" >
              <span>${{ budget.amountSpent }} out of ${{ budget.allowance }}</span>
            </div>
            </div>
            <br>
            <div>
                <button @click="editBudget(budget)" class="function_button">Edit</button>
                <button @click="deletebudget(budget.id)" class="function_button">Delete</button>
            </div>
          </div>
        </div>
        <div v-if="showExceedanceModal" class="modal">
            <div class="modal-content">
                <h2>Budget Exceeded Warning</h2>
                <p>{{ exceedanceMessage }}</p>
                <button @click="showExceedanceModal = false">Close</button>
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
        showExceedanceModal: false,
        newBudget: {
          user_id: localStorage.getItem('user_id'),
          category: '',
          allowance: 0,
          amountSpent: 0
        },
        currentBudget: {},
        exceedanceMessage: ''
      };
    },
    created() {
      this.fetchBudgets();
    },
    methods: {
      fetchBudgets() {
        axios.get('https://mercury.swin.edu.au/cos30043/s103491209/budget.php', {
          params: {
            user_id: this.user_id
          }
        })
        .then(response => {
          this.budgets = response.data;
          console.log(this.budgets);
          this.checkBudgetExceedance();
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
      },
      checkBudgetExceedance() {
        this.exceedanceMessage = '';  // Reset the message each time we check
        this.budgets.forEach(budget => {
            if (parseFloat(budget.amountSpent) > parseFloat(budget.allowance)) {
            this.exceedanceMessage += `Amount spent exceeded budget for ${budget.category}. `;
            this.showExceedanceModal = true;  // Show the message or modal
      }
        });
    },
    progressStyle(budget) {
      const percentage = (budget.amountSpent / budget.allowance) * 100;
      return {
        width: `${Math.max(-100, Math.min(percentage, 100))}%`,
        backgroundColor: percentage > 100 ? '#ff6f61' : '#4dd0e1'
      };
    },
    logout() {
      localStorage.removeItem('user_id'); // Clear user session
      this.$router.push('/').then(() => {
      history.replaceState(null, null, '/'); // Replace the history state
      });
  }
    }
    
  };
  </script>
  
  <style scoped>
  .budget-container {
    padding: 20px;
  }

  .logout-button {
  position: absolute;
  top: 10px; /* Adjust based on desired spacing */
  right: 10px; /* Adjust based on desired spacing */
  background-color: #ff4444; /* Red color for logout to indicate action */
  }

.nav-button.logout-button {
  background-color: #ff4444; /* Red color for logout to indicate action */
}

.nav-button.logout-button:hover {
  background-color: #cc0000; /* Darker red on hover */
}
  
  .budget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap; /* Allow header elements to wrap on smaller screens */
  }
  
  .nav-buttons button {
    margin: 5px;
    flex-grow: 1; /* Ensure buttons expand to fill space */
  }
  
  .budget-item {
    margin: 20px;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 10px;
  }
  
  .budget-item h3 {
    margin-top: 0;
  }
  
  .progress-container {
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
    font-size: 14px;
  }
  
  .nav-button, .function_button, .add-budget-button {
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    background-color: #4dd0e1;
    color: white;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }
  
  .nav-button:hover, .function_button:hover, .add-budget-button:hover {
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
    width: 90%; /* Ensures modal content is not too wide on small devices */
  }
  
  /* Media Queries for smaller devices */
  @media (max-width: 768px) {
    .nav-buttons button {
      width: 100%; /* Full width for better accessibility on smaller screens */
      margin-top: 5px;
    }
  
    .budget-item, .modal-content {
      width: 100%; /* Full width to use available space */
      margin: 10px 0;
    }
  
    .function_button, .add-budget-button {
      width: auto; /* Allow buttons to grow with the text */
      padding: 10px 16px;
      margin-right: 8px ;
    }
  
    .function_button, .nav-button, .add-budget-button {
      font-size: 12px; /* Smaller font size for better fit */
    }

  }
  
  @media (max-width: 480px) {
    .budget-header {
      flex-direction: column;
    }
  
    .progress-container, .progress-bar {
      height: 15px; /* Smaller progress bars for less vertical space usage */
    }
  }
  </style>
  