<?php
class mysql {
	var $db_host;
	var $db_prefix;
	var $db_name;
	var $db_user;
	var $db_pass;
	var $connect;
	function mysql($host, $prefix, $name, $user, $pass) {
		$this->db_host = $host;
		$this->db_prefix = $prefix;
		$this->db_name = $name;
		$this->db_user = $user;
		$this->db_pass = $pass;	
	}
	function connect() {
		$this->connect = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die("Невозможно соединиться с сервером ".$this->db_host.": ".mysql_error()); 
		mysql_select_db($this->db_name, $this->connect) or die("Невозможно соединиться с базой ".$this->db_name.": ".mysql_error());
	}
	function disconnect() {
		mysql_close($this->connect);
	}
	function set_names($charset) {
		mysql_query('SET NAMES '.$charset);	
	}
}
?>