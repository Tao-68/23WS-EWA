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
        error_reporting(E_ALL);
    
        $articles = $this->getViewData();
        $this->generatePageHeader('Bestellung');
        echo "<h1> Bestellung </h1>";
        echo "<h2>Speisekarte</h2>";
    
        $imageFolder = "../images/";
    
        foreach ($articles as $article) {
            echo "<div>";          
            $imageName = str_replace(' ', '_', strtolower($article['name'])) . ".jpg";
            $imagePath = $imageFolder . $imageName;

            if (file_exists($imagePath)) {
                echo "<img src=\"" . htmlspecialchars($imagePath) . "\" alt='{$article['name']}' width='150' height='150'/>";
            } else {
                echo "<p>Image not found</p>";
            }
    
            echo "<p>" . htmlspecialchars($article['name']) . "</p>";
            echo "<p>{$article['price']} €</p>";
            echo "</div>";
        }
    
        $this->pizzaSelection($articles);
        $this->generatePageFooter();
    }

    protected function pizzaSelection(array $articles)
    {
        echo "<form action='Bestellung.php' method='post'>";
        echo "<div>";
        echo "<label>";
        echo "<select tabindex='0' name='warenkorb[]' multiple size='3'>";

        foreach ($articles as $article) {
            echo "<option value=" . htmlspecialchars($article['article_id']) . ">" . htmlspecialchars($article['name']) . "</option>";
        }

        echo "</select>";
        echo "</label>";
        echo "</div>";
        echo "<input type='text' value='' name='address' placeholder='Ihre Adresse'/>";
        echo "<div>";
        echo "<input tabindex='1' type='reset' name='deleteAll' value='Alle Löschen'/>";
        echo "<input tabindex='2' type='button' name='delete' value='Löschen'/>";
        echo "<input tabindex='3' type='submit' value='Bestellen'/>";
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

        header('Location: http://localhost/Praktikum/Prak3/Bestellung.php');
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
?>
