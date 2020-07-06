<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class ClientStatus for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 7
 *
 * @file     ClientStatus.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @version  2.0 
 */

// to do: change name 'ClientStatus' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent 
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class. 
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking 
 * during implementation.
 
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 */
class ClientStatus extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    //private $Orders = null;
    private $serializedData = null;
    
    /**
     * Instantiates members (to be defined above).   
     * Calls the constructor of the parent i.e. page class.
     * So the database connection is established.
     *
     * @return none
     */
    protected function __construct() 
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
        //$Orders = array();
    }
    
    /**
     * Cleans up what ever is needed.   
     * Calls the destructor of the parent i.e. page class.
     * So the database connection is closed.
     *
     * @return none
     */
    public function __destruct() 
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData()
    {
        $Orders = array();
        // to do: fetch data for this view from the database
        if(isset($_SESSION["pizzaid"])) {
            $pizza_id = $_SESSION["pizzaid"];
            //query fuer aktulle session
            $SQLquery = "SELECT DISTINCT name, status, ordered_articles.id FROM article, `order`, `ordered_articles`
            WHERE article.id = ordered_articles.f_article_id AND `ordered_articles`.`f_order_id` = '$pizza_id'";

            $Recordset = $this->_database->query($SQLquery);

            if(!$Recordset)
                throw new Exception("Query failed: ".$Connection->error);
            else {
                while($Record = $Recordset->fetch_assoc()) {
                    $this->Orders[] = array($Record["name"], $Record["status"], $Record["id"]);
                }
            }
            $Recordset->free();
            return $Orders;
        }
    }
    
    /**
     * First the necessary data is fetched and then the HTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView() 
    {
        $Orders = $this->getViewData();
        header("Content-Type: application/json; charset=UTF-8");
        $this->serializedData = json_encode($this->Orders);
        echo $this->serializedData;
        //$this->generatePageHeader('to do: change headline');
        // to do: call generateView() for all members
        // to do: output view of this page
        //$this->generatePageFooter();
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this page is supposed to do something with submitted
     * data do it here. 
     * If the page contains blocks, delegate processing of the 
	 * respective subsets of data to them.
     *
     * @return none 
     */
    protected function processReceivedData() 
    {

        parent::processReceivedData();

    }

    /**
     * This main-function has the only purpose to create an instance 
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     *
     * @return none 
     */    
    public static function main() 
    {
        try {
            $page = new ClientStatus();
            
            $page->processReceivedData();
            session_start();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
ClientStatus::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >