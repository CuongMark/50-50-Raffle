<?php

class Magestore_Rafflee_Block_Adminhtml_Rafflee_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('rafflee_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('rafflee')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('rafflee')->__('Item Information'),
			'title'	 => Mage::helper('rafflee')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('rafflee/adminhtml_rafflee_edit_tab_form')->toHtml(),
		));
		$this->addTab('tickets', array(
			'label'	 => Mage::helper('rafflee')->__('Tickets'),
			'title'	 => Mage::helper('rafflee')->__('Tickets'),
			'content'=> $this->getLayout()->createBlock('rafflee/adminhtml_rafflee_edit_tab_tickets')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}