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
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_TRANSACTION);
                $this->add_lapp_function(0, _("Create Pay&slip Batches"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_TRANSACTION);
				$this->add_lapp_function(0, _("Make &Payment Advices"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_TRANSACTION);
                $this->add_rapp_function(0, _("Manage Employee $Leave"),
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
				$this->add_lapp_function(2, _("Employee &Contracts"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_MAINTENANCE);
						
						
						
                $this->add_rapp_function(2, _("Manage &Departments"),
                        $path_to_root.'/modules/payroll/manage/department.php', 'SA_PAYROLL', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Manage &Job Positions"),
                        $path_to_root.'/modules/payroll/jobpositions.php', 'SA_PAYROLL', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Manage &Leave Types"),
                        $path_to_root.'/modules/payroll/manage/leavetypes.php', 'SA_PAYROLL', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Payroll Rules"),
                        $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("Leave Rules"),
                      $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_MAINTENANCE);
                $this->add_rapp_function(2, _("Salary Structures"),
                      $path_to_root.'/modules/payroll/', 'SA_PAYROLL', MENU_MAINTENANCE);
				$this->add_rapp_function(2, _("P&ayroll Settings"),
                      $path_to_root.'/modules/payroll/manage/gl_accounts.php', 'SA_PAYROLL', MENU_MAINTENANCE);
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
                return array($security_areas, $security_sections);
        }

        function activate_extension($company, $check_only=true) {
            global $db_connections;

            $updates = array( 'update.sql' => array('payroll') );
            return $this->update_databases($company, $updates, $check_only);
    }

 }

?>