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
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/items_cart.inc");

//--------------------------------------------------------------------------------


//payslip batch functions=======================================================	
function display_employee_batch_payslip()
{	
	
	
	$sql = get_sql_for_payslip_batch();
	$cols = array(
		_("Employee Name ") , 
		_("Job Position"), 
		_("Debit")=>array('type'=>'amount'), 
		_("Credit")=>array('type'=>'amount'),
		_("Payable")=>array('fun'=>'ac_payable','align'=>'right'), 
		submit('BatchPaySlip',_("Batch"), false, _("Batch Payslip")) 
			=> array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center')
		
		);
	$table =& new_db_pager('payslip_tbl', $sql, $cols);
	display_db_pager($table);
	
}

function display_payslip_batch_header()
{
	start_table(TABLESTYLE2);
		start_row();
		date_cells(_("Date:"),'date_');
		text_cells(_("To the order Of:"),'to_the_order_of');
		text_cells(_("Narration"),'memo_');
		date_cells(_("From Date:"),'from_date');
		date_cells(_("To Date:"),'to_date');
		end_row();
	end_table();
}

//-----------------------------------------------------------------------------
//============================================================================

//payment advice  batch functions=================================================
function display_employee_batch_advice()
{	
	
	
	$sql = get_sql_for_payslips(get_post('employee', -1), get_post('FromDate'),
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
	
}



function display_advice_batch_header()
{
	start_table(TABLESTYLE2);
		start_row();
		date_cells(_("Date:"),'date_');
		bank_accounts_list_cells(_("From:"), 'bank_account', null, true);
		bank_balance_cells(get_post('bank_account'));
		end_row();
	end_table();
}

//==============================================================================


function bank_balance_cells($bank_acc, $parms='')
{
	global $path_to_root;
	
	$to = add_days(Today(), 1);
	$bal = get_balance_before_for_bank_account($bank_acc, $to);
	label_cells( _("Bank Balance:"),
		"<a target='_blank' " . ($bal<0 ? 'class="redfg"' : '')
		."href='$path_to_root/gl/inquiry/bank_inquiry.php?bank_account=".$bank_acc."'"
		." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >&nbsp;"
		. price_format($bal)
		."</a>", $parms);
}




?>
