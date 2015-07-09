<?php
/*
  Paycheeque.php
  Begin to process paychecks
    list employees, enter hours, set pay period
    submit for calculation and review
*/

/////////////////////////////////////////////////////
//Includes
/////////////////////////////////////////////////////
$page_security = 'SA_PAYCHECK_PRINTABLE';
$path_to_root="../..";

require("includes/payroll_cart.inc");
//include_once($path_to_root."/modules/payroll/includes/payroll_db.inc");
include($path_to_root . "/includes/session.inc");
add_access_extensions();

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
require("includes/payroll_calc.inc");
require("includes/payroll_db.inc");

//-------------------------------------------------------------------------
if(isset($_GET['EditPayCheque']))
{
	$trans_no=$_GET['EditPayCheque'];
	$help_context = "Edit Paycheque";
}
elseif(isset($_GET['NewPayCheque']))
{
	$trans_no=$_GET['NewPayCheque'];
	$help_context = "Add Paycheque";
}
//-------------------------------------------------------------------------
	
function handle_submit()
{	

	update_cheque($_POST['chequedate'],$_POST['chequeno'],$_POST['id']);
	display_notification("cheque details added");
}

//-------------------------------------------------------------------------
if(isset($_POST['submit'])) 
	handle_submit();

	
	

$advice = get_payment_advice_details($trans_no);
//echo "<pre>";print_r($advice);echo "</pre>";exit;
page($help_context, @$_REQUEST['popup'], false, "", $js);
start_form();
	start_outer_table(TABLESTYLE2,'width=70%');
		table_section(1);
		table_section_title(_("Paycheque Details"));
			label_row(_("Employee name:"),$advice['emp_name']);
			label_row(_("Trans Date:"),$advice['tran_date']);
			label_row(_("Department:"),$advice['dept_name']);
			label_row(_("Job:"),$advice['job_name']);
			label_row(_("Memo:"),$advice['memo_']);
			label_row(_("Type no:"),$advice['type_no']);
			label_row(_("Account:"),$advice['account']);
			label_row(_("Account name:"),$advice['bank_account_name']);
			label_row(_("Account no:"),$advice['bank_account_number']);
			label_row(_("Bank name:"),$advice['bank_name']);
			label_row(_("Bank address:"),$advice['bank_address']);
			label_row(_("Amount:"),$advice['amount']);
			
		table_section(2);
			hidden('id',$advice['id']);
			if($advice['account_type'] == BT_CHEQUE)
			{

			$_POST['chequeno'] = @$advice['cheque_no'];
			if($advice['cheque_date'] !='0000-00-00')
			$_POST['chequedate'] = sql2date($advice['cheque_date']);

			table_section_title(_("Enter cheque details"));
				date_row(_("Cheque date:"),'chequedate');
				text_row(_("Cheque no:"), 'chequeno',  null, 30,40);
				end_outer_table(TABLESTYLE2);
				div_start('controls');
				submit_center_first('submit', _("Update Cheque details"), true, '', 'default');

				$button_label='<button type="button" class="inputsubmit">Print Payslip</button>';
				$ar = array('PARAM_0' => $advice['type_no'],'PARAM_1' =>$advice['payslip_no'],'PARAM_2' =>$advice['emp_id']); 
				echo print_link($button_label,'payslip',$ar);

				$button_label='<button type="button" class="inputsubmit">Print cheque</button>';
				$ar = array('PARAM_0' => $_GET['EditPayCheque'],'PARAM_1' =>ST_JOURNAL); 
				echo print_link($button_label,'cheque',$ar);
				div_end();
				

			}
			else
			end_outer_table(TABLESTYLE2);
end_form();
end_page();

?>  
 
