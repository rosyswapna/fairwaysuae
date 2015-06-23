<?php

$page_security='SA_PAYROLL_REPORTS_SLIP';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
//include_once($path_to_root . "/modules/payroll/includes/db/payroll_structure_db.inc");
add_access_extensions();


print_payslip_report();



function print_payslip_report()
{
	global $path_to_root;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$employee = $_POST['PARAM_0'];
	$department = $_POST['PARAM_1'];
	$jobposition = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	
	$orientation = ($orientation ? 'L' : 'P');
	$cols = array(2, 40, 150, 250, 340,400,460);
	
	$headers=array(_('SlNo'), _('Employee Name'),  _('Job Position'), _('Department'), _('Debit'), _('Credit'), _('Payable'));
	
	$aligns = array('left', 'left', 'left',	'left', 'left','left','left');
	
	$rep = new FrontReport(_('Pay Slip Report'), "Pay Slip Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);
			
			$rep->Font();
			$rep->Info($params,$cols,$headers,$aligns);
			$rep->NewPage();
	
	
	
	$rep->End();
	
}

?>