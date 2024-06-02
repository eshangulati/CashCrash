import axios from 'axios';

const API_URL = 'http://localhost/transactions.php';

class TransactionService {
    getTransactions(userId) {
        return axios.get(`${API_URL}?user_id=${userId}`);
    }

    createTransaction(transaction) {
        return axios.post(API_URL, transaction);
    }

    updateTransaction(transaction) {
        return axios.put(API_URL, transaction);
    }

    deleteTransaction(transactionId) {
        return axios.delete(API_URL, { data: { id: transactionId } });
    }
}

export default new TransactionService();
