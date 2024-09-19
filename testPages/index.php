
<?php 
  //session_start();
  include "../dbconnect.php";

  $sql = "INSERT INTO tickets (ticketID,name,email,phone,subject,ticketType,ticketText,ticketStatus,created_at,updated_at)
          VALUES('123','Tony Stark',
          'starkindustries@gmail.com','+01 2129704133',
          'Inquiry Message','Does this works for Hulk',
          'my friend hulk is loosing his hair and  i am hoping to gift this kind of a comb for his next birth day. But I want to know is this will have effect on his hair or deos in work for people like hulk or it doe not work for artificial mutants',
          'New','2024-09-14 19:46:47',
          '2024-09-14 19:46:47')";

   mysqli_query($conn,$sql);       

   mysqli_close($conn);        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>

    </style>
</head>
<body>
    <header>
         <h1>MYSQL with mysqli</h1>
         
         
    </header>
    <main>
       <p></p>
    </main>
    
</body>
</html>


<?php 
  
?>