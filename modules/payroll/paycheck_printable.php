<?php


$page_security = 'SA_PAYCHECK_PRINTABLE';
$path_to_root = "../..";


include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root."/modules/payroll/includes/payroll_ui.inc");
include_once($path_to_root."/modules/payroll/includes/payroll_db.inc");


add_access_extensions();
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Manage Paycheck"), @$_REQUEST['popup'], false, "", $js);



function fn_paycheque_link($row)
	{
		
		if ($row['cheque_no']!=NULL)
			{
			$str="/modules/payroll/paycheque.php?EditPayCheque=".$row['type_no'];
			return $str ? pager_link(_('Edit'), $str, ICON_EDIT) : ''; 
			
			}
		else
			{	
				
			$str="/modules/payroll/paycheque.php?NewPayCheque=".$row['type_no'];
			return $str ? pager_link(_('Process'), $str, ICON_CREDIT) : '';
			}
	}


if (list_updated('Employee')) 
{
	if(get_post('Employee') != ALL_TEXT){

		//display_notification("ok");
		$disable = get_post('Employee') !== '';

		//$Ajax->addDisable(true, 'job_position_id', $disable);
		//$Ajax->addDisable(true, 'department_id', $disable);
		$Ajax->addDisable(true, '_JobPosition_edit', $disable);
		$Ajax->addDisable(true, '_Department_edit', $disable);
	
		// if search is not empty rewrite table
		//if ($disable) {
			//$Ajax->addFocus(true, 'employee');
		//} else
			//$Ajax->addFocus(true, 'job_position_id');
		//$Ajax->activate('emp_inquiry_tbl');
	}
	
}

start_form();

	start_table(TABLESTYLE_NOBORDER);
		start_row();
			employee_list_cells(_("Employee"), "Employee",null,_("All Employees"),true);
			department_list_cells(_("Department"),'Department',null,_("All Departments"));
			job_list_cells(_("Job Position"), "JobPosition",null,_("All Jobs"));
			date_cells(_("From:"), 'FromDate', '', null, 0, -1, 0);
			date_cells(_("To:"), 'ToDate');

			submit_cells('Search', _("Search"), '', '', 'default');
		end_row();
	end_table();
	
	


	$sql = get_sql_for_payment_advices(get_post('Employee', -1),
		get_post('JobPosition', -1),get_post('Department', -1) ,get_post('FromDate'),
		get_post('ToDate'));

	$cols = array(
		_("SlNo"), 
		_("Date") => array('type' => 'date'),
		_("Employee") , 
		_("Reference") , 
		_("Memo"), 
		_("Account") ,
		_("Amount"),
		//_(" ") => array('align'=>'right', 'type'=>'amount'),
			array('insert'=>true, 'fun'=>'fn_paycheque_link')	
	);

	$table =& new_db_pager('paycheck_tbl', $sql, $cols);

	$table->width = "80%";

	display_db_pager($table);

end_form();

end_page();
