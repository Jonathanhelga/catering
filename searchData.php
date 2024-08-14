<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $username = $_SESSION["username"];
    $userID = $_SESSION["id"];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Search</title>
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            background-color: rgb(249, 225, 196);
            font-size: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align items to the top */
            min-height: 100vh; /* Minimum height to allow content to grow */
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
            margin-top: 20px; /* Optional: adds space from the top */
        }
        #upperTitle {
            border: 3px solid green;
            border-radius: 10px 30px;
            background-color: rgb(131, 152, 103);
            padding: 10px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="date"] {
            font-size: 100%;
            width: 60%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select {
            width: 60%;
            height: 25px;
            font-size: 100%;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .grid-container2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .grid-container3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
        }
        .btn {
            grid-column: span 2;
        }
        .btn input[type="submit"] {
            width: 30%;
            padding: 5px;
            font-size: 15px;
            border-radius: 12px;
            background-color: #04AA6D;
            color: rgb(249, 244, 255);
            cursor: pointer;
            border: none;
        }
        #results {
            margin-top: 20px;
        }
        #results table {
            margin: 15px auto; /* Centers the table horizontally */
            border-collapse: collapse;
        }
        #results table, #results th, #results td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const element = document.getElementById("option");
        const searchOptions = document.getElementById("searchOptions");
        
        element.addEventListener('change', updatePage);
        
        function updatePage() {
            searchOptions.innerHTML = ''; // Clear previous content
            const template = document.getElementById(element.value).content.cloneNode(true);
            searchOptions.appendChild(template);
            let headers = [];

            if (element.value === "customerTemplate") { 
                headers = [
                        { display: "Customer Type", value: "Cust_Type" },
                        { display: "Customer Name", value: "Cust_Name" },
                        { display: "Location", value: "Location" },
                        { display: "Contact Name", value: "Contact_Name" },
                        { display: "Contact Number", value: "Contact_Number" }
                ];
                showAll(headers,'customerTemplate');
                addInputListener(headers, 'customerTemplate'); 
            }
            else if (element.value === "orderTemplate") { 
                headers = [
                        { display: "Customer Name", value: "Cust_Name" },
                        { display: "Order Date", value: "Order_Date" },
                        { display: "Delivery Date", value: "Delivery_Date" },
                        { display: "Product Name", value: "Product_Name" },
                        { display: "Product Amount", value: "Product_Amt" },
                        { display: "Total", value: "Total_Price" }
                ];
                showAll(headers,'orderTemplate');
                addInputListener(headers, 'orderTemplate');
            } 
            else if (element.value === "vendorTemplate") { 
                headers = [
                        { display: "Vendor Name", value: "Vendor_Name" },
                        { display: "Product", value: "Product" },
                        { display: "Contact Person", value: "Contact_Person" },
                        { display: "Contact Number", value: "Contact_Number" }
                ];
                showAll(headers,'vendorTemplate');
                addInputListener(headers, 'vendorTemplate');
            } 
            else if (element.value === "productTemplate") { 
                headers = [
                        { display: "Product Source", value: "Product_Source" },
                        { display: "Product Name", value: "Product_Name" },
                        { display: "Product Price", value: "Product_Price" }
                ];
                showAll(headers,'productTemplate');
                addInputListener(headers, 'productTemplate');
            }
        }

        function showAll(headers, template) {
            fetch(`search.php?template=${template}`)
            .then(response => response.json())
            .then(data => {
                let resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = '';
                const tbl = document.createElement("table");
                const tblBody = document.createElement("tbody");
                
                // Create header row
                const headerRow = document.createElement('tr');
                headers.forEach(header => {
                    const headerCell = document.createElement("th");
                    const headerTextNode = document.createTextNode(header.display);
                    headerCell.appendChild(headerTextNode);
                    headerRow.appendChild(headerCell);
                });
                tblBody.appendChild(headerRow);

                // Create data rows
                data.forEach(item => {
                    const newRow = document.createElement('tr');
                    headers.forEach(header => {
                        const newCell = document.createElement('td');
                        const cellText = item[header.value]; // Access the item property by header value
                        const textNode = document.createTextNode(cellText);
                        newCell.appendChild(textNode);
                        newRow.appendChild(newCell);
                    });
                    tblBody.appendChild(newRow);
                });

                tbl.appendChild(tblBody);
                resultsDiv.appendChild(tbl);
            })
            .catch(error => console.error('Error:', error));
        }

        function addInputListener(headers,template) {
            const inputs = document.querySelectorAll('#searchOptions input');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    const params = { template: template };
                    inputs.forEach(input => { params[input.id] = input.value; });
                    
                    const queryString = new URLSearchParams(params).toString();
                    fetch(`search.php?${queryString}`)
                        .then(response => response.json())
                        .then(data => {
                            let resultsDiv = document.getElementById('results');
                            resultsDiv.innerHTML = '';
                            const tbl = document.createElement("table");
                            const tblBody = document.createElement("tbody");
                            
                            // Create header row
                            const headerRow = document.createElement('tr');
                            headers.forEach(header => {
                                const headerCell = document.createElement("th");
                                const headerTextNode = document.createTextNode(header.display);
                                headerCell.appendChild(headerTextNode);
                                headerRow.appendChild(headerCell);
                            });
                            tblBody.appendChild(headerRow);

                            // Create data rows
                            data.forEach(item => {
                                const newRow = document.createElement('tr');
                                headers.forEach(header => {
                                    const newCell = document.createElement('td');
                                    const cellText = item[header.value]; // Access the item property by header value
                                    const textNode = document.createTextNode(cellText);
                                    newCell.appendChild(textNode);
                                    newRow.appendChild(newCell);
                                });
                                tblBody.appendChild(newRow);
                            });

                            tbl.appendChild(tblBody);
                            resultsDiv.appendChild(tbl);
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        }
        updatePage(); // Initial call to display default option
    });
    </script>
    <script>
        function backToFront() {
            location.replace("actionOptions.php");
        }
    </script>
</head>
<body>
    <template id="customerTemplate">
        <label for="custName">Customer Name:</label>
        <input type="text" id="custName">
    </template>
    <template id="orderTemplate">
        <div class="grid-container3">
            <div>
                <label for="custName">Customer Name:</label>
                <input type="text" id="custName">
            </div>
            <div>
                <label for="orderDate">Order Date:</label>
                <input type="date" id="orderDate">
            </div>
            <div>
                <label for="deliveryDate">Delivery Date:</label>
                <input type="date" id="deliveryDate">
            </div>
        </div>
    </template>
    <template id="vendorTemplate">
        <div class="grid-container2">
            <div>
                <label for="vendorName">Vendor Name:</label>
                <input type="text" id="vendorName">
            </div>
            <div>
                <label for="vendorProduct">Vendor Product:</label>
                <input type="text" id="vendorProduct">
            </div>
        </div>
    </template>
    <template id="productTemplate">
        <label for="productName">Product Name:</label>
        <input type="text" id="productName">
    </template>

    <section class="pageForming">
        <section id="upperTitle">
            <h1>Data Search</h1>
        </section>
        <section id="content">
            <div>
                <select id="option" name="option">
                    <option value="customerTemplate">Customers</option>
                    <option value="orderTemplate">Orders</option>
                    <option value="vendorTemplate">Vendors</option>
                    <option value="productTemplate">Products</option>
                </select>
            </div>
            <div id="searchOptions"></div>
        </section>
        <section id="results"></section>
        <div class="btn">
            <input type="submit" value="Back to Front Page" onclick="backToFront()">
        </div>
    </section>
</body>
</html>
