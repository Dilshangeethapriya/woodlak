<!DOCTYPE html>
<?php session_start();?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Previous Orders</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="nav.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
      body{
          font-family: sans-serif;
      }
  </style>
</head>
<body>

   <?php include "../../includes/navbar.php" ?>
    <script>
        function responsive() {
            var x = document.getElementById("content");
            if (x.classList.contains("hidden")) {
                x.classList.remove("hidden");
            } else {
                x.classList.add("hidden");
            }
        }
    </script>
    
    <div class="bg">
      <div class="container">
      <div class="order-status">
        <!-- Pop-up Modal --> 
<div id="confirmation-modal" class="modal hidden">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2>Confirm Your Order</h2>
    <p>Are you sure you want to confirm this order?</p>
    <button class="confirm-order-btn" data-order-id="">Yes, Confirm</button>
    <button class="cancel-btn" onclick="closeModal()">Cancel</button>
    
  </div>
</div>
    

      <h1 class="page-title">Your Previous Orders</h1>
      <div class="status-icons">
      <div class="pending__file" >
                <label for="pending-file" class="pending__file-btn" ></label>
                
            </div>
            <div class="delivered__file">
                <label for="delivered-file" class="delivered__file-btn" ></label>
            </div>

            <div class="completed__file">
                <label for="completed-file" class="completed__file-btn" ></label>
            </div>

            <div class="decliend__file" >
                <label for="decliend-file" class="decliend__file-btn" ></label>
            </div>
      </div>
      </div>
        <div class="table-container">
          <table class="orders-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Delivery Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="orders-body">
             
  
   

            </tbody>
          </table>
        </div>
       



<script> function openModal() {
    document.getElementById("confirmation-modal").classList.remove("hidden");
  }
  
  function closeModal() {
    document.getElementById("confirmation-modal").classList.add("hidden");
  } </script>
      </div>
    </div>

<script src="order.js"> </script>
</body>
</html>
