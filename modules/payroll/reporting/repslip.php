<?php

$page_security='SA_PAYROLL_REPORTS_SLIP';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/modules/payroll/includes/payroll_db.inc");
add_access_extensions();


print_payslip_report();


function get_debit_n_credit($type,$type_no){
	$sql = "SELECT SUM(IF(amount >= 0, amount, 0)) as debit, 
		SUM(IF(amount < 0, -amount, 0)) as credit
		FROM ".TB_PREF."gl_trans
		WHERE type = ".db_escape($type)." AND type_no = ".db_escape($type_no)
		." AND account <> " .AC_PAYABLE." GROUP BY type_no";


	
	$result =  db_query($sql, "could not get debit and credit total");
	return db_fetch($result);
}



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
	$cols = array(2, 25, 125, 230, 340, 400,460,515);
	
	$headers=array(_('SlNo'), _('Employee Name'),  _('Job Position'), _('Department'), _('Debit'), _('Credit'), _('Payable'));
	
	$aligns = array('left', 'left', 'left',	'left', 'left','left','left');
	
	$rep = new FrontReport(_('Pay Slip Report'), "Pay Slip Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);

	$dec = user_price_dec();
			
	$rep->Font();
	$rep->Info($params,$cols,$headers,$aligns);
	$rep->NewPage();

	$sql = get_sql_for_payslips($employee, '', '', $department, $jobposition);
	$result = db_query($sql, "could not get payslips");

	$slno = 1;
	while($row = db_fetch($result)){

		$rep->TextCol(0, 1,$slno);
		$rep->TextCol(1, 2,$row['emp_name']);
		$rep->TextCol(2, 3,$row['job_position']);
		$rep->TextCol(3, 4,$row['department']);

		$totals = get_debit_n_credit($row['type'],$row['type_no']);

		$DisplayCredit = number_format2($totals['credit']?$totals['credit']:0,$dec);
		$DisplayDebit = number_format2($totals['debit']?$totals['debit']:0,$dec);
		$DisplayPayable = number_format2($row['amount'],$dec);

		

		$rep->TextCol(4, 5,$DisplayCredit);
		$rep->TextCol(5, 6, $DisplayDebit);
		$rep->TextCol(6, 7, $DisplayPayable);

		$rep->NewLine(1.2);
		$slno++;
	}
	
	
	
	$rep->End();
	
}

?>
