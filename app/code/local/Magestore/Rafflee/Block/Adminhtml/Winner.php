<?php

class Magestore_Rafflee_Block_Adminhtml_Winner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_winner';
		$this->_blockGroup = 'rafflee';
		$this->_headerText = Mage::helper('rafflee')->__('Ticket');
		// $this->_addButtonLabel = Mage::helper('rafflee')->__('Add Item');
		parent::__construct();
	}
}