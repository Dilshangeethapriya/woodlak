<?php 

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
        
        <html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses', 'Profit'],
          ['2014', 1000, 400, 200],
          ['2015', 1170, 460, 250],
          ['2016', 660, 1120, 300],
          ['2017', 1030, 540, 350]
        ]);

        var options = {
          chart: {
            title: 'Company Performance',
            subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          },
          bars: 'horizontal' // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
  


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data1 = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          width: 900,
        height: 400,
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data1, options);
      }
    </script>

    <style>
.mySlides {display:none}
.w3-left, .w3-right, .w3-badge {cursor:pointer}
.w3-badge {height:13px;width:13px;padding:0}
</style>

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
    <div class="w3-content w3-display-container" >

<div class="mySlides" id="piechart_3d" style="width: 1000px; height: 500px;"></div>
<div  class="mySlides" id="barchart_material" style="width: 900px; height: 500px;"></div>
<div class="mySlides" id="curve_chart" style="width: 1000px; height: 500px"></div>
<div class="w3-center w3-container w3-section w3-large w3-text-black w3-display-bottommiddle" style="width:100%">
    <div class="w3-left w3-hover-text-khaki" onclick="plusDivs(-1)">&#10094;</div>
    <div class="w3-right w3-hover-text-khaki" onclick="plusDivs(1)">&#10095;</div>
    <span class="w3-badge demo w3-border w3-transparent w3-hover-black" onclick="currentDiv(1)"></span>
    <span class="w3-badge demo w3-border w3-transparent w3-hover-black" onclick="currentDiv(2)"></span>
    <span class="w3-badge demo w3-border w3-transparent w3-hover-black" onclick="currentDiv(3)"></span>
  </div>
</div>

    </main>
    
   </div>

   <script>
var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n) {
  showDivs(slideIndex += n);
}

function currentDiv(n) {
  showDivs(slideIndex = n);
}

function showDivs(n) {
  var i;
  var x = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  if (n > x.length) {slideIndex = 1}
  if (n < 1) {slideIndex = x.length}
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" w3-white", "");
  }
  x[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " w3-white";
}
</script>

</body>

</html>

<?php
// Close the connection
mysqli_close($conn);
?>  