<?php class Magestore_Raffle_Block_Adminhtml_Raffle_Renderer_Winprice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{    public function render(Varien_Object $row){		$ticketId = $row->getTicketId();		$winnumbers = Mage::getModel('rafflee/randnum')->getCollection()->addFieldToFilter('ticket_id',$ticketId);		$winnumbers->getSelect()            			->joinLeft(                				array('prizes'=>Mage::getModel('core/resource')->getTableName('raffle_prizes')),                "main_table.prize_id = prizes.prize_id",				array('price'=>'prizes.price')        				);		$winNumbers = 0;			foreach($winnumbers as $_winnumber){			$winprice += $_winnumber->getPrice();		}        				if($ticketId){			return Mage::helper('core')->currency($winprice);			}        				return '';		}	}