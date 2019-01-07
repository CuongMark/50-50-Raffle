<?php
$installer = $this;
$installer->startSetup();
$installer->run(
    "
    ALTER TABLE {$this->getTable('rafflee')} ADD `total_ticket` INT(11) NOT NULL;
"
);
$installer->endSetup();