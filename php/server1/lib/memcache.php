<?php

/**
 * Class for Memcache servers(s)				  
 * @package XBase                         
 */

// GwrMemCache include base class
require_once('xbase.php');

/**
 * Class for MemCache Server cluster
 * handles connections, connection pooling, server Ips
 * @author John Pettit <john.pettit@xervanik.com>
 * @version 1.0.0
 * @package XBase
 * @class XMemCache
 */

class XMemCache extends xbase {
    /**
     * list of memcache server ips
     * @var string
     */
	private $serverips = "127.0.0.1";

	/**
	 * redis server port 
	 * @var integer
	 */
	private $serverport = 11211;

	/**
	 * the actual redis server, usable after connecting
	 * @var object
	 */
	private $server;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->server = new Memcache;
		if(! $this->server->connect($this->serverips,$this->serverport))
			throw new Exception("Cannot connect to Memcache server(s).");
	}

	/**
	 * get     provides get function
	 * @param string the key to get
	 * @returns string   empty if nothing to get
	 */
	public function get($key)
	{
		$res = $this->server->get($key);
		$this->log->info('Memcache: getting '.$key.' returns '.$res);
		return $res;
	}

	/**
	 * set   provides set functionality
	 * @param string the key to set
	 * @param string the value to set it to
	 * @returns boolean
	 */
	public function set($key, $value)
	{
		$res = $this->server->set($key,$value);
		$this->log->info('Memcache: setting '.$key.' to '.$value);
		if(! $res)
		{
			$this->errorMessage = "Memcache error - setting";
			return false;
		}
		return true;
	}

    /**
     * setWithExpiration   provides set functionality with specified expiration
     * @param string the key to set
     * @param string the value to set it to
     * @param int expiration in seconds
     * @returns boolean
     */
    public function setWithExpiration($key,$value,$expiration)
    {
        if($expiration < 1)
        {
            $this->errorMessage = "Expire it please!";
            return false;
        }
        $res = $this->server->set($key,$value,0,$expiration);
        $this->log->info('Memcache: setting '.$key.' to '.$value.' with exp '.$expiration);
        if(! $res)
        {
            $this->errorMessage = "Memcache error - setting";
            return false;
        }
        return true;
    }

    /**
     * delete   remove a key
     * @param string key to delete
     * @returns boolean
     */
    public function delete($key)
    {
        $res = $this->server->delete($key);
        $this->log->info('Memcache: deleting '.$key);
        return true;
    }
}
?>
