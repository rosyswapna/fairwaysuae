<?php

global $reports, $dim;

$reports->addReportClass(_('Check'), RC_CHECK);
$reports->addReport(RC_CHECK  	, "_check_print",_('Printable &Check'),
    array(
	_('Transaction') => 'GL_REMITTANCE',
	_("Issue Date") => 'DATE',
	_("Payee Name") => 'TEXT',
	_("Amount") => 'TEXT',
        _('Destination') => 'DESTINATION'));
?>
