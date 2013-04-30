<?php

/**
 * Class for Cassandra Servers Cluster  
 * Copyright 2013 John Pettit <john.pettit@xervanik.com>               
 * @package XCassandra                        
 */

// XCassandra include base class
require_once('xbase.php');
require_once('/var/www/phpcassa/autoload.php');

use phpcassa\Connection\ConnectionPool;
use phpcassa\ColumnFamily;
use phpcassa\Schema\StrategyClass;

/**
 * Class for Cassandra Server cluster
 * handles connections, connection pooling, server Ips
 * as well as insert and select requests
 * @author John Pettit <john.pettit@xervanik.com>
 * @version 1.0.0
 * @package XBase
 * @class XCassandra
 */

class XCassandra extends xbase {
	/**
     * list of Cassandra server ips and ports
     * @access private
     * @var array
     */
	private $serverIps = array("127.0.0.1:9160");
    
    /**
     * Cassandra cluster username
     * @access private
     * @var string
     */
	private $serverUsername = "";

    /**
     * Cassandra cluster password
     * @access private
     * @var string
     */
	private $serverPassword = "";

    /**
     * Cassandra Connection Pool
     * @access private
     * @var object
     */
    private $connPool;

    /**
     * Cassandra Column Family object
     * @access private
     * @var object
     */
    private $columnFamily; 
    /**
     * Cassandra Key Space
     * @access private
     * @var string
     */
    private $keySpace;

    /**
     * Constructor
     */
	public function __construct()
	{
		parent::__construct();
        //no continuous hashing algorithim here!
		//cassandra does it auto magic like
		$this->keySpace = "ping";
        $this->connPool = new ConnectionPool($this->keySpace, $this->serverIps);
        $this->columnFamily = $this->connPool->get();
        $this->columnFamily->client->set_cql_version("3.0.0");
        $this->log->info('Cassandra Construct!');
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
        //close connection?
		$this->log->info('Cassandra Destruct!');
		parent::__destruct();
	}

    /**
     * insertPing
     * @param integer @newid ID of inserted data
     * @param integer @timestamp Timestamp of inserted data
     * @param string @note A note
	 * @pram float @temp The temperature of the ping
     * @return true|false Success or Failure
     */
	public function insertPing($newid, $timestamp, $note, $temp)
	{
		$this->log->info('Cassandra:insertPing:'.$newid.' - '.$timestamp.' - '.$note.' - '.$temp);
		try
		{
			//cassandra like Timestamps in MS
			$timestamp = $timestamp * 1000;
			$res = $this->columnFamily->client->execute_cql3_query("INSERT INTO ping (id, pingtime, note, temp) VALUES ($newid,'$timestamp','$note',$temp);",2,1);
			$this->log->info("Cassandra:insertPing - result:" . $res->getName() . ' -- ' . $res->type);
			return true;
		}
		catch(Exception $ex)
	    {
        	$this->errorMessage = "Cassandra Exception:insertPing - ".$ex->getMessage().$ex->getTraceAsString();
			$this->log->error($this->errorMessage);
        	return false;
		}	
	}

    /**
     * Get Last Ping
     * @param integer @id row ID to get
     * @return array timestamp, note, temp
     */
    public function getLastPing($id)
    {
        $this->log->info('Cassandra:getLastPing:' . $id);
        try
        {
            $res = $this->columnFamily->client->execute_cql3_query("SELECT * FROM Ping WHERE id = $id ORDER BY pingtime DESC LIMIT 1;",2,1);
            $this->log->info("Cassandra:getLastPing:result:" . $res->getName() . ' -- ' . $res->type);
            return $res;
        }
        catch(Exception $ex)
        {
			$this->errorMessage = "Cassandra:getLastPing:".$ex->getMessage();
            $this->log->error($this->errorMessage);
            return false;
        }
    }

	/**
	 * getPingRange
	 * @param integer @id row ID to get
	 * @param integer @start Start tim to fetch
	 * @param integer @end End time to fetch
	 * return array of CqlResilts
 	 */
	public function getPingRange($id, $start, $end)
	{
		$this->log->info('Csssandra:getPingRange:'.$id.'-'.$start.'-'-$end);
		try
		{
			//Cassandra likes milliseonds
			$start = $start * 1000;
			$end= $end * 1000;
			$res = $this->columnFamily->client->execute_cql3_query("SELECT * FROM Ping WHERE id = $id AND pingtime > $start AND pingtime <= $end;",2,1);
			$this->log->info('Cassandra:getPingRange:'.$id.':'.$start.':'.$end);
			return $res;
		}
		catch(Exception $ex)
		{
			$this->errorMessage = "Cassandra:getPingRange:".$ex->getMessage();
			$this->log->error($this->errorMessage);
			return false;
		}
	}
}

?>
