<?php

class Magestore_Rafflee_Model_Rafflee extends Mage_Core_Model_Abstract
{
	const STATUS_NOT_START	= 0;
	const STATUS_PROCCESSING= 1;
	const STATUS_FINISHED	= 2;
	const STATUS_CLOSED	    = 3;
	const STATUS_DISABLED	= 4;
	const LIMIT_TIME	= 1;
	const NOT_LIMIT_TIME	= 0;

    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";
    const XML_PATH_ADMIN_EMAIL_IDENTITY = "trans_email/ident_general";
    const XML_PATH_TO_ADMIN     = "rafflee/emails/to_admin";

	public function _construct(){
		parent::_construct();
		$this->_init('rafflee/rafflee');
	}

	public function createProduct(){
		$name = $this->getName();
		$price = $this->getPrice();
		$productId = $this->getProductId();
		$qty = $this->getData('total_ticket')&&$this->getData('limit_time')? $this->getData('total_ticket'):1000000;//$this->getTotal();
		if($productId){
			$product  =  Mage::getModel('catalog/product')->load($productId);
		}else{
			$product = Mage::getModel('catalog/product');
			$attributeSetName = 'Default';
			$entityType = Mage::getSingleton('eav/entity_type')->loadByCode('catalog_product');
			$entityTypeId = $entityType->getId();
			$product->setStockData(array(
				'is_in_stock' => 1,
				'manage_stock' => 0,
				'qty' => $qty
			));
			$setId = Mage::getResourceModel('catalog/setup', 'core_setup')->getAttributeSetId($entityTypeId, $attributeSetName);
			$product->setAttributeSetId($setId);
			$product->setTypeId('virtual');
			$product->setSku('raffle_' . $name);
			$product->setWebsiteIDs(array(1));
			$product->setTaxClassId(0);
			$product->setCategoryIds(array(Mage::app()->getStore()->getRootCategoryId()));
			$product->setCreatedAt(now());
			$product->setPrice($price);
		}			
		$product->setName($name);
		$product->setDescription($this->getData('description'));
		$product->setShortDescription($this->getData('description'));
		$product->setStatus(1);
		try{
			if(!$productId)
				$product->getResource()->save($product);
			$product->save($product);
			$this->setProductId($product->getId())->save();
			return $product->getId();
		}catch(Exception $e){
		}
	}


	public function getTimeleft(){
		$timestamp = strtotime(now());
		$endTime = Mage::getModel('core/date')->timestamp($this->getEndTime());
		return ($endtime - $timestamp>0)?$endtime - $timestamp:0;
	}

	/**
	 * @return Magestore_Rafflee_Model_Mysql4_Rafflee_Collection
	 */
	public function getRafflees(){
		$this->updateAllStatus();
		return $this->getCollection()->addFieldToFilter('status',self::STATUS_PROCCESSING);
	}

	public function addTickets($customerId, $qty, $oder_id) {
		$ticket = Mage::getModel('rafflee/tickets')
			->setData('rafflee_id',$this->getId())
			->setData('customer_id',$customerId)
			->setOrderId($oder_id)
			->setData('total',$qty)
			->setData('num_start',$this->getCurrentNumber()+1)
			->setData('num_end',$this->getCurrentNumber() + $qty)
			->setData('created_time',now())
			->setStatus(Magestore_Rafflee_Model_Tickets::STATUS_ENABLED)
			->save()
			->emailNewTickets();
		// if($this->getLimitTime()&&$this->getData('total_ticket')<=$ticket->getNumEnd()){
			$this->setData('current_number',$ticket->getNumEnd())->updateStatus();
		// }
		return $ticket;
	}

