<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//department Database Functions
/////////////////////////////////////////////////////
function add_department($name)
{
	$sql = "INSERT INTO ".TB_PREF."departments (dept_name ) VALUES ("
		.db_escape($name).")";

	db_query($sql,"The department could not be added");
}


//--------------------------------------------------------------------------------------------
function update_department($id,$name)
{

	$sql = "UPDATE ".TB_PREF."departments SET
		dept_name=".db_escape($name)."
		WHERE dept_id=".db_escape($id);

	db_query($sql,"The department could not be updated");

}

//--------------------------------------------------------------------------------------------

function delete_department($dept_id)
{
	begin_transaction();
	delete_entity_contacts('customer', $customer_id);

	$sql = "DELETE FROM ".TB_PREF."departments WHERE dept_id=".db_escape($dept_id);;
	db_query($sql,"cannot delete department");
	commit_transaction();
}

//--------------------------------------------------------------------------------------------
function get_all_leave($active=true){
  //get only active departments by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT dept_id, dept_name FROM ".TB_PREF."departments $act_sql";
  
  return db_query($sql, "The departments table is inaccessible");

}

//--------------------------------------------------------------------------------------------
function get_leave($department_id)
{
	$sql = "SELECT * FROM ".TB_PREF."departments WHERE dept_id=".db_escape($department_id);

	$result = db_query($sql, "could not get department");

	return db_fetch($result);
}

//--------------------------------------------------------------------------------------------
function db_has_leaves()
{
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."departments");
}




?>