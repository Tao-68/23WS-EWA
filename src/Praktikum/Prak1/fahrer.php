<?php
echo <<<FAHRER
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <title>Fahrer</title>
</head>
<body>
<section id="Bestellungen">
    <form action="https://echo.fbi.h-da.de/" id="FahrerStatus" method="post">
        <p>Order ID: Order#2442</p>
        <p>Adresse: Darmstadt 123 </p>
        <p>Status:</p>
        <input type="radio" id="fertig" name="FahrerStatus" value="fertig">
        <label for="fertig">Fertig</label>
        <br>
        <input type="radio" id="fertig" name="FahrerStatus" value="unterwegs">
        <label for="unterwegs">Unterwegs</label>
        <br>
        <input type="radio" id="geliefert" name="FahrerStatus" value="geliefert">
        <label for="geliefert">Geliefert</label>
        <br>
        <input type="submit" value="OK">
    </form>
</section>
</body>
</html>
FAHRER;
