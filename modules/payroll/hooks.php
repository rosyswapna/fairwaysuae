<?php
define('SS_PAYROLL',71<<8);

class payroll_app extends application
{
        function payroll_app()
        {
		global $path_to_root;

                $this->application("payroll", _($this->help_context = "HR P&ayroll"));

                $this->add_module(_("Transactions"));
                $this->add_lapp_function(0, _("Manage &Employee Payslip"),
                        $path_to_root.'/modules/payroll/emp_payslip.php?NewPayslip=Yes', 'SA_PAYSLIP', MENU_TRANSACTION);
                $this->add_lapp_function(0, _("Create Pay&slip Batches"),
                        $path_to_root.'/modules/payroll/payslip_batches.php', 'SA_PAYSLIP_BATCHES', MENU_TRANSACTION);
				$this->add_lapp_function(0, _("Make &Payment Advices"),
                        $path_to_root.'/modules/payroll/inquiry/payment_advices.php', 'SA_PMTADVICE', MENU_TRANSACTION);
                $this->add_rapp_function(0, _("Make Employee Payment Advice"),
                        $path_to_root.'/modules/payroll/payment_advice.php?NewPaymentAdvice=Yes', 'SA_PMTADVICE', MENU_TRANSACTION);
				$this->add_rapp_function(0, _("Manage &Employee Recipts/Deposits"),
                        $path_to_root.'/modules/payroll/employee_res_dep.php', 'SA_EMPLOYEES_RECIPTS_DEPOSITS', MENU_TRANSACTION);
				$this->add_rapp_function(0, _("Manage &Employee Expences"),
                        $path_to_root.'/modules/payroll/employee_expences.php', 'SA_EMPLOYEES_EXPENCES', MENU_TRANSACTION);
                $this->add_rapp_function(0, _("Manage Pay&check Printable"),
                        $path_to_root.'/modules/payroll/paycheck_printable.php', 'SA_PAYCHECK_PRINTABLE', MENU_TRANSACTION);


						
                $this->add_module(_("Inquiries and Reports"));
                $this->add_lapp_function(1, _("Employee Tr&anscation Inquiry"),
                        $path_to_root.'/modules/payroll/inquiry/employee_inquiry.php', 'SA_EMPTRANS', MENU_INQUIRY);
                $this->add_lapp_function(1, _("Payroll Liabialities &Inquiry"),
                         $path_to_root.'/modules/payroll/inquiry/payroll_liabialities.php', 'SA_LBLTYQRY', MENU_INQUIRY);
				$this->add_rapp_function(1, _("Employee Pay&roll Reports"),
                        $path_to_root.'/reporting/reports_main.php?Class=9', 'SA_PAYROLL_REPORTS', MENU_REPORT);
				//$this->add_rapp_function(1, _("Customer and Sales &Reports"),
			//"reporting/reports_main.php?Class=0", 'SA_SALESTRANSVIEW', MENU_REPORT);
											
						
						
						
                $this->add_module(_("Maintenance"));
				$this->add_lapp_function(2, _("Manage &Employees"),
                        $path_to_root.'/modules/payroll/employees.php', 'SA_PAYROLL', MENU_MAINTENANCE);
				
						
					
						
                $this->add_rapp_function(2, _("Manage &Departments"),
                        $path_to_root.'/modules/payroll/manage/department.php', 'SA_DEPARTMENT', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Manage &Job Positions"),
                        $path_to_root.'/modules/payroll/manage/jobpositions.php', 'SA_JOB_POSITIONS', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Manage &Leave Types"),
                        $path_to_root.'/modules/payroll/manage/leavetypes.php', 'SA_LEAVETYPE', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Payroll Rules"),
                        $path_to_root.'/modules/payroll/manage/payroll_settings.php', 'SA_PAYROLL_SETTINGS', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("PayRule Structure"),
                      $path_to_root.'/modules/payroll/manage/payrule_structure.php', 'SA_PAYRULE_STRUCTURE', MENU_MAINTENANCE);
                $this->add_rapp_function(2, _("Salary Structures"),
                      $path_to_root.'/modules/payroll/manage/salary_structure.php', 'SA_SALARY_STRUCTURE', MENU_MAINTENANCE);
             /*    $this->add_rapp_function(2, _("Pay Types"),
                        $path_to_root.'/modules/payroll/managePayType.php', 'SA_PAYROLL', MENU_MAINTENANCE);
                $this->add_rapp_function(2, _("&Taxes & Deductions"),
                        $path_to_root.'/modules/payroll/manageTaxes.php', 'SA_PAYROLL', MENU_MAINTENANCE); */
						
                $this->add_extensions();
        }
}

