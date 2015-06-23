<?php

$page_security='SA_PAYROLL_REPORTS_STRUCT';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/modules/payroll/includes/db/salary_structure_db.inc");

add_access_extensions();

print_struct_report();

function get_payroll_structure_print($job_position_id)
{
	$sql = "SELECT * FROM ".TB_PREF."payroll_structure WHERE job_position_id=".db_escape($job_position_id);

	$result = db_query($sql, "could not get jobnames");

	$row = db_fetch($result);
	if ($row != false) {
		$row['payroll_rule'] = explode(';', $row['payroll_rule']);
	}

	return $row;

}

function print_struct_report()
{
	global $path_to_root;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$employee = $_POST['PARAM_0'];
	$department = $_POST['PARAM_1'];
	$jobposition = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	
	$orientation = ($orientation ? 'L' : 'P');
	$cols = array(2, 50, 220, 400);
	
	$headers=array(_('SlNo'), _('Paystructure Rules'),  _('Credits'), _('Debit'));
	
	$aligns = array('left', 'left', 'left',	'left');
	
	
	
	$rep = new FrontReport(_('Pay Structure Report'), "Pay Structure Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);
			
			$rep->Font();
			$rep->Info($params,$cols, $headers, $aligns);
			$rep->NewPage();
		
	$result=get_payroll_structure_print();
	$slno=1;
	if(db_num_rows($result) > 0){
		while ($myrow2=db_fetch($result))
		{
			$rep->TextCol(0,1,$slno, -2);
		}
	}
		$slno++;		
	
	$rep->End();
}



?>