<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 7
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @version  2.0 
 */

// to do: change name 'PageTemplate' throughout this file
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
class Baker extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    //private $Pizzas;
    
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
        // to do: fetch data for this view from the database
        $SQLQuery = 'SELECT name, ordered_articles.id, status FROM ordered_articles, article, `order`
        WHERE `order`.`id` = ordered_articles.f_order_id AND ordered_articles.f_article_id = article.id AND status >= 0 AND status < 2 
        ORDER BY timestamp';
        $Recordset = $this->_database->query($SQLQuery);
        if(!$Recordset) {
            throw new Exception("Query failed: ".$Connection->error);
        } else {
            while($Record = $Recordset->fetch_assoc()) {
                $this->Pizzas[] = array($Record["name"], $Record["status"], $Record["id"]);
            }
        }
        $Recordset->free();
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
        $this->generatePageHeader('Bäcker', header("refresh:5;url=baker.php"));
        // to do: call generateView() for all members
        // to do: output view of this page
        echo <<<EOT
        <section>
            <div class="container">
                <h1>Bäcker</h1>
EOT;
        if(isset($this->Pizzas)) {
            foreach($this->Pizzas as $Pizza) {
                echo <<<EOT

            <fieldset>
                    <legend accesskey="a">Bestellung $Pizza[2] Pizza $Pizza[0] </legend>
                    <form id="statusform$Pizza[2]" name="statusform$Pizza[2]" action="baker.php" method="POST" accept-charset="UTF-8"> 
                    <input type="radio" id="bestellt$Pizza[2]" name="status" value="0" onclick="document.forms['statusform$Pizza[2]'].submit();" >
                    <label for="bestellt$Pizza[2]">Bestellt</label><br>
                    <input type="radio" id="im_ofen$Pizza[2]" name="status" value="1" onclick="document.forms['statusform$Pizza[2]'].submit();">
                    <label for="im_ofen$Pizza[2]">Im ofen</label><br>
                    <input type="radio" id="fertig$Pizza[2]" name="status" value="2" onclick="document.forms['statusform$Pizza[2]'].submit();">
                    <label for="fertig$Pizza[2]">Fertig</label><br>
                    <input type="hidden" name="pizza" value="$Pizza[2]">
                    </form>
            </fieldset>
           
EOT;
            }
        }
        echo "</div>\n";
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
        // Formular Daten verarbeiten und Status updaten
        if(isset($_POST['pizza'])) {
            $ID_Pizza = $_POST['pizza'];
            if(isset($_POST['status'])) {
                $Status = $_POST['status'];

                $SQLQuery = "UPDATE ordered_articles SET status = '$Status' WHERE id = '$ID_Pizza'";

                if(!$this->_database->query($SQLQuery)) {
                    throw new Exception("Query failed: ".$Connection->error);
                }
            }
            header('Location: baker.php');
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
            $page = new Baker();
            $page->processReceivedData();
            $page->generateView();
            //header("refresh:5;url=baker.php");
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Baker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >