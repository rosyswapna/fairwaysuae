
<?php
////////////////////////////////////////////////////
//salary_structure Database Functions
/////////////////////////////////////////////////////

function add_salary_structure($pay_rules=array())
{
	if(empty($pay_rules))
		return false;

	begin_transaction();
		foreach($pay_rules as $rule){
			insert_structure($rule['job_position_id'],
			$rule['pay_rule_id'],$rule['pay_amount'],$rule['type']);
		}

	commit_transaction();
}



function insert_structure($job_position_id,$pay_rule_id,$pay_amount,$type)
{
	$date_ = date2sql(Today());
	$sql = "INSERT INTO ".TB_PREF."salary_structure(date,job_position_id, pay_rule_id,pay_amount,type)VALUES ('"
	.$date_."',".db_escape($job_position_id).",".db_escape($pay_rule_id).","
	.db_escape($pay_amount).",".db_escape($type).")";

	db_query($sql, "could not add salary structure");
}

function exists_salary_structure($job_position_id)
{
	$sql = "SELECT id FROM ".TB_PREF."salary_structure WHERE job_position_id=".db_escape($job_position_id);

	$result = db_query($sql, "Cannot retreive a salary structure");

	return (db_num_rows($result) > 0);
}



function reset_salary_structure($job_position_id)
{

	$sql = "DELETE FROM ".TB_PREF."salary_structure WHERE job_position_id =".db_escape($job_position_id);
    	db_query($sql, "could not reset salary structure role");
}


function get_salary_structure($job_position_id){

	$sql = "SELECT * FROM ".TB_PREF."salary_structure WHERE job_position_id =".db_escape($job_position_id);

  	return db_query($sql, "Could not get salary structure");
}

function get_emp_salary_structure($emp_id)
{
	$sql = "SELECT str.* FROM ".TB_PREF."salary_structure str, ".TB_PREF."employees  emp
		WHERE str.job_position_id = emp.job_position_id AND emp.emp_id =".db_escape($emp_id);

	//echo $sql;exit;
	return db_query($sql, "Could not get employee salary structure");
}




?>

