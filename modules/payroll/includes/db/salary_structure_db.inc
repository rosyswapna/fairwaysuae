////////////////////////////////////////////////////
//salary_structure Database Functions
/////////////////////////////////////////////////////


function add_salary_structure($job_id,$rule)
{
	$sql = "INSERT INTO ".TB_PREF."salary_structure (job_position_id, payroll_rule)VALUES ("
	
	.db_escape(job_id).","
	.db_escape(implode(';', $rule)).")";

	db_query($sql, "could not add new security role");

}



function update_employee($job_id,$rule )
{

	$sql = "UPDATE ".TB_PREF."salary_structure job_position_id =".db_escape($id)
    	.",description=".db_escape($description)
    	.",sections=".db_escape(implode(';', $sections))
    	.",areas=".db_escape(implode(';', $areas))
    	." WHERE id=".(int)$id;
    	db_query($sql, "could not update role");

}


function get_all_structure($active=true){
  //get only active employees by default
  if ($active == true)
    $act_sql = "WHERE inactive = 0";

  $sql = "SELECT job_position_id, payroll_rule FROM ".TB_PREF."salary_structure $act_sql";
  
  return db_query($sql, "The employee table is inaccessible");

}

