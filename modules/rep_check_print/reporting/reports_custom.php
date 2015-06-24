<?php

global $reports, $dim;
define('RC_CHEQUE', 8);
$reports->addReportClass(_('Check'), RC_CHEQUE);
$reports->addReport(RC_CHEQUE  	, "_check_print",_('Printable &Check'),
    array(
	_('Transaction') => 'GL_REMITTANCE',
	_("Issue Date") => 'DATE',
	_("Payee Name") => 'TEXT',
	_("Amount") => 'TEXT',
        _('Destination') => 'DESTINATION'));
?>
