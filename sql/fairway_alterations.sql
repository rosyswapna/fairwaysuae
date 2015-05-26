ALTER TABLE `0_debtor_trans_details`  ADD `discount_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `discount_percent`;

ALTER TABLE `0_sales_order_details`  ADD `discount_amount` DOUBLE NOT NULL DEFAULT '0';

---21 may 2015-------------
--gl accounts----
INSERT INTO `0_chart_master` (`account_code`, `account_code2`, `account_name`, `account_type`, `inactive`) VALUES
('2385', '', 'Freight Charges', '4', 0),
('2386', '', 'Insurance', '4', 0),
('2387', '', 'Packing Charges', '4', 0),
('2388', '', 'Duties', '4', 0),
('2389', '', 'Service Charge', '4', 0),
('2390', '', 'Commision', '4', 0);

--new gl system setup----
INSERT INTO `0_sys_prefs` (`name`, `category`, `type`, `length`, `value`) VALUES
('default_freight', 'glsetup.general', 'varchar', '15', '2385'),
('default_insurance', 'glsetup.general', 'varchar', '15', '2386'),
('default_pck_charge', 'glsetup.general', 'varchar', '15', '2387'),
('default_duties', 'glsetup.general', 'varchar', '15', '2388'),
('default_service_charge', 'glsetup.general', 'varchar', '15', '2389'),
('default_commission', 'glsetup.general', 'varchar', '15', '2390');


ALTER TABLE `0_debtor_trans`  ADD `ov_freight_charge` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_discount`,  ADD `ov_insurance` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_freight_charge`,  ADD `ov_packing_charge` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_insurance`,  ADD `ov_duties` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_packing_charge`,  ADD `ov_service_charge` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_duties`,  ADD `ov_commission` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_service_charge`;


ALTER TABLE `0_supp_trans`  ADD `ov_freight_charge` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_discount`,  ADD `ov_insurance` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_freight_charge`,  ADD `ov_packing_charge` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_insurance`,  ADD `ov_duties` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_packing_charge`,  ADD `ov_service_charge` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_duties`,  ADD `ov_commission` DOUBLE NOT NULL DEFAULT '0' AFTER `ov_service_charge`;


--Department
---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `0_departments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` text NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;




--job Names
-----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `0_jobnames` (
  `job_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` text NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`job_name_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



--Leave Types
-----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `0_leavetypes` (
  `ltyp_id` int(11) NOT NULL AUTO_INCREMENT,
  `ltyp_name` text NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ltyp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;




--Payroll Category
-----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `0_payrollcategory` (
  `pc_id` int(11) NOT NULL AUTO_INCREMENT,
  `pc_name` text NOT NULL,
  `pc_type` double NOT NULL DEFAULT '1',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `0_payrollcategory` (`pc_id`, `pc_name`, `pc_type`, `inactive`) VALUES
(1, 'Basic', 1, 0),
(2, 'Allownace', 1, 0),
(3, 'Deduction', 0, 0),
(4, 'Net', 1, 0),
(5, 'Gross', 1, 0);

