<?php 
session_start();

if(isset($_POST['logout'])){
    session_destroy();
    header('location:login.php');
    exit();
}

require "Connection.php";

$inspectorName = $_SESSION['inspectorloginid'];

$sql = "SELECT ItemNumber, Description, Vendor, PONumber 
        FROM `product` 
        WHERE `inspectorname` = '$inspectorName' 
        ORDER BY `inspectorname` DESC 
        LIMIT 1";
        
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    $row = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspector Panel</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="generateReport.js" defer></script>
    <script src="liveLocationTracker.js" defer></script> <!-- Include liveLocationTracker.js -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .header form {
            margin-left: auto;
            margin-right: 20px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        .styled-table th {
            background-color: #333;
            color: white;
        }

        input[type="submit"],
        button {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #555;
        }

        #location {
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #location p {
            margin: 0;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 18px;
            }

            form {
                padding: 15px;
            }

            table {
                font-size: 14px;
            }

            input[type="submit"],
            button {
                padding: 8px 16px;
                font-size: 14px;
            }

            .header {
                flex-direction: column;
            }

            .header form {
                margin: 10px 0;
            }
        }

        @media (max-width: 480px) {
            form {
                padding: 10px;
            }

            input[type="text"],
            input[type="file"] {
                padding: 8px;
            }

            table {
                font-size: 12px;
            }

            input[type="submit"],
            button {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="header" style="display: flex;">
        <h1>Welcome To Inspector Panel - <?php echo $_SESSION['inspectorloginid']?> </h1>
        <form method="POST">
            <button name="logout">Log Out</button>
        </form>
    </div>

    <?php if ($row): ?>
        <form method="POST" action="uploadinspectiondetail.php" enctype="multipart/form-data">
            <h1>Confirm Details Below Before Filling Form :</h1>
            
            <label for="itemnumber">Item Number:</label>
            <input type="text" id="itemnumber" name="item_number" required>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" required>
        
            <label for="vendor">Vendor:</label>
            <input type="text" id="vendor" name="vendor" required>
        
            <label for="ponumber">PO Number:</label>
            <input type="text" id="ponumber" name="po_number" required>

            <table border='1' class="styled-table">
                <tr>
                    <th>Field</th>
                    <th>Details</th>
                    <th>Upload Picture</th>
                    <th>Right</th>
                    <th>Wrong</th>
                </tr>
                
                <!-- Existing rows -->
                <tr>
                    <td>ItemNumber</td>
                    <td><?php echo $row['ItemNumber']; ?></td>
                    <td></td>
                    <td><input type="checkbox" name="right_item"></td>
                    <td><input type="checkbox" name="wrong_item"></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><?php echo $row['Description']; ?></td>
                    <td></td>
                    <td><input type="checkbox" name="right_desc"></td>
                    <td><input type="checkbox" name="wrong_desc"></td>
                </tr>
                <tr>
                    <td>Vendor</td>
                    <td><?php echo $row['Vendor']; ?></td>
                    <td></td>
                    <td><input type="checkbox" name="right_vendor"></td>
                    <td><input type="checkbox" name="wrong_vendor"></td>
                </tr>
                <tr>
                    <td>PONumber</td>
                    <td><?php echo $row['PONumber']; ?></td>
                    <td></td>
                    <td><input type="checkbox" name="right_po"></td>
                    <td><input type="checkbox" name="wrong_po"></td>
                </tr>

                <!-- New rows with Upload Buttons -->
                <!-- Row for Product Details -->
                <tr>
                    <td>Product Details</td>
                    <td>
                        <ul>
                            <li>1st Picture: Front View</li>
                            <li>2nd Picture: Back View</li>
                            <li>3rd Picture: Side View</li>
                        </ul>
                    </td>
                    <td>
                        <input type="file" id="product_front_view" name="product_front_view" accept="image/*"><br>
                        <input type="file" id="product_back_view" name="product_back_view" accept="image/*"><br>
                        <input type="file" id="product_side_view" name="product_side_view" accept="image/*">
                    </td>
                    <td><input type="checkbox" name="right_product_details"></td>
                    <td><input type="checkbox" name="wrong_product_details"></td>
                </tr>

                <!-- Row for Gift Box Packaging -->
                <tr>
                    <td>Gift Box Packaging</td>
                    <td>1st Picture: Front View</td>
                    <td><input type="file" id="gift_box_front_view" name="gift_box_front_view" accept="image/*"></td>
                    <td><input type="checkbox" name="right_gift_box"></td>
                    <td><input type="checkbox" name="wrong_gift_box"></td>
                </tr>

                <!-- Row for Barcodes -->
                <tr>
                    <td>Barcodes</td>
                    <td>
                        <ul>
                            <li>1st Picture: Product Barcode</li>
                            <li>2nd Picture: Inner Box Barcode</li>
                            <li>3rd Picture: Outer Box (Cardboard) Barcode</li>
                        </ul>
                    </td>
                    <td>
                        <input type="file" id="product_barcode" name="product_barcode" accept="image/*"><br>
                        <input type="file" id="inner_box_barcode" name="inner_box_barcode" accept="image/*"><br>
                        <input type="file" id="outer_box_barcode" name="outer_box_barcode" accept="image/*">
                    </td>
                    <td>
                        <input type="checkbox" name="product_barcode_confirmed"> Product Barcode Confirmed<br>
                        <input type="checkbox" name="inner_box_barcode_confirmed"> Inner Box Barcode Confirmed<br>
                        <input type="checkbox" name="outer_box_barcode_confirmed"> Outer Box Barcode Confirmed
                    </td>
                    <td><input type="checkbox" name="wrong_barcode"></td>
                </tr>

                <!-- Row for Inner Box/Polybag -->
                <tr>
                    <td>Inner Box/Polybag</td>
                     <td>1st Picture: Front View</td>
                    <td><input type="file" id="inner_box_front_view" name="inner_box_front_view" accept="image/*"></td>
                    <td><input type="checkbox" name="right_inner_box"></td>
                    <td><input type="checkbox" name="wrong_inner_box"></td>
                </tr>

                <!-- Row for Master Carton -->
                <tr>
                    <td>Master Carton</td>
                    <td>1st Picture: Front View</td>
                    <td><input type="file" id="master_carton_front_view" name="master_carton_front_view"  accept="image/*"></td>

                    <td><input type="checkbox" name="right_master_carton"></td>
                    <td><input type="checkbox" name="wrong_master_carton"></td>
                </tr>
            </table>
            <input type="submit" value="Submit" name="submit">
            <button id="generateReport">Generate Project Report</button>
        </form>

        <!-- Location Display -->
        <div id="location">
            <p>Latitude: <span id="latitude">Not Available</span></p>
            <p>Longitude: <span id="longitude">Not Available</span></p>
        </div>

    <?php else: ?>                                                  
        <p>No data found for the inspector: <?php echo $inspectorName; ?></p>
    <?php endif; ?>
</body>
</html>