	public function updateAllStatus(){
		$raffles = $this->getCollection()
			->addFieldToFilter('status', array('nin'=>array(self::STATUS_FINISHED,self::STATUS_CLOSED,self::STATUS_DISABLED)));
		foreach($raffles as $raffle){
			$raffle->updateStatus();
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	public function updateStatus(){
		if(in_array($this->getStatus(),array(self::STATUS_FINISHED,self::STATUS_CLOSED,self::STATUS_DISABLED))){
			return $this;
		}
		$currentStatus = $this->getStatus();
		if($this->getLimitTime()!='0'){
			$timestamp = strtotime(now());
			$startTime = Mage::getModel('core/date')->timestamp($this->getStartTime());
			$endTime = Mage::getModel('core/date')->timestamp($this->getEndTime());
			
			if($timestamp<$startTime){
				$this->setStatus(self::STATUS_NOT_START);
			}elseif($timestamp>=$startTime&&$timestamp<$endTime){
				$this->setStatus(self::STATUS_PROCCESSING);
			}elseif($timestamp>=$endTime){
				$this->setStatus(self::STATUS_FINISHED);
				if(!$this->getWinNumber()){
					$this->setWinNumber(rand(1,$this->getCurrentNumber()));
				}
			}
		}else{
			$timestamp = strtotime(now());
			$startTime = Mage::getModel('core/date')->timestamp($this->getStartTime());

			if($timestamp<$startTime){
				$this->setStatus(self::STATUS_NOT_START);
			}else{
				$this->setStatus(self::STATUS_PROCCESSING);
			}
			// Mage::getSingleton('core/session')->addError(Mage::helper('auction')->__($this->getCurrentNumber()));
			// Mage::getSingleton('core/session')->addError(Mage::helper('auction')->__($this->getTotalTicket()));
			if($this->getCurrentNumber() >= $this->getTotalTicket()){
				$this->setStatus(self::STATUS_FINISHED);
				if(!$this->getWinNumber()){
					$this->setWinNumber(rand(1,$this->getCurrentNumber()));
				}
			}
		}
		if($currentStatus!=$this->getStatus()){
			$this->setIsChangedStatus(true);
			$this->setData('finished_time',now());
			Mage::getSingleton('checkout/session')->setData('finished_rafflee_tickets', array('name'=>$this->getName(), 'number'=>$this->getWinNumber()));
			$this->save();
			if($this->getStatus() == self::STATUS_FINISHED) {
				$this->setWinner();
				$this->notifyRaffeFinished();
			}
		}
		return $this;
	}

	public function getWinner(){
		if(!$this->getData('winner')) {
			$winner = Mage::getModel('rafflee/tickets')->getCollection()
				->addFieldToFilter('rafflee_id', $this->getId())
				->addFieldToFilter('num_start', array('lteq'=>$this->getWinNumber()))
				->addFieldToFilter('num_end', array('gteq'=>$this->getWinNumber()))
				->addFieldToFilter('status', array('in',array(Magestore_Rafflee_Model_Tickets::STATUS_WIN,Magestore_Rafflee_Model_Tickets::STATUS_PAYED)));
			$winner = $winner->getLastItem();
			$this->setData('winner',$winner);
		}
		return $this->getData('winner');
	}

	public function setWinner(){
		$winner = Mage::getModel('rafflee/tickets')->getCollection()
			->addFieldToFilter('rafflee_id', $this->getId())
			->addFieldToFilter('num_start', array('lteq' =>$this->getWinNumber()))
			->addFieldToFilter('num_end', array('gteq' => $this->getWinNumber()))
			->addFieldToFilter('status', array('nin' => array(Magestore_Rafflee_Model_Tickets::STATUS_DISABLED,Magestore_Rafflee_Model_Tickets::STATUS_PAYED)))
			->getLastItem();
		$winner->setStatus(Magestore_Rafflee_Model_Tickets::STATUS_WIN)->save();
		$this->setData('winner',$winner);
		return $winner;
	}

	public function notifyRaffeFinished(){
		$this->getWinner()->emailWinner();
		$this->mailToAdmin();
		$this->mailToLoser();
		return $this;
	}

	public function getCurrentNumber(){
		if(!$this->getData('current_number')) {
			$cur = Mage::getModel('rafflee/tickets')->getCollection()
				->addFieldToFilter('rafflee_id', $this->getId())
//				->addFieldToFilter('status', array('nin', array(Magestore_Rafflee_Model_Tickets::STATUS_LOSE, Magestore_Rafflee_Model_Tickets::STATUS_DISABLED)))
				->setOrder('num_end', 'ASC')
				->getLastItem()
				->getNumEnd();
			$this->setData('current_number',$cur);
		}
		return $this->getData('current_number');
	}

	public function getProccessProductIds(){
		$array = array();
		$raffles = $this->getCollection()
			->addFieldToFilter('status',self::STATUS_PROCCESSING);
		foreach ($raffles as $_raffle) {
			$array[] = $_raffle->getProductId();
		}
		return $array;
	}

	public function loadByProductId($productId) {
		$raffles = Mage::getModel('rafflee/rafflee')->getCollection()
			->addFieldToFilter('status',self::STATUS_PROCCESSING)
			->addFieldToFilter('product_id',$productId)
			->getFirstItem();
		return $raffles;
	}
	static public function getOptionArray(){
		return array(
			self::STATUS_NOT_START	=> Mage::helper('rafflee')->__('Enabled'),
			self::STATUS_PROCCESSING=> Mage::helper('rafflee')->__('Processing'),
			self::STATUS_FINISHED   => Mage::helper('rafflee')->__('Finished'),
			self::STATUS_CLOSED   	=> Mage::helper('rafflee')->__('Closed'),
			self::STATUS_DISABLED   => Mage::helper('rafflee')->__('Disable')
		);
	}

	static public function getLimitOptionArray(){
		return array(
			self::LIMIT_TIME	=> Mage::helper('rafflee')->__('Yes'),
			self::NOT_LIMIT_TIME=> Mage::helper('rafflee')->__('No')
		);
	}

	static public function getOptionHash(){
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}

	public function mailToLoser(){
		$losers = Mage::getModel('rafflee/tickets')->getCollection()
					->addFieldToFilter('rafflee_id',$this->getId())
					// ->addFieldToFilter('customer_id',array('nin',$this->getWinner()->getCustomerId()))
					->addFieldToFilter('status', array('nin'=>array(Magestore_Rafflee_Model_Tickets::STATUS_WIN,Magestore_Rafflee_Model_Tickets::STATUS_PAYED)));
		$losers->getSelect()->group('customer_id');
		foreach ($losers as $_loser) {
			$_loser->setData('raffle',$this)->emailLoser();
		}
		return $this;
	}
	
	public function isProcessing(){
		return $this->getStatus() == self::STATUS_PROCCESSING;
	}
	
    public function mailToAdmin() {
        $storeID = 1;//$this->getStoreId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $template = Mage::getStoreConfig(self::XML_PATH_TO_ADMIN, $storeID);
        $sendTo = array(
            Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_IDENTITY, $storeID)
        );
        
        $mailTemplate = Mage::getModel('core/email_template');
        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                    ->sendTransactional(
                            $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                        'admin_name' => $recipient['name'],
                        'raffle_name' => $this->getName(),
                        'win_number' => $this->getData('win_number')
                    )
            );
        }
        $translate->setTranslateInline(true);
        return $this;
    }
}