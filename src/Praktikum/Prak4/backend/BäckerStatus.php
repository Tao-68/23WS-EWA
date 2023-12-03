<?php
require_once './Page.php';

class Baecker extends Page
{
    protected function __construct()
    {
        // initiate the DB connection
        parent::__construct();
        //session_start();
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

