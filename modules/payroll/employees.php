<?php
/*
  Add, Edit, and List Employees
  Database table is "employees"
  DB fields are in flux

  TODO - employee tax/deduction CRUD  
  
*/

/////////////////////////////////////////////////////
//Includes
/////////////////////////////////////////////////////
$page_security = 'SA_PAYROLL';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");//is this needed?
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();


$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/payroll_db.inc");
include_once($path_to_root . "/modules/payroll/includes/payroll_ui.inc");
include_once($path_to_root . "/modules/payroll/labour.php");


/////////////////////////////////////////////////////
//Data Functions
/////////////////////////////////////////////////////

//--------------------------------------------------------------------------------------------
function can_process()
{
	if (strlen($_POST['EmpFirstName']) == 0){
		display_error(_("The employee first name must be entered."));
		set_focus('EmpFirstName');
		return false;
	} 
	if (strlen($_POST['EmpLastName']) == 0){
		display_error(_("The employee Last name must be entered."));
		set_focus('EmpLastName');
		return false;
	} 
	if (strlen($_POST['EmpAddress']) == 0){
		display_error(_("The employee address must be entered."));
		set_focus('EmpAddress');
		return false;
	} 

	if (!is_date($_POST['EmpHireDate']))
	{
		display_error( _("The date entered is in an invalid format."));
		set_focus('EmpHireDate');
		return false;
	}

	if (get_post('EmpInactive') == 1){
	    if (!is_date($_POST['EmpReleaseDate']))
	    {
		display_error( _("A valid release date must be entered for inactive employees."));
		set_focus('EmpReleaseDate');
		return false;
	    }
	}

	//if (!preg_match("/\d{3}[-\s]?\d{2}[-\s]?\d{4}/", $_POST['EmpTaxId'])){//this checks for Social Security Number (US) formatting. It's certainly too narrow a requirement for use in other countries at this point
	if (strlen($_POST['EmpTaxId']) == 0){//a more universal check - do we have _something_ for the tax id? If this is used, comment out or remove the "else" below and the pregmatch above
		display_error(_("Big Brother needs to see your identification."));
		set_focus('EmpTaxId');
		return false;
	}/* else {
		//let's format these consistantly
		$_POST['EmpTaxId'] = preg_replace("/(\d{3})[-\s]?(\d{2})[-\s]?(\d{4})/", "$1-$2-$3", $_POST['EmpTaxId']);
	}*/

	return true;
}

//--------------------------------------------------------------------------------------------
function can_delete_pay(){
  //TODO the pay rate should not be deleted if...?
  return true;
}

