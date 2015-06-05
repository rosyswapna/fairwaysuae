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


?>
