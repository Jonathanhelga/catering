<?php
session_start();

unset($_SESSION['cart']);
include 'db_connect.php';

$nextOrderID = 0;
$delivery_date = '';
$custName = '';
$productName = [];
$Quantity = [];
$Price = [];
$Total = [];
$index = 0;

function fetchCustomerName($conn, $userID, $customerID) {
    $custName = '';
    $stmt = $conn->prepare("SELECT Cust_Name FROM Customers WHERE UserID = ? AND CustomerID = ?");
    $stmt->bind_param("ii", $userID, $customerID);
    $stmt->execute();
    $stmt->bind_result($custName);
    $stmt->fetch();
    $stmt->close();
    return $custName;
}

function fetchNextOrderID($conn, $userID) {
    $nextOrderID = '';
    $stmt = $conn->prepare("SELECT COALESCE(MAX(OrderID), 0) + 1 AS nextOrderID FROM Orders WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($nextOrderID);
    $stmt->fetch();
    $stmt->close();
    return $nextOrderID;
}

function insertOrder($conn, $userID, $orderID, $customerID, $custName, $deliveryDate) {
    $stmt = $conn->prepare("INSERT INTO Orders(UserID, OrderID, CustomerID, Cust_Name, Delivery_Date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $userID, $orderID, $customerID, $custName, $deliveryDate);
    $stmt->execute();
    $stmt->close();
}

function insertOrderedProduct($conn, $userID, $orderID, $productID, $productAmt) {
    $stmt = $conn->prepare("INSERT IGNORE INTO Ordered_Product(UserID, OrderID, ProductID, Product_Amt) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("iiii", $userID, $orderID, $productID, $productAmt);
    $stmt->execute();
    $stmt->close();
}

function fetchNameAndPrice($conn, $userID, $productID){
    $arr = array();
    $stmt = $conn->prepare("SELECT Product_Price, Product_Name FROM Products WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("ii", $userID, $productID);
    $stmt->execute();

    // Declare temporary variables to store the results
    $price = 0;
    $productName = '';

    // Bind the result to these variables
    $stmt->bind_result($price, $productName);
    $stmt->fetch();
    $stmt->close();

    // Assign the fetched values to the array
    $arr['price'] = $price;
    $arr['productName'] = $productName;

    return $arr;
}


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['submit'])) {
        $customer_ID = $_GET['CustID'] ?? '';
        $delivery_date = $_GET['DeliveryDate'] ?? '';

        // Fetch customer name
        $custName = fetchCustomerName($conn, $_SESSION['id'], $customer_ID);

        // Fetch next order ID
        $nextOrderID = fetchNextOrderID($conn, $_SESSION['id']);

        // Insert order into Orders table
        insertOrder($conn, $_SESSION['id'], $nextOrderID, $customer_ID, $custName, $delivery_date);

        $i = 4;
        foreach ($_GET as $key => $value) {
            if (strpos($key, "text") === 0) {
                $productID = $_GET['text' . $i];
                $productAmt = $_GET['amount' . $i] ?? 0;

                // Fetch product price and name
                // $stmt = $conn->prepare("SELECT Product_Price, Product_Name FROM Products WHERE UserID = ? AND ProductID = ?");
                // $stmt->bind_param("ii", $_SESSION['id'], $productID);
                // $stmt->execute();
                // $stmt->bind_result($Price[$index], $productName[$index]);
                // $stmt->fetch();
                // $stmt->close();
                $nameAndPrice = fetchNameAndPrice($conn, $_SESSION['id'], $productID);

                $Quantity[$index] = $productAmt;
                // $Total[$index] = $Price[$index] * $productAmt;
                $productName[$index] = $nameAndPrice['productName'];
                $Price[$index] = $nameAndPrice['price'];
                $Total[$index] = $nameAndPrice['price'] * $productAmt;

                // Insert ordered product
                insertOrderedProduct($conn, $_SESSION['id'], $nextOrderID, $productID, $productAmt);

                $item1 = [
                    'number' => ($index + 1),
                    'ProductName' => $productName[$index],
                    'Quantity' => $Quantity[$index],
                    'Price' => $Price[$index]
                ];
                $_SESSION['cart'][] = $item1;

                $index++;
                $i += 2;
            }
        }
        $conn->close();

        // Redirect to the invoice page after order submission
        header("Location: createInvoice.php?orderID=$nextOrderID&deliveryDate=$delivery_date&custName=$custName");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Order</title>
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            background-color: rgb(249, 225, 196);
            font-size: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .pageForming {
            border: 6px solid #394b33;
            padding: 30px;
            border-radius: 15px;
            background-color: rgb(187, 168, 147);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        #upperTitle {
            border: 3px solid green;
            border-radius: 10px 30px;
            background-color: rgb(131, 152, 103);
            padding: 10px;
            margin-bottom: 20px;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="date"],
        select {
            width: 80%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="number"] {
            width: 40%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        .submit-btn {
            grid-column: span 2;
        }
        .submit-btn input[type="submit"] {
            width: 30%;
            padding: 5px;
            font-size: 15px;
            border-radius: 12px;
            background-color: #04AA6D;
            color: rgb(249, 244, 255);
            cursor: pointer;
            border: none;
        }
        .message {
            grid-column: span 2;
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
    <script>
        let addedElements = [];
        let i = 4;
        let l = i;

        function addContent(event) {
            event.preventDefault();
            let templates = document.getElementsByTagName("template");
            let clon1 = templates[0].content.cloneNode(true);
            let clon2 = templates[1].content.cloneNode(true);
            clon1.querySelector("select").setAttribute("name", "text" + i);
            clon2.querySelector("input").setAttribute("name", "amount" + i);
            document.querySelector("form").insertBefore(clon1, document.querySelector("form").children[i]);
            i++;
            document.querySelector("form").insertBefore(clon2, document.querySelector("form").children[i]);
            l = i;
            i++;
            addedElements.push(clon1);
            addedElements.push(clon2);
        }

        function removeContent(event) {
            event.preventDefault();
            if (addedElements.length > 0) {
                let lastAdded1 = addedElements.pop();
                let lastAdded2 = addedElements.pop();
                document.querySelector("form").removeChild(document.querySelector("form").children[l]);
                l--;
                document.querySelector("form").removeChild(document.querySelector("form").children[l]);
                i = l;
                l--;
            }
        }
    </script>
    <script>
        function createInvoice(){location.replace("createInvoice.php"); }
    </script>
</head>
<body>
    <template>
        <div>
            <label for="orderedProduct">Ordered Product: </label>
            <select name="OrderedProduct" id="OrderedProduct">
                <?php
                include 'db_connect.php';
                if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
                $pull_sql_product = "SELECT * FROM Products WHERE userID=?";
                $stmt = $conn->prepare($pull_sql_product);
                $stmt->bind_param("i", $_SESSION['id']);
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($each = $result->fetch_assoc()) {
                        echo "<option value=" . htmlspecialchars($each['ProductID']) . ">" . htmlspecialchars($each['Product_Name']) . "</option>";
                    }
                }else {echo '<option value="">No product found</option>'; }
                $result->free();
                $stmt->close();
                $conn->close();
                ?>
            </select>
        </div>
    </template>
    <template>
        <div>
            <label for="orderAmt">Order Amount: </label>
            <input type="number" id="orderAmt">
        </div>
    </template>
    
    <section class="pageForming">
        <section id="upperTitle">
            <h1>Customer Order</h1>   
        </section>
        <section>
            <form action="Order.php" method="GET" id="form">
                <div>
                    <label for="CustName">Customer Name:</label>
                    <select name="CustID" id="CustID">
                        <?php
                        include 'db_connect.php';
                        if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
                        $pull_sql_customer = "SELECT * FROM Customers WHERE userID=?";
                        $stmt = $conn->prepare($pull_sql_customer);
                        $stmt->bind_param("i", $_SESSION['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($each = $result->fetch_assoc()) {
                                echo "<option value=" . htmlspecialchars($each['CustomerID']) . ">" . htmlspecialchars($each['Cust_Name']) . "</option>";
                            }
                        }else {echo '<option value="">No customers found</option>'; }
                        $result->free();
                        $stmt->close();
                        $conn->close();
                        ?>
                    </select>
                </div>
                <div>
                    <label for="DeliveryDate">Delivery Date: </label>
                    <input type="date" id="DeliveryDate" name="DeliveryDate">
                </div>

                <div>
                    <button class="button" onclick="addContent(event)">+</button>  
                </div>

                <div>
                    <button class="button" onclick="removeContent(event)">-</button>  
                </div>
                
                <div class="submit-btn">
                    <input type="submit" name="submit" value="Submit">
                </div>
            </form>
                <?php if (!empty($message)) { ?>
                    <div class="message"><?php echo $message; ?></div>
                <?php } ?>
                
                <div>
                    <a href="actionOptions.php">Back to Front Page </a>
                </div>
        </section>
    </section>
</body>
</html>