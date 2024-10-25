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
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawPieChart);
      function drawPieChart() {
        var data = google.visualization.arrayToDataTable([
            ['Payment Method', 'Count'],

            <?php 
          // Fetch data from the database
          $sql = "SELECT paymentMethod, COUNT(*) as count FROM orders GROUP BY paymentMethod";
          $result = mysqli_query($conn, $sql);

          if ($result) {
              while ($row = mysqli_fetch_assoc($result)) {
                  // Output data for Google Charts (category and count)
                  echo "['" . $row['paymentMethod'] . "', " . $row['count'] . "],";
              }
          }
          ?>
        ]);

        var options = {
          title: 'Distribution of payment methods',
          
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
    </script> 
        
        
  


</head>

<body>
   <?php include "../../includes/adminNavbar.php" ?>
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
   
    <img src="images1.png" width="50px" height="50px"   style=" cursor: pointer; position: absolute; border-radius:50% ;margin-bottom: 1rem; margin-left: 0.5rem; top: 0px; right: 0px" onclick="generatePDF()">
</label>
<div class="mySlides" id="piechart_3d" style="width: 90%; height: 70vh; margin: auto;"></div>


    </main>
    
   </div>
   <script>
function generatePDF() {
    const element = document.querySelector("#piechart_3d");

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