<?php

class Magestore_Rafflee_Block_Adminhtml_Rafflee extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_rafflee';
		$this->_blockGroup = 'rafflee';
		$this->_headerText = Mage::helper('rafflee')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('rafflee')->__('Add Item');
		parent::__construct();
	}
}