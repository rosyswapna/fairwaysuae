ALTER TABLE `0_debtor_trans_details`  ADD `discount_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `discount_percent`;

ALTER TABLE `0_sales_order_details`  ADD `discount_amount` DOUBLE NOT NULL DEFAULT '0';

---21 may 2015-------------
--gl accounts----
INSERT INTO `0_chart_master` (`account_code`, `account_code2`, `account_name`, `account_type`, `inactive`) VALUES
('2385', '', 'Frieght Charges', '4', 0),
('2386', '', 'Insurance', '4', 0),
('2387', '', 'Packing Charges', '4', 0),
('2388', '', 'Duties', '4', 0),
('2389', '', 'Service Charge', '4', 0),
('2390', '', 'Commision', '4', 0);

--new gl system setup----
INSERT INTO `fairwayuae`.`0_sys_prefs` (`name`, `category`, `type`, `length`, `value`) VALUES
('default_frieght', 'glsetup.general', 'varchar', '15', '2385'),
('default_insurance', 'glsetup.general', 'varchar', '15', '2386'),
('default_pck_charge', 'glsetup.general', 'varchar', '15', '2387'),
('default_duties', 'glsetup.general', 'varchar', '15', '2388'),
('default_service_charge', 'glsetup.general', 'varchar', '15', '2389'),
('default_commission', 'glsetup.general', 'varchar', '15', '2390');
