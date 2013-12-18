<?php
//Here select table names
$_tables=array(
    "posts" => array(),
    "postmeta" => array(),
    'options' => array(
        'option_value' => array(
            'condition' => "option_name = 'siteurl' OR option_name = 'home'",
        ),
    ),
    'blogs' => array(
        'domain' => array(),
    ),
    'site' => array(),
	'menuitems' => array(),
	'links' => array(),
	'comments' => array()
);




$old_domain = $_GET['old'];
$new_domain = $_GET['new'];




$old_directory = $_GET['old_dir'];
$new_directory = $_GET['new_dir'];


/*if ( !$old_domain ) { echo "<br />Please specify your old string by using \$_GET['old']"; }
if ( !$new_domain ) { echo "<br />Please specify your new string by using \$_GET['new']"; }
//if ( !$old_directory ) { echo "<br />Please specify your old directory string by using \$_GET['old_dir']"; }
//if ( !$new_directory ) { echo "<br />Please specify your new directory string by using \$_GET['new_dir']"; }
if ( !$old_domain || !$new_domain ) {
    echo '<br />URL should be: http://yourdomain.com/replace_new.php?old={youroldstring}&new={yournewstring}&old_dir={youroldstring}&new_dir={yournewstring}. Note : old_dir and new_dir is optionals';
    die;
}*/

$config = file('wp-config.php');
// shift first line;
array_shift($config);
// pop 3 last line;
array_pop($config);
array_pop($config);
array_pop($config);
eval ( join("\r\n", $config) );
echo '<pre>';
//print_r(DB_NAME);
echo '</pre>';

error_reporting(E_ALL);

//echo DB_NAME;

$dblink = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME, $dblink);

//set execute time
ini_set('max_execution_time', 60*5);

//get old domain if not set
if(empty($old_domain)){
	$sql = "SELECT option_value FROM ".$table_prefix."options WHERE option_name LIKE 'siteurl'";
	$result = mysql_query($sql,$dblink);
	$row = mysql_fetch_array($result);
	$old_domain = $row[0];
	$old_domain = trim($old_domain);
	$old_domain = trim($old_domain,"/");	
}

//get new domain if not set
if(empty($new_domain)){
	$requestUri = $_SERVER['REQUEST_URI'];
	if(!empty($_GET['old'])){
		$requestUri = str_replace("?old=".$_GET['old'],"",$requestUri);
	}
	$requestUri = split("/",$requestUri);
	$resultParse = array();
	for($i=0;$i<count($requestUri)-1;$i++){
		$resultParse[] = $requestUri[$i];
	}
	$resultParse = implode("/",$resultParse);
	
	$new_domain = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$resultParse;
	
	//has char "/" in end of string old domain
	if(strrpos($old_domain,"/") == strlen($old_domain) - 1){
		$new_domain = $new_domain . "/";
	}
}



//check
if ( strlen($old_domain) == 0 ) { echo "<br />Option siteurl in table is empty. Please check again."; }
if ( strlen($new_domain) == 0 ) { echo "<br />Can't get current request url."; }

if ( strlen($old_domain) == 0 || strlen($new_domain) == 0 ) {
    echo '<br />URL should be: http://yourdomain.com/replace_new.php?old_dir={youroldstring}&new_dir={yournewstring}. Note : old_dir and new_dir is optionals';
    die;
}

//echo $old_domain,":",$new_domain;
//exit;


$_tablelist = array_keys($_tables);

