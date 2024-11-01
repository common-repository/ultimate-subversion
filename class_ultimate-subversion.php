<?php 
class UltimateSubversion extends ZO_Basics {
	//############################
	//! PRIVATE VARS
	//############################
	private $plugin_url;

	//############################
	//! PUBLIC VARS
	//############################
	public $table;

	//############################
	//! CONSTRUCTORS
	//############################
	function __construct() {
		parent::__construct();
		global $wpdb;
		global $ZO_US_config;

		$this->table = $wpdb->prefix.'svnrepos';
		
		$this->plugin_url = 'http://code.zero-one.ch';
		$this->plugin_url .= '/?productid=' . md5("Ultimate-Subversion");
		if($this->install_tables()) {
			$this->adding_installation_stats();
			//print "<p>Databasetables has been installed...</p>";
		}
	}
	
	function __destruct() {}
	
	//############################
	//! PRIVATE
	//############################
	private function install_tables() {
		global $wpdb;
		$backval = 0;

		if(strtolower($wpdb->get_var("show tables like '".$this->table."'")) != strtolower($this->table)) {
			// If the table allready exists, return 0 and quit
			// The Table Structure
	   		$structure = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
				`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`Name` VARCHAR( 250 ) NOT NULL ,
				`Repository` VARCHAR( 250 ) NOT NULL ,
				`Path` VARCHAR( 250 ) NOT NULL ,
				`Username` VARCHAR( 250 ) NOT NULL ,
				`Password` VARCHAR( 250 ) NOT NULL ,
				`AlternativeName` VARCHAR( 250 ) NOT NULL ,
				`WebSVNPrefix` VARCHAR( 250 ) NOT NULL ,
				UNIQUE (`Name`)
				) ENGINE = MYISAM";
	    	$wpdb->query($structure);

			// The First Entry
			$firstdata = "INSERT INTO `".$this->table."`(`ID` ,`Name` ,`Repository` ,`Path` ,`Username` ,`Password` ,`AlternativeName` ,`WebSVNPrefix`)
			VALUES (NULL ,  'ultimatesubversion',  'http://svn.wp-plugins.org/',  '/ultimate-subversion/',  '',  '',  'Ultimate-Subversion',  '');";
			$wpdb->query($firstdata);
			$backval++;
		}
	
		return $backval;
	}
	
	private function adding_installation_stats() {
		global $_GET;
		
		if(!array_key_exists('productid', $_GET)) {		
			$ch = curl_init($this->plugin_url); 
	
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_exec($ch); 
	
			// Check if any error occured 
			if(curl_errno($ch)) { 
	    		return true; 
			} else { 
	    		return false; 
			} 
	
			// Close handle 
			curl_close($ch); 
		}
		return false;
	}
	
	private function show_svnlogs($functiondatadetails = array(),$onlyheadrev = 0) {
		global $_basepath;
		require_once($_basepath."/includes/phpsvnclient/phpsvnclient.php");
		
		$backval = "";
		//http://svn.col.genotec.ch/viewphp/revision.php?repname=sysinternal&rev=10
		$config = array();
		$config['repo'] = $functiondatadetails['Repository'];
		$config['user'] = $functiondatadetails['Username'];
		$config['pass'] = $functiondatadetails['Password'];
		
		$svn  = new phpsvnclient;
		$svn->setRepository($config['repo']);
		
		if ( isset($config['user']) && isset($config['pass']) ){
			$svn->setAuth($config['user'], $config['pass']);
		}
		
		$v = $svn->getVersion();
		// Sets the name of the Repository
		if($functiondatadetails['AlternativeName']) {
			$config['reponame'] = $functiondatadetails['AlternativeName'];
		} else {
			$config['reponame'] = $svn->_http->host_name;
		}
		/*$rev = $v-$qty-1;
		$rev = 10;*/
		
		if($functiondatadetails['Path']) {
			//$files = $svn->getDirectoryFiles($functiondatadetails['Path']);			
			$logs = $svn->getFileLogs($functiondatadetails['Path'], -1);
		} else {
			$logs = $svn->getRepositoryLogs(-1);
		}
		
		//$backval .= $this->print_debug($svn->getDirectoryFiles("/trunk/"));
		//$backval .= $this->print_debug($logs);
		//$backval .= $this->print_debug($config);
		$backval .= '
		<script type="text/javascript" language="JavaScript">
			function toggle(ID){
				var div = document.getElementById(\'ultimatesubversion_logs\');
			
				if (div.style.display == \'none\') {
					div.style.display = \'block\';
				} else {
					div.style.display = \'none\';
				}
			}
		</script>';
		$backval .= '<h3>Repository: '.$config['reponame'].'</h3><div id="ultimatesubversion_logs_head"><ul>';
		$head_log = $logs[count($logs)-1];
		$head_log['version'] = 'HEAD';
		$backval .= $this->show_svnlogs_entry($head_log,$functiondatadetails);
		$backval .= '</ul></div>';

		if(!$onlyheadrev and count($logs)>=2) {
			if ( is_array($logs) ) {			
				$backval .= '<a href="javascript:toggle(ultimatesubversion_logs);">Show more logs...</a><div id="ultimatesubversion_logs" style="display:none"><ul>';
				for($i=count($logs)-2;$i>=0;$i--) {
					$backval .= $this->show_svnlogs_entry($logs[$i],$functiondatadetails);
				}
			}
			$backval .= '</ul></div>';
		} 
				
		$backval .= '<br />';
		return $backval;

	}
	
	private function show_svnlogs_entry($logs, $functiondatadetails = array()) {
		require_once(ABSPATH . WPINC . '/registration.php');
		$backval = "";

		if(username_exists( $logs['author'] )) {
			$userdata = get_userdatabylogin( $logs['author'] );
			//$backval .= $this->print_debug($userdata);
			if($userdata->user_url) {
				$logs['author'] = '<a href="'.$userdata->user_url.'" target="_new">'.$userdata->display_name.'</a>';
			} else {
				$logs['author'] = $userdata->display_name;
			}
			$logs['author'] .= ' (<a href="mailto:'.$userdata->user_email.'">'.$userdata->user_email.'</a>)';
		}
		
		if(!$logs['author']) { $logs['author'] = 'unknown'; }
		
		if(array_key_exists('WebSVNPrefix', $functiondatadetails)) {
			if($functiondatadetails['WebSVNPrefix']) {
				$websvnurl = $functiondatadetails['WebSVNPrefix'].'&rev='.$logs['version'];
				$logs['version'] = '<a href="'.$websvnurl.'" target="_new">'.$logs['version'].'</a>';
			}
		}
		
		$time = new DateTime($logs['date']);
		$backval .= '<li>';
		$backval .= '<span class="ultimatesubversion_version">Revsion: ' . $logs['version'] . '</span>';
		$backval .= '<span class="ultimatesubversion_date">  ' . $time->format(get_option('date_format')) . '</span>';
		$backval .= '<span class="ultimatesubversion_author">Author: ' . $logs['author'] . '</span>';
		$backval .= '<span class="ultimatesubversion_comment">Message: ' . $logs['comment'] . '</span>';
		$backval .= "</li>";
		
		return $backval;
	}
	
	//############################
	//! PUBLIC
	//############################
	public function loadfrontend($content) {
		global $ZO_US_config;
		global $wpdb;
		
		$myregex = $ZO_US_config['regex'][0];
		$tmp = $content;
		if (preg_match_all($myregex, $tmp, $matches)){
			foreach($matches[0] as $match) {
				$functiondata = $match;
				$functiondata = strtolower(str_replace("%","",$functiondata));
				$functiondatadetails = explode(":",$functiondata);
				
				//Get the Repository Details
				if(array_key_exists(1, $functiondatadetails)) {
					$wpdb->query("SELECT * FROM ".$this->table." WHERE `Name` like '".$functiondatadetails[1]."' LIMIT 1");
					$functiondatadetails_db = $wpdb->get_results($wpdb->last_query,"ARRAY_A");
				}
				
				switch($functiondatadetails[0]) {
					case "svnlog":
						$backval = $this->show_svnlogs($functiondatadetails_db[0]);
						break;
					case "svnhead":
						$backval = $this->show_svnlogs($functiondatadetails_db[0],1);
						break;
					default:
						unset($backval);
						break;
				}
				if($backval) {
					$content = str_replace($match, $backval, $content);
				}
			}
		}
		return $content;
	}

}
?>