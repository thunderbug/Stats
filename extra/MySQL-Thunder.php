<?php

/**
 * Class MySQL
 *
 * Database Connection
 */
class MySQL{
	private $MySQL;

	private $Session;
	private $DBTable;
	private $CurrentDB;

    public $Queries = 0;
    public $AllQueries = array();

    /**
     * Class Construct
     */
    public function __construct(){
		require_once("/MySQL.php");

		foreach($this->MySQL as $database => $connection){
			$ExplodedDB = explode(",", $connection["database"]);
			$this->Session[$database] = mysqli_connect($connection["host"], $connection["username"], $connection["password"], $ExplodedDB[0]);
			$this->CurrentDB[$database] = $ExplodedDB[0];

			foreach($ExplodedDB as $value){
				$this->DBTable[$value] = $database;
			}
		}
	}

    /**
     * Class Destruct
     */
    public function __destruct(){
		global $Settings;
		
		foreach($this->MySQL as $database => $connection){
			mysqli_close($this->Session[$database]);
		}
	}

    /**
     * Set Connection to database
     * @param $database String Database
     * @return mixed DatabaseResource
     */
    private function setConnection($database){
		if($database == "jff_forum"){
			$database = "forum";
		}

		if($database == "jff_site"){
			$database = "site";
		}

		$host = $this->DBTable[$database];
		if($database != $this->CurrentDB[$host]){
			mysqli_select_db($this->Session[$host],$database);
		}
		
		return $this->Session[$host];
	}

	/*
	* $database = forum or addon
	* $table = database table
	* $fields = fields in the table
	* $clause = the where or limit
	* $values = the values of the clause
	*
	* Example : $MySQL->select("forum","users",array("*"),"WHERE `user_id` = ?",array("1"));
	*
	* Returns array with data from the table
	*/
	public function select($database,$table,$fields,$clause,$values){
        $i = 0;
        $select_field = "";
        $types = array();
		$types[] = "";

		foreach($fields as $field){
			if($i == 0){
				$i++;
				$select_field .= $field;
			}else{
				$select_field .= ", ".$field;
			}
		}

		$stmt = mysqli_prepare($this->setConnection($database), "SELECT ".$select_field." FROM `".$table."` ".$clause);	

		$this->AllQueries[] = "SELECT ".$select_field." FROM `".$table."` ".$clause;
		$ArrayList = debug_backtrace();
		$this->AllQueries[] = $ArrayList[0]["file"]."(".$ArrayList[0]["line"].")";
		
		if($values != ""){
			foreach($values as $key => $value){
				$types[0] .= "s";
				$i++;
				$types[$i] = &$values[$key];
			}
			call_user_func_array(array($stmt,"bind_param"), $types);
		}
		
		if($stmt === false){
			die("SELECT ".$select_field." FROM `".$table."` ".$clause);
		}else{
			$stmt->execute();
			$this->Queries++;
		}
		
		$data = array();
		$variables = array();
		$array = array();
		
		$meta = $stmt->result_metadata();
		while($field = $meta->fetch_field()){
			$variables[] = &$data[$field->name];
		}

		call_user_func_array(array($stmt,"bind_result"), $variables);
		
		$i = 0;
		
		while($stmt->fetch()){
			$array[$i] = array();
			foreach($data as $k=>$v){
				$array[$i][$k] = $v;
			}
			$i++;
		}
		
		if(count($array) == 1){
			$array = $array[0];
			unset($array[0]);
		}
		
		$stmt->free_result();
		$stmt->close();
		
		return array_filter($array);
	}
	
	/*
	* $database = forum or addon
	* $table = database table
	* $fields = fields that change their value
	* $values = the values of those fields in the same order
	* $clause = the where or limit
	* $clause_values = the values of the clause
	*
	* Example : $MySQL->update("forum","users",array("pass"),array("abc"),"WHERE `user_id` = ?",array("1"));
	*
	* Returns nothing
	*/
	public function update($database,$table,$fields,$values,$clause,$clause_values){
		$i = 0;
        $types = array();

		$update = "UPDATE `".$table."` SET ";
		foreach($fields as $value){
			if($i == 0){
				$update .= "`".$value."` = ? ";
				$i++;
			}else{
				$update .= ", `".$value."` = ? ";
			}
		}
		
		$update .= $clause;
		$stmt = mysqli_prepare($this->setConnection($database), $update);

		$this->AllQueries[] = $update;
        $ArrayList = debug_backtrace();
        $this->AllQueries[] = $ArrayList[0]["file"]."(".$ArrayList[0]["line"].")";

		$values = array_merge($values,$clause_values);

        $types[0] = "";
		foreach($values as $key => $value){
			$types[0] .= "s";
			$i++;
			$types[$i] = &$values[$key];
			//Maybe later better dedection what the value is
		}
		
		call_user_func_array(array($stmt,"bind_param"), $types);
		$stmt->execute();
		$this->Queries++;
		$stmt->close();
	}
	
	
	/*
	* $database = forum or addon
	* $table = database table
	* $fields = fields that needs inserted values
	* $values = the values of those fields in the same order
	*
	* Example : $MySQL->insert("forum","users",array("pass"),array("abc123"));
	*
	* Returns nothing
	*/	
	public function insert($database,$table,$fields,$values){
		$query = "INSERT INTO `".$table."` (";
		$i = 0;
        $update1 = "";
        $types = array();
			
		foreach($fields as $row){
			if($i == 0){
				$query .= "`".$row."`";
				$update1 = "? ";
				$i++;
			}else{
				$query .= ", `".$row."`";
				$update1 .= ", ? ";
			}
		}
		
		$query .= ") VALUES (";
		$query .= $update1. ");";
		$stmt = mysqli_prepare($this->setConnection($database), $query);

		$this->AllQueries[] = $query;
        $ArrayList = debug_backtrace();
        $this->AllQueries[] = $ArrayList[0]["file"]."(".$ArrayList[0]["line"].")";

        $types[0] = "";
		foreach($values as $key => $value){
			$types[0] .= "s";
			$i++;
			$types[$i] = &$values[$key];
			//Maybe later better dedection what the value is
		}
		
		call_user_func_array(array($stmt,"bind_param"), $types);
		$stmt->execute();
		$this->Queries++;
		$stmt->close();
	}
	
