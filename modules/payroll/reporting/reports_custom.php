<?php

global $reports, $dim;



$reports->addReportClass(_('Payroll Reports'), RC_HR);
$reports->addReport(RC_HR, 'struct', _('Employee Pay Structure Report'),
	array(	
			_('Employees') => 'EMPLOYEES',
			_('Departments') => 'DEPARTMENTS',
			_('Jobpositions') => 'JOBPOSITIONS',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'slip', _('Employee Pay Slip Report'),
	array(	_('Employees') => 'EMPLOYEES',
			_('Departments') => 'DEPARTMENTS',
			_('Jobpositions') => 'JOBPOSITIONS',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'advice', _('Employee Payment Advice Report'),
	array(	_('Employees') => 'EMPLOYEES',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'release', _('Employee Payment Releases Report'),
	array(	_('Employees') => 'EMPLOYEES',
			_('End Date') => 'DATEENDTAX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_HR, 'list', _('Employee Detail Listing Report'),
	array(	
			_('Employees') => 'EMPLOYEES',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
?>
