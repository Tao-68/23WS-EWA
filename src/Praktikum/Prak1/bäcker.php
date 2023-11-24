<?php
echo <<<BACKER
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Baecker</title>
</head>
<body>
<header>
    <h1>Bestellung</h1>
</header>
<section>
    <h2>Order#2422 : Pizza Hawaii</h2>
    <form action="https://echo.fbi.h-da.de/" method="post" id="baeckerForm" accept-charset="UTF-8">
        <div>
                <input type="radio" name="EssenStatus" id ="Öfen" value="im oefen">
                <label for="EssenStatus"> Im Öfen </label>
                <br>        
                <input type="radio" name="EssenStatus" id="bereit" value="bereit">
                <label for="EssenStatus"> Bereit </label>
                <br>
        </div>
        <input type="submit" value="Aktualisieren"/>
    </form>
</section>
</body>
</html>
BACKER;
