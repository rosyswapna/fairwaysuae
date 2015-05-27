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
$page_security = 'SA_DEPARTMENT';
$path_to_root = "../../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Manage Department"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/db/department_db.inc");
$selected_id = get_post('dept_id','');
//--------------------------------------------------------------------------------------------

function can_process()
{
	if (strlen($_POST['dept_name']) == 0) 
	{
		display_error(_("The department name cannot be empty."));
		set_focus('CustName');
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
		update_department($_POST['dept_id'], $_POST['dept_name']);		
		$Ajax->activate('dept_id'); // in case of status change
		display_notification(_("Department has been updated."));
	} 
	else 
	{ 	//it is a new department

		begin_transaction();
			add_department($_POST['dept_name']);
			$selected_id = $_POST['dept_id'] = db_insert_id();
		commit_transaction();

		display_notification(_("A new department has been added."));		
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
		delete_department($selected_id);

		display_notification(_("Selected department has been deleted."));
		unset($_POST['dept_id']);
		$selected_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete department
}

function department_settings($selected_id) 
{
	global $SysPrefs, $path_to_root;
	
	if (!$selected_id) 
	{
	 	if (list_updated('dept_id') || !isset($_POST['dept_name'])) {
			$_POST['dept_name'] = '';
		}
	}
	else 
	{
		$myrow = get_department($selected_id);
		$_POST['dept_name'] = $myrow["dept_name"];
	}

	start_table(TABLESTYLE2);
		text_row(_("Department Name:").$selected_id, 'dept_name', $_POST['dept_name'], 40, 40);	
	end_table();

	div_start('controls');
	if (!$selected_id)
	{
		submit_center('submit', _("Add New Department"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('submit', _("Update Department"), 
		  _('Update Department data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $selected_id, _("Select this department and return to document entry."));
		submit_center_last('delete', _("Delete Department"), 
		  _('Delete department data if have been never used'), true);
	}
	div_end();
}

//--------------------------------------------------------------------------------------------
 
start_form();

if (db_has_departments()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	department_list_cells(_("Select a department: "), 'dept_id', null,
		_('New department'), true, check_value('show_inactive'));
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();

	end_table();	
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('dept_id');
		set_focus('dept_id');
	}
} 
else 
{
	hidden('dept_id');
}

department_settings($selected_id); 

hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>
