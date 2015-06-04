
<?php
include_once($path_to_root . "/modules/payroll/includes/payroll_db.inc");

function handle_submit1(&$selected_id)
{
	global $Ajax;

		
	if ($selected_id) 
	{

		update_labour($_POST['Labourcard']);
		

		//update_record_status($_POST['EmpId'], $_POST['EmpInactive'], 'employees', 'emp_id');//from sql_functions.inc
		//TODO update release date for inactive employees

		
	} 
	/*else 
	{ 	//it is a new employee

		begin_transaction();
		add_employee($_POST['EmpFirstName'], $_POST['EmpLastName'], $_POST['EmpAddress'], $_POST['EmpPhone'], $_POST['EmpEmail'], $_POST['EmpBirthDate'],
				$_POST['EmpFrequency'], $_POST['EmpStatus'], $_POST['EmpAllowances'],
				input_num('EmpExtraWH'), $_POST['EmpTaxId'], $_POST['EmpRole'], $_POST['EmpHireDate'], $_POST['EmpNotes']);

		$selected_id = $_POST['EmpId'] = db_insert_id();
         
		commit_transaction();

		display_notification(_("A new employee has been added."));
		
		$Ajax->activate('_page_body');
	}*/
}
?>