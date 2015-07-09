-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2015 at 10:58 AM
-- Server version: 5.5.40
-- PHP Version: 5.3.10-1ubuntu3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `fairwayuae`
--

-- --------------------------------------------------------

--
-- Table structure for table `0_departments`
--

CREATE TABLE IF NOT EXISTS `0_departments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` text NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

----------------------------------------------------------

--
-- Table structure for table `0_employees`
--

CREATE TABLE IF NOT EXISTS `0_employees` (
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
  `job_position_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`emp_id`),
  KEY `job_position_id` (`job_position_id`),
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_employee_pay_rates`
--

CREATE TABLE IF NOT EXISTS `0_employee_pay_rates` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `emp_id` tinyint(4) NOT NULL,
  `pay_type_id` tinyint(4) NOT NULL,
  `pay_rate` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Index` (`emp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_jobnames`
--

CREATE TABLE IF NOT EXISTS `0_jobnames` (
  `job_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` text NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`job_name_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_labour`
--

CREATE TABLE IF NOT EXISTS `0_labour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
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
  UNIQUE KEY `labour_card` (`labour_card`,`health_card_no`),
  KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_leavetypes`
--

CREATE TABLE IF NOT EXISTS `0_leavetypes` (
  `ltyp_id` int(11) NOT NULL AUTO_INCREMENT,
  `ltyp_name` text NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ltyp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_passport`
--

CREATE TABLE IF NOT EXISTS `0_passport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `emp_name` varchar(50) NOT NULL,
  `fathers_name` varchar(50) NOT NULL,
  `nationality` varchar(15) NOT NULL,
  `birth_place` varchar(15) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` binary(10) NOT NULL,
  `passport_no` varchar(50) NOT NULL,
  `pspt_issue_date` date NOT NULL,
  `pspt_expiry_date` date NOT NULL,
  `pspt_issue_place` varchar(50) NOT NULL,
  `addr1` varchar(50) NOT NULL,
  `addr2` varchar(50) NOT NULL,
  `addr3` varchar(50) NOT NULL,
  `remark` varchar(50) NOT NULL,
  `site_allocation` varchar(50) NOT NULL,
  `absconded_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_code` (`emp_code`,`passport_no`),
  KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_payrollcategory`
--

CREATE TABLE IF NOT EXISTS `0_payrollcategory` (
  `pc_id` int(11) NOT NULL AUTO_INCREMENT,
  `pc_name` text NOT NULL,
  `pc_type` double NOT NULL DEFAULT '1',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;


--
-- Dumbing data into  table `0_payrollcategory`
--
INSERT INTO `0_payrollcategory` (`pc_id`, `pc_name`, `pc_type`, `inactive`) VALUES
(1, 'Basic', 1, 0),
(2, 'Allownace', 1, 0),
(3, 'Deduction', 0, 0),
(4, 'Net', 1, 0),
(5, 'Gross', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `0_payroll_pay_type`
--

CREATE TABLE IF NOT EXISTS `0_payroll_pay_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `taxable` int(11) NOT NULL,
  `cmethod` int(11) NOT NULL,
  `account_code` varchar(15) NOT NULL,
  `default_rate` float DEFAULT NULL,
  `required` int(11) NOT NULL,
  `automatic` int(11) NOT NULL,
  `inactive` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_payroll_pay_types`
--

CREATE TABLE IF NOT EXISTS `0_payroll_pay_types` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_payroll_structure`
--

CREATE TABLE IF NOT EXISTS `0_payroll_structure` (
  `job_position_id` int(11) NOT NULL,
  `payroll_rule` text NOT NULL,
  KEY `job_position_id` (`job_position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `0_payroll_tax_rate`
--

CREATE TABLE IF NOT EXISTS `0_payroll_tax_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tt_id` int(11) NOT NULL,
  `bracket` varchar(3) NOT NULL,
  `value_from` float DEFAULT NULL,
  `value_to` float DEFAULT NULL,
  `tax_amount` float DEFAULT NULL,
  `variable_from` float DEFAULT NULL,
  `fixed_tax_percent` float DEFAULT NULL,
  `variable` float DEFAULT NULL,
  `variable_tax_percent` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-----------------------------------------------------------

--
-- Table structure for table `0_payroll_tax_type`
--

CREATE TABLE IF NOT EXISTS `0_payroll_tax_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` int(11) NOT NULL,
  `responsibility` tinytext NOT NULL,
  `tax_base` int(11) NOT NULL,
  `allowance` float NOT NULL,
  `accrual_gl_code` varchar(15) NOT NULL,
  `expense_gl_code` varchar(15) NOT NULL,
  `tax_period` int(11) NOT NULL,
  `inactive` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_salary_structure`
--

CREATE TABLE IF NOT EXISTS `0_salary_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `job_position_id` int(11) NOT NULL,
  `pay_rule_id` int(11) NOT NULL,
  `pay_amount` double NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 for credit,1 for debit',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `0_visa`
--

CREATE TABLE IF NOT EXISTS `0_visa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
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
  UNIQUE KEY `visa_no` (`visa_no`,`file_no`),
  KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE `0_gl_trans` ADD `to_the_order_of` VARCHAR(225) NULL , ADD `payslip_no` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `0_gl_trans` ADD `from_date` DATE NOT NULL , ADD `to_date` DATE NOT NULL ;


--
-- Table structure for table `0_paylsip_details`
--

CREATE TABLE IF NOT EXISTS `0_payslip_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payslip_no` int(11) NOT NULL,
  `generated_date` date NOT NULL,
  `to_the_order_of` varchar(255) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `leaves` int(11) NOT NULL,
  `deductable_leaves` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

