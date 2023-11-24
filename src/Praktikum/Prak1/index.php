<?php
echo <<< INDEX
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bestellung</title>
</head>
<body>
    <h1>Bestellung</h1>
<section>
    <h2>Speisekarte</h2>
    <div>
        <img src="../images/Margherita.jpg" alt="" width="150" height="150"/>
        <p>Margherita</p>
        <p>6.00 €</p>
    </div>
    <div>
        <img src="../images/Salami.jpg" alt="" width="150" height="150"/>
        <p>Salami</p>
        <p>7.55 €</p>
    </div>
    <div>
        <img src="../images/Hawaii.jpg" alt="" width="150" height="150"/>
        <p>Hawaii</p>
        <p>6.50 €</p>
    </div>
</section>
<section>
    <h2>Warenkorb</h2>
    <form action="https://echo.fbi.h-da.de/" method="post" id="bestellungForm">
        <label>
            <select tabindex="0" name="warenkorb[]" multiple size="3">
                <option selected>Margherita Pizza</option>
                <option>Hawaii Pizza</option>
                <option>Salami Pizza</option> 
            </select>
        </label>
        <p>16.42 €</p>
        <label>
            <input type="text" value="" name="address" placeholder="Ihre Adresse"/>
        </label>
        <input type="reset" name="deleteAll" value="Alle Löschen"/>
        <input type="button" name="delete" value="Löschen"/>
        <input type="submit" value="Jetzt Bestellen"/>
    </form>
</section>
</body>
</html>
INDEX;
