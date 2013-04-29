<?php

/* Base Class for X PHP Objects                 
 * Copyright 2012 Xervanik Systems             
 * @author John Pettit <john.pettit@xervanik.com>   
 * @package XBase                         
 * @version 1.0.0                             
 * @package xbase
 */

// include Logger
require('log4php/Logger.php');
Logger::configure(dirname(__FILE__).'/log4php.xml');

/**
 * Base Class for X PHP Objects
 * @package XBase
 * @class xbase
 */

class xbase {
	/**
     * Property for holding the errorMessage of a failed operation
     * @access public
     * @var string
     */
	public $errorMessage;

	/**
	 * Property to hold the Logger object
	 * @access protected
	 * @var object
	 */
	protected $log = null;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->log = Logger::getLogger('xbase');
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		$this->log = null;
	}
}
