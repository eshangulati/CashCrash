<template>
    <div class="dashboard-container">
      <header class="dashboard-header">
        <h1>DashBoard</h1>
        <nav class="nav-buttons">
          <button>Budget</button>
          <button>Transaction</button>
          <button>Savings</button>
          <button>Reports</button>
        </nav>
      </header>
      <main class="dashboard-main">
        <section class="balance-overview">
          <h2>Balance Overview</h2>
          <div class="balance-amount">$1489.45</div>
        </section>
        <section class="transaction-history">
          <h2>Transaction History</h2>
          <ul>
            <li>Date: April 12, 2024, Category: Dining, Merchant: Chipotle, Amount: $124.27</li>
            <li>Date: April 12, 2024, Category: Clothing, Merchant: Macy's, Amount: $6.38</li>
            <li>Date: April 13, 2024, Category: Dining, Merchant: Chipotle, Amount: $9.50</li>
            <li>Date: April 16, 2024, Category: Dining, Merchant: Burger King, Amount: $107.33</li>
            <li>Date: April 20, 2024, Category: Electronics, Merchant: Apple Store, Amount: $82.97</li>
            <li>Date: April 25, 2024, Category: Healthcare, Merchant: Walgreens, Amount: $14.10</li>
            <li>Date: April 26, 2024, Category: Entertainment, Merchant: AMC Theaters, Amount: $194.88</li>
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
  export default {
    name: 'DashboardView'
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
  </style>
  