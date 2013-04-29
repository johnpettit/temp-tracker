<?php

/**
 * Class for acccessing data of any sort from any source                 
 * Copyright 2012 Xervanik Systems            
 * @package XDataStore         
 */

// include XCassandra
require_once('cassandra.php');

/**
 * Class for accessing data
 * @author John Pettit <john.pettit@xervanik.com>
 * @version 1.0.0
 * @package XDataStore
 * @class XDataStore
 */

class XDataStore extends xbase
{
    /**
     * Internal var to hold cassandra server object
     * @var XCassandra
     */
	private $cassandra;

	/**
     * Constructor
	 * Fill internal vars with new objects
     */
	public function __construct()
	{
		parent::__construct();
		$this->cassandra = new XCassandra();
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		$this->cassandra = null;
		parent::__destruct();
	}

    /**
     * returnRawColumnData
     * @param object @col CQL column from CF_raw
     * @param integer @valtype Value Type to look for
     * return string ts,val
     */
    private function returnRawColumnData($col,$valtype)
    {
        $temp = $col->columns;
        $ts = $temp[1]->value;
        $tst = unpack("H*",$ts);
        $timestamp = hexdec($tst[1]);
        $data = json_decode($temp[2]->value,true);
        if(is_null($data))
            return "NO JSON";
        $lookfor = "t".$valtype;
        if(isset($data[$lookfor]))
            return $timestamp.",".$data[$lookfor];
        else
            return "0,0";
    }

	/**
	 * insertPing
	 * @param integer @newid ID of row
	 * @param integer @timestamp Insert Datetime
	 * @param string @note A note
	 * @param float @temp The temperature
	 * return boolean
	 */
	public function insertPing($newid, $timestamp, $note, $temp)
	{
		$this->log->info('Datastore:insertPing - '.$newid.' - '.$timestamp.' - '.$note.' - '.$temp);
		$res = $this->cassandra->insertPing($newid, $timestamp, $note, $temp);
		return $res;
	}

	/**
	 * getLastPing
	 * @param integer ID of row
	 * return array or false
	 */
	public function getLastPing($id)
	{
		$this->log->info('Datastore:getLastPing - '.$id);
		$res = $this->cassandra->getLastPing($id);
	
		if(! $res)
		{
			$this->errorMessage = $this->ca->errorMessage;
			return false;
		}

		$result = array();
		foreach($res->rows as &$col)
 	    {
			$temp = $col->columns;	
 			
 			$timestamp = $this->convertTimestamp($temp[1]->value);
			$note = $temp[2]->value;
			$temp = $this->convertTemp($temp[3]->value);
			
			$result["timestamp"] = $timestamp;
			$result["note"] = $note;
			$result["temp"] = $temp;			
		}
		return $result;
	}

	/**
	 * getPingRange
	 */
	public function getPingRange($id,$start,$end)
	{
		$this->log->info('Datastore:getPingRange:'.$id.' - '.$start.' - '.$end);
		$res = $this->cassandra->getPingRange($id,$start,$end);
		
		if(!$res)
		{
			$this->errorMessage = $this->ca->errorMessage;
			return false;
		}
		
		$result = array();
		foreach($res->rows as &$col)
		{
			$temp = $col->columns;
			$piece = array();
			$piece["timestamp"] = $this->convertTimestamp($temp[1]->value);
			$piece["note"] = $temp[2]->value;
			$piece["temp"] = $this->convertTemp($temp[3]->value);
			$result[] = $piece;
		}
		return $result;
	}

	/**
	 * convertTimestamp
	 */
	private function convertTimestamp($cassTimestamp)
	{
		$ts = unpack("H*",$cassTimestamp);
		$timestamp = hexdec($ts[1]);
		return $timestamp;
	}
	 	
	/**
	 * convertTemp
	 */
	private function convertTemp($cassTemp)
	{
		$tt = unpack("f",strrev($cassTemp));
		$temp = $tt[1];
		return $temp;
	}
}
?>
