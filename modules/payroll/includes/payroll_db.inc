<?php
/*
  Master include for payroll db functions
  Includes all other payroll db helper functions
*/
// SC: 18.04.2012: include various helpers

/*
include_once($path_to_root."/modules/payroll/includes/db/paytype_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/employee_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/taxes_db.inc");
*/

include_once($path_to_root."/modules/payroll/includes/db/department_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/employee_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/job_positions_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/leavetypes_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/payroll_structure_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/paytype_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/salary_structure_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/taxes_db.inc");


//get all payroll accounts from chart master
function get_payroll_rules()
{
	
	$sql = "SELECT salary.payroll_rule, chart.account_name 
		FROM ".TB_PREF."salary_structure salary, ".TB_PREF."chart_types type
		INNER JOIN ".TB_PREF."chart_master chart
		ON salary.payroll_rule = chart.account_code
		WHERE chart.account_type=type.id and chart.account_type=".AC_TYPE_PAYROLL;
	return db_query($sql,"oops");
			
}

//get next payslip no from gl trans
function get_next_payslip_no(){
	$sql = "SELECT MAX(payslip_no)+1 FROM ".TB_PREF."gl_trans";
	$result = db_query($sql,"The next payslip number could not be retreived");

    	$row = db_fetch_row($result);
    	return $row[0];
}

function get_sql_for_pmt_advices($employee,$from,$to){
	$sql = "SELECT	gl.tran_date,
		CONCAT(emp.emp_first_name,' ',emp.emp_last_name),
		job.job_name,
		dept.dept_name,
		gl.to_the_order_of,
		ABS(gl.amount) as amount,
		com.memo_,
		IF(ISNULL(a.gl_seq),0,a.gl_seq) as gl_seq
		FROM ".TB_PREF."gl_trans as gl
		 LEFT JOIN ".TB_PREF."audit_trail as a ON
			(gl.type=a.type AND gl.type_no=a.trans_no)
		 LEFT JOIN ".TB_PREF."comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)
		 LEFT JOIN ".TB_PREF."refs as refs ON
			(gl.type=refs.type AND gl.type_no=refs.id)
		 LEFT JOIN ".TB_PREF."users as u ON
			a.user=u.id
		 LEFT JOIN ".TB_PREF."employees as emp ON
			emp.emp_id = gl.person_id
		 LEFT JOIN ".TB_PREF."jobnames as job ON
			emp.job_position_id = job.job_name_id

		 LEFT JOIN ".TB_PREF."departments as dept ON
			emp.department_id = dept.dept_id
		WHERE gl.person_type_id = ".PT_EMPLOYEE."
		AND gl.account = " .AC_PAYABLE . "
		AND gl.tran_date >= '" . date2sql($from) . "'
		AND gl.tran_date <= '" . date2sql($to) . "'
		AND gl.amount!=0";
	
	if ($employee != -1) {
		$sql .= " AND gl.person_id=".db_escape($employee);
	}

	
	
	//$sql .= " GROUP BY gl.tran_date, a.gl_seq, gl.type, gl.type_no";
	return $sql;
}


?>
