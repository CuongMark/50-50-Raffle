<?php

class Magestore_Rafflee_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		Mage::getModel('rafflee/rafflee')->updateAllStatus();
		if (!Mage::registry('current_category')) {
			$category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId())
				->setIsAnchor(1)
				->setName(Mage::helper('core')->__('raffle fifty'))
				->setDisplayMode('PRODUCTS');
			Mage::register('current_category', $category);
		}
		$this->loadLayout();
		$this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('Raffle Fifty'));
		$this->renderLayout();
	}

	public function finishedAction(){
		Mage::getModel('rafflee/rafflee')->updateAllStatus();
		if (!Mage::registry('current_category')) {
			$category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId())
				->setIsAnchor(1)
				->setName(Mage::helper('core')->__('raffle fifty finished'))
				->setDisplayMode('PRODUCTS');
			Mage::register('current_category', $category);
		}
		$this->loadLayout();
		$this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('Raffle Fifty finished'));
		$this->renderLayout();
	}

	public function ticketsAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	// public function testAction(){
		// $ticket = Mage::getModel('rafflee/rafflee')->load(16)->notifyRaffeFinished();
		// Zend_debug::dump($ticket->getData());exit;
	// }
}