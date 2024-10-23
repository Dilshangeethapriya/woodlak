<?php 
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

if(!$conn){
    die("Connection failed because: " . mysqli_connect_error());
} else {
    
}

// Query to get the order status counts grouped by the date (ignoring time)
$query = "
    SELECT DATE(orderDate) as orderDate, 
           SUM(CASE WHEN orderStatus = 'Completed' THEN 1 ELSE 0 END) as Completed,
           SUM(CASE WHEN orderStatus = 'Pending' THEN 1 ELSE 0 END) as Pending,
           SUM(CASE WHEN orderStatus = 'Delivered' THEN 1 ELSE 0 END) as Delivered,
           SUM(CASE WHEN orderStatus = 'Declined' THEN 1 ELSE 0 END) as Declined
    FROM orders
    WHERE orderDate >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
    GROUP BY DATE(orderDate)
    ORDER BY orderDate ASC";
$result = mysqli_query($conn, $query);

$result = mysqli_query($conn, $query);

$chartData = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $chartData[] = [
            'date' => $row['orderDate'],
            'completed' => (int)$row['Completed'],
            'pending' => (int)$row['Pending'],
            'delivered' => (int)$row['Delivered'],
            'declined' => (int)$row['Declined']
        ];
    }
} else {
    echo "No data available for the chart.";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Charts_page </title>
    <link rel="stylesheet" type="text/css" href="chat_list.css">

  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="nav.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
   
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
           google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Delivered', 'Pending', 'Completed', 'Declined'],
            <?php
            foreach($chartData as $data) {
              echo "['" . $data['date'] . "', " . $data['completed'] . ", " . $data['pending'] . ", " . $data['delivered'] . ", " . $data['declined'] . "],";
            }
          ?>
        ]);
      

        var options = {
          title: 'Order Status Over the Last 14 Days',
          hAxis: { title: 'Date', format: 'MMM d' }, // X-axis as Date
          vAxis: { title: 'Number of Orders', minValue: 0 }, // Y-axis as order count
          curveType: 'function',
          legend: { position: 'bottom' },
          colors: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'] // Colors for Completed, Pending, Delivered, Declined
        };

        var chart = new google.charts.Line(document.getElementById('linechart_material'));

chart.draw(data, google.charts.Line.convertOptions(options))
      }
    </script> 
        
        
  


</head>

<body>
    <header class="bg-[#543310] h-20 ">
        <nav class="flex justify-between items-center w-[95%] mx-auto">
            <div class="flex items-center gap-[1vw]">
                <img class="w-16" src="Logo.png" alt="Logo">
                <h1 class="text-xl text-white"><b>WOODLAK</b></h1>
            </div>
            <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start items-center lg:items-start text-center lg:text-right lg:contents" id="content" >
                <ul class="flex lg:flex-row flex-col  lg:gap-[4vw] gap-8">
                    <li>
                        <a class="text-white hover:text-[#D0B8A8] p-2 underline hover:underline-offset-4" href="#">Home</a>
                    </li>
                    <li>
                        <a class="text-white hover:text-[#D0B8A8]" href="#">Contact Us</a>
                    </li>
                    <li>
                        <a class="text-white hover:text-[#D0B8A8]" href="#">About Us</a>
                    </li>
                    <li>
                        <a class="text-white hover:text-[#D0B8A8]" href="#">Products</a>
                    </li>
                    <li>
                        <a class="text-white hover:text-[#D0B8A8]" href="#">Orders</a>
                    </li>
                </ul>
            </div>
            <div class="flex items-center gap-3 ">
                <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]">Register</button>
                <button class="bg-[#74512D] text-white px-5 py-2  rounded-full hover:text-[#D0B8A8]">Login</button>
                <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
            </div>
        </nav>
    </header>
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
   <div class="terms">
    <main class="background" >
    

    <label for="export-file" id="generate-pdf" style=" width: 100%"> 
   
    <img src="images1.png" width="50px" height="50px"   style=" cursor: pointer; position: absolute; border-radius:50% ; top: 0px; right: 0px" onclick="generatePDF()">
</label>

<div class="mySlides" id="linechart_material" style="width: 90%; height: 70vh; margin: auto;"></div>


    </main>
    
   </div>
   <script>
function generatePDF() {
    const element = document.querySelector("#linechart_material");

    const opt = {
        margin:       0.5,
        filename:     'order_list.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
    };

    html2pdf().from(element).set(opt).save();
}
</script>
  
</body>

</html>

<?php
// Close the connection
mysqli_close($conn);
?>  