<?php


global $reports, $dim;



define('RC_HR', 9);


$reports->addReportClass(_('Payroll Reports'), RC_HR);
$reports->addReport(RC_HR, 'struct', _('Employee Pay Structure Report'),
	array(	
			_('Employees') => 'EMPLOYEES_NO_FILTER',
			_('Departments') => 'DEPARTMENTS_NO_FILTER',
			_('Jobpositions') => 'JOBPOSITIONS_NO_FILTER',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'slip', _('Employee Pay Slip Report'),
	array(	_('Employees') => 'EMPLOYEES_NO_FILTER',
			_('Departments') => 'DEPARTMENTS_NO_FILTER',
			_('Jobpositions') => 'JOBPOSITIONS_NO_FILTER',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'advice', _('Employee Payment Advice Report'),
	array(	_('Employees') => 'EMPLOYEES_NO_FILTER',
			_('Departments') => 'DEPARTMENTS_NO_FILTER',
			_('Jobpositions') => 'JOBPOSITIONS_NO_FILTER',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'release', _('Employee Payment Releases Report'),
	array(	_('Employees') => 'EMPLOYEES_NO_FILTER',
			_('Departments') => 'DEPARTMENTS_NO_FILTER',
			_('Jobpositions') => 'JOBPOSITIONS_NO_FILTER',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'list', _('Employee Detail Listing Report'),
	array(	
			_('Employees') => 'EMPLOYEES_NO_FILTER',
			_('Departments') => 'DEPARTMENTS_NO_FILTER',
			_('Jobpositions') => 'JOBPOSITIONS_NO_FILTER',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));


$reports->register_controls('set_payroll_ctrl');


function set_payroll_ctrl($name,$type){

	global $path_to_root;
	//include_once($path_to_root . "/modules/payroll/includes/payroll_ui.inc");
display_notification($path_to_root);exit;
	switch ($type)
	{
		case 'EMPLOYEES_NO_FILTER':
		case 'EMPLOYEES':
						
			if ($type == 'EMPLOYEES_NO_FILTER')
				return employee_list($name, null, _("No Employee Filter"));
			else
				return employee_list($name);
	
	
		case 'DEPARTMENTS_NO_FILTER':
		case 'DEPARTMENTS':
				if ($type == 'DEPARTMENTS_NO_FILTER')
				return department_list($name, null, _("No Department Filter"));
			else
				return department_list($name);
	
		case 'JOBPOSITIONS_NO_FILTER':
		case 'JOBPOSITIONS':
			if ($type == 'JOBPOSITIONS_NO_FILTER')
				return job_list($name, null, _("No Job position Filter"));
			else
				return job_list($name);
	}
	return '';
}



?>
