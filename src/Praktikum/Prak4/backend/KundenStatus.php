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

    protected function processReceivedData()
    {
        parent::processReceivedData();
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
        //JS on the client side can use the fetch API to make an HTTP request to this page and receive the JSON response which it can then use to dynamically update the content of the webpage based on the received data from the back end.
        header("Content-Type: application/json; charset=UTF-8");     
        $data = json_encode($orderedArticles);
        //if not echoed, the client side JS wont be able to receive the data and also the request will fail
        echo $data;
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

