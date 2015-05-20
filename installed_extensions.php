<?php

/* List of installed additional extensions. If extensions are added to the list manually
	make sure they have unique and so far never used extension_ids as a keys,
	and $next_extension_id is also updated. More about format of this file yo will find in 
	FA extension system documentation.
*/

$next_extension_id = 11; // unique id for next installed extension

$installed_extensions = array (
  1 => 
  array (
    'name' => 'Inventory Items CSV Import',
    'package' => 'import_items',
    'version' => '2.3.0-2',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/import_items',
  ),
  4 => 
  array (
    'name' => 'Check Printing based on Tom Hallman, USA',
    'package' => 'rep_check_print',
    'version' => '2.3.0-1',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/rep_check_print',
  ),
  5 => 
  array (
    'name' => 'Dated Stock Sheet',
    'package' => 'rep_dated_stock',
    'version' => '2.3.3-3',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/rep_dated_stock',
  ),
  6 => 
  array (
    'name' => 'Sales Summary Report',
    'package' => 'rep_sales_summary',
    'version' => '2.3.3-3',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/rep_sales_summary',
  ),
  8 => 
  array (
    'name' => 'Cash Flow Statement Report',
    'package' => 'rep_cash_flow_statement',
    'version' => '2.3.0-1',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/rep_cash_flow_statement',
  ),
  9 => 
  array (
    'package' => 'payroll',
    'name' => 'payroll',
    'version' => '-',
    'available' => '',
    'type' => 'extension',
    'path' => 'modules/payroll',
    'active' => false,
  ),
  10 => 
  array (
    'name' => 'Bank Statement w/ Reconcile',
    'package' => 'rep_statement_reconcile',
    'version' => '2.3.3-3',
    'type' => 'extension',
    'active' => false,
    'path' => 'modules/rep_statement_reconcile',
  ),
);
?>