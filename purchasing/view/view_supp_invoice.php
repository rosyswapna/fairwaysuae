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
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root = "../..";

include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "View Supplier Invoice"), true, false, "", $js);

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
} 
elseif (isset($_POST["trans_no"]))
{
	$trans_no = $_POST["trans_no"];
}

$supp_trans = new supp_trans(ST_SUPPINVOICE);

read_supp_invoice($trans_no, ST_SUPPINVOICE, $supp_trans);



$supplier_curr_code = get_supplier_currency($supp_trans->supplier_id);

display_heading(_("SUPPLIER INVOICE") . " # " . $trans_no);
echo "<br>";

start_table(TABLESTYLE, "width='95%'");
start_row();
label_cells(_("Supplier"), $supp_trans->supplier_name, "class='tableheader2'");
label_cells(_("Reference"), $supp_trans->reference, "class='tableheader2'");
label_cells(_("Supplier's Reference"), $supp_trans->supp_reference, "class='tableheader2'");
end_row();
start_row();
label_cells(_("Invoice Date"), $supp_trans->tran_date, "class='tableheader2'");
label_cells(_("Due Date"), $supp_trans->due_date, "class='tableheader2'");
if (!is_company_currency($supplier_curr_code))
	label_cells(_("Currency"), $supplier_curr_code, "class='tableheader2'");
end_row();
comments_display_row(ST_SUPPINVOICE, $trans_no);

end_table(1);


$total_gl = display_gl_items($supp_trans, 2);
$total_grn = display_grn_items($supp_trans, 2);


$display_sub_tot = number_format2($total_gl+$total_grn,user_price_dec());
$display_frieght=number_format2($supp_trans->freight_charge,user_price_dec());
$display_packing=number_format2($supp_trans->packing_charge,user_price_dec());
$display_duties=number_format2($supp_trans->duties,user_price_dec());
$display_service=number_format2($supp_trans->service_charge,user_price_dec());
$display_commission=number_format2($supp_trans->commission,user_price_dec());
$display_insurance=number_format2($supp_trans->insurance,user_price_dec());


start_table(TABLESTYLE, "width='95%'");
label_row(_("Sub Total"), $display_sub_tot, "align=right", "nowrap align=right width='15%'");
label_row(_("Freight"), $display_frieght, "align=right", "nowrap align=right width='15%'");
label_row(_("Insurance"), $display_insurance, "align=right", "nowrap align=right width='15%'");
label_row(_("Packing"),$display_packing, "align=right", "nowrap align=right width='15%'");
label_row(_("Duties"), $display_duties, "align=right", "nowrap align=right width='15%'");
label_row(_("Service"), $display_service, "align=right", "nowrap align=right width='15%'");
label_row(_("Commission"), $display_commission, "align=right", "nowrap align=right width='15%'");

$tax_items = get_trans_tax_details(ST_SUPPINVOICE, $trans_no);
display_supp_trans_tax_details($tax_items, 1);

$billing_suppliments_total = $supp_trans->freight_charge + $supp_trans->insurance + $supp_trans->packing_charge + $supp_trans->duties + $supp_trans->service_charge + $supp_trans->commission;

$display_total = number_format2($supp_trans->ov_amount + $supp_trans->ov_gst+$billing_suppliments_total ,user_price_dec());

label_row(_("TOTAL INVOICE"), $display_total, "colspan=1 align=right", "nowrap align=right");

end_table(1);

$voided = is_voided_display(ST_SUPPINVOICE, $trans_no, _("This invoice has been voided."));

if (!$voided) 
{
	display_allocations_to(PT_SUPPLIER, $supp_trans->supplier_id, ST_SUPPINVOICE, $trans_no, 
		($supp_trans->ov_amount + $supp_trans->ov_gst));
}

end_page(true, false, false, ST_SUPPINVOICE, $trans_no);

?>