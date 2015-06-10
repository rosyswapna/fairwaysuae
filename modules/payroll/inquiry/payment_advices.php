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


if (isset($_GET['BatchPaymentAdvice'])) {

	$batch 	= $_SESSION['PaymentAdviceBatch'];
	unset($_SESSION['PaymentAdviceBatch']);

	foreach($batch as $trans_id){
		write_cart($trans_id,$post['to_the_order_of']);
	}

	display_notification("Batch payment advice processed");
}

//create cart
function write_cart($trans_id)
{
	global $Refs;
	$type = $trans_no = 0;
	if (isset($_SESSION['journal_items']))
	{
		unset ($_SESSION['journal_items']);
	}

	$payslip = get_payslip($trans_id);

	$cart = new items_cart($type);
	$cart->clear_items();

    	$cart->order_id = $trans_no;
	$cart->paytype = PT_EMPLOYEE;
	
	$cart->reference = $Refs->get_next(0);
	$cart->tran_date = new_doc_date();
	if (!is_date_in_fiscalyear($cart->tran_date))
		$cart->tran_date = end_fiscalyear();
	
	$cart->memo_	 = "Payment advice gl entry For Payslip".$payslip['payslip_no'];


	if($payslip){
		$cart->person_id = $payslip['person_id'];
		$cart->to_the_order_of = $payslip['to_the_order_of'];
		$cart->payslip_no = $payslip['payslip_no'];

		$ac_pmt_amt = -($payslip['amount']);
		$cash_amt = -($ac_pmt_amt);

		$bank = get_default_bank_account();
	
		$cart->add_gl_item(AC_PAYABLE, 0, 0, $ac_pmt_amt, '');
		$cart->add_gl_item($bank['account_code'], 0, 0, $cash_amt, '');
	}

	$_SESSION['journal_items'] = &$cart;

	$trans_no = write_payslip($cart, check_value('Reverse'));
	//echo "<pre>";print_r($_SESSION['journal_items']);echo "</pre>";exit;
}



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

if (isset($_POST['BatchPaymentAdvice']))
{
	$del_count = 0;
	foreach($_POST['Sel_'] as $trans_id) {
	  	$checkbox = 'Sel_'.$trans_id;
	  	if (check_value($checkbox)){
		    	$selected[] = $trans_id;
		    	$del_count++;
	  	}
	}

	if (!$del_count) {
		display_error(_('For batch payment advice you should
		    select at least one.'));
	} else {
		$_SESSION['PaymentAdviceBatch'] = $selected;
		
		meta_forward($_SERVER['PHP_SELF'],'BatchPaymentAdvice=Yes');
	}
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


$sql = get_sql_for_payslips(get_post('employee', -1), get_post('FromDate'),
	get_post('ToDate'));

//echo $sql;exit;

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
