<?php 
include("config.php");
include("mysql.class.php");
include("tree.class.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Бинарное дерево</title>
</head>
<body>
<div style="width:800px;">
<div style="float:left;width:300px;word-wrap:break-word;">
<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>?action=go">
<input type="text" name="number" /><input type="submit" name="go" value="Обход" />
</form>
<br/>
<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>?action=clear">
<input type="submit" name="clear" value="Очистить линки" />
</form>
<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>?action=search">
<input type="submit" name="search" value="Найти линки" />
</form>
<?php
$mysql = new mysql($db_host, "", $db_base, $db_user, $db_pass);
$mysql->connect();
$tree = new tree("tree");
$query = "SELECT * FROM `{$tree->table}`";
$mysql->set_names("utf-8");
switch ($_GET['action']) {
	case 'clear':
	if(isset($_POST['clear']))
	{
		$result = mysql_query($query);
		echo '<table border="1">'; 
		while ($row = mysql_fetch_assoc($result))
		echo '<tr><td>'.$row['id'].'</td><td>'.$row['chto'].'</td><td>'.$row['kuda'].'</td><td>-</td><td>-</td></tr>';
		echo '</table>';
	}
	break;
	case 'search':
	if(isset($_POST['search']))
	{
		$result = mysql_query($query);
		echo '<table border="1">'; 
		while ($row = mysql_fetch_assoc($result))
		echo '<tr><td>'.$row['id'].'</td><td>'.$row['chto'].'</td><td>'.$row['kuda'].'</td><td>'.$tree->find_llink($row['chto']).'</td><td>'.$tree->find_rlink($row['kuda'], $row['id']).'</td></tr>';
		echo '</table>';
	}
	break;
	case 'go':
	if(isset($_POST['number'])) {
		$tree->set_current($_POST['number']);
		//$tree->set_start($_POST['number']);
		$tree->go();
		echo $tree->nice_steps("&rarr;");
		//echo $tree->get_prev(11, 1222);
	}
	break;
	case 'patch':
		if(isset($_POST['patch'])) {
			$steps = substr($_POST['steps'], 0, strlen($_POST['steps']) - 1);
			echo str_replace("|", "&rarr;", $steps);
			$step_array = explode("|", $steps);
			echo '<table border="1">';
				$query = "SELECT * FROM `{$tree->table}`";
				$result = mysql_query($query);
				//echo array_search(8, $step_array);
				//echo " ".$step_array[0];
				while ($row = mysql_fetch_assoc($result)) {
				if(is_int(array_search($row['id'], $step_array)) == false)
					echo '<tr><td>'.$row['id'].'</td><td>'.$row['chto'].'</td><td>'.$row['kuda'].'</td><td>-</td><td>-</td></tr>';
				else echo '<tr><td>'.$row['id'].'</td><td>'.$row['chto'].'</td><td>'.$row['kuda'].'</td><td>'.$tree->find_llink($row['chto']).'</td><td>'.$tree->find_rlink($row['kuda'], $row['id']).'</td></tr>';
				}
			echo '</table>';
		}
	break;
	default:
		$result = mysql_query($query);
		echo '<table border="1">'; 
		while ($row = mysql_fetch_assoc($result))
		echo '<tr><td>'.$row['id'].'</td><td>'.$row['chto'].'</td><td>'.$row['kuda'].'</td><td>-</td><td>-</td></tr>';
		echo '</table>';
	break;
}
$mysql->disconnect();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=patch" method="post">
<input type="hidden" name="steps" value="<?php echo $tree->steps; ?>" />
<input type="submit" name="patch" <?php if (!isset($_POST['go'])) echo 'disabled="disabled"'; ?> value="Прошивка" />
</form>
</div>
<div style="float:right;background:url(img/1.png) no-repeat;width:500px;height:500px;position:fixed;left:300px;">
</div>
</div>
</body>
</html>