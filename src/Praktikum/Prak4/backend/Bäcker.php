<?php
require_once './Page.php';

class Baecker extends Page
{
    protected function __construct()
    {
        // initiate the DB connection
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT ordered_article_id, ordering_id, ordered_article.article_id, status , article.name FROM ordered_article LEFT JOIN article ON ordered_article.article_id = article.article_id";
        $result = $this->_database->query($sql);
        if (!$result)
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);

        $orderedArticles = array();
        while ($row = $result->fetch_assoc())
        {
            $orderedArticles[] = $row;
        }
        $result->free();
        return $orderedArticles;
    }    

    protected function processReceivedData()
    {
        parent::processReceivedData();
    
        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        {
            return;
        }

        if (isset($_POST['ordered_article_id']) && is_numeric($_POST['ordered_article_id'])) {
            $id = $_POST['ordered_article_id'];
           // echo $id;
        } 
        else 
        {
            return;
        }
    
        if (isset($_POST['food_status']) && is_numeric($_POST['food_status'])) 
        {
            $status = $_POST['food_status'];
            //echo $status;
        } 
        else 
        {
            return;
        }

        $this->updateTableOrderedArticle($id,$status);
       
        //redirect to same page after processing 
        header('Location: http://localhost/Praktikum/Prak4/backend/Bäcker.php');
    }
    
    
    protected function updateTableOrderedArticle($id, $status)
    {
        $sqlUpdateStatus = "UPDATE ordered_article SET ordered_article.status = $status WHERE ordered_article.ordered_article_id = $id";
        $stmtUpdateStatus = $this->_database->prepare($sqlUpdateStatus);
        if (!$stmtUpdateStatus)
        {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $stmtUpdateStatus->execute();
        $stmtUpdateStatus->close();
        //echo $sqlUpdateStatus;
    }

    protected function generateView()
    {
        $orderedArticles = $this->getViewData();
        header("Refresh: 5; url=http://localhost/Praktikum/Prak4/backend/B%C3%A4cker.php");
        $this->generatePageHeader('Bäcker'); 
        //echo "<body onload=\"processBacker()\">";
        echo "<body>";
        echo "<h1>Bäcker</h1>";

        $hasOrdersToBake = false;
        if (sizeof($orderedArticles) == 0) 
        {
            echo "<div style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen</h1>";
            echo "<img src=\"../../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            return;
        }
        foreach ($orderedArticles as $orderedArticle) 
        {
            $status = intval($orderedArticle['status']);
            if ($status <= 3) {
                $hasOrdersToBake = true;
                break;
            }
        }

        if (!$hasOrdersToBake) 
        {
            echo "<div style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen vorzubereiten</h1>";
            echo "<img src=\"../../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            $this->generatePageFooter();
            return;
        }
        
        foreach ($orderedArticles as $orderedArticle) 
        {
            $id = isset($orderedArticle['ordering_id']) ? $orderedArticle['ordering_id'] : '<Unknown ID>';
            $status = intval($orderedArticle['status']);
            //echo $status;
            if ($status > 3)
                continue;
        
            echo "<form id=\"backerForm{$orderedArticle['ordered_article_id']}\" action=\"Bäcker.php\" method=\"post\" data-name=\"backerForm{$orderedArticle['ordering_id']}\" >";
            
            // check if keys exist before accessing them
            $name = isset($orderedArticle['name']) ? $orderedArticle['name'] : '<Unknown Name>';
            $toSubmit= isset($orderedArticle['ordered_article_id'])? $orderedArticle['ordered_article_id']:'<Unknown ID>';

            echo "<input type='hidden' name='ordered_article_id' value=" . htmlspecialchars($toSubmit) . " />";
            echo <<<EOT
            <section>
                <h2>Order #{$id}: {$name}</h2>
                <p>Status:</p>
            EOT;

            $radioButtons = [
                0 => 'Zubereitung',
                1 => 'Im Öfen',
                2 => 'Fertig',
                3 => 'Warte zur Abholung',
            ];
            //for each key value pair in the "dictionary" radiobuttons, where 0...3 are $key and the texts are $value
            foreach ($radioButtons as $key => $value) {
                $isChecked = $status == $key ? 'checked' : ' ';
                echo <<<EOT
                    <input type="radio" name="food_status" value={$key} {$isChecked} /> 
                    <label for="food_status$key"> {$value} </label>
                EOT;
            }
            echo "</section>";
            echo "<br>";
            //echo "<input type=\"submit\" value=\"Aktualisieren\"/>";
            echo "</form>";
        }
        $this->generatePageFooter();
    }

    protected function generatePageHeader($headline = "")
    {
        $headline = htmlspecialchars($headline);
        header("Content-type: text/html; charset=UTF-8");
        echo <<<EOT
        <!DOCTYPE html>
        <html lang="de">
        <head>
            <meta charset="UTF-8">
            <title>{$headline}</title>
            <script src='../frontend/backer.js' defer></script>
            <!--link rel="stylesheet" href="../styles/bäcker.css"-->
        </head>
        EOT;
    }

    public static function main()
    {
        try
        {
            $page = new Baecker();
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

//since main is marked as static func, it doesnt need an instance of a class to be called
Baecker::main();

