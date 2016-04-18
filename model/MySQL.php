<?php
include_once 'Registry.php';

class MySQL {
	protected $connection;
    protected $game;
	protected $database;
	
	protected static $cache;

    public function __construct($game){
        if($this->game != $game){
            unset($this->connection);
            $this->game = $game;
        }
    }

	/**
	 * Function that returns the current mysqli connection object on success, or a boolean false on failure
	 * @return boolean|mysqli 
	 */
	public function connect() {
		//create a new mysqli connection if is not already set
		if(!isset($this->connection)) {
            require_once("./config.php");
            $this->connection = new mysqli($config[$this->game]["host"],$config[$this->game]["username"],$config[$this->game]["password"],$config[$this->game]["database"]);
        }
		//handle error
		if($this->connection === false) {
			return false;
		}
		return $this->connection;
	}
	
	public function cacheInstance(){
		if(!isset(self::$cache))
			self::$cache = new Registry();
		return self::$cache;
	}
	
	/**
	 * Shortcut function ,executes the given query
	 * @param string $sql the query in string form to execute on the database
	 * @return mixed metadata resulting from query
	 */
	public function query($sql){
		$conn = $this->connect();
		$result = $conn->query($sql);
		return $result;
	}
	
	/**
	 * Executes the query and returns an array of requested data
	 * @param string $query query to execute
	 * @return boolean|multitype:unknown false if empty, or array of data
	 */
	public function select($query){
		$cache = $this->cacheInstance();
		$last = $cache->get("lastquery");
		
		if($last != null && $last==$query){
			return $cache->get("lastres");
		}
		
		$data = $this->query($query);
		if (empty($data))
			return false;
		
		$res = array();
		while ($row = $data->fetch_assoc()){
			$res[] = $row;
		}
		$cache->set("lastquery", $query);
		$cache->set("lastres", $res);
		return $res;
	}
	
	/**
	 * Simple "debug" function to check for errors
	 * @return multitype array containing both code and error message
	 */
	function error(){
		$conn = $this->connect();
		$err = array();
		$err[] = $conn->error;
		$err[] = $conn->errno;
		return $err;
	}
	
	/**
	 * Escapes the string parameter
	 * @param unknown $string escaped string
	 */
	function escape($string){
		$conn = $this->connect();
		return $conn->real_escape_string($string);
	}
	
	/**
	 * Returns a string with added quotes
	 * @param unknown $string values to quote
	 * @return string quoted value
	 */
	function quote($string){
		$esc = $this->escape($string);
		return "'".$esc."'";
	}
	
	/**
	 * Returns the total rows affected by the last query
	 */
	function rows(){
		return $this->connection->affected_rows;
	}
	
}
?>