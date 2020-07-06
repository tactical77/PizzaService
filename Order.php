<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Order for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7
 *
 * @file     Order.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  2.0
 */

// to do: change name 'Order' throughout this file
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
class Order extends Page
{

    //private $pizzas = null;

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

        //$pizzas = array();

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
        $pizzas = array();
        // fetch data for this view from the database
        $SQLabfrage = "SELECT name, price, id from article";
        $Recordset = $this->_database->query($SQLabfrage);
        if(!$Recordset)
            throw new Exception("Query failed: ".$Connection->error);
        else {
            while($Record = $Recordset->fetch_assoc()) {
                $this->pizzas[] = array($Record["name"], $Record["price"], $Record["id"]);
            }
        }
        $Recordset->free();
        return $pizzas;
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
        //get data from db and generate PageHeader HTML
        $pizzas = $this->getViewData();
        $this->generatePageHeader('Bestellung');
        // to do: call generateView() for all members
        // to do: output view of this page

        //generate pizza menu
        echo <<<EOT
            <div class="container">
            <h1>Bestellung</h1>
            <div class="Pizza_class">
            <section>
                <h2>Speisekarte</h2>

EOT;

        foreach($this->pizzas as $Pizza) {
            //format to 2 digits
            $price = number_format($Pizza[1], 2);
            //array cast for java script
            $js_pizza_array = json_encode($Pizza);

        echo <<<EOT
                    
                    <article>
                        <h3>Pizza $Pizza[0]</h3>
                        <img src="pizza.png" width="150" height="150" alt="" title="$Pizza[0] pizza" onclick='add_warenkorb($js_pizza_array)' />
                        <p>$Pizza[0]</p>
                        <p>$price €</p>
                    </article>

EOT;
        }

        echo <<<EOT

            </section>
            </div>
    <section>
        
        <h2 class="cart">Warenkorb</h2>
            
                <form onsubmit="return check_formular()" action="client.php" method="POST" accept-charset="UTF-8">
                <div class="form-group">
                    <label for="select_pizza">Ihre Auswahl: </label>
                        <select id="select_pizza" name="Selection[]" size="10" tabindex="1" multiple>


                        </select>
                </div>

            <p>Gesamtpreis:  <span id="price_id">0,00</span> €</p>

            <span class="form-group">
                <label for="Adresse">Adresse: </label>
                <input type="text" id="Adresse" placeholder="Ihre Adresse" value="" name="Address" onblur='check_addr()'  required />
            </span>
            <span class="buttons">
                <input type="reset" name="alle_loeschen" value="Alle löschen" onclick="remove_all()" />

                <input type="button" name="auswhal_loeschen" value="Auswahl löschen" onclick='remove_element()' />

                <input type="submit" id="submit" value="Bestellen" disabled/>
            </span>

       

        </form>
    </section>
    </div>

EOT;



        //generate PageFooter HTML
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
        //session_start();

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
            $page = new Order();
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
Order::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
