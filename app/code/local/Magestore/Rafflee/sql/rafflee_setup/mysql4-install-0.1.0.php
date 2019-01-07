<?php

$installer = $this;
$installer->startSetup();

$installer->run("

  CREATE TABLE IF NOT EXISTS {$this->getTable('rafflee')} (
  `rafflee_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `win_number` int(11) NOT NULL,
  `description` text NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rafflee_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

  CREATE TABLE IF NOT EXISTS {$this->getTable('rafflee_tickets')} (
  `ticket_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '1',
  `rafflee_id` int(11) DEFAULT NULL,
  `num_start` int(11) DEFAULT NULL,
  `num_end` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `created_time` datetime NOT NULL,
  `payout` varchar(255) NOT NULL,
  `payout_time` datetime NOT NULL,
  `user_payout` int(11) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticket_id`),
  KEY `rafflee_id` (`rafflee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");



$installer->endSetup(); 