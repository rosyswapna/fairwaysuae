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

/**
	Document blueprint use following parameters set in doctext.inc:

	$Addr1,	$Addr2 - address info
	$Payment_Terms - payment terms line
	$Footer - footer texts
	$this->company - company info
	$this->title - report title
	$this->formData - some other info
***/

		$this->row = $this->pageHeight - $this->topMargin;

		$upper = $this->row - 2 * $this->lineHeight;
		$lower = $this->bottomMargin + 8 * $this->lineHeight;
		$iline1 = $upper;
		$iline2 = $iline1 - 4 * $this->lineHeight;
		$iline3 = $iline2 - 1 * $this->lineHeight;
		$iline4 = $iline3 - 1.8 * $this->lineHeight;
		$iline5 = $iline4 - 20 * $this->lineHeight;
		$iline6 = $iline5 - 1 * $this->lineHeight;
		$iline7 = $iline6 - 1.5 * $this->lineHeight;
		$iline8 = $iline7 - 1.5 * $this->lineHeight;
		$iline9 = $iline8 - 1.5 * $this->lineHeight;
		$iline10 = $lower;
		$right = $this->pageWidth - $this->rightMargin;
		$width = ($right - $this->leftMargin) / 5;
		$icol = $this->pageWidth / 2;
		$ccol = $this->cols[0] + 4;
		$c2col = $ccol + 60;
		$ccol2 = $icol / 2;
		$mcol = $icol + 8;
		$mcol2 = $this->pageWidth - $ccol2;
		$cols = count($this->cols);

		$hcol1 = $this->leftMargin;
		$hcol2 = $this->cols[4]-60;
		$hcol = $hcol2+5;
		$this->Line1($iline1, 0, $hcol1, $hcol2);
		$this->Line1($iline1, 0, $hcol, $right);
		$this->Line1($iline2, 0, $hcol1, $hcol2);
		$this->Line1($iline2, 0, $hcol, $right);

		$this->LineTo($hcol1, $iline1 ,$hcol1, $iline2);
		$this->LineTo($hcol2, $iline1 ,$hcol2, $iline2);
		$this->LineTo($hcol, $iline1 ,$hcol, $iline2);
		$this->LineTo($right, $iline1 ,$right, $iline2);
	
		
		$this->Line($iline3);
		$this->Line($iline4);
		$this->Line($iline5);
		//$this->Line1($iline6, 0, $hcol1, $hcol2);
		//$this->Line1($iline7, 0, $hcol1, $hcol2);
		//$this->Line1($iline8, 0, $hcol1, $hcol2);
		//$this->Line1($iline9, 0, $hcol1, $hcol2);
		$this->LineTo($this->leftMargin, $iline3 ,$this->leftMargin, $iline5);
		$col = $this->leftMargin;
		for ($i = 1; $i < 6; $i++)
		{
			$this->LineTo($this->cols[$i], $iline3,$this->cols[$i], $iline5);
		}
		$this->LineTo($right, $iline3 ,$right, $iline5);
		

		// Document title
		$this->fontSize += 8;
		$this->Font('bold');
		$this->TextWrap($left, $this->row, $right, $this->title, 'center');
		$this->Font();
		$this->fontSize -= 8;
		//if ($this->pageNumber > 1)
			$this->Text($mcol + 180, _("Page No : ") . ' ' . $this->pageNumber);
		$this->NewLine();
		
		$adrline = $this->row;

		
		$this->Font();
		$this->row = $iline1 - $this->lineHeight;
		$this->Text($mcol + 100, _("Date"));
		$this->Text($mcol + 135, ": ".sql2date($this->formData['document_date']));
		$this->NewLine();
		$this->Text($mcol + 100, _("Branch"));
		$this->Text($mcol + 135, ": ".$this->formData['br_name']);
		$this->NewLine();
		$this->Text($mcol + 100, _("Sl.No"));
		$this->Text($mcol + 135, ": ".$this->formData['document_number']);

		$this->row = $iline1 - $this->lineHeight;
		$this->Text($ccol, _("Customer Name:"));
		$this->NewLine();
		$this->Font('bold');
		$this->Text($ccol, $Addr2['name']);
		$this->Font();
		

		// Line headers
		$this->row = $iline3 - $this->lineHeight - 1;
		$this->Font('bold');
		$count = count($this->headers);
		$this->cols[$count] = $right - 3;
		for ($i = 0; $i < $count; $i++)
			$this->TextCol($i, $i + 1, $this->headers[$i], -2);
		$this->Font();
		
		$this->row = $iline10 - $this->lineHeight - 1;
		$this->TextCol(0, 3,  _("Receiver's Signature"), -2);
		$this->TextCol(3, 6, $this->company['coy_name'], -2);

		

		$this->Font();
		$temp = $iline4 - $this->lineHeight - 2;
?>
