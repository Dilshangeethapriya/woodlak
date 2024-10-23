<?php
include 'dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Product Catalog</title>
    <meta charset="utf-8">
	<meta name="veiwport" content="width=device-width,intial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../resources/css/findComb.css">

    <style>
            body{
                background: url('../resources/images/bg1.png');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
                font-family: sans-serif;
            }
            img.darker {
                transition: all 0.2s ease-in-out;
            }

            img.darker:hover {
                filter: brightness(70%);
            }
            .hide{
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }

            .showhide:hover + .hide{
                opacity: 1;
                visibility: visible;
                
            }
            .add_new{
                padding: 10px 20px;
                color: white;
                background-color: #B99470;
                margin-top:10px;
                margin-right:35px;
                border-radius: 25px;
            }

            .disabled {
                pointer-events: none;
                opacity: 0.5; 
            }

    </style>
</head>
    <body>
    <?php include  '../includes/navbar.php'; ?>
    <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="flex: 1; text-align: center; margin: 0; font-size:50px; margin-left:20px; margin-top:40px; margin-bottom:20px" class="text-[#543310]"><b>The Natural Choice for Healthier Hair</b></h1>
            <button class="add_new bg-[#74512D;] hover:bg-[#543310]" onclick="openFindForm()">Find Your Comb</button>
        </div>

        <!--Find Comb Form-->

        <div id="popup" class="popup z-50">
        <div class="popup-content" style="background-color: #EADDCA; height: 650px;">
            <span class="close" id="closePopup" onclick="closeFindForm()">&times;</span>
            <h2 class="text-2xl">Reveal My Comb</h2>
            
            <div  class="flex-container">
                <div class="button-div" id="curly">
                    <a href="view_product.php?PRODUCTC= + 1003">
                    <img src="../resources/images/Curly_quiz.png" alt="Curly Hair">
                    <h3><b>Curly</b></h3>
                    </a>
                </div>
                <div class="button-div" id="thick">
                    <a href="view_product.php?PRODUCTC= + 1002">
                    <img src="../resources/images/Thick_quiz.png">
                    <h3><b>Thick</b></h3>
                    </a>
                </div>
                <div class="button-div" id="straight">
                    <a href="view_product.php?PRODUCTC= + 1001">
                    <img src="../resources/images/Straight_quiz.png">
                    <h3><b>Straight</b></h3>
                    </a>
                </div>
            </div>
            
            <form id="findCombForm" action="" method="post">
                <div class="mb-4">
                    <h3 class="text-xl"><b>How would you describe your hair texture?</b></h3>
                    <label for="texture" class="text-l" >Texture : </label>
                    <input type="text" id="texture" name="texture" style="width:200px; height:30px; border-radius: 10px; padding:5px;" class="mt-2 mb-2" readonly>
                    <div class="flex justify-between items-center mt-2 ">
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectTexture('Fine')">Fine</button>
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectTexture('Medium')">Medium</button>
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectTexture('Dense')">Dense</button>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="text-xl"><b>What types of knots do you usually encounter?</b></h3>
                    <label for="knots">Knots : </label>
                    <input type="text" id="knots" name="knots" style="width:200px; height:30px; border-radius: 10px; padding:5px;" class="mt-2 mb-2" readonly>
                    <div class="flex justify-between items-center mt-2  ">
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectKnots('Minor Tangles')">Minor Tangles</button>
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectKnots('Moderate Knots')">Moderate Knots</button>
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectKnots('Large Knots')">Large Knots</button>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="text-xl"><b>Do you prefer to detangle your hair when it's wet or dry?</b></h3>
                    <label for="prefer">Preference : </label>
                    <input type="text" id="prefer" name="prefer" style="width:200px; height:30px; border-radius: 10px; padding:5px;" class="mt-2 mb-2" readonly>
                    <div class="flex justify-between items-center mt-2  ">
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectPrefer('Wet')">Wet</button>
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectPrefer('Dry')">Dry</button>
                    <button type="button" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="selectPrefer('Either works for me')">Either works for me</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


        <div class="catalog grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 p-8 max-w-screen-2xl m-auto">   
            <?php
            $sql = "SELECT productID, productName, price, image, stockLevel FROM product";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $stock_limit = 15;
                    echo '<div class="product bg-[#FFF8E8] p-6 rounded-lg shadow-2xl " style="height: 550px">';
                    echo '<a href="view_product.php?PRODUCTC=' . $row['productID'] . '"><img src="../'.$row['image'].'" alt="' . $row['productName'] . '" class="darker showhide w-full h-[55%] object-cover mb-4 rounded-lg"><p class="text-center hide">More Info</p></a>';
                    echo '<a href="view_product.php?PRODUCTC=' . $row['productID'] . '"><h2 class="text-3xl font-bold mb-2 text-center hover:text-[#5a2b09]">' . $row['productName'] . '</h2></a>';
                    echo '<p class="price text-2xl mb-6 text-center">Rs.' . $row['price'] . '</p>';
                    echo '<form method="POST" action="add_to_cart.php">';
                    echo '<input type="hidden" name="productID" value="' . $row['productID'] . '">';
                    echo '<input type="hidden" name="productName" value="' . $row['productName'] . '">';
                    echo '<input type="hidden" name="price" value="' . $row['price'] . '">';

                    if ($row['stockLevel'] <= $stock_limit) {
                        echo '<button type="submit" class="bg-[#78350f] hover:bg-[#5a2b09] text-white rounded-full px-10 py-3 text-l border-2 border-[#78350f] mx-auto mb-4 flex items-center disabled" disabled>';
                        echo '<b>Out of Stock</b>';
                    } else {
                        echo '<button type="submit" class="bg-[#78350f] hover:bg-[#5a2b09] text-white rounded-full px-10 py-3 text-l border-2 border-[#78350f] mx-auto mb-4 flex items-center">';
                        echo '<b>Add to Cart</b>';
                    }
                    echo '</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }

            mysqli_close($conn);
            ?>
        </div>
        <a href="shopping_cart.php" class="shoppingCart bg-[#78350f] hover:bg-white text-white hover:text-[#78350f] fixed bottom-3 right-5  p-3 rounded-full shadow-lg">
        <i class="bi bi-cart4 text-2xl"></i>
        </a>
    </body>
    <script>
            function openFindForm() {
                document.getElementById('popup').style.display = 'block';
            }

            function closeFindForm() {
                document.getElementById('popup').style.display = 'none';
                document.querySelector("input[name='texture']").value="";
                document.querySelector("input[name='knots']").value="";
                document.querySelector("input[name='prefer']").value="";
                document.getElementById('straight').classList.remove('disabled');
                document.getElementById('curly').classList.remove('disabled');
                document.getElementById('thick').classList.remove('disabled');
            }

            function selectTexture(texture) {
                document.querySelector("input[name='texture']").value=texture;
                findComb();
            }

            function selectKnots(knots) {
                document.querySelector("input[name='knots']").value=knots;
                findComb();
            }

            function selectPrefer(prefer) {
                document.querySelector("input[name='prefer']").value=prefer;
                findComb();
            }

            function findComb() {
            var texture = document.getElementById("texture").value;
            var knots = document.getElementById("knots").value;
            var prefer = document.getElementById("prefer").value;

            if(!(texture==="" || knots==="" || prefer==="")){
                console.log("empty...");
                if(texture==="Fine"){
                    document.getElementById('straight').classList.remove('disabled');
                    document.getElementById('thick').classList.add('disabled'); 
                    document.getElementById('curly').classList.add('disabled'); 
                }else if(texture==="Medium"){
                    document.getElementById('thick').classList.remove('disabled'); 
                    document.getElementById('curly').classList.remove('disabled');
                    document.getElementById('straight').classList.add('disabled');
                    if(knots==="Minor Tangles" || knots==="Moderate Knots"){
                        document.getElementById('thick').classList.remove('disabled');
                        document.getElementById('curly').classList.add('disabled');
                        document.getElementById('straight').classList.add('disabled');
                    }else{
                        document.getElementById('curly').classList.remove('disabled');
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                    }
                }else if(texture==="Dense"){
                    document.getElementById('thick').classList.remove('disabled'); 
                    document.getElementById('curly').classList.remove('disabled');
                    document.getElementById('straight').classList.add('disabled');
                    if(knots==="Minor Tangles"){
                        document.getElementById('thick').classList.remove('disabled');
                        document.getElementById('curly').classList.add('disabled');
                        document.getElementById('straight').classList.add('disabled');
                    }else{
                        document.getElementById('curly').classList.remove('disabled');
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                    }
                }
            }else if(!(texture==="" || knots==="")){
                document.getElementById('straight').classList.remove('disabled');
                document.getElementById('thick').classList.remove('disabled'); 
                document.getElementById('curly').classList.remove('disabled');
                if(texture==="Fine"){
                    document.getElementById('straight').classList.remove('disabled');
                    document.getElementById('thick').classList.add('disabled'); 
                    document.getElementById('curly').classList.add('disabled'); 
                }else if(texture==="Medium"){
                    document.getElementById('thick').classList.remove('disabled'); 
                    document.getElementById('curly').classList.remove('disabled');
                    document.getElementById('straight').classList.add('disabled');
                    if(knots==="Minor Tangles" || knots==="Moderate Knots"){
                        document.getElementById('thick').classList.remove('disabled');
                        document.getElementById('curly').classList.add('disabled');
                        document.getElementById('straight').classList.add('disabled');
                    }else{
                        document.getElementById('curly').classList.remove('disabled');
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                    }
                }else if(texture==="Dense"){
                    document.getElementById('thick').classList.remove('disabled'); 
                    document.getElementById('curly').classList.remove('disabled');
                    document.getElementById('straight').classList.add('disabled');
                    if(knots==="Minor Tangles"){
                        document.getElementById('thick').classList.remove('disabled');
                        document.getElementById('curly').classList.add('disabled');
                        document.getElementById('straight').classList.add('disabled');
                    }else{
                        document.getElementById('curly').classList.remove('disabled');
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                    }
                }

            }else if(!(texture==="" || prefer==="")){
                document.getElementById('straight').classList.remove('disabled');
                document.getElementById('thick').classList.remove('disabled'); 
                document.getElementById('curly').classList.remove('disabled');
                if(texture==="Fine"){
                    document.getElementById('straight').classList.remove('disabled');
                    document.getElementById('thick').classList.add('disabled'); 
                    document.getElementById('curly').classList.add('disabled');
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    } 
                }else if(texture==="Medium"){
                    document.getElementById('thick').classList.remove('disabled'); 
                    document.getElementById('curly').classList.remove('disabled');
                    document.getElementById('straight').classList.add('disabled');
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }else if(texture==="Dense"){
                    document.getElementById('thick').classList.remove('disabled'); 
                    document.getElementById('curly').classList.remove('disabled');
                    document.getElementById('straight').classList.add('disabled');
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }

            }else if(!(knots==="" || prefer==="")){
                document.getElementById('straight').classList.remove('disabled');
                document.getElementById('thick').classList.remove('disabled'); 
                document.getElementById('curly').classList.remove('disabled');
                if(knots==="Minor Tangles"){
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    } 
                }else if(texture==="Moderate Knots"){
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }else if(texture==="Large Knots"){
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }

            }else{
                document.getElementById('straight').classList.remove('disabled');
                document.getElementById('thick').classList.remove('disabled'); 
                document.getElementById('curly').classList.remove('disabled');
                if(texture){
                    if(texture==="Fine"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(texture==="Medium"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }else if(texture==="Dense"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }
                if(!(knots==="")){
                    if(knots==="Minor Tangles"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(knots==="Moderate Knots"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(knots==="Large Knots"){
                        document.getElementById('straight').classList.add('disabled');
                        document.getElementById('thick').classList.add('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }

                if(!(prefer==="")){
                    if(prefer==="Wet"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.add('disabled');
                    }else if(prefer==="Dry" || prefer==="Either works for me"){
                        document.getElementById('straight').classList.remove('disabled');
                        document.getElementById('thick').classList.remove('disabled'); 
                        document.getElementById('curly').classList.remove('disabled');
                    }
                }
            }

            }

            window.onclick = function(event) {
            if (event.target === popup) {
                popup.style.display = "none";
            }
            }

            document.querySelector('.popup-content').onclick = function(event) {
                event.stopPropagation(); 
            }
    </script>
</html>


