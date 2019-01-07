<?php

class Magestore_Rafflee_Model_Mysql4_Rafflee extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('rafflee/rafflee', 'rafflee_id');
	}
}