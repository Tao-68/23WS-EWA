<?php
require_once "./Page.php";

class Fahrer extends Page
{
    protected function __constructor()
    {
        //initiate the DB connection
        parent::__construct();
    }

    public function __destructor()
    {
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT ordered_article.ordered_article_id, ordered_article.article_id, ordered_article.ordering_id, ordered_article.status, ordering.address, article.name, article.price
        FROM ordered_article
        LEFT JOIN ordering ON ordered_article.ordering_id = ordering.ordering_id
        LEFT JOIN article ON ordered_article.article_id = article.article_id
        WHERE ordered_article.status >= 2";

        $result = $this->_database->query($sql);
        if (!$result)
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);

        $orderedArticles = array();
        while ($row = $result->fetch_assoc()) 
        {
            $orderedArticles[] = $row;
        }
        $result->free();

        $bestellungen = array();
        foreach ($orderedArticles as $row) 
        {
            $bestellungen[$row["ordering_id"]][] = $row;
        }
        return $bestellungen;
    }

    protected function processReceivedData()
    {
        parent::processReceivedData();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        {
            return;
        }

        if (isset($_POST['ordering_id']) && is_numeric($_POST['ordering_id'])) 
        {
            $ordering_id = $_POST['ordering_id'];
        } 
        else 
        {
            return;
        }

        if (isset($_POST['status']) && is_numeric($_POST['status'])) 
        {
            $status = $_POST['status'];
        }
        else 
        {
            return;
        }

        header('Location: http://localhost/Praktikum/Prak2/Fahrer.php');
    }

    protected function generateView()
    {
        $bestellungen = $this->getViewData();
        header("Refresh: 5; url=http://localhost/Praktikum/Prak2/Fahrer.php");
        $this->generatePageHeader('Fahrer');

        echo"<h1>Fahrer</h1>";
        
        if (sizeof($bestellungen) == 0) 
        {
            echo "<div style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen</h1>";
            echo "<img src=\"../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            return;
        }

        foreach ($bestellungen as $orderedArticles)
        {
            $price = array_reduce($orderedArticles, function ($value, $i) 
            {
                $value += $i['price'];
                return $value;
            }, 0);
        
            echo "<h3>Order #{$orderedArticles[0]["ordering_id"]}: {$orderedArticles[0]['address']}.</h3>";
            echo "<h3> Summe: {$price} EURO</h3>";
        
            echo "<form action=\"Fahrer.php\" method=\"post\">";
            foreach ($orderedArticles as $orderedArticle) 
            {
                echo "<section>";
                echo "<h5>Nummer {$orderedArticle["ordered_article_id"]}: Pizza {$orderedArticle["name"]}</h5>";
                echo "<p>Status:</p>";
        
                $isChecked = $orderedArticle['status'] == 2 ? 'checked' : null;
                echo <<<EOT
                <label>
                    <input type="radio" name="status[{$orderedArticle['ordered_article_id']}]" value=2 {$isChecked} /> 
                    Warte zum liefern
                </label>
                EOT;
        
                $isChecked = $orderedArticle['status'] == 3 ? 'checked' : null;
                echo <<<EOT
                <label>
                    <input type="radio" name="status[{$orderedArticle['ordered_article_id']}]" value=3 {$isChecked} /> 
                    Unterwegs
                </label>
                EOT;
        
                echo "</section>";
                echo "<br>";
            }
        
            echo "<input type='hidden' name='ordering_id' value={$orderedArticles[0]["ordering_id"]} />";
            echo "<input type=\"submit\" value=\"Aktualisieren\"/>";
            echo "</form>";
        }

        $this->generatePageFooter();
    }

    public static function main()
    {
        try 
        {
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } 
        catch (Exception $e)
        {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Fahrer::main();

