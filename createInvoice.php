<?php
session_start();

$nextOrderID = isset($_GET['orderID']) ? $_GET['orderID'] : '';
$delivery_date = isset($_GET['deliveryDate']) ? $_GET['deliveryDate'] : '';
$custName = isset($_GET['custName']) ? $_GET['custName'] : '';

// Assume these arrays are passed via session or query parameters
$total = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #ddd8d8;
        }

        #invoiceForming {
            width: 10in;
            height:auto;
            max-width: 10in;
            background-color: #fff;
            padding: 20px;
            margin: 50px auto;
            border: 6px solid #394b33;
        }

        #headerForming {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        #headerFormingTitle {
            flex: 1;
        }

        #headerFormingTitle h1 {
            margin: 0;
            font-size: 1.8em;
            color: #394b33;
        }

        #headerFormingTitle h6, #headerFormingTitle h4 {
            margin: 0;
            padding: 2px 0;
            color: #555;
        }

        #headerFormingDetails {
            flex: 1;
            text-align: left;
            padding-top: 10px;
        }

        #headerFormingDetails p {
            margin: 0;
            padding: 5px 0;
            font-size: 0.9em;
        }

        #notification {
            border: 2px solid #000;
            padding: 5px;
            margin-top: 10px;
            text-align: center;
            font-size: 0.9em;
            font-weight: bold;
        }

        #tableForming table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
            font-size: 0.9em;
        }

        #tableForming th, #tableForming td {
            border: 2px solid #858585;
            padding: 10px;
            text-align: left;
        }

        #tableForming th {
            background-color: #394b33;
            color: #fff;
        }

        #lowerPartForming {
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            margin-top: 20px;
            
        }

        #combine {
            width: 60%;
            display: flex;
            justify-content: space-between;
        }

        #tandaTerima, #hormatKami {
            text-align: center;
            width: 45%;
        }

        #tandaTerima h3, #hormatKami h3 {
            padding-bottom: 45px;
            margin: 0;
        }

        #tandaTerima h4, #hormatKami h4 {
            margin: 0;
            margin-top: 10px;
            font-size: 0.9em;
            color: #555;
        }

        #totalPriceContainer {
            width: 30%;
            height: 30%;
            display: flex;
            flex-direction: row;
            align-items: center;  /* Vertically center align the text and table */
            padding: 10px;
            margin-left: 15px;
        }

        #totalPriceContainer h4 {
            margin: 0;
            margin-bottom: 5px;
            font-size: 1.2em;
            color: #394b33;
        }

        #totalPriceContainer table {
            width: 100%;
            border-collapse: collapse;
        }

        #totalPriceContainer th, #totalPriceContainer td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 5px;
        }

        .submit-btn {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .submit-btn input[type="submit"] {
            width: 150px;
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            background-color: #394b33;
            color: #fff;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .submit-btn input[type="submit"]:hover {
            background-color: #2d3c29;
        }

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            #invoiceForming, #invoiceForming * {
                visibility: visible;
            }
            #invoiceForming {
                position: absolute;
                left: 0;
                top: 0;
            }

            .submit-btn {
                display: none;
            }
        }

    </style>
</head>
<body>
    <section id="invoiceForming">
        <div id="headerForming">
            <div id="headerFormingTitle">
                <h1>House of Ndeso</h1>
                <h6>Food, Beverages & General Supplies</h6>
                <h4>CV. Bertha Makmur Abadi</h4>
                <h6>HP/WA: 083874924009</h6>
                <h6>e-mail : hardjo.soesan@gmail.com</h6>
                <p>Invoice Number: <?php echo htmlspecialchars($nextOrderID); ?></p>
            </div>
            <div id="headerFormingDetails">
                <p>DATE: <?php echo htmlspecialchars($delivery_date); ?></p>
                <p>Kepada Yth: <?php echo htmlspecialchars($custName); ?></p>
                <div id="notification">
                    <p>MOHON DIPERIKSA</p>
                    <p>(Barang yang sudah diterima tidak dapat ditukar/dikembalikan)</p>
                </div>
            </div>
        </div>

        <div id="tableForming">
            <table>
                <thead>
                    <tr id="generateTH">
                        <th style="width: 5%;">No.</th>
                        <th style="width: 40%;">Nama Barang</th>
                        <th style="width: 15%;">Quantity</th>
                        <th style="width: 20%;">Price</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $total[] = $item['Price'] * $item['Quantity'];
                                echo "<tr>";
                                echo "<td>" . $item['number'] . "</td>";
                                echo "<td>" . $item['ProductName']  . "</td>";
                                echo "<td>" . $item['Quantity']  . "</td>";
                                echo "<td>" . $item['Price']  . "</td>";
                                echo "<td>" . $item['Price'] * $item['Quantity']  . "</td>";
                                echo "</tr>";
                            }
                        }
                        else{
                            echo "<tr>";
                            echo "<td>" . "empty cart" . "</td>";
                            echo "<td>" . "empty cart"  . "</td>";
                            echo "<td>" . "empty cart"  . "</td>";
                            echo "<td>" . "empty cart"  . "</td>";
                            echo "</tr>";
                        }
                        unset($_SESSION['cart']);
                    ?>
                </tbody>
            </table>
        </div>

        <div id="lowerPartForming">
            <div id="combine">
                <div id="tandaTerima">
                    <h3>Tanda Terima</h3>
                    <p>(-------------------)</p>
                    <h4>Stempel & Nama Jelas</h4>
                </div>
                <div id="hormatKami">
                    <h3>Hormat Kami</h3>
                    <p>(-------------------)</p>
                    <h4>Nama Jelas</h4>
                </div>
            </div>
            <div id="totalPriceContainer">
                <h4>Total Price:</h4>
                <table>
                    <tr>
                        <td><?php echo htmlspecialchars(array_sum($total)); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </section>
    <div class="submit-btn">
        <button onclick="window.print()">Print Invoice</button>
    </div>
</body>
</html>
