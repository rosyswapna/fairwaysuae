DROP TABLE IF EXISTS `0_employees`;
CREATE TABLE `0_employees` (
 `emp_id` int(11) NOT NULL AUTO_INCREMENT,
 `emp_first_name` varchar(100) DEFAULT NULL,
 `emp_last_name` varchar(100) DEFAULT NULL,
 `emp_address` tinytext,
 `emp_phone` varchar(30) DEFAULT NULL,
 `emp_email` varchar(100) DEFAULT NULL,
 `emp_birthdate` date NOT NULL,
 `emp_payfrequency` varchar(30) DEFAULT NULL,
 `emp_filingstatus` varchar(100) DEFAULT NULL,
 `emp_allowances` int(2) DEFAULT NULL,
 `emp_extrawitholding` float DEFAULT NULL,
 `emp_taxid` varchar(11) DEFAULT NULL,
 `emp_hiredate` date DEFAULT NULL,
 `emp_releasedate` date DEFAULT NULL,
 `emp_type` varchar(30) DEFAULT NULL,
 `emp_notes` tinytext NOT NULL,
 `inactive` tinyint(1) NOT NULL DEFAULT '0',
 PRIMARY KEY (`emp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `0_employee_pay_rates`;
CREATE TABLE `0_employee_pay_rates` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `emp_id` tinyint(4) NOT NULL,
  `pay_type_id` tinyint(4) NOT NULL,
  `pay_rate` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Index` (`emp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `0_payroll_pay_types`;
CREATE TABLE `0_payroll_pay_types` (
 `id` tinyint(4) NOT NULL AUTO_INCREMENT,
 `type` int(11) NOT NULL,
 `name` varchar(100) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `0_payroll_tax_rates`;
-- CREATE TABLE `0_payroll_tax_rates` (
-- `id` int(11) NOT NULL AUTO_INCREMENT,
-- `type_id` int(11) NOT NULL,
-- `rate` double NOT NULL,
-- `threshold` double DEFAULT NULL,
-- `status` tinyint(4) DEFAULT NULL,
-- PRIMARY KEY (`id`)
-- ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- SC: 18.04.2012: modified table, see comments
-- the following table set can be used as well for deductions
DROP TABLE IF EXISTS `0_payroll_tax_type`;
CREATE TABLE `0_payroll_tax_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(3) NOT NULL, -- user assigned code, may appear on payslip
  `name` VARCHAR(100) NOT NULL, -- relevant description
  `type` INT NOT NULL, -- tax or deduction
  `responsibility` tinytext NOT NULL, -- employer/employee
  `tax_base` INT NOT NULL, -- see includes/ui/taxes_ui.inc: base salary, realized salary or other
--  `allowance` float NOT NULL,
  `accrual_gl_code` VARCHAR(15) NOT NULL, -- accounting stuff
  `expense_gl_code` VARCHAR(15) NOT NULL, -- accounting stuff
  `tax_period` int(11) NOT NULL, --time basis for figures - annual, monthly, etc.
  `inactive` INT NOT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY (`code`)
);
--

DROP TABLE IF EXISTS `0_payroll_tax_rate`;
CREATE TABLE `0_payroll_tax_rate` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tt_id` INT(11) NOT NULL, -- foreign key payroll_tax_type
  `bracket` VARCHAR(3) NOT NULL, -- user assigned unique code
  `value_from` FLOAT, -- from this value this bracket applies
  `value_to` FLOAT, -- until this value this bracked applies
  `tax_amount` FLOAT, -- on this bracket, there is this fixed amount as tax
  `variable_from` FLOAT, -- what exceeds this amount is charged by percent
  `fixed_tax_percent` FLOAT, -- percent of charge
  `variable` FLOAT, -- variable parameter
  `variable_tax_percent` FLOAT, -- percent of charge, to be multiplied by variable
  
  PRIMARY KEY (`id`)
);


-- SC: 19/04/2012: add table for defining pay types
DROP TABLE IF EXISTS `0_payroll_other_income_type`;
DROP TABLE IF EXISTS `0_payroll_pay_type`;
CREATE TABLE `0_payroll_pay_type` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(3) NOT NULL, -- user assigned code, may appear on payslip
    `name` VARCHAR(100) NOT NULL, -- relevant description
    `taxable` INT NOT NULL, -- yes/no
    `cmethod` INT NOT NULL, -- fixed amount/percent from base/ multipply by # of worked days
    `account_code` VARCHAR(15) NOT NULL,
    `default_rate` FLOAT, -- a default amount/rate to be proposed to user in UI
    `required` INT NOT NULL, -- whether this type is mandatory for an employee
    `automatic` INT NOT NULL, -- whether this paytype is processed automatically at payroll procedure
    `inactive` INT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`code`)
);


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


-- --------------------------------------------------------

--
-- Table structure for table `0_payroll_structure`
--

CREATE TABLE IF NOT EXISTS `0_payroll_structure` (
  `job_position_id` int(11) NOT NULL,
  `payroll_rule` text NOT NULL,
  KEY `job_position_id` (`job_position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `0_salary_structure`
--

CREATE TABLE IF NOT EXISTS `0_salary_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `job_position_id` int(11) NOT NULL,
  `pay_rule _id` int(11) NOT NULL,
  `pay_amount` double NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 for credit,1 for debit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



----31may 2015-
ALTER TABLE `0_employees`  ADD `job_position_id` INT(11) NOT NULL AFTER `emp_notes`,  ADD INDEX (`job_position_id`);


----03june 2015
-- Table structure for table `0_passport`
--
CREATE TABLE IF NOT EXISTS `0_passport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_code` int(11) NOT NULL,
  `emp_name` varchar(50) NOT NULL,
  `fathers_name` varchar(50) NOT NULL,
  `nationality` varchar(15) NOT NULL,
  `birth_place` varchar(15) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` binary(10) NOT NULL,
  `passport_no` varchar(50) NOT NULL,
  `pspt_issue_date` date NOT NULL,
  `pspt_expiry_date` date NOT NULL,
  `pspt_issue_place` date NOT NULL,
  `addr1` varchar(50) NOT NULL,
  `addr2` varchar(50) NOT NULL,
  `addr3` varchar(50) NOT NULL,
  `remark` varchar(50) NOT NULL,
  `site_allocation` varchar(50) NOT NULL,
  `absconded_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_code` (`emp_code`,`passport_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- Table structure for table `0_visa`
--
CREATE TABLE IF NOT EXISTS `0_visa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visa_no` varchar(15) NOT NULL,
  `file_no` int(11) NOT NULL,
  `id_card` varchar(15) NOT NULL,
  `visa_date_issued` date NOT NULL,
  `visa_date_expiry` date NOT NULL,
  `visa_date_renew` date NOT NULL,
  `id_card_issued` date NOT NULL,
  `id_card_expiry` date NOT NULL,
  `designation` varchar(15) NOT NULL,
  `designation_visa` varchar(15) NOT NULL,
  `profession_id` varchar(15) NOT NULL,
  `atten_id` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visa_no` (`visa_no`,`file_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--
-- Table structure for table `0_labour`
--

CREATE TABLE IF NOT EXISTS `0_labour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `labour_card` varchar(15) DEFAULT NULL,
  `lbr_card_isuue_date` date DEFAULT NULL,
  `lbr_card_exp_date` date DEFAULT NULL,
  `health_card_no` varchar(15) DEFAULT NULL,
  `hc_issue_date` date DEFAULT NULL,
  `hc_exp_date` date DEFAULT NULL,
  `hc_information` varchar(15) NOT NULL,
  `phone` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `labour_card` (`labour_card`,`health_card_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



ALTER TABLE `0_labour`  ADD `emp_id` INT(11) NOT NULL AFTER `id`,  ADD INDEX (`emp_id`) ;

ALTER TABLE `0_passport` ADD `emp_id` INT(11) NOT NULL AFTER `id`, ADD INDEX (`emp_id`) ;

ALTER TABLE `0_visa` ADD `emp_id` INT(11) NOT NULL AFTER `id`, ADD INDEX (`emp_id`) ;

------05-06-2015------
ALTER TABLE `0_passport` CHANGE `pspt_issue_place` `pspt_issue_place` VARCHAR(50) NOT NULL;
ALTER TABLE `0_passport` CHANGE `emp_code` `emp_code` VARCHAR(50) NOT NULL;

ALTER TABLE `0_employees`  ADD `department_id` INT(11) NOT NULL AFTER `job_position_id`,  ADD INDEX (`department_id`);

ALTER TABLE `0_gl_trans` ADD `to_the_order_of` VARCHAR(225) NULL , ADD `payslip_no` INT(11) NOT NULL DEFAULT '0' ;
