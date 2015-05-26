<?php
$page_security = 'SA_PAYROLL';
$path_to_root = "../..";

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
include_once($path_to_root . "/modules/payroll/includes/db/jobpositions_db.inc");

page(_($help_context = "Manage Job Positions"), @$_REQUEST['popup'], false, "", $js); //TODO look into help_context and 'popup'





function can_process()
{
	if (strlen($_POST['job_name']) == 0) 
	{
		display_error(_("The job name cannot be empty."));
		set_focus('job_name');
		return false;
	} 
return true;
}

function handle_submit()
{
	global $path_to_root, $Ajax;

	if (!can_process())
		return;
		add_jobnames(get_post('job_name'));
		display_notification(_("Job has been added."));
		$Ajax->activate('_page_body');
	
}
if(isset($_POST['Add']))
{	
	handle_submit();
}



start_form();

start_table(TABLESTYLE2);

		text_row(_("Job Positon:"), 'job_name', null, 40, 40);

end_table();

			
				submit_center('Add', _("Add Job Positon"), true);
			
end_form();
end_page(@$_REQUEST['popup']);


?>