ALTER TABLE `0_debtor_trans_details`  ADD `discount_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `discount_percent`;

ALTER TABLE `0_sales_order_details`  ADD `discount_amount` DOUBLE NOT NULL DEFAULT '0';
