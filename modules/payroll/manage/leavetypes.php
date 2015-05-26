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
$page_security = 'SA_LEAVE_TYPES';
$path_to_root = "../../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Manage Leave Types"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/db/leavetypes_db.inc");
$selected_id = get_post('ltyp_id','');
//--------------------------------------------------------------------------------------------

function can_process()
{
	if (strlen($_POST['ltyp_name']) == 0) 
	{
		display_error(_("The leave name cannot be empty."));
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
		update_leaves($_POST['ltyp_id'], $_POST['ltyp_name']);		
		$Ajax->activate('ltyp_id'); // in case of status change
		display_notification(_("Leave has been updated."));
	} 
	else 
	{ 	//it is a new department

		begin_transaction();
			add_leave($_POST['ltyp_name']);
			$selected_id = $_POST['ltyp_id'] = db_insert_id();
		commit_transaction();

		display_notification(_("A new leave has been added."));		
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
		delete_leaves($selected_id);

		display_notification(_("Selected leave has been deleted."));
		unset($_POST['ltyp_id']);
		$selected_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete department
}

function leave_settings($selected_id) 
{
	global $SysPrefs, $path_to_root, $auto_create_branch;
	
	if (!$selected_id) 
	{
	 	if (list_updated('ltyp_id') || !isset($_POST['ltyp_name'])) {
			$_POST['ltyp_name'] = '';
		}
	}
	else 
	{
		$myrow = get_leaves($selected_id);
		$_POST['ltyp_name'] = $myrow["ltyp_name"];
	}

	start_table(TABLESTYLE2);
		text_row(_("Leave Type:").$selected_id, 'ltyp_name', $_POST['ltyp_name'], 40, 40);	
	end_table();

	div_start('controls');
	if (!$selected_id)
	{
		submit_center('submit', _("Add New Leave"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('submit', _("Update Leave"), 
		  _('Update Leave data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $selected_id, _("Select this Leave and return to document entry."));
		submit_center_last('delete', _("Delete Leave"), 
		  _('Delete Leavet data if have been never used'), true);
	}
	div_end();
}

//--------------------------------------------------------------------------------------------
 
start_form();

if (db_has_leaves()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	leave_list_cells(_("Select a leave_type: "), 'ltyp_id', null,
		_('New Leave'), true, check_value('show_inactive'));
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();

	end_table();	
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('ltyp_id');
		set_focus('ltyp_id');
	}
} 
else 
{
	hidden('ltyp_id');
}

leave_settings($selected_id); 

hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>
