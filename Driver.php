<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Driver for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 7
 *
 * @file     Driver.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @version  2.0 
 */

// to do: change name 'Driver' throughout this file
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
class Driver extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    //private $orders = null;
    
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
        //$orders = array();
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
        $orders = array();
        // to do: fetch data for this view from the database
        
        $SQLQuery = "SELECT o.`address`, o.`id` 
        FROM `order` o INNER JOIN `ordered_articles` i
        ON i.`f_order_id` = o.`id`
        GROUP BY o.`address`, o.`id`
        HAVING SUM(`Status` <= 1 OR `Status` >= 4) = 0
        ORDER BY timestamp";
        
        $Recordset = $this->_database->query($SQLQuery);

        if(!$Recordset) 
            throw new Exception("Query failed: ".$Connection->error);
        else {
            while($Record = $Recordset->fetch_assoc()) {
                $this->orders[] = array($Record["address"], $Record["id"]);
            }
        }
        $Recordset->free();
        return $orders;
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
        $orders = $this->getViewData();
        $this->generatePageHeader('Fahrer', header("refresh:5;url=driver.php"));
        // to do: call generateView() for all members
        // to do: output view of this page

        echo <<<EOT
<section>
    <div class="container">
        <h1>Fahrer</h1>
EOT;

        if(isset($this->orders)) {
            foreach($this->orders as $Order) {
                //XSS Protection
                $address = htmlspecialchars($Order[0]);
                echo <<<EOT

            <fieldset>
                <legend>Bestellung $Order[1]</legend>
                <form id="statusform$Order[1]" name="statusform$Order[1]" action="driver.php" method="POST" accept-charset="UTF-8"> 
                <p>$address</p>
                <p>Status</p>
                <input type="radio" id="fertig$Order[1]" name="status" value="2" onclick="document.forms['statusform$Order[1]'].submit();"/>
                <label for="fertig$Order[1]">Fertig</label>
                <input type="radio" id="unterwegs$Order[1]" name="status" value="3" onclick="document.forms['statusform$Order[1]'].submit();"/>
                <label for="unterwegs$Order[1]">Unterwegs</label>
                <input type="radio" id="geliefert$Order[1]" name="status" value="4" onclick="document.forms['statusform$Order[1]'].submit();"/>
                <label for="geliefert$Order[1]">Geliefert</label>
                <input type="hidden" name="pizza" value="$Order[1]">
                </form>
            </fieldset>

EOT;
            }
    }
        echo "\t</div>\n";
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
        if(isset($_POST['pizza'])) {
            $ID = $_POST['pizza'];
            if(isset($_POST['status'])) {
                $Status = $_POST['status'];

                $SQLQuery = "UPDATE ordered_articles SET status = '$Status' WHERE f_order_id = '$ID'";

                if(!$this->_database->query($SQLQuery)) {
                    throw new Exception("Query failed: ".$Connection->error);
                }
            }
            header('Location: driver.php');
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
            $page = new Driver();
            $page->processReceivedData();
            $page->generateView();
            //header("refresh:5; url=driver.php");
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Driver::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >