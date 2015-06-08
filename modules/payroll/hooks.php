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
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_TRANSACTION);
				$this->add_rapp_function(0, _("Manage &Employee Expenses"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_TRANSACTION);
                $this->add_rapp_function(0, _("Manage Pay&check Printable"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_TRANSACTION);


						
                $this->add_module(_("Inquiries and Reports"));
                $this->add_lapp_function(1, _("Employee Tr&anscation Inquiry"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_INQUIRY);
                $this->add_lapp_function(1, _("Payroll Liabialities &Inquiry"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_INQUIRY);
				$this->add_rapp_function(1, _("Employee Pay&roll Reports"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_INQUIRY);
											
						
						
						
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
		
                return array($security_areas, $security_sections);
        }

        function activate_extension($company, $check_only=true) {
            global $db_connections;

            $updates = array( 'update.sql' => array('payroll') );
            return $this->update_databases($company, $updates, $check_only);
    }

 }

?>
