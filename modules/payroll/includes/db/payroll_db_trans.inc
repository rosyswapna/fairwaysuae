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
//--------------------------------------------------------------------------------

// Base function for adding a GL transaction
// $date_ is display date (non-sql)
// $amount is in $currency currency
// if $currency is not set, then defaults to no conversion

function add_payroll_trans($type, $trans_id, $date_, $account, $dimension, $dimension2, $memo_,
	$amount, $currency=null, $person_type_id=null, $person_id=null,	$err_msg="", $rate=0,
	$to_the_order_of=null,$payslip_no=0,$from_date,$to_date)
{
	global $use_audit_trail;

	$date = date2sql($date_);
	$from_date=date2sql($from_date);
	$to_date=date2sql($to_date);
	if ($currency != null)
	{
		if ($rate == 0)
			$amount_in_home_currency = to_home_currency($amount, $currency, $date_);
		else
			$amount_in_home_currency = round2($amount * $rate,  user_price_dec());
	}		
	else
		$amount_in_home_currency = round2($amount, user_price_dec());
	if ($dimension == null || $dimension < 0)
		$dimension = 0;
	if ($dimension2 == null || $dimension2 < 0)
		$dimension2 = 0;
	if (isset($use_audit_trail) && $use_audit_trail)
	{
		if ($memo_ == "" || $memo_ == null)
			$memo_ = $_SESSION["wa_current_user"]->username;
		else
			$memo_ = $_SESSION["wa_current_user"]->username . " - " . $memo_;
	}
	$sql = "INSERT INTO ".TB_PREF."gl_trans ( type, type_no, tran_date,
		account, dimension_id, dimension2_id, memo_, amount, to_the_order_of, payslip_no,from_date,to_date";

	if ($person_type_id != null)
		$sql .= ", person_type_id, person_id";

	$sql .= ") ";

	$sql .= "VALUES (".db_escape($type).", ".db_escape($trans_id).", '$date',
		".db_escape($account).", ".db_escape($dimension).", "
		.db_escape($dimension2).", ".db_escape($memo_).", "
		.db_escape($amount_in_home_currency).","
		.db_escape($to_the_order_of).",".db_escape($payslip_no).",'$from_date','$to_date'";

	if ($person_type_id != null)
		$sql .= ", ".db_escape($person_type_id).", ". db_escape($person_id);

	$sql .= ") ";

	if ($err_msg == "")
		$err_msg = "The GL transaction could not be inserted";

	db_query($sql, $err_msg);
	return $amount_in_home_currency;
}
//--------------------------------------------------------------------------------

// GL Trans for standard costing, always home currency regardless of person
// $date_ is display date (non-sql)
// $amount is in HOME currency



function get_expences_n_deposits($emp_id)
{
	$sql= "SELECT * FROM ".TB_PREF."gl_trans WHERE type IN (".ST_BANKPAYMENT.",".ST_BANKDEPOSIT.") AND person_type_id=".PT_EMPLOYEE." AND account NOT IN (SELECT account_code FROM ".TB_PREF."bank_accounts) AND person_id=".db_escape($emp_id);
	
	//display_notification($sql);
	return db_query($sql,"oops");
}

?>
