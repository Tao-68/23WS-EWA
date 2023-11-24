<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/favicon.ico">
    <title>Baker</title>
</head>
<body>
    <?php
    $pizzaOptions = [
        "optionMargherita" => "Margherita Pizza",
        "optionSalami" => "Salami Pizza",
        "optionHawaii" => "Hawaii Pizza"
    ];

<<<<<<< HEAD
=======
    // Generate a unique order ID
>>>>>>> main
    $orderId = "Order#" . uniqid();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["optionsToOrder"]) && !empty($_POST["optionsToOrder"])) {
            echo "<h1>Order ID: $orderId</h1>";
            echo "<h2>Selected Pizzas:</h2>";
            foreach ($_POST["optionsToOrder"] as $selectedOption) {
                $pizzaName = isset($pizzaOptions[$selectedOption]) ? $pizzaOptions[$selectedOption] : "Unknown Pizza";
                echo "<p>$pizzaName</p>";
            }
            echo '<button>Start Baking</button>';
        } else {
            echo "<h1>No pizzas ordered yet.</h1>";
        }
    }
    ?>
    <form name="deliveryForm" action="fahrer.php" method="post" target="_blank">
        <?php
        if (isset($_POST["optionsToOrder"])) {
            foreach ($_POST["optionsToOrder"] as $selectedOption) {
                echo "<input type='hidden' name='deliveries[]' value='$selectedOption'>";
            }
            echo "<input type='hidden' name='orderId' value='$orderId'>";
        }

        if (isset($_POST["customerAddress"]) && !empty($_POST["customerAddress"])) {
            $customerAddress = $_POST["customerAddress"];
            echo "<input type='hidden' name='customerAddress' value='$customerAddress'>";
        }
        ?>
        <button type="submit" name="abbrechen">Abbrechen</button>
        <button type="submit" name="fertig">Fertig</button>
    </form>
</body>
</html>