//--------------------------------------------------------------------------------------------
function handle_submit(&$selected_id)
{
	global $Ajax;

	if (!can_process())
		return;
		
	if ($selected_id) 
	{

		update_employee($_POST['EmpFirstName'], $_POST['EmpLastName'], $_POST['EmpAddress'], 
				$_POST['EmpPhone'], $_POST['EmpEmail'], $_POST['EmpBirthDate'],
				$_POST['EmpFrequency'], $_POST['EmpStatus'], $_POST['EmpAllowances'],
				input_num('EmpExtraWH'), $_POST['EmpTaxId'], $_POST['EmpRole'], $_POST['EmpHireDate'], 
				$_POST['EmpNotes'], $selected_id, get_post('EmpInactive'), get_post('EmpInactive') ? $_POST['EmpReleaseDate'] : null, $_POST['Department'],$_POST['JobPosition']);
		

		//update_record_status($_POST['EmpId'], $_POST['EmpInactive'], 'employees', 'emp_id');//from sql_functions.inc
		//TODO update release date for inactive employees

		$Ajax->activate('EmpId'); // in case of status change //html field name
		display_notification(_("Employee details has been updated."));
	} 
	else 
	{ 	//it is a new employee

		begin_transaction();
		add_employee($_POST['EmpFirstName'], $_POST['EmpLastName'], $_POST['EmpAddress'], $_POST['EmpPhone'], $_POST['EmpEmail'], $_POST['EmpBirthDate'],
				$_POST['EmpFrequency'], $_POST['EmpStatus'], $_POST['EmpAllowances'],
				input_num('EmpExtraWH'), $_POST['EmpTaxId'], $_POST['EmpRole'], $_POST['EmpHireDate'], $_POST['EmpNotes'],$_POST['Department'],$_POST['JobPosition']);

		$selected_id = $_POST['EmpId'] = db_insert_id();
         
		commit_transaction();

		display_notification(_("A new employee has been added."));
		
		$Ajax->activate('_page_body');
	}
}
//-------------------------------------------------------------------------------------------submit1
function handle_submit1(&$selected_id)
{
	global $Ajax;
	
if ($_POST['labour_id'] == "")
			$_POST['labour_id'] =0;
	
	if (input_num('labour_id')>0)
		{
			update_labour($_POST['Labourcard'],$_POST['Labourcardissuedate'],$_POST['Labourcardexpirydate'],$_POST['HealthCard'],
			$_POST['Healthcardissuedate'],$_POST['Healthcardexpirydate'],$_POST['HealthCardinformation'],$_POST['Phone'],$_POST['Email'],$_POST['labour_id']);
			display_notification("Labour details Updated");	
		}
	else
		{	
			add_labour($selected_id,$_POST['Labourcard'],$_POST['Labourcardissuedate'],$_POST['Labourcardexpirydate'],
			$_POST['HealthCard'],$_POST['Healthcardissuedate'],$_POST['Healthcardexpirydate'],$_POST['HealthCardinformation'],
			$_POST['Phone'],$_POST['Email']);

			display_notification("Labour details Added");	
		}
}
function handle_submit2(&$selected_id)//SAVE VISA
{
	global $Ajax;
	if ($_POST['visa_id'] == "")
		$_POST['visa_id'] =0;
	
	if (input_num('visa_id') > 0)
	{
		update_visa($_POST['visa'],$_POST['fileno'],$_POST['idcardno'],$_POST['visaIssueDate'],$_POST['visaExpiryDate'], $_POST['visaRenewDate'],$_POST['IdCardIssued'],$_POST['IdCardExpiry'],$_POST['designation'],
		$_POST['designationvisa'],$_POST['profession'],$_POST['attenidno'],$_POST['visa_id']);
		display_notification("visa details Updated");	
	}
	else
	{
		add_visa($selected_id,$_POST['visa'],$_POST['fileno'],$_POST['idcardno'],$_POST['visaIssueDate'],$_POST['visaExpiryDate'],
		$_POST['visaRenewDate'],$_POST['IdCardIssued'],$_POST['IdCardExpiry'],$_POST['designation'],
		$_POST['designationvisa'],$_POST['profession'],$_POST['attenidno']);
		display_notification("visa details Added");	
	}
}

function handle_submit3(&$selected_id)//passport save
{
	global $Ajax;	

	
	if ($_POST['passport_id'] == "")
			$_POST['passport_id'] =0;
	
	if (input_num('passport_id')>0) 
	{
		
		update_passport($_POST['empcode'],$_POST['empname'],$_POST['fathersname'],$_POST['nationality'],$_POST['placeofbirth'],$_POST['dateofbirth'],$_POST['gender'],$_POST['passport'],$_POST['passissuedate'],$_POST['passexpirydate'],$_POST['passplaceissued'],$_POST['addr1'],$_POST['addr2'],$_POST['addr3'],$_POST['remark'],$_POST['alocation'],$_POST['abscondeddate'],$_POST['passport_id']);
		display_notification("Passport details Updated");	
	}
	else
	{
		add_passport($selected_id,$_POST['empcode'],$_POST['empname'],$_POST['fathersname'],$_POST['nationality'],$_POST['placeofbirth'],
		$_POST['dateofbirth'],$_POST['gender'],$_POST['passport'],$_POST['passissuedate'],$_POST['passexpirydate'],
		$_POST['passplaceissued'],$_POST['addr1'],$_POST['addr2'],$_POST['addr3'],$_POST['remark'],$_POST['alocation'],
		$_POST['abscondeddate']);

		display_notification("Passport details Added");
	}
}
//--------------------------------------------------------------------------------------------
function handle_submit_pay($emp_id){

    global $Ajax;

    if (!check_num('rate', 0.01)){
	display_error(_("Oh dear. We really ought to pay this person <em>something</em>"));
	set_focus('rate');
    } else {
	if ($_POST['rate_id'] != -1) {//editing record
	    //display_notification("rate id is ".$_POST['rate_id']);
	    update_employee_compensation($emp_id, $_POST['pay_type'], input_num('rate'), $_POST['rate_id']);
	    display_notification(_("Pay rate has been updated."));
	} else {//new pay rate record
	    //display_notification("rate id is ".$_POST['rate_id']);
	    add_employee_compensation($emp_id, $_POST['pay_type'], input_num('rate'));
	    display_notification(_("Pay rate has been added."));
	}
	$Ajax->activate('_page_body');
    }
}

