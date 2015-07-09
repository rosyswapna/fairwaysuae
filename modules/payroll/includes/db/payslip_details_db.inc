<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//department Database Functions
/////////////////////////////////////////////////////
function add_payslip_details($payslip_no,$to_the_order_of,$from_date,$to_date,$leaves,$deductable_leaves)
{

	$from = date2sql($from_date);
	$to = date2sql($to_date);
	$generated = date2sql(Today());
	$sql = "INSERT INTO ".TB_PREF."payslip_details (payslip_no,generated_date,to_the_order_of,from_date,to_date,leaves,deductable_leaves) VALUES ("
		.db_escape($payslip_no).",'$generated',".db_escape($to_the_order_of).",'$from'
		,'$to',".db_escape($leaves).",".db_escape($deductable_leaves)
		.")";

//echo $sql;exit;
	
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
