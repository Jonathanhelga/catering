<?php

//cc
session_start();

include 'db_connect.php';

$product_source = "";
$product_name = "";
$product_price = "";
$message = "";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['submit'])){
        $product_source = isset($_GET['Type']) ? $_GET['Type'] : '';
        $product_name = isset($_GET['ProductName']) ? $_GET['ProductName'] : '';
        $product_price = isset($_GET['ProductPrice']) ? $_GET['ProductPrice'] : '';
    
        $pull_sql = "SELECT * FROM Products WHERE Product_Name=? AND UserID=?";
        $stmt = $conn->prepare($pull_sql);
        $stmt->bind_param("si", $product_name, $_SESSION["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if($result->num_rows > 0){
            $message = "Product already exists in the database.";
        }
        else{
            $stmt = $conn->prepare("SELECT COALESCE(MAX(ProductID), 0) + 1 AS nextProductID FROM Products WHERE UserID = ?");
            $stmt->bind_param("i", $_SESSION["id"]);
            $stmt->execute();
            $stmt->bind_result($nextProductID);
            $stmt->fetch();
            $stmt->close();


            $input_sql = "INSERT INTO Products (UserID, ProductID, Product_Source, Product_Name, Product_Price)
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($input_sql);
            $stmt->bind_param("iissi", $_SESSION["id"], $nextProductID, $product_source, $product_name, $product_price);
            
            if ($stmt->execute()) { $message = "Product added successfully."; } 
            else { $message = "Error occurred: " . $prepare->error; }
        }
        $stmt->close();
        $conn->close();
    }
    
}
?>
<!DOCTYPE html> 
<html>
    <head>
        <meta charset="utf-8">
        <title>Item Detail</title>
        <style>
            body{
                font-family: 'Trebuchet MS', sans-serif;
                background-color:rgb(249,225,196);
                font-size:100%;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .pageForming{
                border:6px solid #394b33;
                padding: 30px;
                border-radius: 15px;
                background-color: rgb(187, 168, 147);
                width:80%;
                max-width: 600px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            #upperTitle{
                border: 3px solid green;
                border-radius: 10px 30px;
                margin-bottom: 20px;
                background-color: rgb(131, 152, 103);
                text-align: center;
                padding: 10px;
            }
            form {
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
            }
            label {
                display: block;
                margin-bottom: 5px;
            }
            input[type="text"],
            input[type="number"]{
                width: 80%;
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            select{
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            
            #submit {
                grid-column: span 2;
                padding: 10px;
                background-color: #394b33;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            #submit:hover {
                background-color: #2e3d27;
            }  
            .message {
                grid-column: span 2;
                text-align: center;
                color: red;
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <section class="pageForming">
            <section id="upperTitle">
                <h1>New Product</h1>
            </section>
            <section>
                <form action="item.php" method="GET">
                    <div>
                        <label for="type">Product Source:</label>
                            <select name="Type" id="Type">
                                <option value="selfMade">Self Made</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                    </div>
                    <div>
                        <label for="ProductName">Product Name:</label>
                        <input type="text" id="ProductName" name="ProductName">
                    </div>
                    <div>
                        <label for="ProductPrice">Product Price:</label>
                        <input type="number" id="ProductPrice" name="ProductPrice">
                    </div>
                    <div>
                        <input name="submit" id="submit" type="submit" value="Submit">
                    </div>
                </form>
                <br>
                <a href="actionOptions.php">Back to Front Page </a>
            </section>
        </section>
        

    </body>
</html>