<?php
echo <<< INDEX
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bestellung</title>
</head>
<body>
    <section id="menuSection">
        <h2>Speisekarte</h2>
        <img src="../images/Margherita.jpg" alt="" width="150" height="150"/>
        <p>Margherita : 6.00 €</p>
        <img src="../images/Salami.jpg" alt="" width="150" height="150"/>
        <p>Salami: 7.55 €</p>
        <img src="../images/Hawaii.jpg" alt="" width="150" height="150"/>
        <p>Hawaii : 6.50 €</p>
    </section>

    <section id="warenkobSection">
        <h2>Warenkorb</h2>
        <form action="https://echo.fbi.h-da.de/" method="post" id="bestellungForm"> 
            <select tabindex="0" name="warenkorb[]" multiple size="3">
                <option selected>Margherita Pizza</option>
                <option>Hawaii Pizza</option>
                <option>Salami Pizza</option> 
            </select>
            <p>Total Price : 16.42 €</p>
            <input type="text" value="" name="address" placeholder="Ihre Adresse"/>
            <div id="buttons">    
            <input type="reset" name="deleteAll" value="Alle Löschen"/>
            <input type="button" name="delete" value="Löschen"/>
            <input type="submit" value="Jetzt Bestellen"/>
            </div>
        </form>
    </section>
</body>
</html>
INDEX;
