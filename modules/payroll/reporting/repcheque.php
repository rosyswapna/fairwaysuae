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
$page_security ='SA_PAYCHECK_PRINTABLE';
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
function get_remittance($type, $trans_no)
{
   	$sql="SELECT bt.cheque_date,bt.cheque_no,CONCAT(emp.emp_first_name,' ',emp.emp_last_name) as emp_name,gl.amount
	FROM ".TB_PREF."bank_trans as bt
	LEFT JOIN ".TB_PREF."employees as emp ON
			emp.emp_id = gl.person_id
	LEFT JOIN ".TB_PREF."departments as dp ON
			dp.dept_id = gl.person_id
	LEFT JOIN ".TB_PREF."jobnames as jb ON
			jb.job_name_id = emp.job_position_id
	LEFT JOIN ".TB_PREF."bank_trans as bt ON
			bt.trans_no = gl.type_no AND bt.type=gl.type
	LEFT JOIN ".TB_PREF."bank_accounts as ba ON
			ba.id = bt.bank_act
	
	WHERE gl.type=" .ST_JOURNAL. " AND gl.account IN (select account_code from ".TB_PREF."bank_accounts) AND type_no=".db_escape($trans_no);
	
	$res=db_query($sql,"oops");
	return db_fetch($res);
}


//-------------------------------------------------------------------------------------------------
function price_in_words_custom($number)
{    
       
    if (strpos($number, '.') !== false) {
               list($number, $fraction) = explode('.', $number);
        if(strlen($fraction) == 1){
            $temp = $fraction;
            $fraction = $fraction * 10;
        }

        }


    $Bn = floor($number / 1000000000); /* Billions (giga) */
    $number -= $Bn * 1000000000;
    $Gn = floor($number / 1000000);  /* Millions (mega) */
    $number -= $Gn * 1000000;
    $kn = floor($number / 1000);     /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100);      /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10);       /* Tens (deca) */
    $n = $number % 10;               /* Ones */

    $res = "";
    $rupe = "";

    if ($Bn)
        $rupe .= _number_to_words($Bn) . " Billion";
    if ($Gn)
        $rupe .= (empty($rupe) ? "" : " ") . _number_to_words($Gn) . " Million";
    if ($kn)
        $rupe .= (empty($rupe) ? "" : " ") . _number_to_words($kn) . " Thousand";
    if ($Hn)
        $rupe .= (empty($rupe) ? "" : " ") . _number_to_words($Hn) . " Hundred";

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
        "Nineteen");
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
        "Seventy", "Eighty", "Ninety");
   
    $paise ='';
    //fraction part
    if (null !== $fraction && is_numeric($fraction)) {
       
       

        if($fraction%10 == 0){
            if($temp == 1)
                $paise .= " ".$ones[$fraction];
            else
                $paise .= " ".$tens[$temp];
        }else{
            $f = floor($fraction / 10);       /* Tens (deca) */
            $s = $fraction % 10;               /* Ones */
            if ($f < 2)
                $paise .= $ones[$f * 10 + $s];
            else
            {
                $paise .= " ".$tens[$f];
                if ($s)
                $paise .= "-" . $ones[$s];
            }
        }
        $paise .= " Paise";

    }

    //------tens
    if ($Dn || $n)
    {
        if (!empty($rupe) && empty($paise))
            $rupe .= " and ";
        if ($Dn < 2)
            $rupe .= " ".$ones[$Dn * 10 + $n];
        else
        {
            $rupe .= " ".$tens[$Dn];
            if ($n)
            $rupe .= "-" . $ones[$n];
        }
    }
    if(!empty($paise)){
        if(!empty($rupe))
            $res .= "Rupees ".$rupe." and ".$paise;
        else
            $res .= $paise;
    }else if(!empty($rupe)){
        $res .= $rupe;
    }
       
   


    if (empty($res))
        $res = "zero";

    //echo $res;exit;
    return $res;

} 

//----------------------------------------------------------------------------------------------------

