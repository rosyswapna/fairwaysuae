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

	
	

$sql=get_payment_advice_details($trans_no);
page($help_context, @$_REQUEST['popup'], false, "", $js);
start_form();
	start_outer_table(TABLESTYLE2);
		table_section(1);
		table_section_title(_("Paycheque Details"));
			label_row(_("Employee name:"),$sql['emp_name']);
			label_row(_("Trans Date:"),$sql['tran_date']);
			label_row(_("Department:"),$sql['dept_name']);
			label_row(_("Job:"),$sql['job_name']);
			label_row(_("Memo:"),$sql['memo_']);
			label_row(_("Type no:"),$sql['type_no']);
			label_row(_("Account:"),$sql['account']);
			label_row(_("Account name:"),$sql['bank_account_name']);
			label_row(_("Account no:"),$sql['bank_account_number']);
			label_row(_("Bank name:"),$sql['bank_name']);
			label_row(_("Bank address:"),$sql['bank_address']);
			label_row(_("Amount:"),$sql['amount']);
			
		table_section(2);
		$ch=$sql['account_type'];
		hidden('id',$sql['id']);
			if($ch==BT_CHEQUE)
			{
			table_section_title(_("Enter cheque details"));
				date_row(_("Cheque date:"),'chequedate');
				text_row(_("Cheque no:"), 'chequeno',  40, 30);
				end_outer_table(TABLESTYLE2);
				div_start('controls');
				submit_center_first('submit', _("Update Cheque details"), true, '', 'default');
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
 
