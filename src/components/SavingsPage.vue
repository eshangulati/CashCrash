<template>
    <div class="savings-container">
      <button @click="logout" class="nav-button logout-button">Logout</button>
        <header class="savings-header">
            <h1>Savings Goals</h1>
            <nav class="nav-buttons">
            <router-link :to="`/dashboard/${user_id}`">
                <button class="nav-button">Dashboard</button>
            </router-link>
            <router-link :to="`/budget/${user_id}`">
                <button class="nav-button">Budget</button>
            </router-link>
            <router-link :to="`/transaction/${user_id}`">
                <button class="nav-button">Transactions</button>
            </router-link>
            <router-link :to="`/reports/${user_id}`">
                <button class="nav-button">Reports</button>
            </router-link>
            </nav>
        </header>
        <div>
            <h2>Add New Goal</h2>
            <select v-model="newGoalCategory" >
                <option disabled value="">Select Category</option>
                <option value="Grocery">Grocery</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Shopping">Shopping</option>
                <option value="Education">Education</option>
            </select>
        <input v-model.number="newGoalAmount" type="number" placeholder="Goal Amount" min="0.01" step="0.01" />
      <button @click="addNewGoal" class="nav-button">Add Goal</button>
        </div>
        <div class="savings-circles">
            <div v-for="goal in savingsGoals" :key="goal.id" class="savings-goal">
            <div class="progress-bar-container" :class="{'alert': getBudgetInfo(goal.category).remaining < 0}">
            <div class="progress-bar" :style="progressBarWidth(goal)"></div>
            </div>
                <p>{{ goal.category }}</p>
                <p>${{ getBudgetInfo(goal.category).remaining }} saved of ${{ goal.goal }}</p>
            <button @click="setGoal(goal)" class="function_button">Set Goal</button>
            </div>
        </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name : 'SavingsView',
    data() {
      return {
        user_id: localStorage.getItem('user_id'),
        savingsGoals: [],
        budgets: [],
        newGoalCategory: '',
        newGoalAmount: 0
      };
    },
    created() {
      this.fetchSavingsGoals();
      this.fetchBudgets();
    },
    methods: {
      fetchSavingsGoals() {
        axios.get('https://mercury.swin.edu.au/cos30043/s103491209/savings.php', { params: { user_id: this.user_id }})
          .then(response => {
            this.savingsGoals = response.data;
          })
          .catch(error => {
            console.error('Error fetching savings goals:', error);
          });
      },
      fetchBudgets() {
        axios.get('http://localhost/budget.php', { params: { user_id: this.user_id }})
          .then(response => {
            this.budgets = response.data;
          })
          .catch(error => {
            console.error('Error fetching budgets:', error);
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
      setGoal(goal) {
      const newGoalValue = parseFloat(prompt(`Set new savings goal for ${goal.category}. Current goal: $${goal.goal}`, goal.goal));
      if (!isNaN(newGoalValue) && newGoalValue > 0) {
        this.updateGoal(goal, newGoalValue);
      } else {
        alert("Please enter a valid number greater than 0.");
      }
    },
    updateGoal(goal, newGoalValue) {
      axios.put('http://localhost/savings.php', {
        id: goal.id,
        goal: newGoalValue
      })
      .then(() => {
        this.fetchSavingsGoals(); // Refresh the goals after update
        alert("Savings goal updated successfully!");
      })
      .catch(error => {
        console.error('Error updating goal:', error);
        alert("Failed to update goal.");
      });
    },
    addNewGoal() {
    if (!this.newGoalCategory || this.newGoalAmount <= 0) {
      alert("Please fill all fields correctly.");
      return;
    }
    

    const goalData = {
      user_id: this.user_id,
      category: this.newGoalCategory,
      goal: this.newGoalAmount
    };
    axios.post('http://localhost/savings.php', goalData)
      .then(() => {
        this.fetchSavingsGoals();  // Refresh the list of goals
        alert("New savings goal added successfully!");
        this.newGoalCategory = '';  // Reset the input fields
        this.newGoalAmount = 0;
      })
      .catch(error => {
        console.error('Error adding new savings goal:', error);
        alert("Failed to add new savings goal.");
      });
  },
  progressBarWidth(goal) {
    const budgetInfo = this.getBudgetInfo(goal.category);
    let percentage = (budgetInfo.remaining / goal.goal) * 100;
    percentage = Math.max(-100, Math.min(percentage, 100)); // Clamp percentage to 0-100%
  
  // Determine the background color based on the percentage
    const backgroundColor = percentage < 0 ? '#ff6f61' : '#4dd0e1';

    return {
        width: `${percentage}%`,
        backgroundColor: backgroundColor
    };
}

  
}
  };
  </script>
  
  <style scoped>
  .savings-container {
      padding: 20px;
  }  
  .savings-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap; /* Allows header content to wrap on smaller screens */
  }

  .nav-buttons button {
  margin: 5px;
  flex-grow: 1;
}

.function_button {
      margin: 0 5px;
      padding: 10px 20px;
      border: none;
      border-radius: 10px;
      background-color: #4dd0e1;
      color: white;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
}
.logout-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: #ff4444; /* Red color for logout to indicate action */
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px; /* Ensure the font size is consistent */
  transition: background-color 0.3s;
}
  .nav-button.logout-button {
    background-color: #ff4444; /* Red color for logout to indicate action */
  }

  .nav-button.logout-button:hover {
    background-color: #cc0000; /* Darker red on hover */
  }


  
  .nav-button{
      margin: 0 5px;
      padding: 10px 20px;
      border: none;
      border-radius: 10px;
      background-color: #4dd0e1;
      color: white;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
  }
  
  .savings-circles {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
  }
  
  .savings-goal {
      width: 100%; /* Full width on small screens */
      margin: 10px 0;
      padding: 10px;
      background-color: #f3f3f3;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
  
  /* Media Queries for smaller devices */
  @media (max-width: 768px) {
      .savings-header {
          flex-direction: column;
          align-items: stretch;
      }
  
      .nav-buttons {
          width: 100%; /* Full width for easier navigation on small devices */
          margin-top: 10px;
      }
  
      .nav-buttons button {
          width: 100%; /* Full-width buttons for better accessibility */
          margin: 5px 0;
          font-size: 12px;
      }
  
      .savings-goal {
          padding: 8px; /* Slightly less padding to fit smaller screens better */
          font-size: 14px; /* Smaller font size for content */
      }
  }
  
  @media (max-width: 480px) {
      .savings-header {
          padding: 10px;
      }
  
      .nav-button {
          font-size: 14px;
          padding: 8px 10px;
      }
  }
  </style>
  