<?php
session_start();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Kitchen Ndeso</title>
        <style>
            body{
                font-family: 'Trebuchet MS', sans-serif;
                background-color:rgb(249,225,196);
                font-size:100%;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .pageForming{
                border: 6px solid #394b33;
                padding: 30px;
                border-radius: 15px;
                background-color: rgb(187, 168, 147);
                width: 80%;
                max-width: 600px;
                text-align: center;
            }

            #upperTitle{
                border: 3px solid green;
                border-radius:10px 30px;
                background-color:rgb(131, 152, 103);
                padding: 10px;   
                margin-bottom: 20px;
            }
            .button {
                background-color: #04AA6D;
                border: none;
                border-radius: 12px;
                margin: 10px;
                width: 150px;
                height: 50px;
                color: rgb(249, 244, 255);
                cursor: pointer;
                font-size: 16px;
            }
            .button:hover {
                background-color: #039a60;
            }
            footer {
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #ccc;
            }
            footer small {
                display: block;
                margin-bottom: 10px;
            }
            footer nav a {
                margin: 0 10px;
                text-decoration: none;
                color: #394b33;
            }
            footer nav a:hover {
                text-decoration: underline;
            }
        </style>
        <script>
            function addCustomer(){ location.replace("Customer.php"); }
            function addItem(){ location.replace("Item.php"); }
            function addVendor(){location.replace("Vendor.php"); }
            function newOrder(){location.replace("Order.php"); }
            function searchData(){location.replace("searchData.php");}
        </script>
    </head>
    <body>
        <section class="pageForming">
            <section id="upperTitle">
                <h1>Hello <?php echo $_SESSION["username"];?> </h1>
            </section>
            <section>
                <button onclick="searchData()" class="button" value="search">Search Data</button>
            </section>
                <button onclick="addCustomer()" class="button" value="AddCustomer">Add Customer</button>
                <button onclick="addItem()" class="button" value="AddItem">Add Item</button>
                <button onclick="addVendor()" class="button" value="AddVendor">Add Vendor</button>
            <section>
                <button onclick="newOrder()" class="button" value="newOrder">New Order</button>
            </section>
                    
            <footer>
                <small>&copy; Problem &nbsp;</small>
                <nav>
                    <a href="mailto:your-email@gmail.com">Gmail</a>
                    <a href="https://wa.me/your-phone-number">WhatsApp</a>
                    <a href="line://ti/p/@your-line-id">Line</a>
                </nav>
            </footer>
        </section>
        

    </body>
</html>