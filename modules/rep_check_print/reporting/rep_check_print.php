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
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SUPPTRANSVIEW' : 'SA_SUPPBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.2 $
// Creator:	Joe Hunt - Based on the new Report Engine by Tom Hallman
// Creator:	Based on Tom Hallman's Report.
// Date:	2010-03-03
// Title:	Printable Check
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

//----------------------------------------------------------------------------------------------------

print_check();

//----------------------------------------------------------------------------------------------------
function get_remittance($counter)
{
   	$sql = "SELECT gl_trans.tran_date,gl_trans.amount AS Total, 
		if(person_type_id =".PT_CUSTOMER.",debtor.name,
		if(person_type_id =".PT_SUPPLIER.",supplier.supp_name,'')) AS Name
   		
		FROM ".TB_PREF."gl_trans gl_trans
		LEFT JOIN ".TB_PREF."debtors_master debtor ON debtor.debtor_no = person_id
		LEFT JOIN ".TB_PREF."suppliers supplier ON supplier.supplier_id = person_id
		WHERE counter = ".db_escape($counter);
			
		
   	$result = db_query($sql, "The remittance cannot be retrieved");
   	if (db_num_rows($result) == 0)
   		return false;
    	return db_fetch($result);
}

function get_allocations_for_remittance($supplier_id, $type, $trans_no)
{
	$sql = get_alloc_supp_sql("amt, supp_reference, trans.alloc", "trans.trans_no = alloc.trans_no_to
		AND trans.type = alloc.trans_type_to
		AND alloc.trans_no_from=".db_escape($trans_no)."
		AND alloc.trans_type_from=".db_escape($type)."
		AND trans.supplier_id=".db_escape($supplier_id),
		TB_PREF."supp_allocations as alloc");
	$sql .= " ORDER BY trans_no";
	return db_query($sql, "Cannot retreive alloc to transactions");
}
//-------------------------------------------------------------------------------------------------


//----------------------------------------------------------------------------------------------------

function print_check()
{
    global $path_to_root, $systypes_array, $print_invoice_no;

    // Get the payment
	$counter = $_POST['PARAM_0'];
	$issueDate = $_POST['PARAM_1'];
	$payeName = $_POST['PARAM_2'];
	$amount = $_POST['PARAM_3'];
    	$destination = $_POST['PARAM_4'];
 	
 	
	$dec = user_price_dec();
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if($counter > 0){
		$from_trans = get_remittance($counter);
		$Name = $from_trans['Name'];
		$total_amt = abs($from_trans['Total']);
   		$date = sql2date($from_trans['tran_date']);
	}else{
		$Name = $payeName;
		$total_amt = (float)$amount;
   		$date = $issueDate;
	}
    

	// Get check information
   	
    	// Begin the report
    	$rep = new FrontReport(_('Printable Check'), "PrintableCheck", user_pagesize());
	$rep->SetHeaderType(null);    
    	$rep->NewPage();
    	// Set the font
   	 $rep->Font('','courier');
    	$rep->fontSize = 12;
   
    	//////////////////
    	// Check portion
    
    	$rep->NewLine(1,0,76);
    	$rep->cols = array(63,300, 340);
    	$rep->aligns = array('left', 'left', 'left','right');
    
	// Date
	$rep->NewLine(0,0,76);
    	$rep->DateCol(2,10, $rep->DatePrettyPrint($date, 0, 3));

    	// Pay to  
	$rep->NewLine(1,0,23);  
    	$rep->TextCol(0, 1, $Name);
    
    	// Amount (words)	
    	$rep->NewLine(1,0,23);
    	$rep->TextCol(0, 2,  price_in_words_custom($total_amt));
    
    	// Amount (numeric)
	$rep->NewLine(0.5,0,26);
    	$rep->TextCol(2, 10, number_format2($total_amt, $dec));
    
   	$rep->End();
}

//--------------------------------------------------------------------------------


?>
