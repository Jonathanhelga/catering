<?php
session_start();
include 'db_connect.php';

$vendor = "Vendor";
$pull_sql = "SELECT Product_Name FROM Products WHERE Product_Source=?";
$stmt = $conn->prepare($pull_sql);
$stmt->bind_param("s", $vendor);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$Date = $vendor_name = $vendor_product = $contact_person = $contact_number = $message = "";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['submit'])) {
        $Date = $_GET['Date'] ?? '';
        $vendor_name = $_GET['VendorName'] ?? '';
        $vendor_product = $_GET['VendorProduct'] ?? '';
        $contact_person = $_GET['ContactPerson'] ?? '';
        $contact_number = $_GET['ContactNum'] ?? '';

        $existVendor = $conn->prepare("SELECT Product FROM Vendors WHERE Product=?");
        $existVendor->bind_param("s", $vendor_product);
        $existVendor->execute();
        $existVendor->store_result();
        if ($existVendor->num_rows() > 0) {
            $message = "Vendor for this product already exists.";
        } else {
            $stmt = $conn->prepare("SELECT COALESCE(MAX(VendorID), 0) + 1 AS nextVendorID FROM Vendors WHERE UserID = ?");
            $stmt->bind_param("i", $_SESSION["id"]);
            $stmt->execute();
            $stmt->bind_result($nextVendorID);
            $stmt->fetch();
            $stmt->close();

            $push_vendor = "INSERT INTO Vendors(UserID, VendorID, Date_Vendor_Added, Vendor_Name, Product, Contact_Person, Contact_Number) VALUES(?, ?, ?, ?, ?, ?, ?)";
            $prepare = $conn->prepare($push_vendor);
            $prepare->bind_param("iisssss", $_SESSION["id"], $nextVendorID, $Date, $vendor_name, $vendor_product, $contact_person, $contact_number);

            if ($prepare->execute()) {
                $message = "Vendor added successfully.";
            } else {
                $message = "Error occurred: " . $prepare->error;
            }
            $prepare->close();
        }
        $existVendor->close();
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Detail</title>
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            background-color: rgb(249,225,196);
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
        .date-btn {
            grid-column: span 2;
        }
        .date-btn input[type="submit"] {
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
</head>
<body>
    <section class="pageForming">
        <section id="upperTitle">
            <h1>CVendor Detail</h1>
        </section>
        <section>
            <form action="Vendor.php" method="GET">
                <div class="date-btn">
                    <label for="Date">Date:</label>
                    <input type="date" id="Date" name="Date">
                </div>
                <div>
                    <label for="VendorName">Vendor Name:</label>
                    <input type="text" id="VendorName" name="VendorName">
                </div>
                <div>
                    <label for="VendorProduct">Product:</label>
                    <select id="VendorProduct" name="VendorProduct">
                        <?php while($row = $result->fetch_assoc()): ?>
                            <option value="<?php echo $row['Product_Name']; ?>"><?php echo $row['Product_Name']; ?></option>
                        <?php endwhile; ?>
                    </select>
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
                    <input id="submit" name="submit" type="submit" value="Submit">
                </div>
            </form>
                <?php if (!empty($message)) { ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php } ?>  
                <br>
                <a  href="actionOptions.php">Back to Front Page </a>
        </section>
    </section>
</body>
</html>