	/*
	* $database = forum or addon
	* $table = database table
	* $clause = the where or limit
	* $clause_values = the values of the clause
	*
	* Example : $MySQL->delete("forum","users","WHERE `user_id` = ?",array("1"));
	*
	* Returns nothing
	*/
	public function delete($database,$table,$clause,$values){
        $i = 0;
        $types = array();

		$query = "DELETE FROM `".$table."` ".$clause;
		$stmt = mysqli_prepare($this->setConnection($database), $query);

		$this->AllQueries[] = $query;
        $ArrayList = debug_backtrace();
        $this->AllQueries[] = $ArrayList[0]["file"]."(".$ArrayList[0]["line"].")";

		if($values != ""){
			foreach($values as $key => $value){
				$types[0] .= "s";
				$i++;
				$types[$i] = &$values[$key];
				//Maybe later better dedection what the value is
			}
			call_user_func_array(array($stmt,"bind_param"), $types);
		}
		
		$stmt->execute();

		$this->Queries++;
		$stmt->close();
	}
	
	/*
	* $database = forum or addon
	* $table = database table
	* $clause = the where or limit
	* $values = the values of the clause
	*
	* Example : $MySQL->rows("forum","users","WHERE `user_id` = ?",array("1"));
	*
	* Returns amount of rows
	*/
	public function rows($database,$table,$clause,$values){
        $i = 0;
        $types = array();

		$stmt = mysqli_prepare($this->setConnection($database), "SELECT COUNT(*) FROM `".$table."` ".$clause);

	    $this->AllQueries[] = "SELECT COUNT(*) FROM `".$table."` ".$clause;
        $ArrayList = debug_backtrace();
        $this->AllQueries[] = $ArrayList[0]["file"]."(".$ArrayList[0]["line"].")";
		
		if($values != ""){
			foreach($values as $key => $value){
				if($value != ""){
					$types[0] .= "s";
					$i++;
					$types[$i] = &$values[$key];
					//Maybe later better dedection what the value is
				}
			}
			call_user_func_array(array($stmt,"bind_param"), $types);
		}
		
		$stmt->execute();
		$this->Queries++;
	
		$data = array();
		$variables = array();
		$array = array();
		
		$meta = $stmt->result_metadata();
		while($field = $meta->fetch_field()){
			$variables[] = &$data[$field->name];
		}
		
		call_user_func_array(array($stmt,"bind_result"), $variables);
		
		$i = 0;
		
		while($stmt->fetch()){
			$array[$i] = array();
			foreach($data as $k=>$v){
				$array[$i][$k] = $v;
			}
			$i++;
		}
		
		$stmt->free_result();
        $stmt->close();

		return $array[0]['COUNT(*)'];
	}

    /**
     * Truncate table
     * @param $database String Database
     * @param $table String Table
     */
    public function truncate($database,$table){
		$query = "TRUNCATE `".$table."`";
		mysqli_query($this->setConnection($database),$query);
		$this->Queries++;
	}

    /**
     * OldStyles Query
     * @param $database String Database
     * @param $query String Query
     * @return array Results
     */
    public function oldQuery($database,$query){
	    $this->AllQueries[] = $query;
        $ArrayList = debug_backtrace();
        $this->AllQueries[] = $ArrayList[0]["file"]."(".$ArrayList[0]["line"].")";

		$result = mysqli_query($this->setConnection($database),$query);
		$this->Queries++;
		while($row[] = mysqli_fetch_assoc($result));

		return array_filter($row);
	}

    /**
     * Get Affected Rows from last Query in certain database
     * @param $database String Database
     * @return int Rows
     */
    public function getAffectedRows($database){
        return mysqli_affected_rows($this->setConnection($database));
    }

    /**
     * Get Secured String for use in a query
     * @param $data String Input
     * @return string Output
     */
    public function getEscapedString($data){
        return mysqli_escape_string($this->setConnection("site"),$data);
    }
}