//--------------------------------------------------------------------------------------------
function handle_delete_pay($rate_id){

  global $Ajax;

  if(can_delete_pay()){//TODO not sure what needs to be checked first
    remove_employee_compensation($rate_id);
    display_notification("Pay rate has been deleted");
    $Ajax->activate('_page_body');
  } else {
    display_error("This compensation type cannot be deleted");
  }
}

/////////////////////////////////////////////////////
//UI Functions
/////////////////////////////////////////////////////
function employee_settings($selected_id){

	if (!$selected_id) 
	{
	 	//if (list_updated('EmpId') || !isset($_POST['EmpId'])) {//what is this?
			$_POST['EmpFirstName'] = $_POST['EmpLastName'] = $_POST['EmpAddress'] = $_POST['EmpPhone'] = $_POST['EmpEmail'] = 
			$_POST['EmpTaxId'] = $_POST['EmpRole'] = $_POST['EmpPayType'] =
			$_POST['EmpPayRate'] = $_POST['EmpFrequency'] = $_POST['EmpStatus'] =
			$_POST['EmpAllowances'] = $_POST['EmpExtraWH'] = $_POST['EmpNotes'] ='';
			$_POST['EmpBirthDate'] = $_POST['EmpHireDate'] = Today();
  
		//}
	}
	else 
	{
	    $myrow = get_employee($selected_id);
	    $_POST['EmpFirstName'] = $myrow["emp_first_name"];
	    $_POST['EmpLastName'] = $myrow["emp_last_name"];
	    $_POST['EmpAddress'] =  $myrow["emp_address"];
	    $_POST['EmpPhone'] =  $myrow["emp_phone"];
	    $_POST['EmpEmail'] = $myrow["emp_email"];
	    $_POST['EmpBirthDate'] = sql2date($myrow["emp_birthdate"]);
	    $_POST['EmpNotes'] =  $myrow["emp_notes"];
	    $_POST['EmpTaxId'] =  $myrow["emp_taxid"];
	    $_POST['EmpRole'] =  $myrow["emp_type"];
	    $_POST['EmpFrequency'] =  $myrow["emp_payfrequency"];
	    $_POST['EmpStatus'] = $myrow["emp_filingstatus"];
	    $_POST['EmpAllowances'] =  $myrow["emp_allowances"];
	    $_POST['EmpExtraWH'] = $myrow["emp_extrawitholding"];
	    $_POST['EmpHireDate'] = sql2date($myrow["emp_hiredate"]);
	    $_POST['EmpInactive'] = $myrow["inactive"];
	    $_POST['EmpReleaseDate'] = sql2date($myrow["emp_releasedate"]); 
	    $_POST['Department'] = $myrow["department_id"];
	    $_POST['JobPosition'] = $myrow["job_position_id"];
	}

	div_start('emp_info');
	start_outer_table(TABLESTYLE2);
	table_section(1);
	table_section_title(_("Name and Address"));

	text_row(_("Employee First Name:"), 'EmpFirstName', $_POST['EmpFirstName'], 40, 80);
	text_row(_("Employee Last Name:"), 'EmpLastName', $_POST['EmpLastName'], 40, 80);
	textarea_row(_("Address:"), 'EmpAddress', $_POST['EmpAddress'], 35, 5);

	text_row(_("Phone:"), 'EmpPhone', $_POST['EmpPhone'], 40, 80);
	email_row(_("e-Mail:"), 'EmpEmail', $_POST['EmpEmail'], 40, 80);

	date_row(_("Birth Date:"), 'EmpBirthDate');
	textarea_row(_("General Notes:"), 'EmpNotes', null, 35, 5);

	
	table_section(2);

	table_section_title(_("Employment Information"));

	text_row(_("Enrollment Number:"), 'EmpTaxId', $_POST['EmpTaxId'], 40, 80);
	payroll_emp_role_list_row(_("Designated Role:"), 'EmpRole', $_POST['EmpRole']); //Officer/Employee...
	payroll_payfreq_list_row(_("Payroll Frequency:"), 'EmpFrequency', $_POST['EmpFrequency']);//weekly/biweekly/semi-monthly...
	payroll_filing_status_list_row(_("Current Status:"), 'EmpStatus', $_POST['EmpStatus']);//Single, Married...
	text_row(_("Allowances:"), 'EmpAllowances', $_POST['EmpAllowances'], 40, 80);
	amount_row(_("Extra Witholding:"), 'EmpExtraWH', $_POST['EmpExtraWH']);
	
	table_section_title(_(" "));//just a spacer
	date_row(_("Hire Date:"), 'EmpHireDate');

	department_list_row(_("Department"),'Department',$_POST['Department'],false);
	job_list_row(_("Job Position"),'JobPosition',$_POST['JobPosition'],false);

	if ($selected_id) {
	    check_row('Inactive:', 'EmpInactive');
	    date_row(_("Release Date:"), 'EmpReleaseDate');
	}


	end_outer_table(1);
	div_end();

	div_start('controls');
	if (!$selected_id)
		submit_center('submit', _("Add New Employee"), true, '', 'default');
	else 
		submit_center('submit', _("Update Employee"), _('Update employee data'), @$_REQUEST['popup'] ? true : 'default');
	
	div_end();
}

//Entry form for employee compensation
//--------------------------------------------------------------------------------------------
function employee_comp($emp_id){

    global $Ajax;

    if(!db_has_pay_types()){
	display_error("No pay types have been defined");
	return;
    }

    br();
    //display the existing compensation rates for this employee
    start_table(TABLESTYLE);
    $th = array(_("Description"), _("Rate"), "", "");
    table_header($th);

    $result = get_employee_compensation($emp_id);
    $k = 0;
    while($myrow = db_fetch($result)){
      alt_table_row_color($k);
      label_cell($myrow["name"]);
      label_cell($myrow["pay_rate"]);
    
      edit_button_cell("EditPay".$myrow["id"], _("Edit Pay"));
      delete_button_cell("DeletePay".$myrow["id"], _("Delete Pay"));

      end_row();
    }
    end_table(1);

    if(!isset($_POST['rate_id'])){
	$_POST['pay_type'] = key(get_pay_type_array());
	$_POST['rate'] = get_default_pay_rate($_POST['pay_type']);
	$_POST['rate_id'] = -1;
    }

    //add or edit compensation
    div_start('comp_table');
    start_table(TABLESTYLE2);
    payroll_pay_type_list_row(_("Type:"), 'pay_type', $_POST['pay_type'], true);
    amount_row(_("Rate:"), 'rate', $_POST['rate']);
    hidden('rate_id', $_POST['rate_id']);
    end_table(1);  

    if (find_submit('EditPay') == -1)
	submit_center('SubmitPay', _("Add Compensation"), true, '', 'default');
    else
	submit_center('SubmitPay', _("Update Compensation"), true, '', 'default');

    div_end();
}


