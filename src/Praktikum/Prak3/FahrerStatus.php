<?php
require_once './Page.php';

class Fahrer extends Page
{
    protected function __construct()
    {
        // initiate the DB connection
        parent::__construct();
        session_start();
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
    }
    
    protected function generateView()
    {
        $orderedArticles = $this->getViewData();
        header("Content-Type: application/json; charset=UTF-8");     
        $data = json_encode($orderedArticles);
        //if not echoed,sometimes the client side JS may not be able to receive the data and the request may even fail
        echo $data;
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

//since main is marked as static func, it doesnt need an instance of a class to be called
Fahrer::main();

