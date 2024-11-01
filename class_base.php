<?php 
class ZO_Basics {
	//############################
	//! PRIVATE VARS
	//############################
	
	//############################
	//! PUBLIC VARS
	//############################


	//############################
	//! CONSTRUCTORS
	//############################
	function __construct() {}
	function __destruct() {}
	
	//############################
	//! PRIVATE
	//############################

	//############################
	//! PUBLIC
	//############################
	public function print_debug($message)  {
		$backval = "<pre>".print_r($message,1)."</pre>";
		return $backval;
	}
	
	public function do_createTable($myresult,$tableName, $keyvalue = 9999, $deleteactive = 1) {
		global $current_user;
    	global $wpdb;

    	get_currentuserinfo();

		$backval = "";
		$queryok = false;

		$fieldnames = array_keys($myresult[0]);
		$numfields = count($fieldnames);
		
		if(array_key_exists('tablehash', $_POST)) {
			if(md5($tableName) == $_POST['tablehash']) {
				$queryok = true;
			} else {
				$queryok = false;
			}
		}
		
		
		if(array_key_exists('updateid',$_POST) and $queryok) {
			$query = "UPDATE `$tableName` SET ";
			for($i = 0; $i < $numfields ; $i++) {
				if(array_key_exists($fieldnames[$i], $_POST)) {
					$value = $_POST[$fieldnames[$i]];
				} else {
					$value = "";
				}
				
				if(stristr($fieldnames[$i],'dateupdate')) {
					$query .= "`".$fieldnames[$i]."` = NOW(),";
				} elseif(stristr($fieldnames[$i],'userid')) {
					$query .= "`".$fieldnames[$i]."` = '".$current_user->user_login."',";
				} else {
					if($value) {
						$query .= "`".$fieldnames[$i]."` = '".$value."',";
					}
				}
			}
			$query = substr($query,0,strlen($query)-1);
			$query .= " WHERE `".$fieldnames[$keyvalue]."` = ".$_POST['updateid']." LIMIT 1";
			
			// Run the query and reload the previous query
			$last_query = $wpdb->last_query;
			$wpdb->query($query);
			$myresult = $wpdb->get_results($last_query,"ARRAY_A");
		}
		
		if(array_key_exists('delid',$_POST) and $queryok) {
			$query = "DELETE FROM `$tableName` WHERE `".$fieldnames[$keyvalue]."` = ".$_POST['delid']." LIMIT 1";
		
			// Run the query and reload the previous query
			$last_query = $wpdb->last_query;
			$wpdb->query($query);
			$myresult = $wpdb->get_results($last_query,"ARRAY_A");
			
		}
		// Table HEAD and FOOT
		$backval .= '<table class="widefat"><thead><tr>';
		for($i = 0; $i < $numfields ; $i++) {
			$namefield = $fieldnames[$i];
			$backval .= '<th>'.$namefield.'</th>';  
		}
		if($deleteactive) { $backval .= '<th>Delete</th>'; }

		$backval .= "</tr></thead><tfoot><tr>";
		for($i = 0; $i < $numfields ; $i++) {
			$namefield = $fieldnames[$i];
			$backval .= '<th>'.$namefield.'</th>';  
		}
		if($deleteactive) { $backval .= '<th>Delete</th>'; }
		$backval .= "</tr></tfoot><tbody>";
		// BODY
		for($i = 0; $i < count($myresult); $i++) {
			$backval .= '<tr><form method="post"  >';
			for($j=0;$j < $numfields; $j++) {
    			if(stristr($fieldnames[$j],'id')) {
    				$backval .= '<td><strong>'.$myresult[$i][$fieldnames[$j]].'</strong></td>';
    			} elseif(stristr($fieldnames[$j],'date') or stristr($fieldnames[$j],'userid')) {
					$backval .= '<td>'.$myresult[$i][$fieldnames[$j]].'</td>';
				} elseif(stristr($fieldnames[$j],'text')) {
					$backval .= '<td><textarea name="'.$fieldnames[$j].'" cols="70" rows="5">'.$myresult[$i][$fieldnames[$j]].'</textarea></td>';
				} else {
					$backval .= '<td><input type="text" name="'.$fieldnames[$j].'" value="'.$myresult[$i][$fieldnames[$j]].'"></td>';
				}

    		}
    		$updatebutton = '<input type="hidden" name="page" value="'.$_POST['page'].'" /><input type="hidden" name="tablehash" value="'.md5($tableName).'" />
					<input type="hidden" name="updateid" value="'.$myresult[$i][$fieldnames[$keyvalue]].'"><input type="submit" class="button-primary" value="Update" /></form>';
    		if($deleteactive) {
    			$deletebutton = '<form method="post"  >
					<input type="hidden" name="page" value="'.$_POST['page'].'" /><input type="hidden" name="tablehash" value="'.md5($tableName).'" />
					<input type="hidden" name="delid" value="'.$myresult[$i][$fieldnames[$keyvalue]].'"><input type="submit" class="button-primary" value="Delete" /></form>';
    		}
    		$backval .= '<td>'.$updatebutton.''.$deletebutton.'</td>';

    		$backval .= '</tr>';
		}
		
		// ADD NEW ELEMENT LINE
		$backval .= $this->do_createTableAdd($fieldnames,$tableName,$keyvalue);
		$backval .= '</tbody></table>';
		return $backval;
	}
	//#######################
	//! PROTECTED.tablestuff
	//#######################
	/*
		Create an sperate row for adding a new elemnt to the Table
	*/
	protected function do_createTableAdd($fields,$tableName,$keyfield) {
		global $_POST;
		global $wpdb, $current_user;
		
		$queryok = false;

		
		if(array_key_exists('tablehash', $_POST)) {
			if(md5($tableName) == $_POST['tablehash']) {
				$queryok = true;
			} else {
				$queryok = false;
			}
		}
		
		if(array_key_exists('tableforadd',$_POST) and $queryok) {
			$add_fields = "";
			$add_values = "";
			for($i = 0; $i < count($fields) ; $i++) {
				$add_fields .= '`'.$fields[$i].'`,';
				if(!array_key_exists($fields[$i], $_POST)) {
					
					if(stristr($fields[$i],'date')) {
						$add_values .= 'NOW(),';	
					} elseif(stristr($fields[$i],'userid')) {
						$add_values .= "'".$current_user->user_login."',";	
					} else {
						$add_values .= 'NULL,';
					}
					
				} else {
					$add_values .= "'".$_POST[$fields[$i]]."',";
				}
			}
			$add_fields = substr($add_fields,0,strlen($add_fields)-1);
			$add_values = substr($add_values,0,strlen($add_values)-1);
			
			$query = 'INSERT INTO `'.$tableName.'` ('.$add_fields.') VALUES ('.$add_values.')';
			
			// Run the query and reload the previous query
			#$last_query = $wpdb->last_query;
			$wpdb->query($query);
			#$myresult = $wpdb->get_results($last_query,"ARRAY_A");
		}
		$backval = '<tr><form method="post"  >
					<input type="hidden" name="page" value="'.$_POST['page'].'" />
					<input type="hidden" name="tablehash" value="'.md5($tableName).'" />
					<input type="hidden" name="tableforadd" value="'.$tableName.'" />';
		for($i = 0; $i < count($fields) ; $i++) {
			if($i != $keyfield) {
				if(stristr($fields[$i],'date') or stristr($fields[$i],'userid')) {
					$field = "";
				} elseif(stristr($fields[$i],'text')) {
					$field = '<textarea name="'.$fields[$i].'" cols="30" rows="2"></textarea>';
				} else {
					$field = '<input type="text" name="'.$fields[$i].'" value="">';
				}
				$backval .= '<td>'.$field.'</td>';
			} else {
				$backval .= '<td></td>';
			}
		}
		$backval .= '<td><input type="submit" class="button-primary" value="Add" /></form></td>';
		$backval .= '</tr>';
		return $backval;
	}
	
