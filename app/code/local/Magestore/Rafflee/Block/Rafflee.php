<?php

class Magestore_Rafflee_Block_Rafflee extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

	public function getRafflees(){
		return Mage::getModel('rafflee/rafflee')->getRafflees();
	}
	public function addToTopLink() {
		$topBlock = $this->getParentBlock();
		if ($topBlock) {
			$topBlock->addLink($this->__('50/50 Raffle'), 'rafflee', 'rafflee', true, array(), 15);
		}
	}
	
	public function getCustomerTickets(){
		if(!Mage::getSingleton('customer/session')->isLoggedIn())
			return null;
		$tickets = Mage::getModel('rafflee/tickets')
			->getCollection()
			->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
			->setOrder('ticket_id','DESC'); 
		$tickets->getSelect()->joinLeft(array('raffle'=>Mage::getSingleton('core/resource')->getTableName('rafflee')),'main_table.rafflee_id = raffle.rafflee_id',array('name'=>'raffle.name','product_id'=>'raffle.product_id','price'=>'raffle.price * (main_table.num_end - main_table.num_start +1)'));
		return $tickets;
	}
	
	public function getRaffle(){
		if(!$this->getData('raffle')){
			$product =  Mage::registry('current_product');
			$ticket = Mage::getModel('rafflee/rafflee')->getCollection()->addFieldToFilter('product_id',$product->getId())->getFirstItem();
			if($ticket->getId())$ticket->updateStatus();
			$this->setData('raffle',$ticket);
		}
		return $this->getData('raffle');
	}
	
	public function getTicketInCurrentRaffle(){
		if(!$raffleId = $this->getRaffle()->getId())return null;
		if(!Mage::getSingleton('customer/session')->isLoggedIn())return null;
		return Mage::getModel('rafflee/tickets')
			->getCollection()
			->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
			->addFieldToFilter('rafflee_id',$raffleId)
			->setOrder('ticket_id','DESC');
	}
}