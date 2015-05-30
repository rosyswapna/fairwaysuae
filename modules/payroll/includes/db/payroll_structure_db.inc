<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//department Database Functions
/////////////////////////////////////////////////////
//function add_payrule($id,$name)
//{
//$sql = "INSERT INTO ".TB_PREF."payrule_structure (job_position_id,payroll_rule ) VALUES ("
//.db_escape($id).","
//.db_escape($rule).")";
//db_query($sql,"The payrule could not be added");	
//}


//--------------------------------------------------------------------------------------------
function update_payrule($job_id,$rule,$id)
{

	$sql = "UPDATE ".TB_PREF."payrule_structure SET
		job_position_id=".db_escape($job_id)
		.",payroll_rule=".db_escape(implode(';', $rule))
		."WHERE sal_str_id=".(int)$id;
		

	db_query($sql,"The jobnames could not be updated");

}

//--------------------------------------------------------------------------------------------

function delete_payrule($job_id)
{
	

	$sql = "DELETE FROM ".TB_PREF."payrule_structure WHERE payroll_rule=".db_escape($job_id);;
	db_query($sql,"cannot delete job");
	commit_transaction();
}

//--------------------------------------------------------------------------------------------
function get_all_payrule($active=true){
  //get only active jobnames by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT payroll_rule, job_name FROM ".TB_PREF."payrule_structure $act_sql";
  
  return db_query($sql, "The jobnames table is inaccessible");

}

//--------------------------------------------------------------------------------------------
function get_payrule($job_id)
{
	$sql = "SELECT * FROM ".TB_PREF."payrule_structure WHERE job_name_id=".db_escape($job_id);

	$result = db_query($sql, "could not get jobnames");

	return db_fetch($result);
}

//--------------------------------------------------------------------------------------------
function db_has_jobs()
{
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."jobnames");
}




?>