function print_check()
{
    global $path_to_root, $systypes_array, $print_invoice_no;

    // Get the payment
	$fromd = $_POST['PARAM_0'];
	$tod = $_POST['PARAM_1'];
	$fromd = date2sql($fromd);
	$tod = date2sql($tod);

    $from = $_POST['PARAM_0'];
    $destination = $_POST['PARAM_3'];
 	
 	$trans_no = explode("-", $from);
$trans_no[1]=$_POST['PARAM_2'];
$trans_no[0]=$_POST['PARAM_3'];
	$dec = user_price_dec();
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    
	$from_trans = get_remittance($trans_no[1], $trans_no[0]);

    // Get check information
    $total_amt = $from_trans['Total'];
    
    $date = sql2date($from_trans['tran_date']);
    $memo = get_comments_string($trans_no[1], $trans_no[0]);
    
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
    $rep->TextCol(0, 1, $from_trans['supp_name']);
    
    // Amount (words)
		
    $rep->NewLine(1,0,23);
    $rep->TextCol(0, 2,  price_in_words_custom(-$total_amt));
    
    // Amount (numeric)
		$rep->NewLine(0.5,0,26);
    $rep->TextCol(2, 10, number_format2(-$total_amt, $dec));
    
    
    
    // Memo
   /* $rep->NewLine(1,0,78);
    $rep->TextCol(0, 1, $memo);

  	$rep->company = get_company_prefs();*/
  /////////////////////
    // Item details x 2 
    
    for ($section=1; $section<=0; $section++) 
    {
        $rep->fontSize = 12;        
        // Move down to the correct section
        $rep->row = $section == 1 ? 505 : 255;
        $rep->cols = array(20, 340, 470, 588);
        $rep->aligns = array('left', 'left', 'right', 'right');
        
        // Pay to
        $rep->Font('b');
        $rep->TextCol(0, 1, $from_trans['supp_name']);
        $rep->Font();
        
        // Date
        $rep->DateCol(1, 2, $rep->DatePrettyPrint($date, 0, 0));
        
        // Amount (numeric)
        $rep->TextCol(2, 3, number_format2(-$total_amt, 2));
    
        // Add Trans # + Reference
        $rep->NewLine();
        if ($print_invoice_no == 0)
        	$tno = $from_trans['reference'];
        else
        	$tno = $from_trans['trans_no'];
        $rep->TextCol(0, 3, sprintf( _("Payment # %s - from Customer: %s - %s"), $tno,
        	$from_trans['supp_account_no'], $rep->company['coy_name']));
        
        // Add memo
        $rep->NewLine();
        $rep->TextCol(0, 3, _("Memo: ").$memo);    
        
        // TODO: Do we want to set a limit on # of item details?  (Max is probably 6-7)

        // Get item details
		$result = get_allocations_for_remittance($from_trans['supplier_id'], $from_trans['type'], $from_trans['trans_no']);
        // Fill in details
        $rep->NewLine(2);
        $rep->fontSize = 10;
        // Use different columns now for the additional info
        $rep->cols = array(20, 160, 235, 290, 370, 480, 588);
        $rep->aligns = array('left', 'left', 'left', 'right', 'right', 'right');
        
        // Add headers
        $rep->Font('b');
        $rep->TextCol(0, 1, _("Type/Id"));
        $rep->TextCol(1, 2, _("Trans Date"));
        $rep->TextCol(2, 3, _("Due Date"));
        $rep->TextCol(3, 4, _("Total Amount"));
        $rep->TextCol(4, 5, _("Left to Allocate"));
        $rep->TextCol(5, 6, _("This Allocation"));
        $rep->NewLine();
        
        $rep->Font();    
		$total_allocated = 0;
		while ($item=db_fetch($result))
		{
           	$rep->TextCol(0, 1, $systypes_array[$item['type']]." ".$item['supp_reference']);
           	$rep->TextCol(1, 2, sql2date($item['tran_date']));
           	$rep->TextCol(2, 3, sql2date($item['due_date']));
           	$rep->AmountCol(3, 4, $item['Total'], $dec);
           	$rep->AmountCol(4, 5, $item['Total'] - $item['alloc'], $dec);
           	$rep->AmountCol(5, 6, $item['amt'], $dec);
           	$total_allocated += $item['amt'];
       		$rep->NewLine(1, 0, $rep->lineHeight + 3); // Space it out
    	}
    	$rep->NewLine();
       	$rep->TextCol(4, 5, _("Total Allocated"));
       	$rep->AmountCol(5, 6, $total_allocated, $dec);
    	$rep->NewLine();
       	$rep->TextCol(4, 5, _("Left to Allocate"));
       	$rep->AmountCol(5, 6, -$from_trans['Total'] - $total_allocated, $dec);
    	
    } // end of section
    
    $rep->End();
}

//--------------------------------------------------------------------------------


?>
