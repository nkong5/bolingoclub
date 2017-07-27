<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('startpage')};
CREATE TABLE {$this->getTable('startpage')} (
  `startpage_id` int(11) unsigned NOT NULL auto_increment,
  `teaser_1_large` varchar(250) NOT NULL,
  `teaser_1_small_txt` varchar(250) NOT NULL,
  `teaser_1_small` varchar(250) NOT NULL,
  `teaser_1_large_above_txt` varchar(250) NOT NULL,
  `teaser_1_large_below_txt` varchar(250) NOT NULL,
  `teaser_1_link` varchar(250) NOT NULL,

  `teaser_2_large` varchar(250) NOT NULL,
  `teaser_2_small_txt` varchar(250) NOT NULL,
  `teaser_2_small` varchar(250) NOT NULL,
  `teaser_2_large_above_txt` varchar(250) NOT NULL,
  `teaser_2_large_below_txt` varchar(250) NOT NULL,
  `teaser_2_link` varchar(250) NOT NULL,

  `teaser_3_large` varchar(250) NOT NULL,
  `teaser_3_small_txt` varchar(250) NOT NULL,
  `teaser_3_small` varchar(250) NOT NULL,
  `teaser_3_large_above_txt` varchar(250) NOT NULL,
  `teaser_3_large_below_txt` varchar(250) NOT NULL,
  `teaser_3_link` varchar(250) NOT NULL,

  `status` smallint(6) NOT NULL default '0',
  `update_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`startpage_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

");

$installer->endSetup();