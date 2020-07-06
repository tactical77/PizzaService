<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Client for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 7
 *
 * @file     Client.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @version  2.0 
 */

// to do: change name 'Client' throughout this file
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
class Client extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    //private $Pizzas = null;
    
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
        //$Pizzas = array();
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
        $Pizzas = array();
        //bestell id aus session hollen
        if(isset($_SESSION["pizzaid"])) { 
            $pizza_id = $_SESSION["pizzaid"];
            //query for the ordered pizzas
            $SQLquery = "SELECT name, ordered_articles.id FROM article, ordered_articles
            WHERE ordered_articles.f_article_id = article.id AND ordered_articles.f_order_id = $pizza_id";

            $Recordset = $this->_database->query($SQLquery);

            if(!$Recordset)
                throw new Exception("Query failed: ".$Connection->error);
            else {
                while($Record = $Recordset->fetch_assoc()) {
                    $this->Pizzas[] = array($Record["name"], $Record["id"]);
                }
            }
        }
        //$Recordset->free(); //Auskommentiert aufgrund eines Fehlers
        return $Pizzas;
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
        $Pizzas = $this->getViewData();
        $this->generatePageHeader('Kunde');
        echo "<script src='StatusUpdate.js'></script>";
        // to do: call generateView() for all members
        // to do: output view of this page

        echo <<<EOT

        <section>
        <script>handle()</script>
EOT;
    
/*    
        
        if(isset($this->Pizzas)) {
            foreach($this->Pizzas as $Pizza) {
            
            
            echo <<<EOT

                
            <form id="bestell-id-$Pizza[1]">
                <fieldset>
                <legend>$Pizza[0]</legend>
                    <input type="radio" id="bestellt$Pizza[1]" name="pizza$Pizza[1]" value="bestellt" checked>
                    <label for="bestellt$Pizza[1]" >Bestellt</label><br>
                    <input type="radio" id="im_ofen$Pizza[1]" name="pizza$Pizza[1]" value="im_ofen">
                    <label for="im_ofen$Pizza[1]" >Im Ofen</label><br>
                    <input type="radio" id="fertig$Pizza[1]" name="pizza$Pizza[1]" value="fertig">
                    <label for="fertig$Pizza[1]" >Fertig</label><br>
                    <input type="radio" id="unterwegs$Pizza[1]" name="pizza$Pizza[1]" value="unterwegs">
                    <label for="unterwegs$Pizza[1]" >Unterwegs</label><br>
                    <input type="radio" id="geliefert$Pizza[1]" name="pizza$Pizza[1]" value="geliefert">
                    <label for="geliefert$Pizza[1]" >Geliefert</label><br>
                </fieldset>
            </form>
        
EOT;
                
            
            }
        }*/
        echo "</section>";
        
        $this->generatePageFooter();
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
        //create session
        session_start();
        // to do: call processReceivedData() for all members
        if(isset($_POST['Address']) and isset($_POST['Selection'])) {
            $Orders = $_POST['Selection'];
            //SQL Injection Protection
            $Address = $this->_database->real_escape_string($_POST['Address']);
            

            //Query fuer die Tabelle Order
            $SQLabfrage = "INSERT INTO `order` (`address`, `timestamp`) VALUES
            ('$Address', NOW());";

            //DB Order und Address reinschreiben
            if(!$this->_database->query($SQLabfrage)) {
                throw new Exception("Query failed: ".$Connection->error);
            }

            //BestelltePizza DB Query und Variablen
            $fOrderID = $this->_database->insert_id;
            $_SESSION["pizzaid"] = $fOrderID;
            

            foreach($Orders as $Order) {
                $SQLquery = "INSERT INTO `ordered_articles` (`f_article_id`, `f_order_id`, `status`) VALUES
                ('$Order', '$fOrderID', '0')";


                if(!$this->_database->query($SQLquery)) {
                    throw new Exception("Query failed: ".$Connection->error);
                }
            }
        
        header('Location: Client.php');
        }
        
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
            $page = new Client();
            $page->processReceivedData();
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
Client::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >