<?php

//JM: 23/04/12 - consolidation of db includes

/////////////////////////////////////////////////////
//leaves Database Functions
/////////////////////////////////////////////////////
function add_leave($name)
{
	$sql = "INSERT INTO ".TB_PREF."leavetypes (ltyp_name ) VALUES ("
		.db_escape($name).")";

	db_query($sql,"The leave could not be added");
}


//--------------------------------------------------------------------------------------------
function update_leaves($id,$name)
{

	$sql = "UPDATE ".TB_PREF."leavetypes SET
		dept_name=".db_escape($name)."
		WHERE dept_id=".db_escape($id);

	db_query($sql,"The leave could not be updated");

}

//--------------------------------------------------------------------------------------------

function delete_leaves($dept_id)
{
	begin_transaction();
	delete_entity_contacts('customer', $customer_id);

	$sql = "DELETE FROM ".TB_PREF."leaves WHERE dept_id=".db_escape($dept_id);;
	db_query($sql,"cannot delete leaves");
	commit_transaction();
}

//--------------------------------------------------------------------------------------------
function get_all_leaves($active=true){
  //get only active leavess by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT dept_id, dept_name FROM ".TB_PREF."leaves $act_sql";
  
  return db_query($sql, "The leaves table is inaccessible");

}

//--------------------------------------------------------------------------------------------
function get_leaves($leaves_id)
{
	$sql = "SELECT * FROM ".TB_PREF."leaves WHERE dept_id=".db_escape($leaves_id);

	$result = db_query($sql, "could not get leaves");

	return db_fetch($result);
}

//--------------------------------------------------------------------------------------------
function db_has_leavess()
{
	return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."leaves");
}




?>