<?php

include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");


// SC: 18.04.2012: include various helpers
include_once($path_to_root."/modules/payroll/includes/ui/employee_ui.inc");
include_once($path_to_root."/modules/payroll/includes/ui/departments_ui.inc");
include_once($path_to_root."/modules/payroll/includes/ui/payslip_batches_ui.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/payslip_ui.inc");
include_once($path_to_root."/modules/payroll/includes/ui/paytype_ui.inc");
include_once($path_to_root."/modules/payroll/includes/ui/taxes_ui.inc");



function job_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT job_name_id,job_name FROM ".TB_PREF."jobnames ";

	$mode = get_company_pref('no_leave_list');

	if ($editkey)
		set_editor('job', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'job_name_id', 'job_name',
	array(
	    'order' => array('job_name'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
        'search' => array("job_name"),        
		'spec_option' => $spec_option === true ? _("All jobs") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new job') :
		_('Select job'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('leave');
	return $ret;
}


function job_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo job_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function job_list_row($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	echo "<tr>";
	job_list_cells($label, $name, $selected_id, $all_option, 
	$submit_on_change, $show_inactive, $editkey);
	echo "</tr>";
}

//------------------------------------------------------------------------
function leave_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT ltyp_id,ltyp_name FROM ".TB_PREF."leavetypes ";

	$mode = get_company_pref('no_leave_list');

	if ($editkey)
		set_editor('leave', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'ltyp_id', 'ltyp_name',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('ltyp_name'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
        'search' => array("ltyp_name","name","tax_id"),        
		'spec_option' => $spec_option === true ? _("All leaves") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new leave') :
		_('Select department'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('leave');
	return $ret;
}


function leave_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo leave_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

//-----------------------------------------------------------------------------
function payroll_accounts_list($name, $selected_id=null, $skip_bank_accounts=false,
	$cells=false, $all_option=false, $submit_on_change=false, $all=false)
{
	
		$sql = "SELECT chart.account_code, chart.account_name, type.name, chart.inactive, type.id
			FROM ".TB_PREF."chart_master chart,".TB_PREF."chart_types type
			WHERE chart.account_type=type.id and chart.account_type=".AC_TYPE_PAYROLL;

	return combo_input($name, $selected_id, $sql, 'chart.account_code', 'chart.account_name',
	array(
		'format' => '_format_account',
		'spec_option' => $all_option===true ?  _("Use Item Sales Accounts") : $all_option,
		'spec_id' => '',
		'type' => 2,
		'order' => array('type.class_id','type.id','account_code'),
		'search_box' => $cells,
			'search_submit' => false,
			'size' => 12,
			'max' => 10,
			'cells' => true,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'category' => 2,
		'show_inactive' => $all
	) );

}


function payroll_list_cells($label, $name, $selected_id=null, 
	$skip_bank_accounts=false, $cells=false, $all_option=false, 
	$submit_on_change=false, $all=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo payroll_accounts_list($name, $selected_id, 
		$skip_bank_accounts, $cells, $all_option, $submit_on_change, $all);
	echo "</td>\n";
}





?>
