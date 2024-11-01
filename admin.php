<?php 
function ZO_US_display_adminmenu() {
	global $wpdb;
	global $ZO_US_config; // Genotec Global Vars
	global $_ultimatesubversion;

	// variables for the field and option names 
	$formname = 'ZO_US__adm01';
    $hidden_field_name = 'mt_submit_hidden';
	
	// See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Save the posted value in the database
        ZO_US_register_settings();
        // Put an options updated message on the screen
		?>
		<div class="updated"><p><strong><?php _e('Options saved.', $formname ); ?></strong></p></div>
		<?php
    }
	
	// Now display the options editing screen
	$myoptionlist = ZO_US_create_optionlist($formname);
	// options form
	echo '<div id="icon-plugins" class="icon32"></br></div>'."<h2>" . __( $ZO_US_config['name_long']. ' Settings', $formname ) . "</h2>";
	echo '<div class="wrap">';
	if(count($myoptionlist)) {
		?>
	    <h1>Settings</h1>
	
		<form name="form1" method="post" action="">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
			<?php echo $myoptionlist[1]; ?>
		
		<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options', $formname) ?>" />
		</p>
		<hr />
		</form><?php
	}
	echo '<h2>Edit Repositories</h2>';
	$query = "select * from ".$_ultimatesubversion->table." ORDER BY Name";
	echo $_ultimatesubversion->do_createTable($wpdb->get_results($query,"ARRAY_A"),$_ultimatesubversion->table,0,1);
	echo "</br>";	echo '</div>';

}

/*
Stores the settings in the Database
*/
function ZO_US_register_settings() {
	global $ZO_US_config; 
	// Get all fiels which as to get stored
	$option_array = array_keys($ZO_US_config['options']);
	
	// and go trough them
	for($i = 0; $i < count($option_array); $i++) {
		$options_array = array_keys($ZO_US_config['options'][$option_array[$i]]);
		for($j = 0; $j < count($options_array); $j++) {
			$opt_val = $_POST[ $options_array[$j] ];
        	// Save the posted value in the database
        	update_option( $options_array[$j], $opt_val );
        }
	}
}
	
function ZO_US_create_optionlist($formname) {
	global $ZO_US_config; // Genotec Global Vars

	$option_array = array_keys($ZO_US_config['options']);
	$backval = array();
	for($i = 0; $i < count($option_array); $i++) {
		$backval[1] .= '<fieldset>
		<legend><h3>'.$option_array[$i].'</h3></legend>
		<ul>';

		#print '<div id="fergcorp_'.$option_array[$i].'" class="postbox closed">
		#<div class="handlediv" title="Zum umschalten klicken"><br></div><h3 class="hndle"><span>'.$option_array[$i].'</span></h3>
		#<div class="inside">';

		$options_array = array_keys($ZO_US_config['options'][$option_array[$i]]);

		for($j = 0; $j < count($options_array); $j++) {
			$backval[0] .= $options_array[$j]. ",";
			$label = $ZO_US_config['options'][$option_array[$i]][$options_array[$j]];
			#$backval[1] .= '<tr><td>'. $label.' </br></td><td><input type="text" name="'.$options_array[$j].'" value="'.get_option($options_array[$j]).'" size="20"></td></tr>';

			$backval[1] .= '<li><label for="field_'.$options_array[$j].'">'.$label.'</label><input id="field_'.$options_array[$j].'" type="text" name="'.$options_array[$j].'" value="'.get_option($options_array[$j]).'" size="20"></li>';
		}
		$backval[1] .= '</ul></fieldset>';
		#$backval[1] .= '</div></div>';

	}
	
	return $backval;
}
?>