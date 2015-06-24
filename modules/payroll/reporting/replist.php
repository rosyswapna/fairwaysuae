<?php

$page_security='SA_PAYROLL_REPORTS_LIST';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
add_access_extensions();


print_replist_report();

function get_empl_list($emp, $department, $position)
{

	$sql = "SELECT * FROM ".TB_PREF."employees as emp
	
	LEFT JOIN ".TB_PREF."passport as pt ON
	pt.emp_id=emp.emp_id";
	
	/*LEFT JOIN ".TB_PREF."jobnames as jb ON
	jb.job_name_id=emp.job_position_id
	
	LEFT JOIN ".TB_PREF."departments as dpt ON
	dpt.dept_id=emp.department_id*/
	
	if($department){
		$sql .= " WHERE department_id=".db_escape($department);
	}elseif($position){
		$sql .= " WHERE job_position_id=".db_escape($position);
	}elseif($emp){
		$sql .= " WHERE emp.emp_id=".db_escape($emp);
	}

	return db_query($sql, "could not get employees");
}

function print_replist_report()
{
	global $path_to_root;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$employee = $_POST['PARAM_0'];
	$department = $_POST['PARAM_1'];
	$jobposition = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	
	$orientation = ($orientation ? 'L' : 'P');
	$cols = array(2, 70, 180, 300, 400,460);
	
	
	$headers=array(_('Emp Code'), _('Employee Name'),  _('Job Position'), _('Department'), _('Passport NO'));
	
	$aligns = array('center', 'left', 'center',	'center','center');
	
	$rep = new FrontReport(_('Detail Listing Report'), "Detail Listing Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);
			
			$rep->Font();
			$rep->Info($params,$cols,$headers,$aligns);
			$rep->NewPage();
			
			
		$result = get_empl_list($employee,$department,$jobposition);
		
	while ($myrow=db_fetch($result))
	{
		
		
			$rep->TextCol(0, 1,$myrow['emp_code']);
			$rep->TextCol(1, 2,$myrow['emp_first_name']." ".$myrow['emp_last_name']);
			$rep->TextCol(2, 3,$myrow['job_position_id']);
			$rep->TextCol(3, 4,$myrow['department_id']);
			$rep->TextCol(4, 5,$myrow['passport_no']);
		
		
		$rep->NewLine(2);
	}

	$rep->End();
}


?>