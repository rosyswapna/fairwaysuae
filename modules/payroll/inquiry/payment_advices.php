<?php


$page_security = 'SA_PMTADVICE';
$path_to_root = "../../..";


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
	
page(_($help_context = "Make Payment Advices"), @$_REQUEST['popup'], false, "", $js);


function pmt_advice_link($row)
{
	return pager_link(_('PaymentAdvice'), "/modules/payroll/payment_advice.php?PaymentAdvice=" 
			.$row['type_no'], ICON_DOC);
}

function batch_checkbox($row)
{
	$name = "Sel_" .$row['type_no'];
	return 	"<input type='checkbox' name='$name' value='1' >"
	 ."<input name='Sel_[".$row['type_no']."]' type='hidden' value='"
	 .$row['type_no']."'>\n";
}



start_form();

start_table(TABLESTYLE_NOBORDER);
	start_row();
		employee_list_cells(_("Employee"), "employee",null,_("All Employees"));

		date_cells(_("From:"), 'FromDate', '', null, 0, -1, 0);
		date_cells(_("To:"), 'ToDate');

		submit_cells('Search', _("Search"), '', '', 'default');
	end_row();
end_table();


$sql = get_sql_for_pmt_advices(get_post('employee', -1), get_post('FromDate'),
	get_post('ToDate'));

$cols = array(
	_("Pay Generated") => array('type'=>'date'), 
	_("Employee Name") ,
	_("Job Position") , 
	_("Department") , 
	_("To The Order Of"), 
	_("Amount Payable") => array('type'=>'amount'),
	_("Narration"),
	submit('BatchPaymentAdvice',_("Batch"), false, _("Batch Payment Advice")) 
			=> array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),
	array('insert'=>true, 'fun'=>'pmt_advice_link')
);

$table =& new_db_pager('pmt_advs_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager($table);


end_form();
end_page();
