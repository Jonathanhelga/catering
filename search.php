<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and session ID is set
if (!isset($_SESSION["id"])) {
    echo json_encode([]);
    exit;
}

$template = $_GET['template'] ?? '';
$userID = $_SESSION["id"];
$params = $_GET;

$query = "";
$paramsArray = [];

if ($template === "customerTemplate") {
    $custName = $params['custName'] ?? '';
    $query = "SELECT Cust_Type, Cust_Name, Location, Contact_Name, Contact_Number FROM customers WHERE UserID = ?";
    $paramsArray[] = $userID;

    if (!empty($custName)) {  
        $query .= " AND Cust_Name LIKE ?";
        $paramsArray[] = "%$custName%";
    }
} 
elseif ($template === "orderTemplate") {    
    $custName = $params['custName'] ?? '';
    $orderDate = $params['orderDate'] ?? '';
    $deliveryDate = $params['deliveryDate'] ?? '';
    $query = "SELECT 
                o.OrderID, 
                o.Cust_Name, 
                o.Order_Date, 
                o.Delivery_Date, 
                p.Product_Name, 
                op.Product_Amt, 
                (op.Product_Amt * p.Product_Price) AS Total_Price
            FROM Customers c
            JOIN Orders o ON o.UserID = c.UserID AND o.Cust_Name = c.Cust_Name
            JOIN Ordered_Product op ON o.UserID = op.UserID AND o.OrderID = op.OrderID
            JOIN Products p ON op.UserID = p.UserID AND op.ProductID = p.ProductID
            WHERE o.UserID = ?";
    $paramsArray[] = $userID;

    if (!empty($custName)) {
        $query .= " AND o.Cust_Name LIKE ?";
        $paramsArray[] = "%$custName%";
    }
    if (!empty($orderDate)) {
        $query .= " AND o.Order_Date = ?";
        $paramsArray[] = $orderDate;
    }
    if (!empty($deliveryDate)) {
        $query .= " AND o.Delivery_Date = ?";
        $paramsArray[] = $deliveryDate;
    }
} 
elseif ($template === "vendorTemplate") {
    $vendorName = $params['vendorName'] ?? '';
    $vendorProduct = $params['vendorProduct'] ?? '';
    $query = "SELECT Vendor_Name, Date_Vendor_Added, Product, Contact_Person, Contact_Number FROM vendors WHERE UserID = ?";
    $paramsArray[] = $userID;

    if (!empty($vendorName)) {
        $query .= " AND Vendor_Name LIKE ?";
        $paramsArray[] = "%$vendorName%";
    }
    if (!empty($vendorProduct)) {
        $query .= " AND Product LIKE ?";
        $paramsArray[] = "%$vendorProduct%";
    }
} 
elseif ($template === "productTemplate") {
    $productName = $params['productName'] ?? '';
    $query = "SELECT Product_Source, Product_Name, Product_Price FROM products WHERE UserID = ?";
    $paramsArray[] = $userID;

    if (!empty($productName)) {
        $query .= " AND Product_Name LIKE ?";
        $paramsArray[] = "%$productName%";
    }
}

if ($query !== "") {
    $stmt = $conn->prepare($query);

    // Dynamically determine types
    $types = str_repeat('s', count($paramsArray));
    $stmt->bind_param($types, ...$paramsArray);

    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
} else {
    $data = [];
}

echo json_encode($data);
$conn->close();
