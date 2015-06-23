<?php

$page_security='SA_PAYROLL_REPORTS_STRUCT';


$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/modules/payroll/includes/db/salary_structure_db.inc");

add_access_extensions();

print_struct_report();

function get_emp_list($emp, $department, $position)
{

	$sql = "SELECT * FROM ".TB_PREF."employees";

	if($department){
		$sql .= " WHERE department_id=".db_escape($department);
	}elseif($position){
		$sql .= " WHERE job_position_id=".db_escape($department);
	}elseif($emp){
		$sql .= " WHERE emp_id=".db_escape($emp);
	}

	return db_query($sql, "could not get employees");
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
	$cols = array(2, 30, 340, 440);
	
	$headers=array(_('SlNo'), _('Paystructure Rules'),  _('Credits'), _('Debit'));
	
	$aligns = array('left', 'left', 'left',	'left');
	
	
	$rep = new FrontReport(_('Pay Structure Report'), "Pay Structure Report", user_pagesize(), 9, $orientation);
	
	if ($orientation == 'L')
		recalculate_cols($cols);

	$dec = user_price_dec();
			
	$rep->Font();
	$rep->Info($params,$cols, $headers, $aligns);
	$rep->NewPage();
		
	$result = get_emp_list($employee,$department,$jobposition);

	
	while ($myrow=db_fetch($result))
	{

		$rep->Font('B');
		$rep->TextCol(1, 2,_("Employee : ").$myrow['emp_first_name']." ".$myrow['emp_last_name']);
		$rep->Font();
		$rep->NewLine(2);

		if(!exists_salary_structure($myrow['job_position_id'])){

			$rep->Font('I');
			$rep->TextCol(1, 4,_("Salary Stucture Not defined!"));
			$rep->NewLine();
			$rep->Font();

			$line = $rep->row;
			
		}else{
		
			$strRes = get_salary_structure($myrow['job_position_id']);
			$slno=1;
			while($strRow = db_fetch($strRes)){
				$rep->TextCol(0, 1,$slno);
				$rep->TextCol(1, 2,$strRow['account_name']);

				number_format2($myrow2["unit_price"],$dec);

				if($strRow['type'] == CREDIT){
					$credit = number_format2($strRow['pay_amount'],$dec);
					$debit = number_format2(0,$dec);
				}else{
					$credit = number_format2(0,$dec);
					$debit = number_format2($strRow['pay_amount'],$dec);
				}

				$rep->TextCol(2, 3,$credit);
				$rep->TextCol(3, 4, $debit);


				$rep->NewLine(1.2);

				$line = $rep->row;
				$slno++;
			}
			
		}

		$rep->Line($line);
		
		$rep->NewLine();

	}
			
	
	$rep->End();
}



?>
