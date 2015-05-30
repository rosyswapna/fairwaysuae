<?php
function get_payroll_rules()
{
	
		$sql = "SELECT salary.payroll_rule, chart.account_name 
			FROM ".TB_PREF."salary_structure salary, ".TB_PREF."chart_types type
			INNER JOIN ".TB_PREF."chart_master chart
			ON salary.payroll_rule = chart.account_code
			WHERE chart.account_type=type.id and chart.account_type=".AC_TYPE_PAYROLL;
		return db_query($sql,"oops");
			
}

?>