//--------------------------------------------------------------------------------------------
function passport($selected_id)
{
	$myrow = get_passport($selected_id);
	$new_passport=true;
	if (!$myrow) 
	{
	 	
		$_POST['empcode'] = $_POST['empname'] = $_POST['fathersname'] = $_POST['nationality'] = $_POST['placeofbirth'] 
		= $_POST['dateofbirth'] = $_POST['gender'] = $_POST['passport'] =$_POST['passissuedate'] = $_POST['passexpirydate'] 
		=$_POST['passplaceissued'] =$_POST['addr1'] =$_POST['addr2'] =$_POST['addr3'] =$_POST['remark'] =$_POST['alocation']
		=$_POST['abscondeddate'] = $_POST['passport_id']='';
  
	}
	else 
	{
		$new_passport 			= false;
		$_POST['passport_id'] 		= $myrow["id"];
		$_POST['empcode'] 		= $myrow["emp_code"];
		$_POST['empname'] 		= $myrow["emp_name"];
		$_POST['fathersname'] 		= $myrow["fathers_name"];
		$_POST['nationality'] 		= $myrow["nationality"];
		$_POST['placeofbirth'] 		= $myrow["birth_place"];
		$_POST['dateofbirth'] 		= sql2date($myrow["birth_date"]);
		$_POST['gender'] 		= $myrow["gender"];
		$_POST['passport'] 		= $myrow["passport_no"];
		$_POST['passissuedate'] 	= sql2date($myrow["pspt_issue_date"]);
		$_POST['passexpirydate'] 	= sql2date($myrow["pspt_expiry_date"]);
		$_POST['passplaceissued'] 	= $myrow["pspt_issue_place"];
		$_POST['addr1'] 		= $myrow["addr1"];
		$_POST['addr2'] 		= $myrow["addr2"];
		$_POST['addr3'] 		= $myrow["addr3"];
		$_POST['remark'] 		= $myrow["remark"];
		$_POST['alocation'] 		= $myrow["site_allocation"];
		$_POST['abscondeddate'] 	= sql2date($myrow["absconded_date"]);
	}

	start_outer_table(TABLESTYLE2);
		table_section(1);
		table_section_title(_("Employee Details"));
			hidden("passport_id");
			text_row(_("Emp Code:"), 'empcode', $_POST['empcode'], 40, 80);
			text_row(_("Emp Name:"), 'empname',$_POST['empname'], 40, 80);
			text_row(_("Fathers Name:"), 'fathersname', $_POST['fathersname'], 40, 80);
			text_row(_("Nationality:"), 'nationality',$_POST['nationality'], 40, 80);
			text_row(_("Place Of Birth:"), 'placeofbirth', $_POST['placeofbirth'], 40, 80);
			date_row(_("Date Of Birth :"), 'dateofbirth',$_POST['dateofbirth']);
			start_row();
			label_cell('Gender');
			echo "<td>";
			echo radio(_("Male:"), 'gender',MALE,NULL ,40, 80);
			echo radio(_("Female:"), 'gender',FEMALE,NULL, 40, 80);
			echo "</td>";
			end_row();

		table_section(2);
		table_section_title(_("Passport Details"));
			text_row(_("Passport:"), 'passport',$_POST['passport'], 40, 80);
			date_row(_("Pasport Issue Date :"), 'passissuedate',$_POST['passissuedate']);
			date_row(_("Pasport Expiry Date :"), 'passexpirydate',$_POST['passexpirydate']);
			text_row(_("Passport place of issued:"), 'passplaceissued',$_POST['passplaceissued'], 40, 80);
			text_row(_("address1:"), 'addr1',$_POST['addr1'], 40, 80);
			text_row(_("address2:"), 'addr2',$_POST['addr2'], 40, 80);
			text_row(_("address3:"), 'addr3',$_POST['addr3'], 40, 80);
			text_row(_("Remark:"), 'remark',$_POST['remark'], 40, 80);
			text_row(_("Site Alocation:"), 'alocation',$_POST['alocation'], 40, 80);
			date_row(_("Absconded Date :"), 'abscondeddate',$_POST['abscondeddate']);
	end_outer_table(TABLESTYLE2);

	div_start('controls');
		if ($new_passport)
			submit_center('submit3', _("Add New passport"), true, '', 'default');
		else 
			submit_center('submit3', _("Update Passport Details"), _('Update Passport Details'), @$_REQUEST['popup'] ? true : 'passportupdate');
	div_end();
}
//-------------------------------------------------------------------------------------


