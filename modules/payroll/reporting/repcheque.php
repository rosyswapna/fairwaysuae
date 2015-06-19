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
$page_security = 'SA_PAYCHECK_REPORT';
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
add_access_extensions();

//----------------------------------------------------------------------------------------------------

print_check();

//----------------------------------------------------------------------------------------------------
function get_remittance($type,$trans_no)
{
	
   $sql="SELECT bt.cheque_date,bt.cheque_no,bt.amount,CONCAT(emp.emp_first_name,' ',emp.emp_last_name) as emp_name
	FROM ".TB_PREF."bank_trans as bt
	
	LEFT JOIN ".TB_PREF."employees as emp ON
			bt.person_id = emp.emp_id
			
	
	WHERE bt.type=".db_escape($type)." AND  bt.trans_no=".db_escape($trans_no);
	
	$res=db_query($sql,"oops");
	
	//display_notification($sql);
	
	return db_fetch($res);
	
}


//-------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------

function print_check()
{
    global $path_to_root, $systypes_array, $print_invoice_no;

    // Get the payment
	
	
	$trans_no = $_POST['PARAM_0'];
	$type = $_POST['PARAM_1'];
    
 	
	//echo $trans_no;
	
	$dec = user_price_dec();
	
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	
		
		$from_trans = get_remittance($type,$trans_no);
		//print_r($from_trans);
		
		$date = sql2date($from_trans['cheque_date']);
		$cheque_no = $from_trans['cheque_no'];
		$total_amt = abs($from_trans['amount']);
		$Name = $from_trans['emp_name'];
    

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