class hooks_payroll extends hooks {
        var $module_name = 'payroll'; // extension module name.

        function install_tabs($app) {
                set_ext_domain('modules/payroll');        // set text domain for gettext
                $app->add_application(new payroll_app); // add menu tab defined by example_class
                set_ext_domain();
        }

        function install_access() {
                $security_sections[SS_PAYROLL] = _("Payroll");
                $security_areas['SA_PAYROLL'] = array(SS_PAYROLL|1, _("Process Payroll and Reports "));
		$security_areas['SA_DEPARTMENT'] = array(SS_PAYROLL|1, _("Manage Departments "));
		$security_areas['SA_PAYROLL_SETTINGS'] = array(SS_PAYROLL|1, _("Payroll Settings"));
		$security_areas['SA_LEAVETYPE'] = array(SS_PAYROLL|1, _("Manage Leaves"));
		$security_areas['SA_JOB_POSITIONS'] = array(SS_PAYROLL|1, _("Manage Job Positions"));
		$security_areas['SA_SALARY_STRUCTURE'] = array(SS_PAYROLL|1, _("Manage Salary Structure"));
		$security_areas['SA_PAYRULE_STRUCTURE'] = array(SS_PAYROLL|1, _("Manage Payrule Structure"));
		$security_areas['SA_PAYSLIP'] = array(SS_PAYROLL|1, _("Manage Employee Payslip"));
		$security_areas['SA_PAYSLIP_BATCHES'] = array(SS_PAYROLL|1, _("Manage Payslip Batches"));

		$security_areas['SA_PMTADVICE'] = array(SS_PAYROLL|1, _("Make Payment Advices"));
		$security_areas['SA_LBLTYQRY'] = array(SS_PAYROLL|1, _("Payroll Liabialities Inquiry"));
		$security_areas['SA_EMPTRANS'] = array(SS_PAYROLL|1, _("Employee Inquiry"));

		$security_areas['SA_PMTADVICE'] = array(SS_PAYROLL|1, _("Payment Advice"));
		$security_areas['SA_PAYCHECK_PRINTABLE'] = array(SS_PAYROLL|1, _("paycheck printable"));
		$security_areas['SA_PAYCHECK_REPORT'] = array(SS_PAYROLL|1, _("paycheck report"));
		
		$security_areas['SA_PAYROLL_REPORTS'] = array(SS_PAYROLL|1, _("payroll report"));
		$security_areas['SA_PAYROLL_REPORTS_STRUCT'] = array(SS_PAYROLL|1, _("payroll report structure"));
		$security_areas['SA_PAYROLL_REPORTS_SLIP'] = array(SS_PAYROLL|1, _("payroll report slip"));
		$security_areas['SA_PAYROLL_REPORTS_ADVICE'] = array(SS_PAYROLL|1, _("payroll report advice"));
		$security_areas['SA_PAYROLL_REPORTS_LIST'] = array(SS_PAYROLL|1, _("payroll report list"));
		$security_areas['SA_PAYROLL_REPORTS_RELEASE'] = array(SS_PAYROLL|1, _("payroll report release"));
		
		$security_areas['SA_EMPLOYEES_RECIPTS_DEPOSITS'] = array(SS_PAYROLL|1, _("Manage Employee recipts/deposits"));
		$security_areas['SA_EMPLOYEES_EXPENCES'] = array(SS_PAYROLL|1, _("Manage Employee recipts/deposits"));

		



		
                return array($security_areas, $security_sections);
        }

        function activate_extension($company, $check_only=true) {
            global $db_connections;

            $updates = array( 'update.sql' => array('payroll') );
            return $this->update_databases($company, $updates, $check_only);
    }

 }

?>
