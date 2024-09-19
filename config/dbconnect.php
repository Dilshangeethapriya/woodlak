<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  

       $servername = "localhost";
       $username = "root";
       $password = "";
       $database = "WoodLak";
   
       try {
          
           $conn = new mysqli($servername, $username, $password, $database);
   

           if ($conn->connect_error) {

            throw new Exception("Error: " . $conn->connect_error);
           }
   
   
       } catch (Exception $e) {
 
           echo "<strong>Sorry..! We are having problems connecting to the database..</strong>. <br>" . $e->getMessage();

       }
