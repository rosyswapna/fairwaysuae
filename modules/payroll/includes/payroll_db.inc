<?php
/*
  Master include for payroll db functions
  Includes all other payroll db helper functions
*/
// SC: 18.04.2012: include various helpers


include_once($path_to_root."/modules/payroll/includes/db/department_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/employee_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/jobpositions_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/leavetypes_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/payroll_structure_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/paytype_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/salary_structure_db.inc");
include_once($path_to_root."/modules/payroll/includes/db/taxes_db.inc");


//write payslip and payment advice
function write_payslip(&$cart, $reverse, $use_transaction=true)
{
	global $Refs;

	$date_ = $cart->tran_date;
	$ref   = $cart->reference;
	$memo_ = $cart->memo_;
	$trans_type = $cart->trans_type;
	$new = $cart->order_id == 0;
	
	if ($new)
	    $cart->order_id = get_next_trans_no($trans_type);

    $trans_id = $cart->order_id;

	if ($use_transaction)
		begin_transaction();
	
	if(!$new)
		void_journal_trans($trans_type, $trans_id, false);

	foreach ($cart->gl_items as $journal_item)
	{
		// post to first found bank account using given gl acount code.
		$is_bank_to = is_bank_account($journal_item->code_id);

		add_gl_trans($trans_type, $trans_id, $date_, $journal_item->code_id,
			$journal_item->dimension_id, $journal_item->dimension2_id,
			$journal_item->reference, $journal_item->amount,null,
			$cart->paytype,$cart->person_id,"",0,
			$cart->to_the_order_of,$cart->payslip_no);


	    	if ($is_bank_to)
	    	{
	    		add_bank_trans($trans_type, $trans_id, $is_bank_to, $ref,
	    			$date_, $journal_item->amount,$cart->paytype,$cart->person_id, get_company_currency(),
	    			"Cannot insert a destination bank transaction");
	    	}

		// store tax details if the gl account is a tax account
		add_gl_tax_details($journal_item->code_id, 
			ST_JOURNAL, $trans_id, $journal_item->amount, 1, $date_, $memo_);
	}
	
	$Refs->save($trans_type, $trans_id, $ref);
	if ($new) {
		add_comments($trans_type, $trans_id, $date_, $memo_);
	} else {
		update_comments($trans_type, $trans_id, null, $memo_);
	}

	add_audit_trail($trans_type, $trans_id, $date_);

	if ($reverse)
	{
    	//$reversingDate = date(user_date_display(),
    	//	Mktime(0,0,0,get_month($date_)+1,1,get_year($date_)));
    	$reversingDate = begin_month(add_months($date_, 1));

    	$trans_id_reverse = get_next_trans_no($trans_type);

    	foreach ($cart->gl_items as $journal_item)
    	{
			$is_bank_to = is_bank_account($journal_item->code_id);

    		add_gl_trans($trans_type, $trans_id_reverse, $reversingDate,
    			$journal_item->code_id, $journal_item->dimension_id, $journal_item->dimension2_id,
    			$journal_item->reference, -$journal_item->amount);
    		if ($is_bank_to)
    		{
    			add_bank_trans($trans_type, $trans_id_reverse, $is_bank_to, $ref,
    				$reversingDate, -$journal_item->amount,
    				0, "", get_company_currency(),
    				"Cannot insert a destination bank transaction");
    		}
			// store tax details if the gl account is a tax account
			add_gl_tax_details($journal_item->code_id, 
				ST_JOURNAL, $trans_id, $journal_item->amount, 1, $reversingDate, $memo_);
    	}

    	add_comments($trans_type, $trans_id_reverse, $reversingDate, $memo_);

    	$Refs->save($trans_type, $trans_id_reverse, $ref);
		add_audit_trail($trans_type, $trans_id_reverse, $reversingDate);
	}

	if ($use_transaction)
		commit_transaction();

	return $trans_id;
}

//get all payroll accounts from chart master
function get_payroll_rules()
{
	

	$sql = "SELECT account_code, account_name 
		FROM ".TB_PREF."chart_master
		WHERE account_type=".AC_TYPE_PAYROLL;

	return db_query($sql,"oops");
			
}

//get next payslip no from gl trans
function get_next_payslip_no(){
	$sql = "SELECT MAX(payslip_no)+1 FROM ".TB_PREF."gl_trans";
	$result = db_query($sql,"The next payslip number could not be retreived");

    	$row = db_fetch_row($result);
    	return ($row[0])?$row[0]:1;
}

