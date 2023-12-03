<?php
require_once "./Page.php";

class Kunde extends Page
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
        $lastOrderID = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 0;

        $sql = "SELECT ordered_article.ordered_article_id, ordered_article.article_id, ordered_article.ordering_id, ordered_article.status, article.name 
                FROM ordered_article 
                LEFT JOIN article ON ordered_article.article_id = article.article_id
                WHERE ordered_article.ordering_id = ?";

        $stmt = $this->_database->prepare($sql);
        if (!$stmt) 
        {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $stmt->bind_param("i", $lastOrderID);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) 
        {
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);
        }

        $orderedArticles = array();
        while ($row = $result->fetch_assoc()) 
        {
            $orderedArticles[] = $row;
        }

        $stmt->close();
        $result->free();

        return $orderedArticles;
    }

    protected function generateView()
    {
        $orderedArticles = $this->getViewData();
        $this->generatePageHeader('Kunde');
        echo "<body onload=\"process()\">";
        echo "<h1>Lieferstatus: </h1>";

        if (sizeof($orderedArticles) == 0) 
        {
            echo "<div id=\"dogDiv\" style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen</h1>";
            echo "<img src=\"../../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            return;
        }

        $groupedOrders = array();
        foreach ($orderedArticles as $orderedArticle) 
        {
            $status = intval($orderedArticle['status']);
            $orderId = $orderedArticle["ordering_id"];
            $orderedArticleId = $orderedArticle["ordered_article_id"];
            $pizzaName = htmlspecialchars($orderedArticle["name"]);

            switch ($status) 
            {
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
                case 5:
                    $statusText = "Geliefert";
                    break;
                default:
                    $statusText = "Unknown Status";
                    break;
            }

            if (!isset($groupedOrders[$orderId])) 
            {
                $groupedOrders[$orderId] = array();
            }

            $groupedOrders[$orderId][] = array(
                'pizzaName' => $pizzaName,
                'statusText' => $statusText,
                'orderedArticleId' => $orderedArticleId
            );
        }

        foreach ($groupedOrders as $orderId => $orders)
        {
            echo "<div id='order_{$orderId}' data-name='order_{$orderId}' class='orderContainer'>";         
            foreach ($orders as $order) 
            {
                echo "<p>Order #{$orderId}:</p>";
                echo "<p>Pizza {$order['pizzaName']}</p>";
                echo "<p data-status-{$order['orderedArticleId']}='{$order['orderedArticleId']}'>Status: {$order['statusText']}</p>";
                echo "<br>";
                echo "<input type='hidden' name='mylabel' value=" . htmlspecialchars($order['orderedArticleId']) . " />";
            }
            echo "</div>";
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

        $_SESSION['last_order_id'] = $id;

        header('Location: http://localhost/Praktikum/Prak4/backend/Kunde.php');
    }

    // ALWAYS INCLUDE THE DEFER ATTRIBUTE IN SRC WHEN INCLUDING JS FILES
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
            <script src="../frontend/kunde.js" defer></script>
            <link rel="stylesheet" href="../styles/kunde.css">
        </head>
        EOT;
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
            echo $e->getMessage();
        }
    }
}

Kunde::main();

