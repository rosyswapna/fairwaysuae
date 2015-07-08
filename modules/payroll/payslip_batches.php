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
$page_security = 'SA_PAYSLIP_BATCHES';
$path_to_root = "../..";
include_once($path_to_root . "/includes/ui/items_cart.inc");
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

//include_once($path_to_root . "/gl/includes/gl_db.inc");
//include_once($path_to_root . "/gl/includes/gl_ui.inc");

include_once($path_to_root . "/modules/payroll/includes/payroll_ui.inc");
include_once($path_to_root . "/modules/payroll/includes/payroll_db.inc");

$js = '';
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();


$_SESSION['page_title'] = _($help_context = "Manage Employee Payslip Batches");

page($_SESSION['page_title'], false, false,'', $js);
//-----------------------------------------------------------------------------------------------


if (isset($_GET['BatchPaySlip'])) {

	$batch 	= $_SESSION['PaySlipBatch']['employees'];
	$post 	= $_SESSION['PaySlipBatch']['post'];
	unset($_SESSION['PaySlipBatch']);

	foreach($batch as $emp_id){
		write_cart($emp_id,$post['to_the_order_of'],$post['date_'],$post['memo_'],$post['from_date'],$post['to_date']);
	}

	display_notification("Batch Payslip processed");
}

//create cart
function write_cart($emp_id,$to_the_order_of,$date_,$narration,$from_date,$to_date)
{
	global $Refs;
	$type = $trans_no = 0;
	if (isset($_SESSION['journal_items']))
	{
		unset ($_SESSION['journal_items']);
	}

	$cart = new items_cart($type);
	$cart->clear_items();

    	$cart->order_id = $trans_no;
	$cart->paytype = PT_EMPLOYEE;
	$cart->person_id = $emp_id;
	$cart->to_the_order_of = $to_the_order_of;
	$cart->payslip_no = get_next_payslip_no();
	
	$cart->reference = $Refs->get_next(0);
	$cart->tran_date = new_doc_date();
	if (!is_date_in_fiscalyear($cart->tran_date))
		$cart->tran_date = end_fiscalyear();
	
	$cart->memo_	 = $narration;
	$cart->tran_date = $date_;
	$cart->from_date=$from_date;
	$cart->to_date=$to_date;
	
	//get employee salary structure----------------====================
	$salary_rules = get_emp_salary_structure($emp_id);
	$ac_payable_amount=0;
	if(db_num_rows($salary_rules) > 0){

		$totalCredit = $totalDebit = 0;
		while($myrow = db_fetch($salary_rules)){
			//echo "<pre>";print_r($myrow);echo "</pre>";exit;
			$cart->add_gl_item($myrow['pay_rule_id'], 0,0, ($myrow['type'] == CREDIT) ? -$myrow['pay_amount']:$myrow['pay_amount'], '');

			if($myrow['type'] == CREDIT){
				$totalCredit += $myrow['pay_amount'];
			}else{
				$totalDebit += $myrow['pay_amount'];
			}
		}
		
		
	}

	//get expenses and employee deposits
	$expences = get_expences_n_deposits($emp_id);
	if(db_num_rows($expences) > 0){
	
		while($myrow1 = db_fetch($expences)){
			
			$cart->add_gl_item($myrow1['account'], 0,0,$myrow1['amount'], '');
			$amount=abs($myrow1['amount']);
			if($myrow1['type'] == ST_BANKDEPOSIT){
				$totalCredit +=$amount;
			}else{
				$totalDebit += $amount;
			}	
		}
	}
	//account payable amount 
	$ac_payable_amount = $totalCredit - $totalDebit;
	if($ac_payable_amount!=0){
		$cart->add_gl_item(AC_PAYABLE, 0,0, $ac_payable_amount, '');
	}
	//======================

	$_SESSION['journal_items'] = &$cart;

	$trans_no = write_payslip($cart, check_value('Reverse'));
	//echo "<pre>";print_r($_SESSION['journal_items']);echo "</pre>";exit;
}

function batch_checkbox($row)
{
	$name = "Sel_" .$row['emp_id'];
	return "<input type='checkbox' name='$name' value='1' >"
	 ."<input name='Sel_[".$row['emp_id']."]' type='hidden' value='"
	 .$row['emp_id']."'>\n";
}

function ac_payable($row)
{
	$amt_pay = $row['Cr']-$row['Dr'];
	if($amt_pay < 0)
		return price_format(abs($amt_pay))." Cr";
	else if($amt_pay > 0)
		return price_format($amt_pay)." Dr";
	else
		return price_format($amt_pay);
}

if (isset($_POST['BatchPaySlip']))
{
	
	$del_count = 0;
	foreach($_POST['Sel_'] as $emp_id) {
	  	$checkbox = 'Sel_'.$emp_id;
	  	if (check_value($checkbox)){
		    	$selected[] = $emp_id;
		    	$del_count++;
	  	}
	}

	if (!$del_count) {
		display_error(_('For batch payslip you should
		    select at least one.'));
	} else {
		$_SESSION['PaySlipBatch']['employees'] = $selected;
		$_SESSION['PaySlipBatch']['post'] = array('to_the_order_of' => $_POST['to_the_order_of'],
							'date_' => $_POST['date_'],
							'memo_' => $_POST['memo_']
							);
		meta_forward($_SERVER['PHP_SELF'],'BatchPaySlip=Yes');
	}

}
//-----------------------------------------------------------------------------------------------

start_form();

	start_table(TABLESTYLE_NOBORDER);
		start_row();
			job_list_cells(_("Job Position"), "job_position_id",null,_("All Jobs"));

			department_list_cells(_("Department"),'department_id',null,_("All Departments"));

			submit_cells('Search', _("Search"), '', '', 'default');
		end_row();
	end_table();


	start_table(TABLESTYLE, "width='80%'", 10);
		echo "<tr><td>";
		display_payslip_batch_header();
		echo "</td></tr>";
		echo "<tr><td>";
		display_employee_batch_payslip();
		echo "</td></tr>";
	end_table(1);





end_form();
//------------------------------------------------------------------------------------------------

end_page();

?>
