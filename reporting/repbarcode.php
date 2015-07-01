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
$page_security = 'SA_PAYCHECK_REPORT';
// ----------------------------------------------------------------
// $ Revision:	2.2 $
// Creator:	Joe Hunt - Based on the new Report Engine by Tom Hallman
// Creator:	Based on Tom Hallman's Report.
// Date:	2010-03-03
// Title:	Printable Check
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
add_access_extensions();

//----------------------------------------------------------------------------------------------------

print_code();


//----------------------------------------------------------------------------------------------------

function print_code()
{
    global $path_to_root;
	
	
	$item_code = $_POST['PARAM_0'];
	$code=$_POST['PARAM_1'];
	$desc=$_POST['PARAM_2'];
    	
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    	// Begin the report
		
    	$rep = new FrontReport(_('Printable Check'), "PrintableCheck", user_pagesize());
		
	$rep->SetHeaderType(null);    
    	$rep->NewPage();
    	// Set the font
   	 $rep->Font('','courier');
    	$rep->fontSize = 12;
   
    	$rep->NewLine(1,0,76);
    	$rep->cols = array(63,300, 340);
    	$rep->aligns = array('left', 'left', 'left','right');
    
    	// Company Name
	$rep->NewLine(1,0,23);  
    	$rep->TextCol(0, 1, 'Fairwell');
		
    
    	// Barcode
		$rep->write1DBarcode($item_code,'UPCA',65,150);
		
		//Item_Code
		$rep->NewLine(2,0,23);
    	$rep->TextCol(0, 2,  $code);
		
   	$rep->End();
}

?>
