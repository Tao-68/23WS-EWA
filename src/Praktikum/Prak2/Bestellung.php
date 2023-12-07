<?php
require_once './Page.php';
error_reporting(E_ALL);

class Bestellung extends Page
{
    protected function __construct()
    {
        //initiate the DB connection
        parent::__construct();
    }

    public function __destruct()
    {
        //close the DB connection
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT * FROM article";

        $result = $this->_database->query($sql);
        if (!$result)
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        
        $articles = array();
        while ($row = $result->fetch_assoc()) 
        {
            $articles[] = $row;
        }
        $result->free();
        return $articles;
    }

    protected function generateView()
    {        
        $articles = $this->getViewData();
        $this->generatePageHeader('Bestellung');
        echo "<h2>Speisekarte</h2>";
        $imageFolder = "../images/";
        foreach ($articles as $article) 
        {
            echo "<div>";
            $imageName = str_replace(' ', '_', strtolower($article['name'])) . ".jpg";
            $imagePath = $imageFolder . $imageName;
    
            if (file_exists($imagePath))            
                echo "<img src=\"" . htmlspecialchars($imagePath) . "\" alt='{$article['name']}' width='150' height='150'/>";        
            else           
                echo "<img src=\"$imageFolder" . "defaultImage.jpg\" alt='No Picture Found' width='150' height='150'/>";
        
            echo "<p>{$article['name']}: {$article['price']} €</p>";
            echo "</div>";
        }
    
        $this->pizzaSelection($articles);
        $this->generatePageFooter();
    }

    protected function pizzaSelection(array $articles)
    {
        echo <<<EOT
        <form action='Bestellung.php' method='post'>
        <div>
        <select tabindex='0' name='warenkorb[]' multiple size='3'>
        EOT;
    
        foreach ($articles as $article) 
        {
            echo "<option  value={$article['article_id']}>" . $article['name'] . "</option>";   
        }
    
        echo <<<EOT
        </select>
        </div>
        <input type='text' value='' name='address' placeholder='Ihre Adresse'/>
        <div id='buttons'>
        <input tabindex='1' type='reset' name='deleteAll' value='Alle Löschen'/>
        <input tabindex='2' type='button' name='delete' value='Löschen'/>
        <input tabindex='3' type='submit' value='Bestellen'/>
        </div>
        </form>
    EOT;
    }

    protected function processReceivedData()
    {
        parent::processReceivedData();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        {
            return;
        }

        if (isset($_POST['warenkorb']) && is_array($_POST['warenkorb']))
        {
            $warenkorb = $_POST['warenkorb'];
           // print_r($warenkorb);
        } 
        else 
        {
            return;
        }

        if (isset($_POST['address']) && is_string($_POST['address'])) 
        {
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
            //if the PK already exists, the insert will fail, this still needs to be handled
            $ordering_id = rand(1, 1000);
            $this->insertIntoOrdering($ordering_id, $address);
            foreach($warenkorb as $individual_order)
            {    
                $this->insertIntoOrdered($individual_order, $ordering_id);
            }   
        } 
        catch (Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
        //redirect to the same page to prevent resubmission
        header('Location: http://localhost/Praktikum/Prak2/Bestellung.php');
    }

    protected function insertIntoOrdered(string $orderItem, int $ordering_id)
    {
        $sqlOrderedArticle = "INSERT INTO ordered_article (ordered_article_id, ordering_id, article_id, status) VALUES (?, ?, ?, 0)";
        $stmtOrderedArticle = $this->_database->prepare($sqlOrderedArticle);    
        if (!$stmtOrderedArticle) 
        {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $ordered_article_id = rand(1, 1000);
        $stmtOrderedArticle->bind_param("iii", $ordered_article_id, $ordering_id, $orderItem); //using $orderItem works because when we submitted the form, we submitted an array of the field "value" ("<option  value={$article['article_id']}>") which was assigned as numbers up above
        $stmtOrderedArticle->execute();

        $stmtOrderedArticle->close();
    }

    protected function insertIntoOrdering(int $ordering_id, string $address)
    {
        $sqlOrdering = "INSERT INTO ordering (ordering_id, address, ordering_time) VALUES (?, ?, NOW())";
        $stmtOrdering = $this->_database->prepare($sqlOrdering);
        if (!$stmtOrdering) 
        {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $stmtOrdering->bind_param("is", $ordering_id, $address);
        $stmtOrdering->execute();
        $stmtOrdering->close();
    }


    public static function main()
    {
        try 
        {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } 
        catch (Exception $e) 
        {
            echo $e->getMessage();
        }
    }
}

Bestellung::main();
