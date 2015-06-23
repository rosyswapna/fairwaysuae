<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//department Database Functions
/////////////////////////////////////////////////////
function add_payroll($job_position_id,$payroll_rule)
{
	$sql = "INSERT INTO ".TB_PREF."payroll_structure (job_position_id,payroll_rule ) VALUES ("
	.db_escape($job_position_id).","
	.db_escape(implode(';',$payroll_rule)).")";
	db_query($sql,"The payroll could not be added");	
}


//--------------------------------------------------------------------------------------------
function update_payroll($job_position_id,$payroll_rule)
{

	$sql = "UPDATE ".TB_PREF."payroll_structure SET
		payroll_rule=".db_escape(implode(';', $payroll_rule))
		."WHERE job_position_id=".db_escape($job_position_id);
		

	db_query($sql,"The payroll could not be updated");

}
//--------------------------------------------------------------------------------------------
function exist_payroll($job_position_id)
{
	$sql = "SELECT payroll_rule FROM ".TB_PREF."payroll_structure WHERE job_position_id=".db_escape($job_position_id);

	$result = db_query($sql, "Cannot retreive a salary structure");

	return (db_num_rows($result) > 0);
}

//--------------------------------------------------------------------------------------------
function reset_payroll($job_position_id)
{

	$sql = "DELETE FROM ".TB_PREF."payroll_structure WHERE job_position_id =".db_escape($job_position_id);
    	db_query($sql, "could not reset salary structure role");
}



//--------------------------------------------------------------------------------------------

function delete_payroll($job_id)
{
	

	$sql = "DELETE FROM ".TB_PREF."payroll_structure WHERE payroll_rule=".db_escape($job_id);;
	db_query($sql,"cannot delete job");
	commit_transaction();
}

//--------------------------------------------------------------------------------------------
function get_all_payroll($active=true){

	
  //get only active jobnames by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT payroll_rule, job_name FROM ".TB_PREF."payroll_structure $act_sql";
  display_notification($sql);
  return db_query($sql, "The jobnames table is inaccessible");

}

//--------------------------------------------------------------------------------------------
function get_payroll_structure($job_position_id)
{
	$sql = "SELECT * FROM ".TB_PREF."payroll_structure WHERE job_position_id=".db_escape($job_position_id);

	$result = db_query($sql, "could not get jobnames");

	$row = db_fetch($result);
	if ($row != false) {
		$row['payroll_rule'] = explode(';', $row['payroll_rule']);
	}

	return $row;

}





?>
