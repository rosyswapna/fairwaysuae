<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_SALARY_STRUCTURE';
$path_to_root = "../../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Manage Salary Structure"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/modules/payroll/includes/db/jobpositions_db.inc");
include_once($path_to_root . "/modules/payroll/includes/db/salary_structure_db.inc");
$selected_id = get_post('job_position_id','');
//--------------------------------------------------------------------------------------------

function can_process($selected_id)
{
	if (!$selected_id) 
	{
		display_error(_("Select job position"));
		set_focus('job_position_id');
		return false;
	} 

	foreach($_POST as $p=>$val){

		if(substr($p,0,7) == "Account"){

			if(input_num("Debit".$val) > 0 && input_num("Credit".$val)){
				display_error(_("Both Credit and Debit amount for a payroll rule not allowed."));
				set_focus("Debit".$val);
				return false;
			}

		}
	}

	return true;
}

//--------------------------------------------------------------------------------------------

function handle_submit(&$selected_id)
{
	global $path_to_root, $Ajax;

	if (!can_process($selected_id))
		return;

	//echo "<pre>";print_r($_POST);echo "</pre>";exit;
	$payroll_rules = array();
	foreach($_POST as $p=>$val){
		if(substr($p,0,7) == "Account"){

			if(input_num("Debit".$val) > 0){
				$type = DEBIT;
				$amount = @input_num("Debit".$val);
			}else{
				$type = CREDIT;
				$amount = @input_num("Credit".$val);
			}

			if($amount > 0){
				$payroll_rules[] = array(
				'job_position_id'=> $selected_id,
				'pay_rule_id'	=> $val,
				'pay_amount'	=> $amount,
				'type'		=> $type
				);
			}
			
		}
	}

	if(empty($payroll_rules)){
		display_error(_("No data entered"));
	}else{
	
		if(exists_salary_structure($selected_id)){
			reset_salary_structure($selected_id);
		}

		add_salary_structure($payroll_rules);
			
		display_notification(_("Salary Structure Updated."));		
	}
	$Ajax->activate('_page_body');
}
//--------------------------------------------------------------------------------------------

if (isset($_POST['submit'])) 
{
	handle_submit($selected_id);
}
//-------------------------------------------------------------------------------------------- 

if (isset($_POST['delete'])) 
{

	$cancel_delete = 0;

	if ($cancel_delete == 0) 
	{	
		reset_salary_structure($selected_id);

		display_notification(_("Selected job position's salary structure has been deleted."));
		unset($_POST['job_position_id']);
		$selected_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete job position
}

function payroll_rules_settings($selected_id) 
{
	global $SysPrefs, $path_to_root;

	$new = true;

	$result_rules=get_payroll_rules();
	$rules = array();
	while($row_rule=db_fetch($result_rules))
	{
		$rules[] = array(
			'account_input' => "Account".$row_rule['account_code'],
			'debit_input' 	=> "Debit".$row_rule['account_code'],
			'credit_input'	=> "Credit".$row_rule['account_code'],
			'account_code'	=> $row_rule['account_code'],
			'account_name'	=> $row_rule['account_name'],
			);
		$_POST["Debit".$row_rule['account_code']] = price_format(0);
		$_POST["Credit".$row_rule['account_code']] = price_format(0);
			
	}


	
	if ($selected_id) 
	{
		$rsStr = get_salary_structure($selected_id);
		if(db_num_rows($rsStr) > 0)
			$new = false;
		else
			$new = true;
		
		while($rowStr = db_fetch($rsStr)){
			if($rowStr['type'] == DEBIT){//debit side
				$_POST["Debit".$rowStr['pay_rule_id']] = $rowStr['pay_amount'];
			}else{//credit side
				$_POST["Credit".$rowStr['pay_rule_id']] = $rowStr['pay_amount'];
			}
		}
		$_POST['job_position_id'] = $selected_id;
		
	}

	

	br();

	start_table(TABLESTYLE2);
		$th=array(_("Payroll Rules"),_("Debit"),("Credit"));
		table_header($th);
	
		$result_rules=get_payroll_rules();
	
		foreach($rules as $rule)
		{

			
			start_row();
				hidden($rule['account_input'],$rule['account_code']);
				label_cell($rule["account_name"]);
				amount_cells(null,$rule['debit_input']);
				amount_cells(null,$rule['credit_input']);
			end_row();
		}

	end_table(1);

//---------------------------------------------------------------------------------

	div_start('controls');

		if($new){
			submit_center('submit', _("Save Salary Structure"), true, '', 'default');
		}else{
			submit_center_first('submit', _("Save Salary Structure"), 
			  _('Update Salary Structure data'), @$_REQUEST['popup'] ? true : 'default');
			submit_center_last('delete', _("Delete Salary Structure"), 
			  _('Delete Salary Structure data if have been never used'), true);
		}

	div_end();
}

//--------------------------------------------------------------------------------------------
 
start_form();

if (db_has_jobs()) 
{
	start_table(TABLESTYLE2);
	start_row();
	job_list_cells(_("Select a job position: "), 'job_position_id', null,
		_('Select job position'), true, check_value('show_inactive'));
	
	end_row();

	end_table();	
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('job_position_id');
		set_focus('job_position_id');
	}
} 
else 
{
	hidden('job_position_id');
}

payroll_rules_settings($selected_id); 

hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>
