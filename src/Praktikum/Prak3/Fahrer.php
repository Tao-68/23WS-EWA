<?php
error_reporting(E_ALL);
require_once "./Page.php";

class Fahrer extends Page
{
    protected function __construct()
    {
        // initiate the DB connection
        parent::__construct();
    }

    public function __destruct()
    {
        // close the DB connection
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT ordered_article.ordered_article_id, ordered_article.article_id, ordered_article.ordering_id, ordered_article.status, ordering.address, article.price
                FROM ordered_article
                LEFT JOIN ordering ON ordered_article.ordering_id = ordering.ordering_id
                LEFT JOIN article ON ordered_article.article_id = article.article_id
                WHERE ordered_article.status > 2 AND ordered_article.status < 5"; //since we only want to show the orders that are in the process of being delivered

        $result = $this->_database->query($sql);
        if (!$result)
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);

        $orderedArticles = array();
        while ($row = $result->fetch_assoc()) 
        {
            array_push($orderedArticles, $row);
        }
        $result->free();

        $bestellungen = array();
        foreach ($orderedArticles as $row) 
        {
            $key = $row["ordering_id"];
            if (!isset($bestellungen[$key]))
                $bestellungen[$key] = array();

            array_push($bestellungen[$key], $row);
        }
        return $bestellungen;
    }

    protected function processReceivedData()
    {
        parent::processReceivedData();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
            return;
        
        if (isset($_POST['ordering_id']) && is_numeric($_POST['ordering_id'])) 
            $ordering_id = $_POST['ordering_id'];
        else 
            return;  

        if (isset($_POST['status'][$ordering_id]) && is_numeric($_POST['status'][$ordering_id])) 
        {
            $status = $_POST['status'][$ordering_id];
            switch ($status) 
            {
                case 3:
                    $this->updateStatus($ordering_id, 3);
                    break;
                case 4:
                    $this->updateStatus($ordering_id, 4);
                    break;
                case 5:
                    $this->updateStatus($ordering_id, 5);
                    break;
                default:           
                    break;
            }
        }

        header('Location: http://localhost/Praktikum/Prak3/Fahrer.php');
    }

    protected function updateStatus($ordering_id, $newStatus, $oldStatus=null)
    {
        $updateSql = "";
        //if old status is not given, then that means we dont care about the old status and we just update the status to the new status because in scenarios like when the driver is delivering the order but forgot to update the status to "unterwegs" and the customer has already received the order, the driver can still update the status directly to "geliefert" without having to update the status to "unterwegs" first and then again to "geliefert". Also in our case since we group the orders into one order, updating the status should affect all the orders in the Database regardless of their old status
        if($oldStatus === null)
            $updateSql = "UPDATE ordered_article SET status = ? WHERE ordering_id = ?";
        else
            $updateSql = "UPDATE ordered_article SET status = ? WHERE ordering_id = ? AND status= ?";
        
        $stmt = $this->_database->prepare($updateSql);

        if (!$stmt) 
            throw new Exception("Fehler beim Aktualisieren des Status: " . $this->_database->error);
        
        $stmt->bind_param("ii", $newStatus, $ordering_id);
        
        $result = $stmt->execute();
        $stmt->close();

        if (!$result) 
            throw new Exception("Fehler beim Aktualisieren des Status: " . $this->_database->error);  
    }


    protected function generateView()
    {
        $bestellungen = $this->getViewData();
        header("Refresh: 5; url=http://localhost/Praktikum/Prak3/Fahrer.php");
        $this->generatePageHeader('Fahrer');

        echo "<h1>Fahrer</h1>";
        if (sizeof($bestellungen) == 0) 
        {
            echo "<div style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen</h1>";
            echo "<img src=\"../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            return;
        }

        foreach ($bestellungen as $ordering_id => $orderedArticles) 
        {
            $price = array_reduce($orderedArticles, fn($value, $i) =>
                    $value + $i['price'], 0);
            $price = number_format($price, 2, ',', '.'); //because we use , as decimal separator here in Germany
            $address = htmlspecialchars($orderedArticles[0]['address']);
            echo<<<EOT
            <h3>Order #{$ordering_id}: {$address}.</h3>
            <h3> Summe: {$price} EURO</h3>
            <form action="Fahrer.php" method="post">
            <section>
            <p>Status:</p>
            EOT;
            $isChecked2 = $orderedArticles[0]['status'] == 3 ? 'checked' : null;
            echo <<<EOT
            <label>
            <input type="radio" name="status[{$ordering_id}]" value="3" {$isChecked2} /> 
            Warte auf Abholung
            </label>
            EOT;

            $isChecked3 = $orderedArticles[0]['status'] == 4 ? 'checked' : null;
            echo <<<EOT
            <label>
            <input type="radio" name="status[{$ordering_id}]" value="4" {$isChecked3} /> 
            Unterwegs
            </label>
            EOT;

            $isChecked5 = $orderedArticles[0]['status'] == 5 ? 'checked' : null;
            echo <<<EOT
            <label>
                <input type="radio" name="status[{$ordering_id}]" value="5" {$isChecked5} /> 
                Geliefert
            </label>
            EOT;

            echo <<<EOT
            </section>
            <br>
            <input type='hidden' name='ordering_id' value={$ordering_id} />
            <input type="submit" value="Aktualisieren"/>
            </form>
            EOT;
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
            echo $e->getMessage();
        }
    }
}

Fahrer::main();