function get_sql_for_payslips($employee,$from,$to,$dept='',$position=''){
	$sql = "SELECT	gl.tran_date,
		CONCAT(emp.emp_first_name,' ',emp.emp_last_name) as emp_name,
		job.job_name as job_position,
		dept.dept_name as department,
		gl.to_the_order_of,
		ABS(gl.amount) as amount,
		com.memo_,
		IF(ISNULL(a.gl_seq),0,a.gl_seq) as gl_seq,
		COUNT(gl.payslip_no) as PaySlip,
		gl.type_no
		
		FROM ".TB_PREF."gl_trans as gl
		 LEFT JOIN ".TB_PREF."audit_trail as a ON
			(gl.type=a.type AND gl.type_no=a.trans_no)
		 LEFT JOIN ".TB_PREF."comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)
		 LEFT JOIN ".TB_PREF."employees as emp ON
			emp.emp_id = gl.person_id
		 LEFT JOIN ".TB_PREF."jobnames as job ON
			emp.job_position_id = job.job_name_id
		 LEFT JOIN ".TB_PREF."departments as dept ON
			emp.department_id = dept.dept_id

		WHERE gl.person_type_id = ".PT_EMPLOYEE."
		AND gl.account = " .AC_PAYABLE ;

	if($from != '' && $to != ''){
		$sql .= " AND gl.tran_date >= '" . date2sql($from) . "'
			AND gl.tran_date <= '" . date2sql($to) . "'";
	}

	if($dept != ''){
		$sql .= " AND emp.department_id = ".db_escape($dept);
	}elseif($position != ''){
		$sql .= " AND emp.job_position_id = ".db_escape($position);
	}elseif ($employee != -1 && $employee != '') {
		$sql .= " AND gl.person_id=".db_escape($employee);
	}


		
	$sql .= " AND gl.amount!=0 ";
	
	

	$sql .= " GROUP BY gl.payslip_no";
	$sql .= " HAVING PaySlip = 1";
	
	//$sql .= " GROUP BY gl.tran_date, a.gl_seq, gl.type, gl.type_no";

	//echo $sql;
	return $sql;
}


function  get_sql_for_payslip_batch()
{
	

	$sql = "SELECT CONCAT(emp.emp_first_name,' ',emp.emp_last_name) as emp_name,job.job_name,
		SUM(IF(str.type = 0,str.pay_amount,0)) AS Cr, 
		SUM(IF(str.type =1, str.pay_amount,0)) AS Dr,
		emp.job_position_id,
		emp.emp_id
		FROM ".TB_PREF."employees  emp
		LEFT JOIN ".TB_PREF."jobnames as job ON emp.job_position_id = job.job_name_id
		LEFT JOIN ".TB_PREF."salary_structure str ON str.job_position_id= emp.job_position_id
		WHERE emp.inactive = 0";

	if($_POST['job_position_id'] != ALL_TEXT){
		$sql .= " AND emp.job_position_id = ".db_escape($_POST['job_position_id']);
	}

	if($_POST['department_id'] != ALL_TEXT){
		$sql .= " AND emp.department_id = ".db_escape($_POST['department_id']);
	}

	$sql .= " GROUP BY emp.emp_id,emp.job_position_id";

	
	//echo $sql;exit;
	return $sql;
}

function has_payment_advice($trans_id)
{
	
	$sql = "SELECT COUNT(*) AS PaySlip FROM ".TB_PREF."gl_trans 
		WHERE account = " .AC_PAYABLE. " AND payslip_no IN (
			SELECT payslip_no  
			FROM ".TB_PREF."gl_trans 
			WHERE type = " .ST_JOURNAL. " AND account = ".AC_PAYABLE." 
				AND person_type_id = ".PT_EMPLOYEE." and type_no = ".db_escape($trans_id).")";
		
	//echo $sql;exit;
	
	$result = db_query($sql, "Cannot retreive a payslip");
	$row = db_fetch($result);
	if($row['PaySlip'] > 1){
		return true;
	}else{
		return false;
	}
	
}


function get_payslip($trans_id)
{
	$sql = "SELECT * FROM ".TB_PREF."gl_trans 
		WHERE type=" .ST_JOURNAL. " AND account = " .AC_PAYABLE 
		." AND type_no=".db_escape($trans_id);
		
	$result = db_query($sql, "Cannot retreive a gl transaction");

    	return db_fetch($result);
}


