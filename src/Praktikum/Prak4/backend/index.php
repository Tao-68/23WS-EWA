<?php 
//This function turns on output buffering. It means that instead of sending the output directly to the browser, it is captured and stored in an internal buffer. This buffer can then be manipulated or discarded before sending the final output to the browser.
ob_start();
error_reporting(E_ALL);
require_once "./Page.php";

class Index extends Page
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
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
            <link rel="stylesheet" href="../styles/styles.css">
        </head>
        <body>
        EOT;
    }

    protected function generateView()
    {
        $this->generatePageHeader('Start Page');
        echo '<h1>Welcome to the Pizza Shop</h1>';

        echo '<ul>';
        $files = glob('*.php'); //get all the files from CWD with extension PHP
        foreach ($files as $file) 
        { 
            if ($file == 'index.php') continue;
            //PATHINFO_DIRNAME->get the dir , PATHINFO_BASENAME->get the full name, PATHINFO_EXTENSION->get the extension, PATHINFO_FILENAME->get file name without extension       
            $filename = pathinfo($file, PATHINFO_FILENAME);
            echo "<li><a href=\"$file\" target=\"_blank\">$filename</a></li>";        
        }
        echo '</ul>';
        
        $this->generatePageFooter();
    }

    // protected function processReceivedData()
    // {
    //     parent::processReceivedData();
    // }
    
    public static function main()
    {
        try 
        {
            $page = new Index();
            $page->generateView();
        } 
        catch (Exception $e) 
        {
            echo $e->getMessage();
        }
    }
}

Index::main();
//This function flushes the output buffer, sending its contents to the browser and turning off output buffering.
ob_end_flush();

