<?php

class Magestore_Rafflee_Model_Mysql4_Tickets extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('rafflee/tickets', 'ticket_id');
	}
}