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

function display_payslip_header(&$Order)
{
	global $Ajax;

	$qes = has_quick_entries(QE_JOURNAL);
	$new = $Order->order_id==0;

	hidden('ref_original');
	start_outer_table(TABLESTYLE2, "width='90%'");

	table_section(1);

   		date_row(_("Date:"), 'date_', '', $new);
		ref_row(_("Reference:"), 'ref', '');

	table_section(3,"35%");
		date_row(_("From Date:"), 'from_date', '', $new);
		date_row(_("TO Date:"), 'to_date', '', $new);
		

	table_section(2, "35%");
		
		hidden('PayType', PT_EMPLOYEE);
		employee_list_row(_("Pay To: "), 'person_id',null, _("Select Employee"), true, false, true);
		
		text_row(_("To the Order of:"),'to_the_order_of',$_POST['to_the_order_of']);

		if(!isset($_POST['PaySlipNo'])){
			$_POST['PaySlipNo'] = get_next_payslip_no();
		}
		hidden('PaySlipNo',$_POST['PaySlipNo']);
		label_row(_("Payslip No:"),$_POST['PaySlipNo']);
	
	
		
	table_section(4, "30%");
		text_row(_("No of Leaves:"),'leaves');
		text_row(_("Deductable Leaves:"),'deductableleaves');
		

	if (list_updated('person_id')) {

		//generate_gl_items($Order,get_post('person_id'));
		$Order->clear_items();
	}
	
	end_outer_table(1);
	
	
	
}

//-------------------------generate cart gl items-------------------------
function generate_gl_items($Order){

	global $Ajax;

	$emp_id 	= $_POST['person_id'];
	$from_date 	= $_POST['from_date'];
	$to_date 	= $_POST['to_date'];
	

	$Order->clear_items();
	$totalCredit = $totalDebit = 0;
	$ac_payable_amount=0;
	$salary_rules = get_emp_salary_structure($emp_id);
	if(db_num_rows($salary_rules) > 0){

		
		while($myrow = db_fetch($salary_rules) ){

			if($myrow['pay_rule_id'] == GL_BASIC_SALARY){
				$salary_amount = $myrow['pay_amount'];
			}
			
			$Order->add_gl_item($myrow['pay_rule_id'], 0,0, ($myrow['type'] == CREDIT) ? -$myrow['pay_amount']:$myrow['pay_amount'], '');

			if($myrow['type'] == CREDIT){
				$totalCredit += $myrow['pay_amount'];
			}else{
				$totalDebit += $myrow['pay_amount'];
			}
		}
		
	}
	

	//leave calculation
	$deductableleaves = input_num('deductableleaves',0);
	$payroll_days = 1;
	$payroll_days += date_diff2($_POST['to_date'], $_POST['from_date'], "d");
	if($payroll_days < 30){
		$deductableleaves += 30-$payroll_days;
		$_POST['deductableleaves'] = $deductableleaves;
		
	}
	
	$leave_deduct_amount = $salary_amount/30*$deductableleaves;
	if($leave_deduct_amount != 0){
		$Order->add_gl_item(GL_DEDUCT_LEAVE, 0,0,-$leave_deduct_amount, '');
		$totalCredit += $leave_deduct_amount;
	}	

	
	//expenses and deposits
	$expences = get_expences_n_deposits($emp_id,$from_date,$to_date);
	if(db_num_rows($expences) > 0){
	
		while($myrow1 = db_fetch($expences)){
			
			$Order->add_gl_item($myrow1['account'], 0,0,-$myrow1['amount'], '');
			$amount=abs($myrow1['amount']);
			if($myrow1['type'] == ST_BANKDEPOSIT){
				$totalDebit += $amount;
			}else{
				$totalCredit +=$amount;
			}
			
		}
		
	}

	

	$ac_payable_amount = $totalCredit - $totalDebit;
	if($ac_payable_amount!=0){
		
		$Order->add_gl_item(AC_PAYABLE, 0,0, $ac_payable_amount, '');
	}
	//------------------------------------------------==================
	unset($_POST['totamount']); // enable default
	$Ajax->activate('totamount');
	
}


