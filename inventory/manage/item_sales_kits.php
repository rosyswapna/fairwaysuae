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
$page_security = 'SA_ITEMSALESKIT';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

if (!@$_GET['popup'])
page(_($help_context = "Sales Kits & Alias Codes"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/includes/manufacturing.inc");

simple_page_mode(true);
//--------------------------------------------------------------------------------------------------


function display_item_kits($stock_id)
{
	$kits = get_item_kits($stock_id);
	if(db_num_rows($kits) > 0){
	
		table_section(2);
		table_section_title(_("Manage Item Packing"));
		echo "<td>";
		div_start('bom');
			start_table(TABLESTYLE, "width=100%");
			//$th = array(_("KIT"), _("Quantity"), _("Units"),'','');
			$th = array(_("KIT"), _("Quantity"), _("Units"));
			table_header($th);
			$k = 0;
			while($kit = db_fetch($kits)){
				alt_table_row_color($k);
					label_cell($kit["item_code"]);
					qty_cell($kit["quantity"], false, 
						$kit["units"] == '' ? 0 : get_qty_dec($kit["comp_name"]));
					label_cell($kit["units"] == '' ? _('kit') : $kit["units"]);
			 		//edit_button_cell("Edit".$kit['id'], _("Edit"));
			 		//delete_button_cell("Delete".$kit['id'], _("Delete"));
				end_row();
			}

			end_table();
		div_end();
		echo "</td>";
	}
}

//--------------------------------------------------------------------------------------------------

function add_sales_kit($kit_code)
{

	global $Mode,$Ajax;

	if ($_POST['description'] == ''){
      		display_error( _("Item code description cannot be empty."));
		set_focus('description');
		return;
   	}

	if($kit_code == -1){//new

		if (!check_num('quantity', 0))
		{
			display_error(_("The quantity entered must be numeric and greater than zero."));
			set_focus('quantity');
			return;
		}

		$new_kit_code = $_POST['kit_code'].$_POST['kit_type'];
		$kit = get_item_kit($new_kit_code);
    		if (db_num_rows($kit)) {
			  	$input_error = 1;
    	  		display_error( _("This item code is already assigned to stock item or sale kit."));
				set_focus('kit_code');
				return;
		}
		if ($new_kit_code == '') {
	    	  	display_error( _("Kit/alias code cannot be empty."));
				set_focus('kit_code');
				return;
		}


		add_item_code( $new_kit_code, get_post('component'), get_post('description'),
			 get_post('category'), input_num('quantity'), 0);
		display_notification("New alias code has been created.");
		
		
	}else{//edit
		$props = get_kit_props($_POST['item_code']);
		update_item_code($selected_item, $kit_code, get_post('component'),
			$props['description'], $props['category_id'], input_num('quantity'), 0);
		display_notification(_("Component of selected kit has been updated."));
	}
	$_POST['item_code'] = $kit_code;
	$Mode = 'RESET';
	$Ajax->activate('_page_body');
	
	
}
//--------------------------------------------------------------------------------------------------

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST['quantity']);
	unset($_POST['component']);
	//$_POST['description'] = '';
}
if (isset($_GET['stock_id'])){
	$_POST['component'] = $_GET['stock_id'];
	$_POST['kit_code'] = $_GET['stock_id'];

}
$selected_item = $_POST['component'];
//--------------------------------------------------------------------------------------------------

if (isset($_POST['addupdatekit'])){
	add_sales_kit($_POST['item_code']);
}

if ($Mode=='ADD_ITEM' || 'UPDATE_ITEM'){
	add_sales_kit($_POST['item_code']);
}elseif ($Mode == 'Delete'){
	// Before removing last component from selected kit check 
	// if selected kit is not included in any other kit. 
	// 
	$other_kits = get_where_used($_POST['item_code']);
	$num_kits = db_num_rows($other_kits);

	$kit = get_item_kit($_POST['item_code']);
	if ((db_num_rows($kit) == 1) && $num_kits) {

		$msg = _("This item cannot be deleted because it is the last item in the kit used by following kits")
			.':<br>';

		while($num_kits--) {
			$kit = db_fetch($other_kits);
			$msg .= "'".$kit[0]."'";
			if ($num_kits) $msg .= ',';
		}
		display_error($msg);
	} else {
		delete_item_code($selected_id);
		display_notification(_("The component item has been deleted from this bom"));
		$Mode = 'RESET';
	}
}

//--------------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
start_form();

$props = get_kit_props($_POST['item_code']);

$selected_kit = $_POST['item_code'];
//----------------------------------------------------------------------------------


//------modified view start--------------------
hidden('item_code', $selected_id);

if ($Mode == 'Edit') {

	$myrow = get_item_code($selected_id);
	$_POST['component'] = $myrow["stock_id"];
	$_POST['quantity'] = number_format2($myrow["quantity"], get_qty_dec($myrow["stock_id"]));
}
hidden("selected_id", $selected_id);
start_outer_table(TABLESTYLE2);

	table_section(1);
	table_section_title(_("Sales Kit/Item Packing"));
		if ($Mode == 'Edit') {
			label_row(_("Kit Code:"), $myrow["item_code"]);
			text_row(_("Packing Name:"), 'description', null, 33, 33);
			stock_categories_list_row(_("Category:"), 'category', null);
		}else{
			/*start_row();
				text_cells(_("Alias/kit code:"), 'kit_code', null, 15, 21);
				sales_kit_list_cells(null,'kit_type', null);
			end_row();*/
			text_row(_("Alias/kit code:"), 'kit_code', null, 15, 21);
			sales_kit_list_row("Kit Type:",'kit_type', null);

			text_row(_("Packing Name:"), 'description', null, 33, 33);
			stock_categories_list_row(_("Category:"), 'category', null);
			sales_local_items_list_row(_("Component:"),'component', null, false, true);
			qty_row(_("Quantity:"), 'quantity', number_format2(1, $dec), '', $units, $dec);
		}

		display_item_kits($selected_item);
	

end_outer_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

br();

//------modified view end--------------------
	

if (!@$_GET['popup'])
end_form();
//----------------------------------------------------------------------------------


if (!@$_GET['popup'])
end_page();

?>
