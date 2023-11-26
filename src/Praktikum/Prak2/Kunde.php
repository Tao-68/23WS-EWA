<?php 
require_once "./Page.php";

class Kunde extends Page
{
    protected function __construct()
    {
        //initiate the DB connection
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }
    
    protected function getViewData()
    {
        $sql = "SELECT ordered_article.ordered_article_id, ordered_article.article_id, ordered_article.ordering_id, ordered_article.status, article.name FROM ordered_article LEFT JOIN article ON ordered_article.article_id = article.article_id";
       
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

    protected function generateView()
    {
        $orderedArticles = $this->getViewData();
        //var_dump($orderedArticles);
        header("Refresh: 5; url=http://localhost/Praktikum/Prak2/Kunde.php");
        $this->generatePageHeader('Kunde');

        echo "<h1>Lieferstatus: </h1>";

        if (sizeof($orderedArticles) == 0) 
        {
            echo "<div style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen</h1>";
            echo "<img src=\"../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            return;
        }

        $groupedOrders = array();
        foreach ($orderedArticles as $orderedArticle) {
            $status = intval($orderedArticle['status']);
            $orderId = $orderedArticle["ordering_id"];
            $pizzaName = $orderedArticle["name"];
        
            switch ($status) {
                case 0:
                    $statusText = "Zubereitung";
                    break;
                case 1:
                    $statusText = "Im Öfen";
                    break;
                case 2:
                    $statusText = "Fertig";
                    break;
                case 3:
                    $statusText = "Warte auf Abholung";
                    break;
                case 4:
                    $statusText = "Unterwegs";
                    break;
                default:
                    $statusText = "Unknown";
                    break;
            }
        
            if (!isset($groupedOrders[$orderId]))
            {
                $groupedOrders[$orderId] = array();
            }
        
            $groupedOrders[$orderId][] = array(
                'pizzaName' => $pizzaName,
                'statusText' => $statusText
            );
        }
        
        foreach ($groupedOrders as $orderId => $orders) 
        {
            echo "<p>Order #{$orderId}:</p>";      
            foreach ($orders as $order) 
            {
                echo "<p>Pizza {$order['pizzaName']}</p>";
                echo "<p>Status: {$order['statusText']}</p>";
            }
            echo "<br>";
        }
        $this->generatePageFooter();
    }

    protected function processReceivedData()
    {
        parent::processReceivedData();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        {
            return;
        }

        if (isset($_POST['ordered_article_id']) && is_numeric($_POST['ordered_article_id'])) 
        {
            $id = $_POST['ordered_article_id'];
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

        header('Location: http://localhost/Praktikum/Prak2/Kunde.php');
    }

    public static function main()
    {
        try 
        {
            $page = new Kunde();
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

Kunde::main();

