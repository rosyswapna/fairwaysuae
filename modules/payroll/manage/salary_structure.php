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
include_once($path_to_root . "/modules/payroll/includes/db/jobpositions_db.inc");
$selected_id = get_post('job_position_id','');
//--------------------------------------------------------------------------------------------

function can_process()
{
	if (strlen($_POST['job_name']) == 0) 
	{
		display_error(_("The name cannot be empty."));
		set_focus('job_name');
		return false;
	} 

	return true;
}

//--------------------------------------------------------------------------------------------

function handle_submit(&$selected_id)
{
	global $path_to_root, $Ajax;

	if (!can_process())
		return;
		
	if ($selected_id) 
	{	
			
		update_jobnames($_POST['job_position_id'], $_POST['job_position_id'],$selected_id,$payroll);		
		$Ajax->activate('job_position_id'); // in case of status change
		display_notification(_("Job position has been updated."));
	} 
	else 
	{ 	//it is a new job position

		begin_transaction();
			add_jobnames($_POST['account_code']);
			
			$selected_id = $_POST['job_position_id'] = db_insert_id();
		commit_transaction();

		display_notification(_("A new job position has been added."));		
		$Ajax->activate('_page_body');
	}
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
	{	//display_notification($selected_id);exit;
		delete_jobnames($selected_id);

		display_notification(_("Selected job position has been deleted."));
		unset($_POST['job_position_id']);
		$selected_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete job position
}

function payroll_rules_settings($selected_id) 
{
	global $SysPrefs, $path_to_root;
	
	if (!$selected_id) 
	{
	 	if (list_updated('job_position_id') || !isset($_POST['job_name'])) {
			//$myrow = get_salary_structure($selected_id);
			//echo "ok";exit;
			
		}
	}
	else 
	{
		$myrow = get_salary_structure($selected_id);
		$_POST['job_position_id'] = $myrow["job_position_id"];
		
	}

	br();

	start_table(TABLESTYLE2);
		$th=array(_("Payroll Rules"),_("Debit"),("Credit"));
		table_header($th);
	
		$result_rules=get_payroll_rules();
	
		while($row_rule=db_fetch($result_rules))
		{
			$account_name = "Account".$row_rule['account_code'];
			$debit_name = "Debit".$row_rule['account_code'];
			$credit_name = "Credit".$row_rule['account_code'];
			start_row();
				hidden($account_name,$row_rule['account_code']);
				label_cell($row_rule["account_name"]);
				amount_cells(null,$debit_name);
				amount_cells(null,$credit_name);
			end_row();
		}

	end_table(1);

//---------------------------------------------------------------------------------
	div_start('controls');
	if (!$selected_id)
	{
		submit_center('submit', _("Add"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('submit', _("Update"), 
		  _('Update Job position data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $selected_id, _("Select this job position and return to document entry."));
		submit_center_last('delete', _("Delete"), 
		  _('Delete job position data if have been never used'), true);
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
		_('New job position'), true, check_value('show_inactive'));
	
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
