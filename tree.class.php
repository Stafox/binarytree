<?php
class tree {
	var $current;
	var $prev;
	var $table;
	var $steps;
	var $is_visit;
	var $right;
	function tree($tbl) {
		$this->table = $tbl;	
	}
	function set_current($value) {
		$this->current = $value;
	}
	function set_start($value) {
		$this->start = $value;
	}
	function find_llink($chto) {
		$query = "SELECT `id` FROM `{$this->table}` WHERE `kuda` = '{$chto}' LIMIT 1"; #1
		$result = mysql_query($query) or die("#1: Невозможно выполнить SELECT:".mysql_error());
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result);
			return $row['id'];
		} else return -1;
	}
	function find_rlink($kuda, $id) {
		$query = "SELECT `id` FROM `{$this->table}` WHERE `kuda` = '{$kuda}' AND `id` > '$id' LIMIT 1"; #2
		$result = mysql_query($query) or die("#2: Невозможно выполнить SELECT:".mysql_error());
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result);
			return $row['id'];
		} else return -1;
	}
	function get_prev($id, $previos) {
		$query = "SELECT `id` FROM `{$this->table}` WHERE `chto` = (SELECT `kuda` FROM `{$this->table}` WHERE `id` = '$id' LIMIT 1) AND `chto` = '{$previos}'"; #5
		$result = mysql_query($query) or die("#5: Невозможно выполнить SELECT:".mysql_error());
		if(mysql_num_rows($result) != 0) {
			while($row = mysql_fetch_array($result))
				if($this->is_visit[$row['id']])	break;
			return $row['id'];
		} else return -1;
	}
	function select_current($current_id) {
		$query = "SELECT * FROM `{$this->table}` WHERE `id`='{$current_id}'";	#6
		$result = mysql_query($query) or die("#6: Невозможно выполнить SELECT:".mysql_error());
		if(mysql_num_rows($result) == 1) return mysql_fetch_array($result);
		else return 0;
	}
	function go() {
		$query = "SELECT * FROM `{$this->table}` WHERE `id`='{$this->current}'"; #3
		$result = mysql_query($query) or die("#3: Невозможно выполнить SELECT:".mysql_error());
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result);
			$this->prev[$this->current] = $row['kuda'];
			$this->is_visit[$this->current] = true;
			$this->steps .= $this->current."|";
			if ($this->find_rlink($row['kuda'], $this->current) != -1) $this->right[$this->current] = true;
			if ($this->find_llink($row['chto']) != -1) {
				$this->set_current($this->find_llink($row['chto']));
				$arr = $this->select_current($this->current);
				$this->prev[$this->current] = $arr['kuda'];
				$this->is_visit[$this->current] = true;
				$this->go();
			} else if ($this->find_rlink($row['kuda'], $this->current) != -1) {
				$this->set_current($this->find_rlink($row['kuda'], $this->current));
				$arr = $this->select_current($this->current);
				$this->prev[$this->current] = $arr['kuda'];
				//$this->is_visit[$this->current] = true;
				$this->go();
			} else if ($this->find_rlink($row['kuda'], $this->current) == -1 && $this->find_llink($row['chto']) == -1) {
				while($this->get_prev($this->current, $this->prev[$this->current]) > 0) {
					$arr2 = $this->select_current($this->current);
					$this->prev[$this->current] = $arr2['kuda'];
					
					//$this->steps .= '(у '.$this->current.' есть справа '.$this->right[$this->current].')'."( справа заходили: ".$this->is_visit[$this->find_rlink($arr2['kuda'], $this->current)].")";
					
					if ($this->find_rlink($arr2['kuda'], $this->current) != -1 && $this->right[$this->current] && $this->is_visit[$this->find_rlink($arr2['kuda'], $this->current)] == false) {
						$this->set_current($this->find_rlink($arr2['kuda'], $this->current));
						$this->go();
					}
					$this->current = $this->get_prev($this->current, $this->prev[$this->current]);
					if($this->current > 0)$this->steps .= $this->current."|";
				}
				$this->is_visit = array_fill(0, count($this->is_visit), false);
			}
		}
		
		$arr = $this->select_current($this->current);
		if ($this->find_rlink($arr['kuda'], $this->current) != -1) {
			$this->set_current($this->find_rlink($arr['kuda'], $this->current));
			$this->go();
		}	
		
	}
	function test($cur) {
		$arr = $this->select_current($cur);
		if ($this->find_rlink($arr['kuda'], $cur) != -1) {
			$this->set_current($this->find_rlink($arr['kuda'], $cur));
			$this->go();
		}	
	}
	function nice_steps($replace_to) {
		$steps = substr($this->steps, 0, strlen($this->steps) - 1);
		return str_replace("|", $replace_to, $steps);	
	}
}
?>