function display_advice_header(&$Order)
{
	global $Ajax;


	$qes = has_quick_entries(QE_JOURNAL);
	$new = $Order->order_id==0;

	

	hidden('ref_original');
	start_outer_table(TABLESTYLE2, "width='90%'");

	table_section(1);

   		date_row(_("Date:"), 'date_', '', $new);
		ref_row(_("Reference:"), 'ref', '');

	table_section(2, "35%");
		
		hidden('PayType', PT_EMPLOYEE);

		$employee = get_employee($Order->person_id);

		$_POST['to_the_order_of'] = $Order->to_the_order_of;

		if(isset($_GET['PaymentAdvice']) || isset($_POST['PaymentAdvice'])){
			label_row(_("Pay To: "),$employee['emp_first_name']." ".$employee['emp_last_name']);
			hidden('person_id',$Order->person_id);
			label_row(_("To the Order of:"),$Order->to_the_order_of);

			hidden('to_the_order_of');

			if(!isset($_POST['PaymentAdvice']))
				$_POST['PaymentAdvice'] =$_GET['PaymentAdvice'];
			hidden('PaymentAdvice');
		}else{
			employee_list_row(_("Pay To: "), 'person_id',null, _("Select Employee"), true, false, true);
			text_row(_("To the Order of:"),'to_the_order_of');
		}
		
	table_section(3, "35%");	
		if(!isset($_POST['PaySlipNo'])){
			$_POST['PaySlipNo'] = $Order->payslip_no = 0;
		}
		

		bank_accounts_list_row(_("From:"), 'bank_account', null, true);
		bank_balance_row(get_post('bank_account'));
	
		if(isset($_GET['PaymentAdvice']) || isset($_POST['PaymentAdvice'])){
			

		}else{
			employee_payslip_list_row(_("Pay Slip:"),'for_payslip',$_POST['for_payslip'],get_post('person_id'),_("Select One"),true);
		}

		
		//on change employee
		if (list_updated('person_id')) {//get payslip for selected employee
			$Order->clear_items();
			unset($_POST['totamount']); // enable default
			$Ajax->activate('totamount');
			$Ajax->activate('for_payslip');
			$Order->payslip_no = 0;	
		}

		
		//on change payslip transaction or on change bank account
		if (list_updated('for_payslip') || list_updated('bank_account')) {
			$Order->clear_items();
			$_POST['memo_'] = "";

			if(list_updated('for_payslip')){
				unset($_POST['PaySlipNo']);
				$Order->payslip_no = 0;	
				$Order->payslip_trans_no = get_post('for_payslip');
			}

			$payslip = get_payslip($Order->payslip_trans_no);
		
			if($payslip){
				$_POST['memo_'] = "Payment advice gl entry For Payslip".$payslip['payslip_no'];

				if(list_updated('for_payslip')){
					$Order->payslip_trans_no = $payslip['type_no'];
					$Order->person_id = $payslip['person_id'];
					$Order->to_the_order_of = $payslip['to_the_order_of'];
					$Order->payslip_no = $_POST['PaySlipNo'] = $payslip['payslip_no'];
					
				}
				$ac_pmt_amt = -($payslip['amount']);
				$cash_amt = -($ac_pmt_amt);

				$bank_ac_code = get_bank_gl_account(get_post('bank_account'));
	
				$Order->add_gl_item(AC_PAYABLE, 0, 0, $ac_pmt_amt, '');
				$Order->add_gl_item($bank_ac_code, 0, 0, $cash_amt, '');

				
			
			}
			$Ajax->activate('items_table');
		}

		
		


		hidden('PaySlipNo',$Order->payslip_no);
		label_row(_("Payslip No:"),$Order->payslip_no);
	
	end_outer_table(1);
}






//---------------------------------------------------------------------------------

function display_gl_items($title, &$order)
{
	global $path_to_root;

	display_heading($title);

	$dim = get_company_pref('use_dimension');

    div_start('items_table');
	start_table(TABLESTYLE, "width='95%'");
	if ($dim == 2)
		$th = array(_("Account Code"), _("Account Description"), _("Dimension")." 1",
			_("Dimension")." 2", _("Debit"), _("Credit"), _("Memo"), "");
	else if ($dim == 1)
		$th = array(_("Account Code"), _("Account Description"), _("Dimension"),
			_("Debit"), _("Credit"), _("Memo"), "");
	else
		$th = array(_("Account Code"), _("Account Description"),
			_("Debit"), _("Credit"), _("Memo"), "");

	if (count($order->gl_items)) $th[] = '';

	table_header($th);	

	$k = 0;

	$id = find_submit('Edit');
	foreach ($order->gl_items as $line => $item) 
	{
		if ($id != $line)
		{
	    		alt_table_row_color($k);

				label_cells($item->code_id, $item->description);
	    		if ($dim >= 1)
	   				label_cell(get_dimension_string($item->dimension_id, true));
	    		if ($dim > 1)
	   				label_cell(get_dimension_string($item->dimension2_id, true));
	    		if ($item->amount > 0)
	    		{
	    			amount_cell(abs($item->amount));
	    			label_cell("");
	    		}	
	    		else
	    		{
	    			label_cell("");
	    			amount_cell(abs($item->amount));
	    		}	
				label_cell($item->reference);

				edit_button_cell("Edit$line", _("Edit"),
					_('Edit journal line'));
				delete_button_cell("Delete$line", _("Delete"),
					_('Remove line from journal'));
	    		end_row();
		} 
		else 
		{
			gl_edit_item_controls($order, $dim, $line);
		}
	}

	//if ($id == -1)
		//gl_edit_item_controls($order, $dim);

	if ($order->count_gl_items()) 
	{
		$colspan = ($dim == 2 ? "4" : ($dim == 1 ? "3" : "2"));
		start_row();
		label_cell(_("Total Salary"), "align=right colspan=" . $colspan);
		amount_cell($order->gl_items_total_debit());
		amount_cell(abs($order->gl_items_total_credit()));
		label_cell('', "colspan=3");
		end_row();
	}

    end_table();
	div_end();
	
}

//---------------------------------------------------------------------------------

function gl_edit_item_controls(&$order, $dim, $Index=null)
{
	global $Ajax;
	start_row();

	$id = find_submit('Edit');
	if ($Index != -1 && $Index == $id)
	{
	    // Modifying an existing row
		$item = $order->gl_items[$Index];
		$_POST['code_id'] = $item->code_id;
		$_POST['dimension_id'] = $item->dimension_id;
		$_POST['dimension2_id'] = $item->dimension2_id;
		if ($item->amount > 0)
		{
			$_POST['AmountDebit'] = price_format($item->amount);
			$_POST['AmountCredit'] = "";
		}
		else
		{
			$_POST['AmountDebit'] = "";
			$_POST['AmountCredit'] = price_format(abs($item->amount));
		}	
		$_POST['description'] = $item->description;
		$_POST['LineMemo'] = $item->reference;

		hidden('Index', $id);
		$skip_bank = !$_SESSION["wa_current_user"]->can_access('SA_BANKJOURNAL');
		echo gl_all_accounts_list('code_id', null, $skip_bank, true);
		if ($dim >= 1) 
			dimensions_list_cells(null, 'dimension_id', null, true, " ", false, 1);
		if ($dim > 1) 
			dimensions_list_cells(null, 'dimension2_id', null, true, " ", false, 2);
	    $Ajax->activate('items_table');

	}
	else
	{
	    // Adding a new row
		$_POST['AmountDebit'] = '';  //price_format(0);
		$_POST['AmountCredit'] = ''; //price_format(0);
		$_POST['dimension_id'] = 0;
		$_POST['dimension2_id'] = 0;
		//$_POST['LineMemo'] = ""; // let memo go to next line Joe Hunt 2010-05-30
		$_POST['_code_id_edit'] = "";
		$_POST['code_id'] = "";
		if(isset($_POST['_code_id_update'])) {
			    $Ajax->activate('code_id');
		}
		
		$skip_bank = !$_SESSION["wa_current_user"]->can_access('SA_BANKJOURNAL');
		echo gl_all_accounts_list('code_id', null, $skip_bank, true);
		if ($dim >= 1)
			dimensions_list_cells(null, 'dimension_id', null, true, " ", false, 1);
		if ($dim > 1)
			dimensions_list_cells(null, 'dimension2_id', null, true, " ", false, 2);
	}

	if ($dim < 1)
		hidden('dimension_id', 0);
	if ($dim < 2)
		hidden('dimension2_id', 0);

	small_amount_cells(null, 'AmountDebit');
	small_amount_cells(null, 'AmountCredit');
	text_cells_ex(null, 'LineMemo', 35, 255);

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
 		set_focus('amount');
	} 
	else 
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new line to journal'), true);

	end_row();
}

//---------------------------------------------------------------------------------

function gl_options_controls()
{
	  echo "<br><table align='center'>";

	  textarea_row(_("Memo"), 'memo_', null, 50, 3);

	  echo "</table>";
}


//---------------------------------------------------------------------------------


function _format_emp_payslip($row)
{
	
	
	return "Trans# ".$row['type_no'] ." - ". $row['job_name'];
}

function employee_payslip_list($name, $selected_id=null,$employee_id, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	if($employee_id)
	$sql = get_sql_for_payslips($employee_id);
	//display_notification($sql);exit;
	$mode = 0;//We don't have many employees, hardcoding to no search

	if ($editkey)
		set_editor('employee', $name, $editkey);//what is this? //set_editor defined in ui_controls.inc

	$ret = combo_input($name, $selected_id, $sql, 'type_no', 'job_name', //sql fields
	array(
	    'format' => '_format_emp_payslip',
	    
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Payslip") : $spec_option,
		'spec_id' => $all_items,//what is this?
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :
		_('Select employee'),
		'show_inactive' => $show_inactive,
		'opt_value' => 'type_no'
	) );
	if ($editkey)
		$ret .= add_edit_combo('employee');
	return $ret;
}

//--------------------------------------------------------------------------------------------
function employee_payslip_list_cells($label, $name, $selected_id=null,$employee_id, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employee_payslip_list($name, $selected_id,$employee_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function employee_payslip_list_row($label, $name, $selected_id=null, $employee_id, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo employee_payslip_list($name, $selected_id,$employee_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}

?>
