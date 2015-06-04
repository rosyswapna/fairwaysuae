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
$page_security = 'SA_PAYRULE_STRUCTURE';
$path_to_root = "../../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Manage Payrule Structure"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/modules/payroll/includes/db/payroll_structure_db.inc");
include_once($path_to_root . "/modules/payroll/includes/db/jobpositions_db.inc");
$selected_id = get_post('job_position_id','');
//--------------------------------------------------------------------------------------------

/*function can_process()
{
	if (strlen($_POST['job_name']) == 0) 
	{
		display_error(_("The name cannot be empty."));
		set_focus('job_name');
		return false;
	} 

	return true;
}*/

//--------------------------------------------------------------------------------------------


function handle_submit(&$selected_id)
{
	global $path_to_root, $Ajax;

	//if (!can_process())
		//return;
	//echo "<pre>";print_r($_POST);echo "</pre>";exit;
		
	if ($selected_id) 
	{	

		$payrule = array();
		foreach($_POST as $p =>$val) 
		{
			if (substr($p,0,7) == 'Payroll' && $val == 1) 
			{
				$a = substr($p,7);
				$payrule[] = (int)$a;
			}
		}
		if(exist_payroll($selected_id))

			update_payroll($selected_id,$payrule);	
		
		else
			add_payroll($selected_id,$payrule);
		
		$Ajax->activate('job_position_id'); // in case of status change
		display_notification(_("Job position has been updated."));
	} 
	else 
	{ 	//it is a new job position

		begin_transaction();
			add_payroll($_POST['job_position_id'],$payrule);
			
			$selected_id = $_POST['job_position_id'] = db_insert_id();
		commit_transaction();

		display_notification(_("A new job position has been added."));		
		$Ajax->activate('_page_body');
	}
	if(exist_payrule($selected_id)){
			reset_payrule($selected_id);
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
		reset_payrule($selected_id);

		display_notification(_("Selected job position has been deleted."));
		unset($_POST['job_position_id']);
		$selected_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete job position
}

function payroll_rule_settings($selected_id) 
{
	global $SysPrefs, $path_to_root;
	
	$new = true;
	foreach($_POST as $p =>$val) 
	{
		if (substr($p,0,7) == 'Payroll') 
		{
			$_POST[$p] = '';
		}
	}
	
	if ($selected_id) 
	{
		$payroll_structure = get_payroll_structure($selected_id);


		if($payroll_structure){
			$new = false;
			foreach($payroll_structure['payroll_rule'] as $rule_code){
				$_POST['Payroll'.$rule_code] = 1;
			}
			
		}else{
			
		}

		$_POST['job_position_id'] = $selected_id;
	
	}
	
	
	start_table(TABLESTYLE2);
	$th=array(_("Select Payroll Rules"),'');
	table_header($th);
	
	
	$result_rules=get_payroll_rules();
	

	while($row_rule=db_fetch($result_rules))
	{
		check_row($row_rule["account_name"], 'Payroll'.$row_rule["account_code"],null);	
	}
end_table(1);

//---------------------------------------------------------------------------------
	div_start('controls');
	//submit_center_first('submit', _("Update"), 
		 // _('Update payrule data'));
	if($new){
			submit_center('submit', _("Save payroll Structure"), true);
		}
	else{
		submit_center_first('submit', _("Update"), 
		  _('Update payrule data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $selected_id, _("Select this job position and return to document entry."));
		submit_center_last('delete', _("Delete"), 
		  _('Delete job position data if have been never used'), true);
		}
	div_end();
	//start_table(TABLESTYLE2);
		//text_row(_("Job position Name:"), 'job_name', $_POST['job_name'], 40, 40);	
	
	//end_table();
}
//---------------------------------------------------------------------------------check_box





//--------------------------------------------------------------------------------------------
 
start_form();

if (db_has_jobs()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	job_list_cells(_("Select a job position: "), 'job_position_id', null,
		_('select'), true, check_value('show_inactive'));
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
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

payroll_rule_settings($selected_id); 

hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>
