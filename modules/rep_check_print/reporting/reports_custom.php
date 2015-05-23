<?php

global $reports, $dim;

$reports->addReportClass(_('Check'), RC_CHECK);
$reports->addReport(RC_CHECK  	, "_check_print",_('Printable &Check'),
    array(  _('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Payment') => 'REMITTANCE',
            _('Destination') => 'DESTINATION'));
?>
