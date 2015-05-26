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
		WHERE dept_id=".db_escape($id);

	db_query($sql,"The jobnames could not be updated");

}

//--------------------------------------------------------------------------------------------

function delete_jobnames($dept_id)
{
	begin_transaction();
	delete_entity_contacts('customer', $customer_id);

	$sql = "DELETE FROM ".TB_PREF."departments WHERE dept_id=".db_escape($dept_id);;
	db_query($sql,"cannot delete customer");
	commit_transaction();
}

//--------------------------------------------------------------------------------------------
function get_all_job($active=true){
  //get only active departments by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT dept_id, dept_name FROM ".TB_PREF."departments $act_sql";
  
  return db_query($sql, "The departments table is inaccessible");

}

//--------------------------------------------------------------------------------------------
function get_department($department_id)
{
	$sql = "SELECT * FROM ".TB_PREF."departments WHERE dept_id=".db_escape($department_id);

	$result = db_query($sql, "could not get department");

	return db_fetch($result);
}

//--------------------------------------------------------------------------------------------
function db_has_departments()
{
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."departments");
}




?>