function visa($selected_id){
	$myrow = get_visa($selected_id);
	$new_visa=true;
	if (!$myrow) 
	{	
		
	 	//if (list_updated('EmpId') || !isset($_POST['EmpId'])) {//what is this?
			$_POST['visa'] = $_POST['fileno'] = $_POST['visaIssueDate'] = $_POST['visaExpiryDate'] = $_POST['visaRenewDate'] 
			= $_POST['idcardno'] = $_POST['IdCardIssued'] = $_POST['IdCardExpiry'] =$_POST['designation'] = $_POST['designationvisa']
			=$_POST['profession']=$_POST['attenidno']=$_POST['visa_id']='';
  
		//}
	}
	else 
	{
		$new_visa		= false;
		$_POST['visa_id']	= $myrow["id"];
		$_POST['visa'] 		= $myrow["visa_no"];
		$_POST['fileno'] 	= $myrow["file_no"];
		$_POST['visaIssueDate'] = sql2date($myrow["visa_date_issued"]);
		$_POST['visaExpiryDate']= sql2date($myrow["visa_date_expiry"]);
		$_POST['visaRenewDate'] = sql2date($myrow["visa_date_renew"]);
		$_POST['idcardno'] 	= $myrow["id_card"];
		$_POST['IdCardIssued'] 	= sql2date($myrow["id_card_issued"]);
		$_POST['IdCardExpiry'] 	= sql2date($myrow["id_card_expiry"]);
		$_POST['designation'] 	= $myrow["designation"];
		$_POST['designationvisa'] = $myrow["designation_visa"];
		$_POST['profession'] 	=  $myrow["profession_id"];
		$_POST['attenidno'] 	=  $myrow["atten_id"];
	}
	
	start_outer_table(TABLESTYLE2);
		table_section(1);
		table_section_title(_("Visa"));
			hidden("visa_id");
			text_row(_("Visa:"), 'visa', $_POST['visa'], 40, 80);
			text_row(_("File no:"), 'fileno',$_POST['fileno'], 40, 80);
			date_row(_("visa  Date Issued:"), 'visaIssueDate',$_POST['visaIssueDate']);
			date_row(_("visa  Date Expiry:"), 'visaExpiryDate',$_POST['visaExpiryDate']);
			date_row(_("visa  Date Renew:"), 'visaRenewDate',$_POST['visaRenewDate']);
			text_row(_("Id Card no:"), 'idcardno', $_POST['idcardno'], 40, 80);
			date_row(_("Id Card Issued:"), 'IdCardIssued',$_POST['IdCardIssued']);
			date_row(_("Id Card Expiry:"), 'IdCardExpiry',$_POST['IdCardExpiry']);
			text_row(_("Designation in co:"), 'designation',$_POST['designation'], 40, 80);
			text_row(_("Designation(visa):"), 'designationvisa', $_POST['designationvisa'], 40, 80);
			text_row(_("Profession Id:"), 'profession',$_POST['profession'], 40, 80);
			text_row(_("Atten Id no:"), 'attenidno', $_POST['attenidno'], 40, 80);
	end_outer_table(TABLESTYLE2);

	div_start('controls');
		if ($new_visa)
			submit_center('submit2', _("Add visa"), true, '', 'default');
		else 
			submit_center('submit2', _("Update visa"), _('Update employee data'), @$_REQUEST['popup'] ? true : 'labourupdate');
	
	div_end();
}
//----------------------------------------------------------------------------------

function labour($selected_id){
	$myrow = get_labour($selected_id);
	$new_labour=true;
	if (!$myrow) 
	{
		
	 	//if (list_updated('EmpId') || !isset($_POST['EmpId'])) {//what is this?
			$_POST['Labourcard'] = $_POST['Labourcardissuedate'] = $_POST['Labourcardexpirydate'] = $_POST['HealthCard'] = $_POST['Healthcardissuedate'] = $_POST['Healthcardexpirydate'] = $_POST['HealthCardinformation'] = $_POST['Phone'] =
			$_POST['Email'] =$_POST['labour_id']= '';
  
		//}
	}
	else 
	{
		$new_labour		= false;
		$_POST['labour_id']	= $myrow["id"];
		$_POST['Labourcard'] 	= $myrow["labour_card"];
		$_POST['Labourcardissuedate'] 	= sql2date($myrow["lbr_card_isuue_date"]);
		$_POST['Labourcardexpirydate'] 	=  sql2date($myrow["lbr_card_exp_date"]);
		$_POST['HealthCard'] 	=  $myrow["health_card_no"];
		$_POST['Healthcardissuedate'] 	= sql2date($myrow["hc_issue_date"]);
		$_POST['Healthcardexpirydate'] 	= sql2date($myrow["hc_exp_date"]);
		$_POST['HealthCardinformation'] = $myrow["hc_information"];
		$_POST['Phone'] 	=  $myrow["phone"];
		$_POST['Email'] 	=  $myrow["email"];
	}
	
	start_outer_table(TABLESTYLE2);
		table_section(1);
		table_section_title(_("Labour"));
		hidden("labour_id");
		text_row(_("Labour card:"), 'Labourcard',$_POST['labour_card'], 40, 80);
		date_row(_("Labour Card Issue Date:"), 'Labourcardissuedate', $_POST['Labourcardissuedate']);
		date_row(_("Labour Card expiry Date:"), 'Labourcardexpirydate',$_POST['Labourcardexpirydate']);
		text_row(_("Health card no:"), 'HealthCard',$_POST['HealthCard'], 40, 80);
		date_row(_("Health Card Issue Date:"), 'Healthcardissuedate', $_POST['Healthcardissuedate']);
		date_row(_("Health Card Exp Date:"), 'Healthcardexpirydate', $_POST['Healthcardexpirydate']);
		text_row(_("Health Information:"), 'HealthCardinformation',$_POST['HealthCardinformation'], 40, 80);
		text_row(_("Phone:"), 'Phone',$_POST['Phone'], 40, 80);
		text_row(_("Email:"), 'Email',$_POST['Email'], 40, 80);
	end_outer_table(TABLESTYLE2);

	div_start('controls');
		if ($new_labour)
			submit_center('submit1', _("Add Labour"), true, '', 'default');
		else 
			submit_center('submit1', _("Update Labour"), _('Update employee data'), @$_REQUEST['popup'] ? true : 'labourupdate');
	
	div_end();
	
}
//-------------------------------------------------------------------------



