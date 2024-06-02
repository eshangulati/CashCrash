<template>
    <div class="reports-container">
        <button @click="logout" class="nav-button logout-button">Logout</button>
        <header class="reports-header">
            <h1>Reports</h1>
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
            <router-link :to="`/savings/${user_id}`">
                <button class="nav-button">Savings</button>
            </router-link>
            </nav>
        </header>
    </div>
    <button @click="downloadReport" class="nav-button">Download Report</button>
    <div v-if="showRating">
            <div class="rating">
                <span v-for="star in [5, 4, 3, 2, 1]" :key="star"
                      @click="setRating(star)"
                      :class="{'star': true, 'selected': (rating >= star)}">
                    ★
                </span>
            </div>
            <button @click="submitRating" class="submit-button">Submit Rating</button>
    </div>
  </template>
  
  <script>
  import axios from 'axios'; // Ensure Axios is imported
  
  export default {
    name: 'ReportsView',
    data() {
        return {
            user_id: localStorage.getItem('user_id'),
            showRating: false,
            rating: 0
        }
    },
    methods: {
        downloadReport() {
    const url = `https://localhost/report.php?user_id=${this.user_id}`;
    axios({
        url: url,
        method: 'GET',
        responseType: 'blob', // Important
    })
    .then((response) => {
        // Create a new Blob object using the response data of the file
        const fileURL = window.URL.createObjectURL(new Blob([response.data]));
        const fileLink = document.createElement('a');
        fileLink.href = fileURL;
        fileLink.setAttribute('download', 'financial_report.pdf');
        document.body.appendChild(fileLink);
        
        fileLink.click();
        fileLink.remove(); // Clean up after download
        this.showRating = true;
    })
    .catch(error => {
        console.error("Failed to download the file:", error);
    });
    },
    setRating(star) {
            this.rating = star;
    },
    submitRating() {
        alert(`Thank you for your review! You rated this ${this.rating} out of 5 stars.`);
        this.showRating = false; // Hide the rating UI
    }
    }
  }
  </script>

<style scoped>
.reports-container {
    padding: 20px;
}  
.reports-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap; /* Allows items to wrap on smaller screens */
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


.nav-button {
    margin: 5px;
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    background-color: #4dd0e1;
    color: white;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.rating {
    margin-top: 20px;
    font-size: 25px;
    text-align: center; /* Center align stars */
}

.star {
    cursor: pointer;
    color: #ddd;
}

.star.selected {
    color: gold;
}

.submit-button {
    display: block; /* Make the button take the full width */
    width: 100%;
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Media Queries */
@media (max-width: 768px) {
    .reports-header {
        flex-direction: column;
        align-items: stretch;
    }

    .nav-buttons {
        width: 100%;
        margin-top: 10px;
    }

    .nav-buttons button{
        width: 100%;
        margin: 5px 0; /* Adjust margin for vertical stacking */
    }

    .rating {
        font-size: 20px; /* Smaller stars for smaller screens */
    }
}

@media (max-width: 480px) {
    .reports-header {
        padding: 10px;
    }

    .nav-button {
        font-size: 14px;
        padding: 8px 10px;
    }

    .submit-button {
        font-size: 14px;
        padding: 8px 10px;
    }
}
</style>

  