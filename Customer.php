<?php

//cc
session_start();

include 'db_connect.php';

$customer_type = "";
$Date = "";
$customer_name = "";
$customer_location = "";
$contact_person = "";
$contact_number = "";
$message = "";
$nextCustomerID = "";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['submit'])) {
        $customer_type = isset($_GET['type']) ? $_GET['type'] : '';
        $Date = isset($_GET['Date']) ? $_GET['Date'] : '';
        $customer_name = isset($_GET['CustomerName']) ? $_GET['CustomerName'] : '';
        $customer_location = isset($_GET['Cuslocation']) ? $_GET['Cuslocation'] : '';
        $contact_person = isset($_GET['ContactPerson']) ? $_GET['ContactPerson'] : '';
        $contact_number = isset($_GET['ContactNum']) ? $_GET['ContactNum'] : '';

    
        $pull_sql = "SELECT * FROM Customers WHERE Cust_Name=? AND UserID=?";
        $stmt = $conn->prepare($pull_sql);
        $stmt->bind_param("si", $customer_name, $_SESSION["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        if ($result->num_rows > 0) {
            $message = "Customer already exists in the database.";
        } else {
            $stmt = $conn->prepare("SELECT COALESCE(MAX(CustomerID), 0) + 1 AS nextCustomerID FROM Customers WHERE UserID = ?");
            $stmt->bind_param("i", $_SESSION["id"]);
            $stmt->execute();
            $stmt->bind_result($nextCustomerID);
            $stmt->fetch();
            $stmt->close();

            $input_sql = "INSERT INTO Customers (UserID, CustomerID, Cust_Type, Date_Cust_Added, Cust_Name, Location, Contact_Name, Contact_Number)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $prepare = $conn->prepare($input_sql);
            $prepare->bind_param("iisssssi", $_SESSION["id"], $nextCustomerID ,$customer_type, $Date, $customer_name, $customer_location, $contact_person, $contact_number);
            
            if ($prepare->execute()) {$message = "Customer added successfully.";} 
            else { $message = "Error occurred: " . $prepare->error; }
            $prepare->close();
        }
        
        $conn->close();
    }

}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Detail</title>
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            background-color: rgb(249, 225, 196);
            font-size: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .pageForming {
            border: 6px solid #394b33;
            padding: 30px;
            border-radius: 15px;
            background-color: rgb(187, 168, 147);
            width: 80%;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #upperTitle {
            border: 3px solid green;
            border-radius: 10px 30px;
            margin-bottom: 20px;
            background-color: rgb(131, 152, 103);
            text-align: center;
            padding: 10px;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
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
            <h1>Customer Detail</h1>
        </section>
        <section>
            <form action="Customer.php" method="GET">
                <div>
                    <label for="type">Type of Customer:</label>
                    <select name="type" id="type">
                        <option value="individual">Individual</option>
                        <option value="hotel">Hotel</option>
                        <option value="company">Company</option>
                        <option value="reseller">Reseller</option>
                    </select>
                </div>
                <div>
                    <label for="Date">Date:</label>
                    <input type="date" id="Date" name="Date">
                </div>
                <div>
                    <label for="CustomerName">Customer Name:</label>
                    <input type="text" id="CustomerName" name="CustomerName">
                </div>
                <div>
                    <label for="Cuslocation">Location:</label>
                    <input type="text" id="Cuslocation" name="Cuslocation">
                </div>
                <div>
                    <label for="ContactPerson">Contact Person:</label>
                    <input type="text" id="ContactPerson" name="ContactPerson">
                </div>
                <div>
                    <label for="ContactNum">Contact Number:</label>
                    <input type="number" id="ContactNum" name="ContactNum">
                </div>
                <div>
                    <input type="submit" id="submit" name="submit" value="Submit">
                </div>
                <?php if (!empty($message)) { ?>
                    <div class="message"><?php echo $message; ?></div>
                <?php } ?>
                <br>
                <div class="message">
                    <a href="actionOptions.php">Back to Front Page </a>
                </div>
            </form>
        </section>
    </section>
</body>
</html>