//Here select standard table names
$tablelist=mysql_list_tables(DB_NAME);
$table = 0;
while ($table < mysql_num_rows($tablelist)) {
	$tablename = mysql_tablename($tablelist, $table);
	$pattern = preg_replace('/'.$table_prefix.'(\d+_)?/i', '', $tablename);
	
	if($tablename != $table_prefix . "postmeta" && $tablename != $table_prefix . "usermeta" && $tablename != $table_prefix . "contact_form_7"){
		if ( !in_array($pattern, $_tablelist) ) {
			$table++;
			continue;
		}
		echo "Processing ".$tablename." ...<br />";
		flush();
		
		$querytable = "SELECT * FROM " . $tablename;
		$result = mysql_query($querytable, $dblink) or die("Failed Field Query");
		
		echo "Checking fields ...<br />";
		flush();
		
		$i=0;
		$fields = array_keys($_tables[$pattern]);
		while ($i < mysql_num_fields($result)) {
			$field_name=mysql_fetch_field($result, $i);
			echo "Replacing for field ", $field_name->name, "...";
			flush();
			
			if ( !empty($fields) && !in_array($field_name->name, $fields) ) {
				$i++;
				continue;
				//$value = $row[$field_name->name];
			}
			
			$queryreplace = "UPDATE ".$tablename." SET ".$field_name->name."=REPLACE(".$field_name->name.", '$old_domain', '$new_domain')";
			
			if ( isset($_tables[$pattern][$field_name->name]['condition']) ) {
				$where = $_tables[$pattern][$field_name->name]['condition'];
				$queryreplace .= ' WHERE ' . $where;
			}
		
			echo $queryreplace;
			mysql_query($queryreplace, $dblink);
			echo " done.<br />";
			flush();
			$i++;
		}
	}
	
	$table++;
}

include('wp-load.php');

//Here select option table names
$table = 0;
while ($table < mysql_num_rows($tablelist)) {
	$tablename = mysql_tablename($tablelist, $table);
	$pattern = preg_replace('/'.$table_prefix.'(\d+_)?/i', '', $tablename);
	
	if($tablename != $table_prefix . "postmeta" && $tablename != $table_prefix . "usermeta"){
		if ( !in_array($pattern, $_tablelist) ) {
			/*$table++;
			continue;*/
			echo "Processing ".$tablename." ...<br />";
			flush();
			
			$querytable = "SELECT * FROM " . $tablename;
			$result = mysql_query($querytable, $dblink) or die("Failed Field Query");
			
			echo "Checking fields ...<br />";
			flush();
			
			$array_fields = array();
			$i=0;
			while ($i < mysql_num_fields($result)) {
				$field_name=mysql_fetch_field($result, $i);
				$array_fields[$i]['field_name'] = $field_name->name;
				$array_fields[$i]['primary_key'] = $field_name->primary_key;
				$array_fields[$i]['numeric'] = $field_name->numeric;
				$array_fields[$i]['type'] = strtolower($field_name->type);
				$i++;
			}
			echo "<pre>";
			$result = mysql_query($querytable, $dblink) or die("Failed Field Query");
			while($row = mysql_fetch_array($result)){
				$queryreplace = "UPDATE ".$tablename." SET ";
				$conditionsWhere = array();
				$fieldsUpdate = array();
				foreach($array_fields as $field){
					$fieldName = $field['field_name'];
					$fieldValue = $row[$fieldName];
					if($field['primary_key'] == 1){
						if($field['numeric'] == 1){
							$conditionsWhere[] = $fieldName . " = " . $fieldValue;
						}
						else{
							$fieldValue = addslashes($fieldValue);
							$conditionsWhere[] = $fieldName . " LIKE '" . $fieldValue . "'";
						}
					}
					//in mysql type varchar named is string, type text named is blob
					elseif((strpos($field['type'],"string") !== false || strpos($field['type'],"blob") !== false) && !empty($fieldValue) && strpos($fieldValue,$old_domain) !== false){
						$value = maybe_unserialize($fieldValue);
						if($value){
							if (is_array($value)) {
								array_str_replace($value, $old_domain, $new_domain, $old_directory, $new_directory);
							}
							else{
								 if (strpos($value, $old_domain) !== false) {
									$value = str_replace($old_domain,$new_domain,$value);
								 }
							}
							$value = maybe_serialize(stripslashes_deep($value));
							$fieldsUpdate[] = $fieldName . " = '" . $fieldValue . "'";
						}
					}
				}
				
				if(!empty($conditionsWhere) && !empty($fieldsUpdate)){
					$conditionsWhere = implode(" AND ",$conditionsWhere);
					$fieldsUpdate = implode(", ",$fieldsUpdate);
					
					$queryreplace .= $fieldsUpdate . " WHERE " . $conditionsWhere;
					echo $queryreplace;
					mysql_query($queryreplace, $dblink);
					echo " done.<br />";
					flush();
				}
				//break;
			}
			echo "</pre>";
		}
	}
	
	$table++;
}

