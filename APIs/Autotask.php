<?php
/**
 * Autotask.class.php
 * 
 * Class for retrieving ticket info from Autotask
 * 
 * PHP 5.3 OR LATER.
 * 
 * Changes: date - author - changes
 * 
 * @category    Tools
 * @package     gsAutotask
 * @subpackage  gsQueue
 * @author      Rudie Shahinian <rshahinian@company.com>
 * @version     2.0
 */
class gsAutotask {

    /**
     * Queue object used to store ticket data
     * @access private
     * @var Queue
     */
    private $queue;
    
    
    /**
     * Store Autotask SOAP connection
     * @access private
     * @var SoapClient
     */
    private $ATcnx;
    
    
    /**
     * Complete status Autotask id
     * @access private
     * @var integer
     */
    private $ATcompletedCode;
    
    
    /**
     * Autotask username
     * @access private
     * @var string
     */
    private $ATusername;
    
    
    /**
     * Autotask password
     * @access private
     * @var string
     */
    private $ATpassword;
    
    
    /**
     * Autotask WSDL
     * @access private
     * @var string
     */
    private $ATwsdl;
    
    
    /**
     * Autotask ASMX location
     * @access private
     * @var string
     */
    private $ATlocation;
    
    
    /**
     * The class constructor
     * 
     * Setup Autotask SOAP info ans connect.
     */
    function __construct() {
        
        //Setup
        $this->ATcompletedCode = 5;
        $this->ATusername = "rshahinian@company.com";
		    $this->ATpassword = "password";
        $this->ATwsdl = 'https://webservices.autotask.net/atservices/1.5/atws.wsdl';
        $this->location = "https://webservices.autotask.net/atservices/1.5/atws.asmx";
        
        $this->queue = new Queue();
        $this->connectToAutotask();
    }
    
   
    /**
     * Return a gsQueue object populated with current data from Autotask
     * 
     * @access public
     * @return Queue  
     */ 
    public function getQueue() {
        
        $this->ticketsToQueue();
        $this->progressToQueue();
        return $this->queue;
    }      
    
    public function getProgressByDate($day = '') {
        if ($day == ''){
            $day = date('Y-m-d');
        }
        $this->progressToQueue($day);
        return $this->queue;
    }
    
    public function getATTickts() {
        $ATTicketsArray = $this->queryATforAllTickets();
        
        foreach ($ATTicketsArray as $ticket) {
            $ticketarray[$x] = array(
                'TicketNumber' => $ticket->TicketNumber,
                'DueDateTime' => $ticket->DueDateTime,
                'AccountID' => (int)$ticket->AccountID,
                'QueueID' => (int)$ticket->QueueID,
                'Priority' => (int)$ticket->Priority,
                'Status' => (int)$ticket->Status,
                'CreatorResourceID' => (int)$ticket->CreatorResourceID,
                'IssueType' => (int)$ticket->IssueType,
                'SubIssueType' => (int)$ticket->SubIssueType,
                'EstimatedHours' => $ticket->EstimatedHours,
                'AssignedResourceID' => (int)$ticket->AssignedResourceID,
                'CompletedDate' => $ticket->CompletedDate,
                'CreateDate' => $ticket->CreateDate                        
            );
            $x++;
        }
        return $ticketarray;
    }
    /***************************************************************************
     *                              PRIVATE METHODS
     **************************************************************************/
    
    
    /**
     * Connect to Autotask using SOAP. Return true if successful
     * 
     * @access private
     * @return bool
     */
    private function connectToAutotask(){
        try {
            $loginarray = array(
              'login' => $this->ATusername,
              'password' => $this->ATpassword,
              'location'=> $this->ATlocation
            );
            $this->ATcnx = new SoapClient($this->ATwsdl, $loginarray);
            if(!$this->ATcnx) die("Cannot connect to Autotask");
        }
        catch (Exception $e){
            die('AT - caught exception: '. $e->getMessage()."<br>");
        }
        return true;   
    }
    
    
    /**
     * Query Autotask for all Tickets that are not complete and are in either
     * the Bugs or Changes queues. Return result as an array.
     * 
     * @access private
     * @return array 
     */
    private function queryATforTickets(){
        $ATids = array_flip($this->queue->getIDs());
        $xml = array('sXML' => "<queryxml>" .
        "<entity>Ticket</entity>" .
        "<query>" .
        "<field>Status" .
        "<expression op='NotEqual'>".$this->ATcompletedCode."</expression>" .
        "</field>" .
        "<condition>" .
        "<condition>" .
        "<field>QueueID" .
        "<expression op='Equals'>".$ATids['BUGS']."</expression>" .
        "</field>" .
        "</condition>" .
        "<condition operator='OR'>" .
        "<field>QueueID" .
        "<expression op='Equals'>".$ATids['CHANGES']."</expression>" .
        "</field>" .
        "</condition>" .
        "</condition>" .
        "</query>" .
        "</queryxml>");
        
        
        $result = $this->ATcnx->query($xml);
        $ticketsArray = $result->queryResult->EntityResults->Entity;
        return $ticketsArray;
    }
    

