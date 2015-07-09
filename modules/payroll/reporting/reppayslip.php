<?php

$page_security='SA_PAYSLIP_REPORT';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/modules/payroll/includes/db/salary_structure_db.inc");
include_once($path_to_root . "/modules/payroll/includes/db/employee_db.inc");
include_once($path_to_root . "/modules/payroll/includes/db/payslip_details_db.inc");

add_access_extensions();

print_payslip_report();

function get_payslip_list($type_no,$payslip_no)
{

	$sql = "SELECT IF(amount >= 0, amount, 0) as debit,
IF(amount < 0, -amount, 0) as credit,account_code,account_name FROM ".TB_PREF."gl_trans as gl 
	
	
	LEFT JOIN ".TB_PREF."chart_master as cht ON
			gl.account = cht.account_code
			
	WHERE type_no!=".db_escape($type_no)." AND payslip_no=".db_escape($payslip_no)."
	AND type=".ST_JOURNAL ;

	//display_notification($sql);exit;
	return db_query($sql, "could not get employees");
}


function print_payslip_report()
{	
	
	global $path_to_root;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	
	$type_no = $_POST['PARAM_0'];
	$payslip_no=$_POST['PARAM_1'];
	$employee=$_POST['PARAM_2'];
	$emp=get_employee($employee);
	
	$slip=get_payslip_details($payslip_no);
	
	
	$orientation = ($orientation ? 'L' : 'P');
	$cols = array(2, 90, 340, 440);
	
	//$headers=array(_('Account code'), _('Paystructure Rules'),  _('Credits'), _('Debit'));
	
	$aligns = array('left', 'left', 'left',	'left');
	
	
	$rep = new FrontReport(_('PAYSLIP REPORT'), "PaySlip Report", user_pagesize(), 9, $orientation);
	$rep->NewLine(1.5);
	
	if ($orientation == 'L')
		recalculate_cols($cols);
	

	$dec = user_price_dec();
	$rep->SetHeaderType(null);
	$rep->Font();
	$rep->Info($params,$cols, $headers, $aligns);
	$rep->NewPage();
	$iline1 = $rep->row;
	$rep->NewLine(1.5);
	$rep->Line($rep->row);
	
	$iline2 = $iline1 - 4 * $rep->lineHeight;
	
	
	$result = get_payslip_list($type_no,$payslip_no);
	$rep->Font('b');
	$rep->TextWrap($leftmargin,$rep->row, $rep->pageWidth - $rep->rightMargin - $rightmargin - 20, $rep->title, 'center');
	//$rep->TextCol(1, 2,'PAYSLIP REPORT');
	$rep->NewLine(3);
	
	$rep->TextCol(0, 2,_("Employee : ").$emp['emp_first_name']." ".$emp['emp_last_name']);
	$rep->TextCol(2,3,_("Payslip Id :").$emp['emp_id']);
	$rep->NewLine();
	$rep->TextCol(0,2,_("Employee Code : ").$emp['emp_id']);
	$rep->TextCol(2,3,_("Payslip Reference :").$emp['emp_id']);
	$rep->NewLine();
	$rep->TextCol(0,2,_("Payslip Generated Date :").sql2date($slip['generated_date']));
	$rep->TextCol(2,3,_("To The Order Of :").$slip['to_the_order_of']);
	$rep->NewLine();
	$rep->TextCol(0,2,_("Payslip From Date :").sql2date($slip['from_date']));
	$rep->TextCol(2,3,_("Total Leaves :").$slip['leaves']);
	$rep->NewLine();
	$rep->TextCol(0,2,_("Payslip To Date :").sql2date($slip['to_date']));
	$rep->TextCol(2,3,_("Deductable Leaves :").$slip['deductable_leaves']);
	
	$rep->NewLine(5);
	$rep->Font('');
	$rep->Line($rep->row);
	//$rep->row=$iline2+ $rep->lineHeight;
		$rep->Font('b');
		$rep->NewLine(1.2);
		$rep->TextCol(0, 1,'Account');
		$rep->TextCol(1, 2,'Payrules');
		$rep->TextCol(2, 3,'Credits');
		$rep->TextCol(3, 4,'Debit');
		$rep->NewLine(1.2);
		$rep->Line($rep->row);
		$rep->Font('');
	$rep->NewLine();
	while ($myrow=db_fetch($result))
	{
		
		
		$rep->NewLine(1.2);
		$rep->TextCol(0, 1,$myrow['account_code']);
		$rep->TextCol(1, 2,$myrow['account_name']);
		
		$rep->TextCol(2, 3,number_format2($myrow['credit'],$dec));
		$rep->TextCol(3, 4,number_format2($myrow['debit'],$dec));
		
	}$rep->NewLine();
	$rep->Line($rep->row);
	$rep->End();
	
}


?>
