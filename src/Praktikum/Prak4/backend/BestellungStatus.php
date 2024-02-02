<?php
require_once './Page.php';

class Bestellung extends Page
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

    protected function processReceivedData()
    {
        parent::processReceivedData();      
    }
    
    protected function generateView()
    {
        $orderedArticles = $this->getViewData();
        header("Content-Type: application/json; charset=UTF-8");     
        $data = json_encode($orderedArticles);
        //if not echoed, the client side JS wont be able to receive the data and also the request will fail
        echo $data;
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
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

//since main is marked as static func, it doesnt need an instance of a class to be called
Bestellung::main();

