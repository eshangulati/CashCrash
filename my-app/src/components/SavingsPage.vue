<template>
    <div class="savings-container">
      <header>
        <h1>Savings Goals</h1>
      </header>
      <div class="savings-circles">
        <div v-for="goal in savingsGoals" :key="goal.id" class="savings-circle" :class="{'alert': goal.goal - getSpent(goal.category) < 0}">
          <p>{{ goal.category }}</p>
          <p>${{ goal.goal - getSpent(goal.category) }} left of ${{ goal.goal }}</p>
          <button @click="setGoal(goal)">Set Goal</button>
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
        budgets: []
      };
    },
    created() {
      this.fetchSavingsGoals();
      this.fetchBudgets();
    },
    methods: {
      fetchSavingsGoals() {
        axios.get('http://localhost/savings.php', { params: { user_id: this.user_id }})
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
    }
    }
  };
  </script>
  
  <style scoped>
  .savings-container {
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .savings-circles {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
  }
  .savings-circle {
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
    margin: 10px;
    transition: background-color 0.3s;
  }
  .savings-circle.alert {
    background-color: #ff6f61;
  }
  </style>
  