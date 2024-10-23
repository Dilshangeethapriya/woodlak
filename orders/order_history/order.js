document.addEventListener("DOMContentLoaded", function () {
    // Event listeners for status icons
    document.querySelector(".pending__file").addEventListener("click", function () {
        fetchOrders("Pending");
    });

    document.querySelector(".completed__file").addEventListener("click", function () {
        fetchOrders("Completed");
    });

    document.querySelector(".delivered__file").addEventListener("click", function () {
        fetchOrders("Delivered");
    });

    document.querySelector(".decliend__file").addEventListener("click", function () {
        fetchOrders("Declined");
    });

    // Fetch all orders initially
    fetchOrders('');

    // Function to fetch and display orders based on status
    function fetchOrders(status) {
        let xhr = new XMLHttpRequest();
        let url = 'fetch.php?status=' + encodeURIComponent(status);

        xhr.open('GET', url, true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                let tableBody = document.querySelector(".orders-table tbody");
                tableBody.innerHTML = xhr.responseText;
                addDeleteEventListeners(); // Add delete event listeners to each button after loading rows
                addConfirmOrderEventListeners();
            } else {
                console.error('Error fetching data:', xhr.statusText);
            }
        };

        xhr.onerror = function () {
            console.error('Request error...');
        };

        xhr.send();
    }


    // Function to add event listeners to delete buttons
    function addDeleteEventListeners() {
        let deleteButtons = document.querySelectorAll(".delete-btn");
        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                let orderID = this.getAttribute("data-order-id");
                deleteOrder(orderID);
            });
        });
    }

    // Function to delete an order
    function deleteOrder(orderID) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_order.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                fetchOrders(''); // Refresh the orders table
            } else {
                console.error('Error deleting order:', xhr.statusText);
            }
        };

        xhr.onerror = function () {
            console.error('Request error...');
        };

        xhr.send("orderID=" + orderID);
    }
    // Function to add event listeners to "Confirm Your Order" buttons
    function addConfirmOrderEventListeners() {
        let confirmButtons = document.querySelectorAll(".confirm-order-btn");
        confirmButtons.forEach(button => {
            button.addEventListener("click", function () {
                let orderID = this.getAttribute("data-order-id");
                showModal(orderID); // Show modal when the button is clicked
            });
        });
    }
    function showModal(orderID) {
        const modal = document.getElementById('confirmation-modal');
        modal.classList.remove('hidden'); // Show the modal

        // Set up event listener for the "Yes, Confirm" button
        const confirmBtn = modal.querySelector('.confirm-order-btn');
        confirmBtn.setAttribute('data-order-id', orderID);
        confirmBtn.onclick = function () {
            confirmOrder(orderID); // Confirm the order
            closeModal(); // Close the modal
        };
    }

    function closeModal() {
        const modal = document.getElementById('confirmation-modal');
        modal.classList.add('hidden'); // Hide the modal
    }


    function confirmOrder(orderID) {
        console.log("Order ID being sent: " + orderID); 
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "confirm_order.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);  // Log the response for debugging
                
                fetchOrders(''); // Refresh the orders table after confirmation
            } else {
                console.error('Error confirming order:', xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error('Request error...');
        };
    
        xhr.send("orderID=" + orderID);
    }
    


});



  