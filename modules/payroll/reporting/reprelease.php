<?php

$page_security='SA_PAYROLL_REPORTS_RELEASE';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
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
	$cols = array(2, 50, 180, 280, 380,480);
	
	
	$headers=array(_('SlNo'), _('Employee Name'),  _('Job Position'), _('Department'),_('Cheque No'), _('Payable'));
	
	$aligns = array('left', 'left', 'left',	'left', 'left');
	
	$rep = new FrontReport(_('Payment Release Report'), "Payment Release Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);
			
			$rep->Font();
			$rep->Info($params,$cols,$headers,$aligns);
			$rep->NewPage();

	$rep->End();
}

?>