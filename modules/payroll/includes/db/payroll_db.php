<?php
/*
function get_payroll_rules()
{
	
		$sql = "SELECT salary.payroll_rule, chart.account_name 
			FROM ".TB_PREF."salary_structure salary, ".TB_PREF."chart_types type
			INNER JOIN ".TB_PREF."chart_master chart
			ON salary.payroll_rule = chart.account_code
			WHERE chart.account_type=type.id and chart.account_type=".AC_TYPE_PAYROLL;
		return db_query($sql,"oops");
			
}

function get_next_payslip_no(){
	$sql = "SELECT MAX(payslip_no)+1 FROM ".TB_PREF."gl_trans";
	$result = db_query($sql,"The next payslip number could not be retreived");

    	$row = db_fetch_row($result);
    	return $row[0];





 
}*/

?>
