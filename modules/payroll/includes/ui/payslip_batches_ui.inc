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
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/items_cart.inc");

//--------------------------------------------------------------------------------


	
function display_employee_batch_payslip()
{	
	
	
	$sql = get_sql_for_payslip_batch();
	$cols = array(
		_("Employee Name ") , 
		_("Job Position"), 
		_("Debit")=>array('type'=>'amount'), 
		_("Credit")=>array('type'=>'amount'),
		_("Payable")=>array('fun'=>'ac_payable','align'=>'right'), 
		submit('BatchPaySlip',_("Batch"), false, _("Batch Payslip")) 
			=> array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center')
		
		);
	$table =& new_db_pager('payslip_tbl', $sql, $cols);
	display_db_pager($table);
	
}

function display_payslip_header()
{
	start_table(TABLESTYLE2);
		start_row();
		date_cells(_("Date:"),'date_');
		text_cells(_("To the order Of:"),'to_the_order_of');
		text_cells(_("Narration"),'narration');
		end_row();
	end_table();
}




?>
