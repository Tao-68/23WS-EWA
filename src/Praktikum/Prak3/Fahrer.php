<?php
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
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT ordered_article.ordered_article_id, ordered_article.article_id, ordered_article.ordering_id, ordered_article.status, ordering.address, article.price
        FROM ordered_article
        LEFT JOIN ordering ON ordered_article.ordering_id = ordering.ordering_id
        LEFT JOIN article ON ordered_article.article_id = article.article_id
        WHERE ordered_article.status >= 2";

        $result = $this->_database->query($sql);
        if (!$result)
            throw new Exception("Fehler ist aufgetreten: " . $this->_database->error);

        $orderedArticles = array();
        while ($row = $result->fetch_assoc()) {
            array_push($orderedArticles, $row);
        }
        $result->free();

        $bestellungen = array();
        foreach ($orderedArticles as $row) {
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

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        if (isset($_POST['ordering_id']) && is_numeric($_POST['ordering_id'])) {
            $ordering_id = $_POST['ordering_id'];
        } else {
            return;
        }

        if (isset($_POST['status'][$ordering_id]) && is_numeric($_POST['status'][$ordering_id])) {
            $status = $_POST['status'][$ordering_id];
            //echo htmlspecialchars($status);
            if ($status == 3) {
                $updateSql = "UPDATE ordered_article SET status = 3 WHERE ordering_id = $ordering_id AND status = 4";
                $result = $this->_database->query($updateSql);

                if (!$result) {
                    throw new Exception("Fehler beim Aktualisieren des Status: " . $this->_database->error);
                }
            }
            if ($status == 4) {
                $updateSql = "UPDATE ordered_article SET status = 4 WHERE ordering_id = $ordering_id AND status = 3";
                $result = $this->_database->query($updateSql);

                if (!$result) {
                    throw new Exception("Fehler beim Aktualisieren des Status: " . $this->_database->error);
                }
            }
            if ($status == 5) {
                $updateSql = "UPDATE ordered_article SET status = 5 WHERE ordering_id = $ordering_id";
                $result = $this->_database->query($updateSql);

                if (!$result) {
                    throw new Exception("Fehler beim Aktualisieren des Status: " . $this->_database->error);
                }
            }
        }

        header('Location: http://localhost/Praktikum/Prak3/Fahrer.php');
    }


    protected function generateView()
    {
        $bestellungen = $this->getViewData();
        header("Refresh: 5; url=http://localhost/Praktikum/Prak3/Fahrer.php");
        $this->generatePageHeader('Fahrer');

        echo "<h1>Fahrer</h1>";

        if (sizeof($bestellungen) == 0) {
            echo "<div style='text-align: center;'>";
            echo "<h1>Zurzeit gibt es keine Bestellungen</h1>";
            echo "<img src=\"../images/dog.jpeg\" width=500 height=500>";
            echo "</div>";
            return;
        }

        foreach ($bestellungen as $ordering_id => $orderedArticles) {
            $price = array_reduce($orderedArticles, function ($value, $i) {
                $value += $i['price'];
                return $value;
            }, 0);

            echo "<h3>Order #{$ordering_id}: {$orderedArticles[0]['address']}.</h3>";
            echo "<h3> Summe: {$price} EURO</h3>";

            echo "<form action=\"Fahrer.php\" method=\"post\">";
            echo "<section>";
            echo "<p>Status:</p>";

            $isChecked2 = $orderedArticles[0]['status'] == 3 ? 'checked' : null;
            echo <<<EOT
            <label>
                <input type="radio" name="status[{$ordering_id}]" value="3" {$isChecked2} /> 
                Warte zur Abholung
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

            echo "</section>";
            echo "<br>";

            echo "<input type='hidden' name='ordering_id' value={$ordering_id} />";
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
            echo $e->getMessage();
        }
    }
}

Fahrer::main();
?>
