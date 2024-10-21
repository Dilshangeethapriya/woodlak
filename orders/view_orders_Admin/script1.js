document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.input-group input');
    const searchIcon = document.querySelector('.input-group img:nth-child(3)'); // Assuming the search icon is the third image
    const clearIcon = document.querySelector('.input-group img:nth-child(2)'); // Assuming the clear icon is the second image

    // Add references to filter buttons
    const pendingFileBtn = document.querySelector('.pending__file-btn');
    const deliveredFileBtn = document.querySelector('.delivered__file-btn');
    const completedFileBtn = document.querySelector('.completed__file-btn');
    const declinedFileBtn = document.querySelector('.decliend__file-btn');

    function fetchOrders(query = '', status = '') {
        console.log('Fetching orders for query:', query, 'Status:', status); // Debugging
        const xhr = new XMLHttpRequest();
        let url = 'fetch_orders.php?query=' + encodeURIComponent(query) + '&status=' + encodeURIComponent(status);

        xhr.open('GET', url, true);

        xhr.onload = function () {
            if (this.status === 200) {
                const orders = JSON.parse(this.responseText);
                console.log('Orders received:', orders); // Debugging
                const tableBody = document.getElementById('order-table-body');

                tableBody.innerHTML = ''; // Clear existing table data

                if (orders.length === 0) {
                    console.log('No orders found'); // Debugging
                    tableBody.innerHTML = '<tr><td colspan="8">No orders found.</td></tr>';
                } else {
                    orders.forEach(order => {
                        const row = document.createElement('tr');

                        row.innerHTML = `
                            <td><a href="customer_details.php?orderID=${order.orderID}">${order.orderID}</a></td>
                            <td>${order.customerID}</td>
                            <td>${order.total}</td>
                            <td>${order.orderDate}</td>
                            <td>${order.paymentMethod}</td>
                            <td id="order-status-${order.orderID}">${order.orderStatus}</td>
                            <td>
                                <select class="order-status" data-order-id="${order.orderID}">
                                    <option value="Pending" ${order.orderStatus === 'Pending' ? 'selected' : ''}>Pending</option>
                                    <option value="Delivered" ${order.orderStatus === 'Delivered' ? 'selected' : ''}>Delivered</option>
                                    <option value="Completed" ${order.orderStatus === 'Completed' ? 'selected' : ''}>Completed</option>
                                    <option value="Declined" ${order.orderStatus === 'Declined' ? 'selected' : ''}>Declined</option>
                                </select>
                            </td>
                        `;

                        tableBody.appendChild(row);
                    });

                    // Add event listeners for status change
                    document.querySelectorAll('.order-status').forEach(select => {
                        select.addEventListener('change', function () {
                            const orderID = this.getAttribute('data-order-id');
                            const newStatus = this.value;

                            // Send AJAX request to update the status
                            const xhrUpdate = new XMLHttpRequest();
                            xhrUpdate.open('POST', 'update_order_status1.php', true);
                            xhrUpdate.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhrUpdate.onload = function () {
                                if (this.status === 200) {
                                    console.log(`Server response: ${this.responseText}`);
                                    if (this.responseText.includes('Order status updated successfully')) {
                                        // Update the "Updated Status" column without refreshing the page
                                        const statusCell = document.getElementById(`order-status-${orderID}`);
                                        statusCell.textContent = newStatus;
                                    }
                                } else {
                                    console.error('Failed to update status. Server responded with:', this.status);
                                }
                            };
                            xhrUpdate.send(`orderID=${orderID}&status=${newStatus}`);
                        });
                    });
                }
            } else {
                console.error('Failed to fetch orders. Server responded with:', this.status);
            }
        };
        xhr.send();
    }

    // Event listener for search icon click
    searchIcon.addEventListener('click', function () {
        const query = searchInput.value.trim();
        console.log('Search icon clicked, Query:', query); // Debugging
        fetchOrders(query); // Fetch orders filtered by the query
    });

    // Event listener for clear icon click
    clearIcon.addEventListener('click', function () {
        searchInput.value = ''; // Clear the input field
        console.log('Clear icon clicked'); // Debugging
        fetchOrders(); // Fetch all orders again
    });

    // Add event listeners for Pending, Completed, and Declined buttons
    pendingFileBtn.addEventListener('click', function () {
        console.log('Pending filter clicked'); // Debugging
        fetchOrders('', 'Pending'); // Fetch only Pending orders
    });

    deliveredFileBtn.addEventListener('click', function () {
        console.log('Delivered filter clicked'); // Debugging
        fetchOrders('', 'Delivered'); // Fetch only Delivered orders
    });
    
    completedFileBtn.addEventListener('click', function () {
        console.log('Completed filter clicked'); // Debugging
        fetchOrders('', 'Completed'); // Fetch only Completed orders
    });

    declinedFileBtn.addEventListener('click', function () {
        console.log('Declined filter clicked'); // Debugging
        fetchOrders('', 'Declined'); // Fetch only Declined orders
    });

    // Initial fetch to display all orders when the page loads
    fetchOrders();  // Fetch all orders by default
});
