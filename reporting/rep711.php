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

	//$cols = array(4, 60, 225, 300, 355, 385, 450, 515);
	$cols = array(4,30, 100, 250, 300, 350,400);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	$margins = array('top'=>200,
			'bottom'=>30,
			'left'=>150,
			'right'=>30);

	if ($email == 0)
		$rep = new FrontReport(_('SALES-CASH'), "InvoiceBulk", user_pagesize(), 9, $orientation,$margins);
	if ($orientation == 'L')
		recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $i))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($i, ST_SALESINVOICE);
			//echo "<pre>";print_r($myrow);echo "</pre";exit;
			if ($customer && $myrow['debtor_no'] != $customer) {
				continue;
			}
			if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
				continue;
			}
			$baccount = get_default_bank_account($myrow['curr_code']);
			$params['bankaccount'] = $baccount['id'];

			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
				$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			}	
			$rep->SetHeaderType('Header4');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->NewPage();
   			$result = get_customer_trans_details(ST_SALESINVOICE, $i);
			$SubTotal = 0;$slno =1;$totalDiscount = 0;
			while ($myrow2=db_fetch($result))
			{
					//echo "<pre>";print_r($myrow);echo "</pre";exit;
				if ($myrow2["quantity"] == 0)
					continue;

				$Net = round2($sign * ( $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
				$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
				$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
				$DisplayNet = number_format2($Net,$dec);
				$totalDiscount += $Net*$myrow2["discount_percent"];

		
				$rep->TextCol(0, 1,	$slno, -2);
				$rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
				$oldrow = $rep->row;
				$rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{
					$rep->TextCol(3, 4,	$DisplayQty, -2);
					$rep->TextCol(4, 5,	$DisplayPrice, -2);
					$rep->TextCol(5, 6,	$DisplayNet, -2);
				}	
				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
				$slno++;
			}
			$DisplayDiscount = number_format2($totalDiscount,user_percent_dec());
			$netAmount = $SubTotal - $totalDiscount;
			

			$memo = get_comments_string(ST_SALESINVOICE, $i);
			

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
			$DisplayDiscount = number_format2($sign*$DisplayDiscount,$dec);
			$DisplayNet = number_format2($sign*$netAmount,$dec);

    			$rep->row = $rep->bottomMargin + (21 * $rep->lineHeight);
			$doctype = ST_SALESINVOICE;
			//Gross
			//Discount
			//Net Amount
			//Cash Amount
			$tmp = $rep->row;
			$rep->TextCol(3, 5, _("Gross"), -2);
			$rep->TextCol(5, 6,	$DisplaySubTot, -2);
			$rep->NewLine(1.5);
			$rep->TextCol(3, 5, _("Discount"), -2);
			$rep->TextCol(5, 6,	$DisplayDiscount, -2);
			$rep->NewLine(1.5);
			$rep->TextCol(3, 5, _("Net Amount"), -2);
			$rep->TextCol(5, 6,	$DisplayNet, -2);
			$rep->NewLine(1.5);
			$rep->TextCol(3, 5, _("Cash Amount"), -2);
			$rep->TextCol(5, 6,	$DisplayNet, -2);
			$rep->NewLine();
			$rep->row = $tmp;
			$words = price_in_words_custom($myrow['Total']);
			if ($words != "")
			{
				$rep->TextCol(0, 3, $myrow['curr_code'] . " : " . $words, - 2);
			}
			$rep->NewLine(2);
			$rep->TextCol(0,2, "Remarks : ".$myrow['memo_'], - 2);
			$rep->NewLine(2);
			$rep->TextCol(0,2, "SalesMan : ", - 2);
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