/////////////////////////////////////////////////////
//Page Director
/////////////////////////////////////////////////////

page(_($help_context = "Manage Employees"), @$_REQUEST['popup'], false, "", $js); //TODO look into help_context and 'popup'

start_form();

$selected_id = get_post('EmpId','');

//top selector box for adding/editing employees
//--------------------------------------------------------------------------------------------
if (db_has_employees()){
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	employee_list_cells(_("Select an employee: "), 'EmpId', null,
		_('New employee'), true, check_value('show_inactive'));
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('EmpId');
		set_focus('EmpId');
	}
} else {
    display_notification("Add and Manage Employees");
    hidden('EmpId');
}

//--------------------------------------------------------------------------------------------

if (isset($_POST['submit'])) 
  handle_submit($selected_id);

if (isset($_POST['submit1'])) 
  handle_submit1($selected_id); //labour

if (isset($_POST['submit2'])) 
  handle_submit2($selected_id);//visa

if (isset($_POST['submit3'])) 
  handle_submit3($selected_id);//passport


//--------------------------------------------------------------------------------------------
$edit_id = find_submit("EditPay");
if ($edit_id != -1){
	$myrow = db_fetch(get_employee_compensation($selected_id, $edit_id));
	$_POST['pay_type'] = $myrow['pay_type_id'];
	$_POST['rate'] = $myrow['pay_rate'];
	$_POST['rate_id'] = $edit_id;
	$Ajax->activate('comp_table');
}

//--------------------------------------------------------------------------------------------
$delete_id = find_submit("DeletePay");
if ($delete_id != -1)
  handle_delete_pay($delete_id);

//--------------------------------------------------------------------------------------------
if (isset($_POST['SubmitPay'])){
  handle_submit_pay($selected_id);
}

//--------------------------------------------------------------------------------------------
if (get_post('_pay_type_update')) {
    $_POST['rate'] = get_default_pay_rate($_POST['pay_type']);
    $Ajax->activate('rate');
    set_focus('rate');
}

//--------------------------------------------------------------------------------------------
if (get_post('_EmpInactive_update')) {
    $Ajax->activate('EmpReleaseDate');
    set_focus('EmpReleaseDate');
}

//--------------------------------------------------------------------------------------------

if (!$selected_id)
  unset($_POST['_tabs_sel']); // force settings tab for new employee

tabbed_content_start('tabs', array(
			 'employee' => array(_('&Employee'), $selected_id),
		     'passport' => array(_('&Passport'), $selected_id),
		     'visa' => array(_('&Visa'), $selected_id),
		     'labour' => array(_('&Labour'), $selected_id) ));

  switch (get_post('_tabs_sel')) {
    default:
	case 'employee':
      employee_settings($selected_id);
      break;
    case 'passport':
      passport($selected_id);
      break;
    case 'visa':
      visa($selected_id);
      break;
    case 'labour':
      labour($selected_id);
      break;
  }

br();
tabbed_content_end();

//--------------------------------------------------------------------------------------------
hidden('popup', @$_REQUEST['popup']);//What is this?
end_form();
end_page(@$_REQUEST['popup']);

?>