	/*
		Create a nice table from a result ressource. MySQL and MSSQL supported
	*/ 
	protected function do_createTableFromResult($myresult,$dbtype = "mysql") {
		$backval = "";
		
		// Get the Number of Fields
		if(strtolower($dbtype) == 'mssql') { $numfields = mssql_num_fields($myresult); }
		else { $numfields = mysql_num_fields($myresult); }
		
		// Table HEAD and FOOT
		$backval .= '<table class="widefat"><thead><tr>';
		for($i = 0; $i < $numfields ; $i++) {
			if(strtolower($dbtype) == 'mssql') { $namefield = mssql_field_name($myresult,$i); }
			else { $namefield = mysql_field_name($myresult,$i); }
			$backval .= '<th>'.$namefield.'</th>';  
		}
		$backval .= "</tr></thead><tfoot><tr>";
		for($i = 0; $i < $numfields ; $i++) {
			if(strtolower($dbtype) == 'mssql') { $namefield = mssql_field_name($myresult,$i); }
			else { $namefield = mysql_field_name($myresult,$i); }
			$backval .= '<th>'.$namefield.'</th>';  
		}
		$backval .= "</tr></tfoot><tbody>";
		
		// BODY
		if(strtolower($dbtype) == 'mssql') { 
			while ($row = mssql_fetch_array($myresult)) {
    			$backval .= '<tr>';
    			for($i=0;$i < $numfields; $i++) {
    				$backval .= '<td>'.$row[$i].'</td>';
    			}	
    			$backval .= '</tr>';
			}
		} else {
			while ($row = mysql_fetch_array($myresult)) {
    			$backval .= '<tr>';
    			for($i=0;$i < $numfields; $i++) {
    				$backval .= '<td>'.$row[$i].'</td>';
    			}	
    			$backval .= '</tr>';
			}
		}
			
		$backval .= '</tbody></table>';
		return $backval;
	}

}
?>