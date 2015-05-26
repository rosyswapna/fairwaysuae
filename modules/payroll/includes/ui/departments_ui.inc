<?php
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");

$_search_button = "<input %s type='submit' class='combo_submit' style='border:0;background:url($path_to_root/themes/"
	."%s/images/locate.png) no-repeat;%s' aspect='fallback' name='%s' value=' ' title='"._("Set filter")."'> ";

$_select_button = "<input %s type='submit' class='combo_select' style='border:0;background:url($path_to_root/themes/"
	."%s/images/button_ok.png) no-repeat;%s' aspect='fallback' name='%s' value=' ' title='"._("Select")."'> ";

$all_items = ALL_TEXT;


//based on ui_lists.inc
//customer_list function
//--------------------------------------------------------------------------------------------
function department_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT dept_id,dept_name FROM ".TB_PREF."departments ";

	$mode = get_company_pref('no_department_list');

	if ($editkey)
		set_editor('department', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'dept_id', 'dept_name',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('dept_name'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
        'search' => array("dept_name"),        
		'spec_option' => $spec_option === true ? _("All departments") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new department') :
		_('Select department'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('department');
	return $ret;
}



function department_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo department_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}


?> 
