<?php
require_once './Page.php';

class Bestellung extends Page
{
    protected function __construct()
    {
        parent::__construct();
        session_start();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT * FROM article";

        $result = $this->_database->query($sql);
        if (!$result) {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $articles = array();
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
        $result->free();
        return $articles;
    }

    protected function generateView()
    {
    
        $articles =$this->getViewData();

        $this->generatePageHeader('Bestellung');
        echo "<div class='contactContainer'>";
        echo "<div id='contact-us-link' class='contactUs'><a href='https://h-da.de'><img class='contactImages' src='../../images/ContactUs.png' alt='Contact Us'></a></div>";
        echo "<div id='github-link' class='contactUs'><a href='https://code.fbi.h-da.de' target='_blank'><img class='contactImages' src='../../images/Github.png' alt='GitHub'></a></div>";
        echo "</div>";
        echo "<img src=\"../../images/banner.png\" alt='Dough Delight' class='banner'/>";
        echo "<h2 class='main-Heading'>Speisekarte</h2>";
       
        $this->pizzaManipulation($articles);
        $this->pizzaSelection($articles);
        $this->generatePageFooter();
    }

    protected function pizzaManipulation($articles)
    {
        $imageFolder = "../../images/";
        foreach ($articles as $article) {
            echo "<div id=\"pizzaImages\" data-price='{$article['price']}' data-value='{$article['article_id']}'>";
            $imageName = str_replace(' ', '_', strtolower($article['name'])) . ".jpg";
            $imagePath = $imageFolder . $imageName;
    
            if (file_exists($imagePath)) 
                echo "<img src=\"" . htmlspecialchars($imagePath) . "\" alt='{$article['name']}' class='pizza-image' width='150' height='150' data-price='{$article['price']}' />";
            else           
                echo "<img src=\"$imageFolder" . "defaultImage.jpg\" alt='No Picture Found' width='150' height='150'/>";

            echo "<p class='pizza-name'>" . htmlspecialchars($article['name']). ": ";
            echo number_format($article['price'], 2) . " €</p>";
            echo "<input type='hidden' name='singlePizzaPrice' value='{$article['price']}' />";
            echo "</div>";
        }
        echo "<div id='gesamtPreis' style='display: none;'>Gesamtpreis: 0 €</div>";
    }
    

    protected function pizzaSelection(array $articles)
    {
        echo "<form id='pizzaOrderForm' name='pizzaOrderForm' action='Bestellung.php' method='post'>";
        echo "<div>";
        echo "<label>";
        echo "<select tabindex='0' name='warenkorb[]' class='custom-select' id='pizzaSelector'>";
        echo "</select>";
        echo "</label>";
        echo "</div>";
        echo "<input type='text' value='' name='address' placeholder='Schöfferstraße 3, 64295 Darmstadt' class='address-Input'/>";
        echo "<div class=\"center-div\">";
        echo "<input tabindex='1' type='reset' name='deleteAll' value='Alle Löschen' class='buttons'/>";
        echo "<input tabindex='2' type='button' name='delete' value='Löschen' class='buttons'/>";
        echo "<input tabindex='3' type='submit' name='submitOrder' value='Bestellen' class='buttons'/>";
        echo "</div>";
        echo "</form>";
    } 



    protected function processReceivedData()
    {
        parent::processReceivedData();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        if (isset($_POST['warenkorb'], $_POST['address']) && is_array($_POST['warenkorb']) && is_string($_POST['address'])) {
            $warenkorb = $_POST['warenkorb'];
            $address = $_POST['address'];
        } 
        else 
        {
            return;
        }

        if (empty($address)) 
        {
            throw new Exception("Die Lieferadresse darf nicht leer sein.");
        }

        try 
        {
            $ordering_id = rand(1, 1000);
            $this->insertIntoOrdering($ordering_id, $address);
            foreach ($warenkorb as $individual_order) {
                $this->insertIntoOrdered($individual_order, $ordering_id);
            }
            $_SESSION['last_order_id'] = $ordering_id;
        } 
        catch (Exception $e) 
        {
            echo $e->getMessage();
            exit;
        }

        header('Location: http://localhost/Praktikum/Prak4/backend/Bestellung.php');
    }

    protected function insertIntoOrdered(string $orderItem, int $ordering_id)
    {
        $sqlOrderedArticle = "INSERT INTO ordered_article (ordered_article_id, ordering_id, article_id, status) VALUES (?, ?, ?, 0)";
        $stmtOrderedArticle = $this->_database->prepare($sqlOrderedArticle);

        if (!$stmtOrderedArticle) {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $ordered_article_id = rand(1, 1000);
        $stmtOrderedArticle->bind_param("iii", $ordered_article_id, $ordering_id, $orderItem);
        $stmtOrderedArticle->execute();

        $stmtOrderedArticle->close();
    }

    protected function insertIntoOrdering(int $ordering_id, string $address)
    {
        $sqlOrdering = "INSERT INTO ordering (ordering_id, address, ordering_time) VALUES (?, ?, NOW())";
        $stmtOrdering = $this->_database->prepare($sqlOrdering);

        if (!$stmtOrdering) {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $stmtOrdering->bind_param("is", $ordering_id, $address);
        $stmtOrdering->execute();
        $stmtOrdering->close();
    }

    public static function main()
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

Bestellung::main();

