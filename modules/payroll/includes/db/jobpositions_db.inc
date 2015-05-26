<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//department Database Functions
/////////////////////////////////////////////////////
function add_jobnames($name)
{
	$sql = "INSERT INTO ".TB_PREF."jobnames (job_name ) VALUES ("
		.db_escape($name).")";

	db_query($sql,"The job could not be added");
}


//--------------------------------------------------------------------------------------------
function update_jobnames($id,$name)
{

	$sql = "UPDATE ".TB_PREF."jobnames SET
		job_name=".db_escape($name)."
		WHERE job_name_id=".db_escape($id);

	db_query($sql,"The jobnames could not be updated");

}

//--------------------------------------------------------------------------------------------

function delete_jobnames($job_id)
{
	

	$sql = "DELETE FROM ".TB_PREF."jobnames WHERE job_name_id=".db_escape($job_id);;
	db_query($sql,"cannot delete job");
	commit_transaction();
}

//--------------------------------------------------------------------------------------------
function get_all_job($active=true){
  //get only active jobnames by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT job_name_id, job_name FROM ".TB_PREF."jobnames $act_sql";
  
  return db_query($sql, "The jobnames table is inaccessible");

}

//--------------------------------------------------------------------------------------------
function get_jobs($job_id)
{
	$sql = "SELECT * FROM ".TB_PREF."jobnames WHERE job_name_id=".db_escape($job_id);

	$result = db_query($sql, "could not get jobnames");

	return db_fetch($result);
}

//--------------------------------------------------------------------------------------------
function db_has_jobs()
{
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."jobnames");
}




?>