// special case for options
if ($tablename = $table_prefix . "options") {
	echo "Processing ".$tablename." ...<br />";
	$sqlGet = "SELECT * FROM $tablename WHERE option_value LIKE '%$old_domain%'";
	if(!empty($old_directory)){
		$sqlGet .= " OR option_value LIKE '%$old_directory%'";
	}
	
	$option_query = mysql_query($sqlGet);
	while ($option_row = mysql_fetch_assoc($option_query)) {
		//$option_value = @unserialize($option_row['option_value']);
		if(in_array($option_row['option_name'],array('siteurl','home'))){
			continue;
		}
		//check and processing with records store old string
		if(strpos($option_row['option_value'],$old_domain) !== false || (!empty($old_directory) && strpos($option_row['option_value'],$old_directory) !== false)){
			echo "Option " . $option_row['option_name'];
			flush();
			$option_value = get_option($option_row['option_name']);
			
			if ($option_value) {
				array_str_replace($option_value, $old_domain, $new_domain, $old_directory, $new_directory);
				update_option($option_row['option_name'],$option_value);
				echo " done.";
			}
			
		}
		else{
			echo "Option " . $option_row['option_name'] . " hasn't string " . $old_domain;
			if(!empty($old_directory)){
				echo " and string " . $old_directory;
			}
		}
		echo "<br />";
		flush();
	}
}
// special case for postmeta
if($tablename = $table_prefix . "postmeta"){
	echo "Processing ".$tablename." ...<br />";
	$sqlGet = "SELECT * FROM $tablename WHERE meta_value LIKE '%$old_domain%'";
	if(!empty($old_directory)){
		$sqlGet .= " OR meta_value LIKE '%$old_directory%'";
	}
	$option_query = mysql_query($sqlGet);
	while ($option_row = mysql_fetch_assoc($option_query)) {
		//check and processing with records store old string
		if(strpos($option_row['meta_value'],$old_domain) !== false || (!empty($old_directory) && strpos($option_row['meta_value'],$old_directory) !== false)){
			echo "Postmeta " . $option_row['meta_key'] ." of post id " . $option_row['post_id'] . "<br />";
			flush();
			$option_value = get_post_meta($option_row['post_id'],$option_row['meta_key'],true);
			if ($option_value) {
				if (is_array($option_value)) {
					array_str_replace($option_value, $old_domain, $new_domain, $old_directory, $new_directory);
				}
				else{
					 if (strpos($option_value, $old_domain) !== false) {
						$option_value = str_replace($old_domain,$new_domain,$option_value);
					 }
					 if (!empty($old_directory) && strpos($option_value, $old_directory) !== false) {
						$option_value = str_replace($old_directory,$new_directory,$option_value);
					 }
				}
			}
			update_post_meta($option_row['post_id'], $option_row['meta_key'], $option_value);
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Process done.";
		}
		else{
			echo "Postmeta " . $option_row['meta_key'] . " hasn't string " . $old_domain;
			if(!empty($old_directory)){
				echo " and string " . $old_directory;
			}
		}
		echo "<br />";
		flush();
	}
}

