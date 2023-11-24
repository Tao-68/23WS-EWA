<?php
$orderHTML = <<<ORDER
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/favicon.ico">
    <title>Dough Delight</title>
</head>
<body>
  <h1>Bestellung</h1>
  <section id="menu">
    <h2>Speisekarte</h2>    
        <h3>Margherita Pizza : €4.99</h3>
        <img
          src="../images/Margherita.jpg"
          title="Leckere Margherita Pizza mit Tomaten und Mozzarella."
          alt="Unsere Margherita Pizza"
          width="150"
          height="150"
        >
        <h3>Salami Pizza : €7.99</h3>
        <img
          src="../images/Salami.jpg"
          title="Leckere Salami Pizza mit Salami, Mozzarella und Tomatensoße."
          alt="Unsere Salami Pizza"
          width="150"
          height="150"
        >
        <h3>Hawaii Pizza : €5.99</h3>
        <img
          src="../images/Hawaii.jpg"
          title="Leckere Hawaii Pizza mit Champignon, Ananas, Zwiebeln und Mozzarella."
          alt="Unsere Hawaii Pizza"
          width="150"
          height="150"
        >
  </section>
  <section id="warenkorb">
    <h3>Warenkorb</h3>
    <form name="orderOptions" action="bäcker.php" method="post" target="_blank">
        <select name="optionsToOrder[]" size="3" multiple>
          <option value="optionMargherita">Margherita Pizza</option>
          <option value="optionSalami">Salami Pizza</option>
          <option value="optionHawaii">Hawaii Pizza</option>
        </select>
        <p>Gesamt Preis : 14.50€</p>
      <input type="text" name="customerAddress" placeholder="Ihre Addresse">
      <section id="buttons">
        <button id="deleteChoice">Auswahl löschen</button>
        <button id="deleteAllChoices">Alle löschen</button>
        <button id="orderOptions" type="submit" value="Bestellen"> Jetzt Bestellen </button>
      </section>
    </form>
  </section>
</body>
</html>
ORDER;

echo $orderHTML;
?>