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
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Invoices
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

print_invoices();

//----------------------------------------------------------------------------------------------------

function print_invoices()
{
	global $path_to_root, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;
	
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$pay_service = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$customer = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(2, 30, 120, 280, 340, 380, 450);

	// $headers in doctext.inc
	$aligns = array('center', 'left', 'left',	'left', 'left', 'left', 'left', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $i))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($i, ST_SALESINVOICE);

			if($customer && $myrow['debtor_no'] != $customer) {
				continue;
			}
			$baccount = get_default_bank_account($myrow['curr_code']);
			$params['bankaccount'] = $baccount['id'];

			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
				//$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			}
			
			
			$payment_terms = get_payment_terms($myrow['payment_terms']);

            if ($payment_terms['cash_sale'])
                $rep->title = _('CASH-SALES');
            else
                $rep->title = _('CREDIT-SALES');

			
			$rep->SetHeaderType('HeaderINV');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->NewPage();
   			$result = get_customer_trans_details(ST_SALESINVOICE, $i);
			$SubTotal = 0;
			$slno=1;
			$total_discount=0;
			while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;

				//$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				$Net = round2($sign * (  $myrow2["unit_price"] * $myrow2["quantity"]),
				
				   user_price_dec());
				$SubTotal += $Net;
	    	$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayDiscAmt = number_format2($myrow2["discount_amount"],$dec);
			$total_discount+=$DisplayDiscAmt;
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				
				$rep->TextCol(0, 1,$slno, -2);
				$rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
				$oldrow = $rep->row;
				$rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{
					$rep->TextCol(3, 4,	$DisplayQty, -2);
					$rep->TextCol(4, 5,	$myrow2['units'], -2);
					$rep->TextCol(5, 6,	$DisplayPrice, -2);
					//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
					//$rep->TextCol(6, 7,	$DisplayDiscAmt, -2);
					
					$rep->TextCol(6, 8,	$DisplayNet, -2);
				}	
				$slno++;
				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (18 * $rep->lineHeight))
					$rep->NewPage();
			}

			$memo = get_comments_string(ST_SALESINVOICE, $i);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
			$DisplayFreight1 = number_format2($sign*$myrow["ov_freight_charge"],$dec);
			$Displayinsurance = number_format2($sign*$myrow["ov_insurance"],$dec);
			$Displaypacking = number_format2($sign*$myrow["ov_packing_charge"],$dec);
			$Displayduties = number_format2($sign*$myrow["ov_duties"],$dec);
			$Displayservice = number_format2($sign*$myrow["ov_service_charge"],$dec);
			$Displaycommission = number_format2($sign*$myrow["ov_commission"],$dec);
			$discount_total=number_format2($total_discount,$dec);
    		
			$doctype = ST_SALESINVOICE;
			$totals=array(_("Sub-total")=>$DisplaySubTot);
			
			if($DisplayFreight>0){
				$totals[ _("Shipping")]=$DisplayFreight;
				
			}
			
			if($DisplayFreight1>0){
				$totals[_("Freight")]=$DisplayFreight1;
			
			}
			if($Displayinsurance>0){
				$totals[_("Insurance")]=$Displayinsurance;
			
			}
			if($Displaypacking>0){
				$totals[_("Packing ")]=$Displaypacking;
			
			}
			if($Displayduties>0){
				$totals[_("Duties")]=$Displayduties;
			
			}
			if($Displayservice>0){
				$totals[_("Service")]=$Displayservice;
			
			}
			if($Displaycommission>0){
				$totals[_("Commission")]=$Displaycommission;
			
			}
			$totals[_("Total Discount")]=$discount_total;
			
			
			
			
			
			$calc = count($totals)+12;
			$rep->row = $rep->bottomMargin + ($calc * $rep->lineHeight);
			foreach($totals as $label=>$val)
			{$rep->NewLine();
				$rep->TextCol(4, 7,$label, -1);
				$rep->TextCol(6, 8,	$val, -2);
			}
			
			
			
			
			
			
			$rep->NewLine();
			$tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
    			if ($tax_item['amount'] == 0)
    				continue;
    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
    			
    			if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
    				$tax_type_name = $tax_item['tax_type_name'];
    			else
    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

    			if ($tax_item['included_in_price'])
    			{
    				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
    				{
    					if ($first)
    					{	
							$rep->TextCol(4, 7, _("Total Tax Excluded"), -2);
							$rep->TextCol(7, 8,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
						$rep->TextCol(4, 7, $tax_type_name, -2);
						$rep->TextCol(7, 8,	$DisplayTax, -2);
						$first = false;
    				}
    				else
						
						$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
				}
    			else
    			{
					$rep->TextCol(4, 7, $tax_type_name, -2);
					$rep->TextCol(6, 8,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}
		
    		$rep->NewLine(1);
			$billing_total=$myrow["ov_freight_charge"] + $myrow["ov_insurance"] + $myrow["ov_packing_charge"] + $myrow["ov_duties"] + $myrow["ov_service_charge"] + $myrow["ov_commission"];
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]+$billing_total),$dec);
				
			
				
			$rep->Font('B');
			$rep->fontSize += 2;
			$rep->TextCol(4, 6, _("TOTAL INVOICE"), -2);
			$rep->TextCol(6, 8, $DisplayTotal, -2);
			$rep->Font();
			$rep->fontSize -= 2;

			
			
			$words = price_in_words_custom($myrow['Total']);
			if ($words != "")
			{
				
				$rep->Font('B');
				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words ." "._("Only"), - 2);
				$rep->Font('B');
			}
			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

?>
