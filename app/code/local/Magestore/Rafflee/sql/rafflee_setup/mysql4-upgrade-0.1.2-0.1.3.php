<?php
$installer = $this;
$installer->startSetup();
$installer->run(
    "
    ALTER TABLE {$this->getTable('rafflee')} ADD `finished_time` datetime NOT NULL;
"
);
$installer->endSetup();