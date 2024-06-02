<template>
    <div class="transactions-container">
      <button @click="logout" class="nav-button logout-button">Logout</button>
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
      <div>
      <input v-model="searchQuery" placeholder="Search transactions..." @input="filterTransactions">
      <select v-model="selectedCategory" @change="filterTransactions">
        <option value="">All Categories</option>
        <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
      </select>
      </div>
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
          <tr v-for="transaction in paginatedTransactions" :key="transaction.id">
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
      <div class="pagination-controls">
        <button @click="handlePageClick(Math.max(currentPage - 1, 1))" :disabled="currentPage === 1">
        Prev Page
        </button>
        <span>Page {{ currentPage }} of {{ pageCount }}</span>
        <button @click="handlePageClick(Math.min(currentPage + 1, pageCount))" :disabled="currentPage === pageCount">
        Next Page
        </button>
      </div>
  
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
        filteredTransactions: [],
        showAddModal: false,
        showEditModal: false,
        currentPage: 1,
        pageSize: 3,
        newTransaction: {
          user_id: this.$route.params.user_id, // This should be dynamically set to the logged-in user's ID
          date: '',
          category: '',
          merchant: '',
          amount: ''
        },
        currentTransaction: {},
        searchQuery: '',
        selectedCategory: '',
        categories: ['Grocery', 'Entertainment', 'Shopping', 'Education']
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
          this.filterTransactions();
        })
        .catch(error => {
          console.error(error);
        });
    },
    filterTransactions() {
      if (!this.searchQuery && !this.selectedCategory) {
        this.filteredTransactions = this.transactions;
      } else {
        this.filteredTransactions = this.transactions.filter(transaction => {
          const matchesCategory = this.selectedCategory ? transaction.category === this.selectedCategory : true;
          const matchesSearch = transaction.merchant.toLowerCase().includes(this.searchQuery.toLowerCase());
          return matchesCategory && matchesSearch;
        });
      }
      this.currentPage = 1; // Reset to first page after filtering
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
      },
      handlePageClick(pageNumber) {
        this.currentPage = pageNumber;
      },
      logout() {
      localStorage.removeItem('user_id'); // Clear user session
      this.$router.push('/').then(() => {
      history.replaceState(null, null, '/'); // Replace the history state
      });
  }
    },
    computed: {
    paginatedTransactions() {
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      return this.filteredTransactions.slice(start, end);
    },
    pageCount() {
      return Math.ceil(this.filteredTransactions.length / this.pageSize);
    }
  },
  watch: {
    searchQuery: 'filterTransactions',
    selectedCategory: 'filterTransactions'
  }
  };
  </script>
  
  <style scoped>
  .transactions-container {
    padding: 20px;
  }

  input, select {
  padding: 8px 10px;
  margin-right: 10px; /* Adds spacing between input/select and any other elements */
  border: 2px solid #4dd0e1; /* Matching border color to theme */
  border-radius: 5px; /* Rounded corners for input and select */
  outline: none; /* Removes default focus outline */
  font-size: 16px; /* Matching font size for better readability */
}

input::placeholder {
  color: #aaa; /* Lighter text color for placeholder */
}

input:focus, select:focus {
  border-color: #26c6da; /* Darker border on focus */
}
  
  .transaction-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap; /* Allows header content to wrap on smaller screens */
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
    width: 100%; /* Adjust table width to be responsive */
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
  
  .pagination-controls {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }
  
  .pagination-controls button {
    padding: 8px 12px;
    margin: 0 10px;
    background-color: #4dd0e1;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .pagination-controls button:disabled {
    opacity: 0.5;
    cursor: default;
  }
  
  /* Media Queries for smaller devices */
  @media (max-width: 768px) {
    .transaction-header {
      flex-direction: column;
      align-items: stretch;
    }
  
    .nav-buttons {
      width: 100%; /* Full width for easier navigation on small devices */
      margin-top: 10px;
    }
  
    .nav-buttons button, .nav-button .logout-button {
      width: 100%; /* Full-width buttons for better accessibility */
      margin: 5px 0;
    }
  
    .transactions-table {
      font-size: 11px; /* Smaller font size for content */
    }
    input, select {
    width: 100%; /* Full width for input and select to fit mobile screens */
    margin-bottom: 10px; /* Adds space below inputs on small screens */
  }
  }
  
  @media (max-width: 480px) {
    .transaction-header {
      padding: 10px;
    }
  
    .nav-button {
      font-size: 14px;
      padding: 8px 10px;
    }
  }
  </style>
  