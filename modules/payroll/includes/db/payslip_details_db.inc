<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//department Database Functions
/////////////////////////////////////////////////////
function add_payslip_details($name)
{
	$sql = "INSERT INTO ".TB_PREF."payslip_details (payslip_no,to_the_order_of,from_date,to_date,leaves,deductable_leaves) VALUES ("
		.db_escape($payslip_no).",".db_escape($to_the_order_of).",".db_escape($from_date)."
		,".db_escape($to_date).",".db_escape($leaves).",".db_escape($deductable_leaves)
		.")";
	
	db_query($sql,"The payslip details could not be added");
	
}

//--------------------------------------------------------------------------------------------
function get_payslip_details($payslip_no)
{
	$sql = "SELECT * FROM ".TB_PREF."payslip_details WHERE payslip_no=".db_escape($payslip_no);

	$result = db_query($sql, "could not get payslip details");

	return db_fetch($result);
}

//--------------------------------------------------------------------------------------------





?>
