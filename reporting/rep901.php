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
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");
include_once($path_to_root . "/sales/includes/db/cust_trans_db.inc");

//----------------------------------------------------------------------------------------------------


print_sales_report();



//----------------------------------------------------------------------------------------------------

function print_sales_report()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
    	$hidden = $_POST['PARAM_2'];	
    	$pos = $_POST['PARAM_3'];	
		$orientation = $_POST['PARAM_4'];
		$destination = $_POST['PARAM_5'];
	
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	

	$cols = array(2, 40, 130, 230,	300,  350, 430,510);

	$headers = array(_('Id'), _('Reference'), _('Date'), _('Customer '), _('Currency'),_('Amount') ,_('Balance'));

	
	$aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right');

    $params =   array('');

    $rep = new FrontReport(_('Customer Sales Report'), "CustomerSalesReport", user_pagesize(),9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
	$dec = user_price_dec();
	$res=get_sql_for_customer_inquiry();
	
	$qr=db_query($res);
	$rb=0;
	while ($trans=db_fetch($qr))
	{
		$rb += $trans['TotalAmount'];
		$rep->NewLine(2);
		$rep->TextCol(0, 1, $trans['trans_no']);
		$rep->TextCol(1, 2, $trans['reference']);
		$rep->TextCol(2, 3, sql2date($trans['tran_date']));
		$rep->TextCol(3, 4, $trans['name']);
		$rep->TextCol(4, 5, $trans['curr_code']);
		$rep->AmountCol(5,6,$trans['TotalAmount'],$dec);
		//$rep->AmountCol(6, 7, $trans['Rb'],$dec);
		$rep->AmountCol(6, 7, $rb,$dec);
	}
	
		$rep->End();
}

?>
