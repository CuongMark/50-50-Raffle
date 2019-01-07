<?php

class Magestore_Rafflee_Model_Tickets extends Mage_Core_Model_Abstract
{
        const STATUS_ENABLED	= 0;
        const STATUS_WIN	    = 1;
        const STATUS_PAYED	    = 2;
        const STATUS_LOSE	    = 3;
        const STATUS_DISABLED	= 4;
        const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";
        const XML_PATH_TO_WINNER    = "rafflee/emails/to_winner";
        const XML_PATH_TO_LOSER     = "rafflee/emails/to_loser";
        const XML_PATH_TO_ADMIN     = "rafflee/emails/to_admin";
        const XML_PATH_NEW_TICKETS  = "rafflee/emails/new_ticket";

	public function _construct(){
		parent::_construct();
		$this->_init('rafflee/tickets');
	}


    public function getCustomer(){
        if(!$this->getData('customer')){
            $this->setData('customer', Mage::getModel('customer/customer')->load($this->getCustomerId()));
        }
        return $this->getData('customer');
    }

    public function getRaffle(){
        if(!$this->getData('raffle')){
            $this->setData('raffle', Mage::getModel('rafflee/rafflee')->load($this->getRaffleeId()));
        }
        return $this->getData('raffle');
    }

    public function emailNewTickets() {
        $storeID = 1;//$this->getStoreId();
        $customer = $this->getCustomer();
        $raffle = $this->getRaffle();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $template = Mage::getStoreConfig(self::XML_PATH_NEW_TICKETS, $storeID);
        $sendTo = array(
            array(
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            )
        );
        $mailTemplate = Mage::getModel('core/email_template');
        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                    ->sendTransactional(
                            $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                        'customer_name' => $recipient['name'],
                        'raffle_name' => $raffle->getName(),
                        'num_start' => $this->getData('num_start'),
                        'num_end' => $this->getData('num_end'),
                    )
            );
        }
        $translate->setTranslateInline(true);
        return $this;
    }

    public function emailWinner() {
        $storeID = 1;//$this->getStoreId();
        $customer = $this->getCustomer();
        $raffle = $this->getRaffle();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $template = Mage::getStoreConfig(self::XML_PATH_TO_WINNER, $storeID);
        $sendTo = array(
            array(
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            )
        );
        $mailTemplate = Mage::getModel('core/email_template');
        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                    ->sendTransactional(
                            $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                        'customer_name' => $recipient['name'],
                        'raffle_name' => $raffle->getName(),
                        'win_number' => $raffle->getData('win_number'),
                    )
            );
        }
        $translate->setTranslateInline(true);
        return $this;
    }

    public function emailLoser() {
        $storeID = 1;//$this->getStoreId();
        $customer = $this->getCustomer();
        $raffle = $this->getRaffle();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $template = Mage::getStoreConfig(self::XML_PATH_TO_LOSER, $storeID);
        $sendTo = array(
            array(
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            )
        );
        $mailTemplate = Mage::getModel('core/email_template');
        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                    ->sendTransactional(
                            $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                        'customer_name' => $recipient['name'],
                        'raffle_name' => $raffle->getName(),
                        'win_number' => $raffle->getData('win_number')
                    )
            );
        }
        $translate->setTranslateInline(true);
        return $this;
    }

    public static function status(){
        return array(
            self::STATUS_ENABLED	=> Mage::helper('rafflee')->__('Enable'),
            self::STATUS_WIN        => Mage::helper('rafflee')->__('WIN'),
            self::STATUS_PAYED      => Mage::helper('rafflee')->__('Payed'),
            self::STATUS_LOSE       => Mage::helper('rafflee')->__('Lose'),
            self::STATUS_DISABLED   => Mage::helper('rafflee')->__('Díabled')
        );
    }
}