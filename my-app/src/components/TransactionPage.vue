<template>
    <div class="transactions-container">
      <header class="transaction-header">
        <h1>Transactions</h1>
        <nav class="nav-buttons">
              <router-link :to="`/dashboard/${user_id}`">
                  <button class="nav-button">Dashboard</button>
              </router-link>
            <router-link :to="`/budget/${user_id}`">
              <button class="nav-button">Budget</button>
            </router-link>
            <router-link :to="`/savings/${user_id}`">
              <button class="nav-button">Savings</button>
            </router-link>
            <router-link :to="`/reports/${user_id}`">
              <button class="nav-button">Reports</button>
            </router-link>
          </nav>
      </header>
      <br>
      <button @click="showAddModal = true" class="nav-button">Add Transaction</button>
      <br>
      <table class="transactions-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Merchant</th>
            <th>Amount</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in transactions" :key="transaction.id">
            <td>{{ transaction.date }}</td>
            <td>{{ transaction.category }}</td>
            <td>{{ transaction.merchant }}</td>
            <td>{{ transaction.amount }}</td>
            <td>
              <button @click="editTransaction(transaction)">Edit</button>
              <button @click="deleteTransaction(transaction.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <div v-if="showAddModal" class="modal">
        <div class="modal-content">
          <h2>Add Transaction</h2>
          <form @submit.prevent="createTransaction">
            <input v-model="newTransaction.date" type="date" required />
            <select v-model="newTransaction.category" required>
            <option disabled value="">Select Category</option>
            <option value="Grocery">Grocery</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Shopping">Shopping</option>
            <option value="Education">Education</option>
            </select>
            <input v-model="newTransaction.merchant" type="text" placeholder="Merchant" required />
            <input v-model="newTransaction.amount" type="number" step="0.01" placeholder="Amount" required />
            <button type="submit">Add</button>
            <button type="button" @click="showAddModal = false">Cancel</button>
          </form>
        </div>
      </div>
  
      <div v-if="showEditModal" class="modal">
        <div class="modal-content">
          <h2>Edit Transaction</h2>
          <form @submit.prevent="updateTransaction">
            <input v-model="currentTransaction.date" type="date" required />
            <select v-model="currentTransaction.category" required>
            <option disabled value="">Select Category</option>
            <option value="Grocery">Grocery</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Shopping">Shopping</option>
            <option value="Education">Education</option>
            </select>
            <input v-model="currentTransaction.merchant" type="text" placeholder="Merchant" required />
            <input v-model="currentTransaction.amount" type="number" step="0.01" placeholder="Amount" required />
            <button type="submit">Update</button>
            <button type="button" @click="showEditModal = false">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import TransactionService from '@/services/TransactionService';
  
  export default {
    name: 'TransactionView',
    data() {
      return {
        transactions: [],
        showAddModal: false,
        showEditModal: false,
        newTransaction: {
          user_id: this.$route.params.user_id, // This should be dynamically set to the logged-in user's ID
          date: '',
          category: '',
          merchant: '',
          amount: ''
        },
        currentTransaction: {}
      };
    },
    created() {
        this.newTransaction.user_id = localStorage.getItem('user_id');
        this.fetchTransactions();
    },
    methods: {
        fetchTransactions() {
            const userId = localStorage.getItem('user_id');
            TransactionService.getTransactions(userId)
        .then(response => {
            console.log("Transactions fetched: ", response.data);
          this.transactions = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },
      createTransaction() {
        this.newTransaction.user_id = localStorage.getItem('user_id');
        TransactionService.createTransaction(this.newTransaction)
          .then(() => {
            this.fetchTransactions();
            this.showAddModal = false;
            this.resetNewTransaction();
          })
          .catch(error => {
            console.error(error);
          });
      },
      editTransaction(transaction) {
        this.currentTransaction = { ...transaction };
        this.showEditModal = true;
      },
      updateTransaction() {
        TransactionService.updateTransaction(this.currentTransaction)
          .then(() => {
            this.fetchTransactions();
            this.showEditModal = false;
          })
          .catch(error => {
            console.error(error);
          });
      },
      deleteTransaction(transactionId) {
        TransactionService.deleteTransaction(transactionId)
          .then(() => {
            this.fetchTransactions();
          })
          .catch(error => {
            console.error(error);
          });
      },
      resetNewTransaction() {
        this.newTransaction = {
          user_id:localStorage.getItem('user_id'), // This should be dynamically set to the logged-in user's ID
          date: '',
          category: '',
          merchant: '',
          amount: ''
        };
      }
    }
  };
  </script>
  
  <style scoped>
  .transactions-container {
    padding: 20px;
  }

  .transaction-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
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
  .nav-buttons button {
    margin: 0 5px;
  }
  .transactions-table {
  margin: 20px auto;
  border-collapse: collapse;
  width: 80%;
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.transactions-table th,
.transactions-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #4dd0e1;
}

.transactions-table th {
  background-color: #4dd0e1;
  color: white;
}

.transactions-table tr:hover {
  background-color: #f1f1f1;
}

.transactions-table td button {
  margin: 0 5px;
  padding: 5px 10px;
  border: none;
  border-radius: 5px;
  background-color: #4dd0e1;
  color: white;
  cursor: pointer;
  transition: background-color 0.3s;
}

.transactions-table td button:hover {
  background-color: #26c6da;
}
  </style>
  