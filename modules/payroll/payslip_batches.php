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
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

//include_once($path_to_root . "/gl/includes/gl_db.inc");
//include_once($path_to_root . "/gl/includes/gl_ui.inc");

include_once($path_to_root . "/modules/payroll/includes/payroll_ui.inc");
include_once($path_to_root . "/modules/payroll/includes/payroll_db.inc");

$js = '';
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();


$_SESSION['page_title'] = _($help_context = "Manage Employee Payslip Batches");

page($_SESSION['page_title'], false, false,'', $js);
//-----------------------------------------------------------------------------------------------


function batch_checkbox($row)
{
	$name = "Sel_" .$row['job_name_id'];
	return "<input type='checkbox' name='$name' value='1' >"
	 ."<input name='Sel_[".$row['job_name_id']."]' type='hidden' value='"
	 .$row['job_name_id']."'>\n";
}

function ac_payable($row)
{
	$amt_pay = $row['Cr']-$row['Dr'];
	if($amt_pay < 0)
		return price_format(abs($amt_pay))." Cr";
	else if($amt_pay > 0)
		return price_format($amt_pay)." Dr";
	else
		return price_format($amt_pay);
}
//-----------------------------------------------------------------------------------------------

start_form();

	start_table(TABLESTYLE_NOBORDER);
		start_row();
			job_list_cells(_("Job Position"), "employee",null,_("All Jobs"));

			department_list_cells(_("Department"),'department',null,_("All Departments"));

			submit_cells('Search', _("Search"), '', '', 'default');
		end_row();
	end_table();


	start_table(TABLESTYLE, "width='80%'", 10);
		echo "<tr><td>";
		display_payslip_header();
		echo "</td></tr>";
		echo "<tr><td>";
		display_employee_batch_payslip();
		echo "</td></tr>";
	end_table(1);





end_form();
//------------------------------------------------------------------------------------------------

end_page();

?>
