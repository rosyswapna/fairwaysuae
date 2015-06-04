<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_PAYSLIP_BATCHES';
$path_to_root = "../..";
include_once($path_to_root . "/includes/ui/items_cart.inc");

include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/modules/payroll/includes/ui/payslip_batches_ui.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/employee_ui.inc");
include_once($path_to_root . "/modules/payroll/includes/db/salary_structure_db.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/gl/includes/gl_ui.inc");

$js = '';
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

if (isset($_GET['ModifyPaySlip'])) {
	$_SESSION['page_title'] = sprintf(_("Modifying Employee Payslip # %d."), 
		$_GET['trans_no']);
	$help_context = "Modifying Employee Payslip";
} else
	$_SESSION['page_title'] = _($help_context = "Manage Employee Payslip Batches");

page($_SESSION['page_title'], false, false,'', $js);
//--------------------------------------------------------------------------------------------------

function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  set_focus('_code_id_edit');
}
//-----------------------------------------------------------------------------------------------


//-----------------------------------------------------------------------------------------------
$batch=display_employee_batch_payslip();
start_form();
start_table(TABLESTYLE2, "width='70%'", 10);

	$th=array(_("Employee Name"),_("Debit"),_("Credit"),_("Accounts payable"),_("Particulars"),_(""));
table_header($th);
$result_rules=get_payroll_rules();
$rule=db_fetch($result_rules);
	while($res=db_fetch($batch))
	{
	label_cell($res["emp_first_name"]." ".$res["emp_last_name"]);
	amount_cells(null,$rule['debit_input']);
	amount_cells(null,$rule['credit_input']);
	amount_cells(null);
	amount_cells(null);
	check_cells(null);
	end_row();
	}


end_table(1);
echo '<br>';



end_form();
//------------------------------------------------------------------------------------------------

end_page();

?>
