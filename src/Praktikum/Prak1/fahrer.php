<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/favicon.ico">
    <title>Driver</title>
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["orderId"]) && !empty($_POST["orderId"])) {
            $orderId = $_POST["orderId"];
            echo "<h2>Deliveries to Deliver:</h2>";
            echo "<p>Order ID: $orderId</p>";

            if (isset($_POST["customerAddress"]) && !empty($_POST["customerAddress"])) {
                $customerAddress = $_POST["customerAddress"];
                echo "<p>Customer Address: $customerAddress</p>";
            }
        } else {
            echo "<h1>No deliveries to deliver yet.</h1>";
        }
    }
    ?>
</body>
</html>