//sql for employee transaction inquiry
function get_sql_for_emp_inquiry($employee,$job_position,$department,$from,$to){
	$sql = "SELECT	gl.tran_date,
		CONCAT(emp.emp_first_name,' ',emp.emp_last_name),
		job.job_name,
		dept.dept_name,
		gl.to_the_order_of,
		ABS(gl.amount) as amount,
		com.memo_,
		IF(ISNULL(a.gl_seq),0,a.gl_seq) as gl_seq,
		COUNT(gl.payslip_no) as PaySlip,
		gl.type_no,
		gl.type
		
		FROM ".TB_PREF."gl_trans as gl
		 LEFT JOIN ".TB_PREF."audit_trail as a ON
			(gl.type=a.type AND gl.type_no=a.trans_no)
		 LEFT JOIN ".TB_PREF."comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)
		 LEFT JOIN ".TB_PREF."employees as emp ON
			emp.emp_id = gl.person_id
		 LEFT JOIN ".TB_PREF."jobnames as job ON
			emp.job_position_id = job.job_name_id
		 LEFT JOIN ".TB_PREF."departments as dept ON
			emp.department_id = dept.dept_id
		WHERE gl.person_type_id = ".PT_EMPLOYEE."
		AND gl.account = " .AC_PAYABLE ;
		if($from != '' && $to != ''){
			$sql .= " AND gl.tran_date >= '" . date2sql($from) . "'
				AND gl.tran_date <= '" . date2sql($to) . "'";
		}
		
		$sql .= " AND gl.amount!=0 ";
	
	if ($employee != -1) {
		$sql .= " AND gl.person_id=".db_escape($employee);
	}

	$sql .= " GROUP BY gl.payslip_no";
	$sql .= " HAVING PaySlip = 1";
	
	//$sql .= " GROUP BY gl.tran_date, a.gl_seq, gl.type, gl.type_no";

	//echo $sql;
	return $sql;
}
function get_sql_for_payment_advices($employee,$job_position,$department,$from,$to,$advice = 'all')
{
	
	$sql="SELECT gl.tran_date,
		CONCAT(emp.emp_first_name,' ',emp.emp_last_name) as emp_name,
		gl.type_no,
		com.memo_,
		ac.account_name,
		ABS(gl.amount) as amount,
		bt.cheque_no,
		gl.type_no,
		job.job_name as job_position,
		dept.dept_name as department
		
	FROM ".TB_PREF."gl_trans as gl 

	LEFT JOIN ".TB_PREF."employees as emp ON
			emp.emp_id = gl.person_id
	LEFT JOIN ".TB_PREF."bank_trans as bt ON
			bt.type = gl.type AND bt.trans_no=gl.type_no
	LEFT JOIN ".TB_PREF."jobnames as job ON
			emp.job_position_id = job.job_name_id
	LEFT JOIN ".TB_PREF."departments as dept ON
			emp.department_id = dept.dept_id
	LEFT JOIN ".TB_PREF."chart_master ac ON
			gl.account = ac.account_code
	LEFT JOIN ".TB_PREF."comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)

	WHERE gl.type =  " .ST_JOURNAL. " AND gl.account IN (select account_code from ".TB_PREF."bank_accounts) AND gl.person_type_id = ".PT_EMPLOYEE." AND gl.payslip_no > 0  ";
	
	if ($employee != -1 && $employee != '') {
		$sql .= " AND gl.person_id=".db_escape($employee);
	}

	if($from != '' && $to != ''){
		$sql .= " AND gl.tran_date >= '" . date2sql($from) . "'
			AND gl.tran_date <= '" . date2sql($to) . "'";
	}

	/*advice => 'all' - return all payment advices
	advice => 'released' - return payment advices have cheque no
	advice => 'advice' - return payment advices have cheque no*/

	if($advice == 'released'){
		$sql .= " AND bt.cheque_no <> ''";
	}elseif($advice == 'advice'){
		$sql .= " AND bt.cheque_no = ''";
	}

	return $sql;
}
function get_payment_advice_details($trans_no)
{
	$sql="SELECT gl.tran_date,CONCAT(emp.emp_first_name,' ',emp.emp_last_name) as emp_name,'',gl.memo_,gl.account,gl.amount,gl.type_no,dp.dept_name,jb.job_name,ba.bank_account_name,ba.bank_account_number,
	ba.bank_name,ba.bank_address,ba.account_type,bt.id
	FROM ".TB_PREF."gl_trans as gl
	LEFT JOIN ".TB_PREF."employees as emp ON
			emp.emp_id = gl.person_id
	LEFT JOIN ".TB_PREF."departments as dp ON
			dp.dept_id = gl.person_id
	LEFT JOIN ".TB_PREF."jobnames as jb ON
			jb.job_name_id = emp.job_position_id
	LEFT JOIN ".TB_PREF."bank_trans as bt ON
			bt.trans_no = gl.type_no AND bt.type=gl.type
	LEFT JOIN ".TB_PREF."bank_accounts as ba ON
			ba.id = bt.bank_act
	
	WHERE gl.type=" .ST_JOURNAL. " AND gl.account IN (select account_code from ".TB_PREF."bank_accounts) AND type_no=".db_escape($trans_no);
	
	$res=db_query($sql,"oops");
	return db_fetch($res);
	 //echo $res;
	
}

function update_cheque($chequedate,$chequeno,$id)
{		
		
		$sql="UPDATE ".TB_PREF."bank_trans SET
		cheque_date='".date2sql($chequedate)."',
		cheque_no=".db_escape($chequeno)."
		WHERE id=".db_escape($id);
		//display_notification($sql);
		//exit;
		db_query($sql,"cant add cheque details");
		
		
		
		
}


?>
