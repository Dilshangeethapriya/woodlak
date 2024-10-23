<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ORDER LIST</title>
    <link rel="stylesheet" type="text/css" href="style3.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script> <!-- Include html2pdf library -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        #customers_table {
            width: 95%;
            max-width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .chart__file {
            position: relative;
            display: inline-block;
        }

        .chart__dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .chart__dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .chart__dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .chart__file:hover .chart__dropdown-content {
            display: block;
        }

        .chart__dropdown-content {
    display: block; /* Keep it visible by default */
    opacity: 0; /* Start as invisible */
    transition: opacity 0.3s ease; /* Transition for smooth appearance */
}

.chart__file:hover .chart__dropdown-content {
    opacity: 1; /* Show on hover */
}

    </style>
</head>

<body>

<?php include "../../includes/adminNavbar.php" ?>
    <main class="table" id="customers_table">
    
        <section class="table__header">
            <h1>ORDER LIST</h1>
            <div class="input-group">
                <input type="search" id="customer-search" placeholder="Search Order ID or Customer Name...">
                <img src="cross1.jpg" alt="" id="clear-search">
                <img src="image.png" alt="search-icon">
                 </div>
                 <div id="google_translate_element"></div>

                 <div class="chart__file">
           
                <a href="javascript:void(0);"  class="chart__file-btn"></a>
                <div class="chart__dropdown-content">
                    <a href="chart_list1.php">Distribution of payment methods</a>
                     <a href="chart_list3.php">Order Status Over the Last 14 Days</a>
                     <a href="chat_list2.php">Total Income Over the Last 14 Days </a>
                </div>
            </div>
            <div class="pending__file">
                <label for="pending-file" class="pending__file-btn" ></label>
                
            </div>
            <div class="delivered__file">
                <label for="delivered-file" class="delivered__file-btn" ></label>
            </div>

            <div class="completed__file">
                <label for="completed-file" class="completed__file-btn" ></label>
            </div>

            <div class="decliend__file">
                <label for="decliend-file" class="decliend__file-btn" ></label>
            </div>

            <div class="all__file">
                <label for="all-file" class="all__file-btn" ></label>
                
            </div>

            
           
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Export As &nbsp; &#10140;</label>
                    <label for="export-file" id="generate-pdf">PDF <img src="images/pdf.png" alt="" onclick="exportToPDF()" ></label>
                </div>
            </div>
           

        </section>
        <section class="table__body">
            <table>
                <thead>
                    <tr>
                        <th>Order ID </th>
                        <th>Customer ID </th>
                        <th>Amount </th>
                        <th>Order Date </th>
                        <th>Payment Method </th>
                        <th>Updated Status </th>
                        <th>Select Order Status </th>
                        <th>Mark as Delivered</th>
                    </tr>
                </thead>
                <tbody id="order-table-body">

                </tbody>
            </table>
        </section>
    </main>
    <script src="script.js"></script>


    <script>

        function generatePDF() {
            const element = document.querySelector("#customers_table");


            const opt = {
                margin:       0.5,                             
                filename:     'order_list.pdf',                 
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },    
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' } 
            };

          
            html2pdf().from(element).set(opt).save();
        }

  
        document.getElementById("generate-pdf").addEventListener("click", generatePDF);
    </script>

<script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement(
          { pageLanguage: "en" },
          "google_translate_element"
        );
      }
    </script>
    <script
      type="text/javascript"
      src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"
    ></script>

</body>

</html>
