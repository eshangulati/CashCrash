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
        axios.get('http://localhost/budget.php', {
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
    display: inline-block; /* Ensure the buttons are not collapsing or hiding */
    margin: 10px 5px; /* Adjust spacing around buttons */
    padding: 10px 15px; /* More padding for better clickability */
    border: none;
    border-radius: 8px; /* Smooth rounded corners */
    background-color: #17a2b8; /* A brighter, more visible color */
    color: white;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s; /* Smooth transitions on hover */
    box-shadow: 0 2px 4px rgba(0,0,0,0.15); /* Subtle shadow for 3D effect */
}

.function_button:hover, .function_button:focus {
    background-color: #138496; /* Slightly darker on hover for feedback */
    transform: translateY(-2px); /* Lift effect on hover */
    outline: none; /* Remove focus outline */
}

.function_button:active {
    transform: translateY(1px); /* Subtle press effect */
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
  height: 20px;
  background-color: #4dd0e1;
  border-radius: 5px;
  transition: width 0.3s ease;
}

</style>
  