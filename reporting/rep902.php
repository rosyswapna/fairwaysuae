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


print_purchase_report();

function get_sql_purchase_inquiry($from,$to)
{
    //$date_after = date2sql($after_date);
   // $date_to = date2sql($to_date);
   
   $date_from=date2sql($from);
   $date_to=date2sql($to);
	
    $sql = "SELECT trans.type, 
		trans.trans_no,
		trans.reference, 
		supplier.supp_name, 
		trans.supp_reference,
    	trans.tran_date, 
		trans.due_date,
		supplier.curr_code, 
    	(trans.ov_amount + trans.ov_gst  + trans.ov_discount
	+ trans.ov_freight_charge + trans.ov_insurance
	+ trans.ov_packing_charge + trans.ov_duties 
	+ trans.ov_service_charge + trans.ov_commission) AS TotalAmount, 
		trans.alloc AS Allocated,
		((trans.type = ".ST_SUPPINVOICE." OR trans.type = ".ST_SUPPCREDIT.") AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue,
    	(ABS(trans.ov_amount + trans.ov_gst  + trans.ov_discount) - trans.alloc <= ".FLOAT_COMP_DELTA.") AS Settled
    	FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as supplier
    	WHERE supplier.supplier_id = trans.supplier_id
     	AND trans.tran_date >= '$date_from'
    	AND trans.tran_date <= '$date_to'
		AND trans.type=".ST_SUPPINVOICE."
		AND trans.ov_amount != 0";	// exclude voided transactions

	$sql2 = "SELECT ".ST_SUPPRECEIVE." as type, 
		trans.id as trans_no,
		trans.reference, 
		supplier.supp_name, 
		po.requisition_no AS supp_reference,
    	delivery_date as tran_date, 
		'' as due_date,
		supplier.curr_code, 
    	'' AS TotalAmount,
		'' AS Allocated,
		0 as OverDue,
    	1 as Settled
    	FROM ".TB_PREF."grn_batch as trans, ".TB_PREF."suppliers as supplier,".TB_PREF."supp_trans as trans1, ".TB_PREF."purch_orders as po
    	WHERE supplier.supplier_id = trans.supplier_id
    	AND trans.purch_order_no = po.order_no
		AND trans1.type=".ST_SUPPINVOICE."
     	AND trans.delivery_date >= '$date_from'
    	AND trans.delivery_date <= '$date_to'";

  	if ($supplier_id != ALL_TEXT) {
   		$sql .= " AND trans.supplier_id = ".db_escape($supplier_id);
   		//$sql2 .= " AND trans.supplier_id = ".db_escape($supplier_id);
	}
	/*if (($filter == '6')) 
	{
			$sql = $sql2;
	} 
	elseif (!isset($filter) || $filter == ALL_TEXT || $filter == '6') {
		$sql = "SELECT * FROM (($sql) UNION ($sql2)) as tr";
   	}*/

   	if (isset($filter) && $filter != ALL_TEXT)
   	{
   		if (($filter == '1')) 
   		{
   			$sql .= " AND (trans.type = ".ST_SUPPINVOICE." OR trans.type = ".ST_BANKDEPOSIT.")";
   		} 
   		elseif (($filter == '2')) 
   		{
   			$sql .= " AND trans.type = ".ST_SUPPINVOICE." ";
   		} 
   		elseif ($filter == '3') 
   		{
			$sql .= " AND (trans.type = ".ST_SUPPAYMENT." OR trans.type = ".ST_BANKPAYMENT.") ";
   		} 
   		elseif (($filter == '4') || ($filter == '5')) 
   		{
			$sql .= " AND trans.type = ".ST_SUPPCREDIT."  ";
   		}

   		if (($filter == '2') || ($filter == '5')) 
   		{
   			$today =  date2sql(Today());
			$sql .= " AND trans.due_date < '$today' ";
   		}
   	}
   	return db_query($sql,"pruchse report not queried");
	//	return $sql;
}

//----------------------------------------------------------------------------------------------------

function print_purchase_report()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
    	$supp = $_POST['PARAM_2'];	
		$orientation = $_POST['PARAM_7'];
		$destination = $_POST['PARAM_8'];
	
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	

	$cols = array(2, 40, 130, 190,	380,  450, 500);

	$headers = array(_('Id'), _('Reference'), _('Date'), _('Supplier '), _('Currency'), _('Debit'));

	
	$aligns = array('left',	'left',	'left',	'left',	'left', 'right', 'right', 'right');

    $params =   array('');

    $rep = new FrontReport(_('Purchase  Report'), "CustomerSalesReport", user_pagesize(),9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res=get_sql_purchase_inquiry($from,$to);
	//display_notification($res);exit;
	
	while ($trans=db_fetch($res))
	{
		
		$rep->NewLine(2);
		$rep->TextCol(0, 1, $trans['trans_no']);
		$rep->TextCol(1, 2, $trans['reference']);
		$rep->TextCol(2, 3, $trans['tran_date']);
		$rep->TextCol(3, 4, $trans['supp_name']);
		$rep->TextCol(4, 5, $trans['curr_code']);
		$rep->TextCol(5, 6, $trans['TotalAmount']);
	}
	
		$rep->End();
}

?>