    private function queryATforAllTickets(){
        $ATids = array_flip($this->queue->getIDs());
        $xml = array('sXML' => "<queryxml>" .
        "<entity>Ticket</entity>" .
        "<query>" .
        "<condition>" .
        "<condition>" .
        "<field>QueueID" .
        "<expression op='Equals'>".$ATids['BUGS']."</expression>" .
        "</field>" .
        "</condition>" .
        "<condition operator='OR'>" .
        "<field>QueueID" .
        "<expression op='Equals'>".$ATids['CHANGES']."</expression>" .
        "</field>" .
        "</condition>" .
        "</condition>" .
        "</query>" .
        "</queryxml>");


        $result = $this->ATcnx->query($xml);
        $ticketsArray = $result->queryResult->EntityResults->Entity;
        return $ticketsArray;
    }
    /**
     * Query Autotask for all Tickets that are in either Bugs or Changes queues
     * and have either been completed or have been added during the specified 
     * date. If no date has been specified, choose today. Return result as an
     * array.
     * 
     * @access private
     * @return array 
     */
    private function queryATforProgress($date = ''){
        if ($date == ''){
            $date = date('Y-d-m');
        }
        $ATids = array_flip($this->queue->getIDs());
        $xml = array('sXML' => "<queryxml>" .
        "<entity>Ticket</entity>" .
        "<query>" .
            "<condition>" .
                "<condition>" .
                    "<field>CreateDate" .
                        "<expression op='IsThisDay'>".$date."</expression>" .
                    "</field>" .
                "</condition>" .
                "<condition operator='OR'>" .
                    "<field>CompletedDate" .
                        "<expression op='IsThisDay'>".$date."</expression>" .
                    "</field>" .
                "</condition>" .
            "</condition>" .  
            "<condition operator='AND'>" .
                "<condition>" .
                    "<field>QueueID" .
                        "<expression op='Equals'>".$ATids['BUGS']."</expression>" .
                    "</field>" .
                "</condition>" .
                "<condition operator='OR'>" .
                    "<field>QueueID" .
                        "<expression op='Equals'>".$ATids['CHANGES']."</expression>" .
                    "</field>" .
                "</condition>" .
            "</condition>" .                   
        "</query>" .
        "</queryxml>");

        $result = $this->ATcnx->query($xml);
        $progressArray = $result->queryResult->EntityResults->Entity;
        return $progressArray;
    }
    
    
    /**
     * Add to the Queue object with current data from Autotask for all 
     * incomplete tickets in Buge and Changes
     * 
     * @access private
     */
    private function ticketsToQueue() {
        $ATTicketsArray = $this->queryATforTickets();
        
        foreach ($ATTicketsArray as $ticket) {
            $ticketarray[$x] = array(
                'createdate' => $ticket->CreateDate,
                'description' => $ticket->Description,
                'duedatetime' => $ticket->DueDateTime,
                'issuetype' => $ticket->IssueType,
                'subissuetype' => $ticket->SubIssueType,
                'status' => $ticket->Status,
                'ticketnumber' => $ticket->TicketNumber,
                'title' => $ticket->Title,
                'queueid' => $ticket->QueueID,
                'priority' => $ticket->Priority,
                'ticketid' => $ticket->id
            );
            
            
            $this->queue->addToQueueByATID($ticket->QueueID, $ticket->Priority, $ticket->Status);
        }
    }
    
    
    /**
     * Add to the Queue object with today's completed and created progress.
     * 
     * @access private
     */
    private function progressToQueue($today = '') {
        if ($today == ''){
            $today = date('Y-m-d');
        }
        $ATTicketsArray = $this->queryATforProgress($today);
        foreach ($ATTicketsArray as $ticket) {
            $created = false;
            $completed = false;
            $createdDay = substr($ticket->CreateDate, 0, 10);
            $completedDay = substr($ticket->CompletedDate, 0 ,10);

            if ($today == $createdDay) $created = true;
            if ($today == $completedDay) $completed = true;

            $this->queue->addATProgressToQueue($ticket->QueueID, $created, $completed);
        }
    }
 
}
