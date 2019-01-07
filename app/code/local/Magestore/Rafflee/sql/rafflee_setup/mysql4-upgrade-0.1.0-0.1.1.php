<?php
$installer = $this;
$installer->startSetup();
$installer->run(
    "
    ALTER TABLE {$this->getTable('rafflee')} ADD `limit_time` INT(11) NOT NULL;
"
);
$installer->endSetup();