// special case for usermeta
if($tablename = $table_prefix . "usermeta"){
	echo "Processing ".$tablename." ...<br />";
	$sqlGet = "SELECT * FROM $tablename WHERE meta_value LIKE '%$old_domain%'";
	if(!empty($old_directory)){
		$sqlGet .= " OR meta_value LIKE '%$old_directory%'";
	}
	$option_query = mysql_query($sqlGet);
	while ($option_row = mysql_fetch_assoc($option_query)) {
		if(strpos($option_row['meta_value'],$old_domain) !== false || (!empty($old_directory) && strpos($option_row['meta_value'],$old_directory) !== false)){
			echo "Usermeta " . $option_row['meta_key'] ." of user id " . $option_row['user_id'] . "<br />";
			flush();
			$option_value = get_user_meta($option_row['user_id'],$option_row['meta_key'],true);
			if ($option_value) {
				if (is_array($option_value)) {
					array_str_replace($option_value, $old_domain, $new_domain, $old_directory, $new_directory);
				}
				else{
					 if (strpos($option_value, $old_domain) !== false) {
						$option_value = str_replace($old_domain,$new_domain,$option_value);
					 }
					 if (!empty($old_directory) && strpos($option_value, $old_directory) !== false) {
						$option_value = str_replace($old_directory,$new_directory,$option_value);
					 }
				}
			}
			update_user_meta($option_row['user_id'], $option_row['meta_key'], $option_value);
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Process done.";
		}
		else{
			echo "Usermeta " . $option_row['meta_key'] . " hasn't string " . $old_domain;
			if(!empty($old_directory)){
				echo " and string " . $old_directory;
			}
		}
		echo "<br />";
		flush();
	}
}

/*add code to replace with tables used into plugins*/

//plugin contact form
//$tablename = $table_prefix . "contact_form_7";
//replace_value_on_table($tablename);
/*end add*/

exit();

function replace_value_on_table($tablename){
	
	//check table contact form exists
	$sql = "SELECT * FROM information_schema.tables WHERE table_schema = '".DB_NAME."' AND table_name = '$tablename';";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$array_fields_replace = array('form', 'mail', 'mail_2', 'messages', 'additional_settings');
		global $wpdb;
		
		echo "Processing ".$tablename." ...<br />";
		$option_query = mysql_query("SELECT * FROM $tablename");
		while ($option_row = mysql_fetch_assoc($option_query)) {
			$valuesUpdate = array();
			echo "Form contact  " . $option_row['title'] . ":";
			flush();
			$contactFormId = $option_row['cf7_unit_id'];
			foreach($array_fields_replace as $key){
				$value = maybe_unserialize($option_row[$key]);
				if($value){
					if (is_array($value)) {
						array_str_replace($value, $old_domain, $new_domain, $old_directory, $new_directory);
					}
					else{
						 if (strpos($value, $old_domain) !== false) {
							$value = str_replace($old_domain,$new_domain,$value);
						 }
						 if (!empty($old_directory) && strpos($value, $old_directory) !== false) {
							$value = str_replace($old_directory,$new_directory,$value);
						 }
					}
					$value = maybe_serialize(stripslashes_deep($value));
					$valuesUpdate[$key] = $value;
				}
			}
			
			if(!empty($valuesUpdate)){
				$result = $wpdb->update( $tablename, $valuesUpdate,
					array( 'cf7_unit_id' => absint( $contactFormId ) ) );
				
				if($result !== false){
					echo " done";
				}
				else{
					echo " fail";
				}
			}
			else{
				echo " no value to replace";
			}
			echo "<br />";
			flush();
		}
	}
}

// revervse string replace in array
function array_str_replace(&$target_array, $search, $replace, $old_directory, $new_directory) {
    if (is_array($target_array) || is_object($target_array)) {
		$iden = false;
		if (is_array($target_array)){
			$iden = "array";
		}
		elseif(is_object($target_array)){
			$iden = "object";
		}
        foreach ($target_array as $key => $value) {
            if (is_array($value)) {
                array_str_replace($value, $search, $replace, $old_directory, $new_directory);
            } elseif(is_object($value)) {
				array_str_replace($value, $search, $replace, $old_directory, $new_directory);
			} elseif (is_string($value) || is_numeric($value)) {
                if (strpos($value, $search) !== false) {
                    //var_dump("Key is $key and old value is: $value");
					if($iden == "array"){
						$target_array[$key] = str_replace($search, $replace, $target_array[$key]);
						//var_dump("Key is $key and new value is: $target_array[$key]");
					}elseif($iden == "object"){
						$target_array->$key = str_replace($search, $replace, $target_array->$key);
						//var_dump("Key is $key and new value is: ".$target_array->$key);
					}
                }
				/*if (!empty($old_directory) && strpos($target_array[$key], $old_directory) !== false) {
                    $target_array[$key] = str_replace($old_directory, $new_directory, $target_array[$key]);
                }*/
            }
        }
    }
    return false;
}
?>