<?php

$page_security='SA_PAYROLL_REPORTS_ADVICE';



$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/modules/payroll/includes/payroll_db.inc");

add_access_extensions();

print_advice_report();


function print_advice_report()
{
	global $path_to_root;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$employee = $_POST['PARAM_0'];
	$department = $_POST['PARAM_1'];
	$jobposition = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	
	$orientation = ($orientation ? 'L' : 'P');
	$cols = array(2, 50, 180, 320, 480, 515);
	
	
	$headers=array(_('SlNo'), _('Employee Name'),  _('Job Position'), _('Department'), _('Payable'));
	
	$aligns = array('left', 'left', 'left',	'left', 'right');
	
	$rep = new FrontReport(_('Payment Advice Report'), "Payment advice Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);
			
	$rep->Font();
	$rep->Info($params,$cols,$headers,$aligns);
	$rep->NewPage();
	
	$sql = get_sql_for_payment_advices($employee,$jobposition,$department);
	//display_notification($sql);exit;
	$result = db_query($sql, "could not get payslips");

	$slno = 1;
	while($row = db_fetch($result)){

		$rep->TextCol(0, 1,$slno);
		$rep->TextCol(1, 2,$row['emp_name']);
		$rep->TextCol(2, 3,$row['job_position']);
		$rep->TextCol(3, 4,$row['department']);

		$DisplayAmount = number_format2($row['amount'],$dec);

		$rep->TextCol(4, 5, $DisplayAmount);

		$rep->NewLine(1.2);
		$slno++;
	}
			
	$rep->End();
}
?>
