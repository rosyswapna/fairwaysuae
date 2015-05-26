<?php
$page_security = 'SA_PAYROLL';
$path_to_root = "../../..";

include($path_to_root . "/includes/db_pager.inc");//is this needed?
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/db/department_db.inc");

/////////////////////////////////////////////////////
//Page Director
/////////////////////////////////////////////////////

page(_($help_context = "Manage Department"), @$_REQUEST['popup'], false, "", $js); //TODO look into help_context and 'popup'






function can_process()
{
	if (strlen($_POST['dept_name']) == 0) 
	{
		display_error(_("The department name cannot be empty."));
		set_focus('dept_name');
		return false;
	} 
return true;
}

function handle_submit()
{
	global $path_to_root, $Ajax;

	if (!can_process())
		return;
		add_department(get_post('dept_name'));
		$selected_id = $_POST['dept_id'] = db_insert_id();
		display_notification(_("Department has been added."));
		$Ajax->activate('_page_body');
	
}
if(isset($_POST['Add']))
{	
	handle_submit();
}
$selected_id = get_post('dept_id');
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



	
if ($selected_id)
{
	
	 
	$dept_id=get_post('dept_id');
	$department=get_department($dept_id);
	//$_POST['dept_id']=$department['dept_name'];
	$_POST['dept_name']=$department['dept_name'];
	$Ajax->activate('dept_id');
	$Ajax->activate('_page_body');
	
}
//echo @$department['dept_name'];
start_form();
div_start('content');
start_table(TABLESTYLE2);

		
		text_row(_("Department Name:"), 'dept_name', $_POST['dept_name'], 40, 40);
		
		
end_table();
div_end();
			if (!$selected_id)
				submit_center('Add', _("Add Department"), true);
			else
				submit_center('Add', _("Update Department"), true);
end_form();



end_page(@$_REQUEST['popup']);


?>