document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.input-group input');
    const searchIcon = document.querySelector('.input-group img:nth-child(3)');
    const clearIcon = document.querySelector('.input-group img:nth-child(2)');
    const allListBtn = document.querySelector('.all__file-btn'); // Reference to the All List button

    // Add references to filter buttons
    const pendingFileBtn = document.querySelector('.pending__file-btn');
    const deliveredFileBtn = document.querySelector('.delivered__file-btn');
    const completedFileBtn = document.querySelector('.completed__file-btn');
    const declinedFileBtn = document.querySelector('.decliend__file-btn');

    function fetchOrders(query = '', status = '', allList = false) {
        console.log('Fetching orders for query:', query, 'Status:', status, 'All List:', allList); // Debugging
        const xhr = new XMLHttpRequest();
        let url = 'fetch_orders.php?query=' + encodeURIComponent(query) + '&status=' + encodeURIComponent(status);
       
        if (allList) {
            url += '&allList=true'; // Indicate that we want all orders
        }

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
                                    <option value="Completed" ${order.orderStatus === 'Completed' ? 'selected' : ''}>Completed</option>
                                    <option value="Declined" ${order.orderStatus === 'Declined' ? 'selected' : ''}>Declined</option>
                                </select>
                            </td>
                            <td>
                                <button class="tick-button" data-order-id="${order.orderID}">&#10004;</button>
                            </td> <!-- Tick button added -->
                        `;
                    
                        tableBody.appendChild(row);
                    });

                    document.querySelectorAll('.tick-button').forEach(button => {
                        button.addEventListener('click', function() {
                            const orderID = this.getAttribute('data-order-id');
                            updateOrderStatusToDelivered(orderID);
                        });
                    });

                    document.querySelectorAll('.order-status').forEach(select => {
                        select.addEventListener('change', function () {
                            const orderID = this.getAttribute('data-order-id');
                            const newStatus = this.value;

                            const xhrUpdate = new XMLHttpRequest();
                            xhrUpdate.open('POST', 'update_order_status.php', true);
                            xhrUpdate.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhrUpdate.onload = function () {
                                if (this.status === 200) {
                                    console.log(`Server response: ${this.responseText}`);
                                    if (this.responseText.includes('Order status updated successfully')) {
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

    function updateOrderStatusToDelivered(orderID) {
        // Send AJAX request to update the status to 'Delivered'
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_order_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            if (this.status === 200) {
                console.log(`Server response: ${this.responseText}`);
                if (this.responseText.includes('Order status updated successfully')) {
                    // Update the "Updated Status" column
                    const statusCell = document.getElementById(`order-status-${orderID}`);
                    statusCell.textContent = 'Delivered';
    
                    // Now trigger the email sending
                    sendEmail(orderID);
                }
            } else {
                console.error('Failed to update status. Server responded with:', this.status);
            }
        };
        
        xhr.send(`orderID=${orderID}&status=Delivered`);
    }
     
    function sendEmail(orderID) {
        // Send AJAX request to send the email
        const xhrEmail = new XMLHttpRequest();
        xhrEmail.open('POST', 'send_email.php', true);
        xhrEmail.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhrEmail.onload = function() {
            if (this.status === 200) {
                console.log('Email sent successfully:', this.responseText);
            } else {
                console.error('Failed to send email. Server responded with:', this.status);
            }
        };
        
        xhrEmail.send(`orderID=${orderID}`);
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
        fetchOrders(); // Fetch recent orders (last 14 days)
    });

    // Event listener for the All List button
    allListBtn.addEventListener('click', function () {
        console.log('All List button clicked'); // Debugging
        fetchOrders('', '', true); // Fetch all orders from the beginning
    });

    pendingFileBtn.addEventListener('click', function () {
        console.log('Pending filter clicked');
        fetchOrders('', 'Pending');
    });

    deliveredFileBtn.addEventListener('click', function () {
        console.log('Delivered filter clicked');
        fetchOrders('', 'Delivered');
    });

    completedFileBtn.addEventListener('click', function () {
        console.log('Completed filter clicked');
        fetchOrders('', 'Completed');
    });

    declinedFileBtn.addEventListener('click', function () {
        console.log('Declined filter clicked');
        fetchOrders('', 'Declined');
    });

    // Initial fetch to display orders from the past 14 days
    fetchOrders();
});
