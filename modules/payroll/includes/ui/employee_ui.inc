<?php


include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");

$payroll_payfreq = array(
    '260'=>"Daily",
    '52'=>"Weekly",
    '26'=>"Bi-weekly",
    '24'=>"Semi-monthly",
    '12'=>"Monthly",
    '4'=>"Quarterly",
    '1'=>"Annually"
);

$payroll_filing_status= array(
    '1' => "Single", 
    '2' => "Married", 
    '0' => "Do Not Withold"
);

$payroll_emp_role = array(
    '1'=>"Regular",
    '2'=>"Officer",
    '3'=>"Statutory",
    '4'=>"Owner"
);


//--------------------------------------------------------------------------------------------
function payroll_payfreq_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $payroll_payfreq;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $payroll_payfreq, $options );
	echo "</td></tr>\n";
}

//--------------------------------------------------------------------------------------------
function payroll_filing_status_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $payroll_filing_status;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $payroll_filing_status, $options );
	echo "</td></tr>\n";
}

//--------------------------------------------------------------------------------------------
function payroll_emp_role_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
	global $payroll_emp_role;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $payroll_emp_role, $options );
	echo "</td></tr>\n";
}

//--------------------------------------------------------------------------------------------
function payroll_pay_type_list_row($label, $name, $selected_id=null, $submit_on_change=false) {
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, get_pay_type_array(), $options );
	echo "</td></tr>\n";
}

//based on ui_lists.inc
//customer_list function
//--------------------------------------------------------------------------------------------

function employee_name($first_name,$last_name)
{
$sql = "SELECT emp_first_name,emp_last_name FROM ".TB_PREF."employees ";

$ret =combo_input($sql,'emp_first_name','emp_last_name');
return $ret;
}

?> 
