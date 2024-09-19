document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector(".input-group input");
    const searchIcon = document.querySelector(".input-group img:nth-child(3)");
    const clearIcon = document.querySelector(".input-group img:nth-child(2)");

    function fetchOrders(query = "") {
        console.log("Fetching orders for query:", query);
        const xhr = new XMLHttpRequest();
        let url = "fetch_orders.php?query=" + encodeURIComponent(query);

        xhr.open("GET", url, true);

        xhr.onload = function () {
            if (this.status === 200) {
                const orders = JSON.parse(this.responseText);
                console.log("Orders received:", orders);
                const tableBody = document.getElementById("order-table-body");

                tableBody.innerHTML = "";

                if (orders.length === 0) {
                    console.log("No orders found");
                    tableBody.innerHTML =
                        '<tr><td colspan="8">No orders found.</td></tr>';
                } else {
                    orders.forEach((order) => {
                        const row = document.createElement("tr");

                        row.innerHTML = `
                            <td><a href="customer_details.php?orderID=${
                                order.orderID
                            }">${order.orderID}</a></td>
                            <td>${order.customerID}</td>
                            <td>${order.total}</td>
                            <td>${order.orderDate}</td>
                            <td>${order.paymentMethod}</td>
                            <td id="order-status-${order.orderID}">${
                            order.orderStatus
                        }</td>
                            <td>
                                <select class="order-status" data-order-id="${
                                    order.orderID
                                }">
                                    <option value="pending" ${
                                        order.orderStatus === "Pending"
                                            ? "selected"
                                            : ""
                                    }>Pending</option>
                                    <option value="completed" ${
                                        order.orderStatus === "Completed"
                                            ? "selected"
                                            : ""
                                    }>Completed</option>
                                    <option value="declined" ${
                                        order.orderStatus === "Declined"
                                            ? "selected"
                                            : ""
                                    }>Declined</option>
                                </select>
                            </td>
                        `;

                        tableBody.appendChild(row);
                    });

                    document
                        .querySelectorAll(".order-status")
                        .forEach((select) => {
                            select.addEventListener("change", function () {
                                const orderID =
                                    this.getAttribute("data-order-id");
                                const newStatus = this.value;

                                const xhrUpdate = new XMLHttpRequest();
                                xhrUpdate.open(
                                    "POST",
                                    "update_order_status.php",
                                    true
                                );
                                xhrUpdate.setRequestHeader(
                                    "Content-Type",
                                    "application/x-www-form-urlencoded"
                                );
                                xhrUpdate.onload = function () {
                                    if (this.status === 200) {
                                        console.log(
                                            `Server response: ${this.responseText}`
                                        );
                                    } else {
                                        console.error(
                                            "Failed to update status. Server responded with:",
                                            this.status
                                        );
                                    }
                                };
                                xhrUpdate.send(
                                    `orderID=${orderID}&status=${newStatus}`
                                );
                            });
                        });
                }
            } else {
                console.error(
                    "Failed to fetch orders. Server responded with:",
                    this.status
                );
            }
        };
        xhr.send();
    }

    searchIcon.addEventListener("click", function () {
        const query = searchInput.value.trim();
        console.log("Search icon clicked, Query:", query);
        fetchOrders(query);
    });

    clearIcon.addEventListener("click", function () {
        searchInput.value = "";
        console.log("Clear icon clicked");
        fetchOrders();
    });

    fetchOrders();
});
