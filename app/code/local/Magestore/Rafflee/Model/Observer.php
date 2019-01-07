<?php

class Magestore_Rafflee_Model_Observer {

    public function getlink() {
        $link = Mage::app()->getRequest()->getRouteName() .
            Mage::app()->getRequest()->getControllerName() .
            Mage::app()->getRequest()->getActionName() .
            Mage::app()->getRequest()->getModuleName();
        return $link;
    }

    public function catalog_product_collection_apply_limitations_before($observer) {
        if (!Mage::registry('load_list_rafflee')) {
            Mage::register('load_list_rafflee', '1');
            if ($this->getlink() == 'raffleeindexindexrafflee'){
                $productCollection = $observer['collection'];
    			$productCollection->getSelect()->joinLeft(array('raffle'=>Mage::getSingleton('core/resource')->getTableName('rafflee')),'e.entity_id = raffle.product_id',array('end_time'=>'raffle.end_time','raffle_price'=>'raffle.price','limit_time'=>'raffle.limit_time','total_ticket'=>'raffle.total_ticket'))
    				->where('raffle.status='.Magestore_Rafflee_Model_Rafflee::STATUS_PROCCESSING);
                $productCollection->getSelect()->joinLeft(array('ticket'=>Mage::getSingleton('core/resource')->getTableName('rafflee_tickets')),'raffle.rafflee_id = ticket.rafflee_id',array('total'=>'MAX(ticket.num_end)'))
                    ->group('e.entity_id');
            }else if($this->getlink() == 'raffleeindexfinishedrafflee'){
                $productCollection = $observer['collection'];
                $productCollection->getSelect()->joinLeft(array('raffle'=>Mage::getSingleton('core/resource')->getTableName('rafflee')),'e.entity_id = raffle.product_id',array('raffle_price'=>'raffle.price','finished_time'=>'raffle.finished_time','win_number'=>'raffle.win_number'))
                    ->where('raffle.status='.Magestore_Rafflee_Model_Rafflee::STATUS_FINISHED);
                $productCollection->getSelect()->joinLeft(array('ticket'=>Mage::getSingleton('core/resource')->getTableName('rafflee_tickets')),'raffle.rafflee_id = ticket.rafflee_id',array('total'=>'MAX(ticket.num_end)'))
                    ->group('e.entity_id');
            }
            return $this;
        }
    }

    public function sales_order_save_after($observer){
        $orderStateActivePackage = Mage::getStoreConfig('rafflee/general/create_tickets_when_state_order');
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        if($order->getStatus() == $orderStateActivePackage) {
			$array = array();
            foreach ($order->getAllVisibleItems() as $item) {
                $productId = $item->getProductId();
                $raffle = Mage::getModel('rafflee/rafflee')->loadByProductId($productId);
                if ($raffle->getId()&&$raffle->isProcessing()) {
                    $ticket = $raffle->addTickets($customerId, $item->getQtyOrdered(), $order->getId());
                    $array[] = array('name'=>$raffle->getName(),'start'=>$ticket->getNumStart(),'end'=>$ticket->getNumEnd());
                }
            }
			if(count($array))
				Mage::getSingleton('checkout/session')->setData('bought_rafflee_tickets', $array);
        }
    }



    public function addToTopmenu(Varien_Event_Observer $observer){
        $display = 1;
        if($display){
            try{
                $menu = $observer->getMenu();
                $tree = $menu->getTree();
                $node = new Varien_Data_Tree_Node(array(
                        'name'   => __('50/50 Raffle'),
                        'id'     => 'rafflee',
                        'url'    => Mage::app()->getStore()->getUrl('rafflee'), 
                ), 'id', $tree, $menu);
                $menu->addChild($node);
				$tree = $node->getTree();
				$data = array(
					'name'   => __('Finished 50/50 Raffle'),
					'id'     => 'finished_rafflee',
					'url'    => Mage::app()->getStore()->getUrl('rafflee/index/finished'),
				);
				$subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
                $node->addChild($subNode);
            } catch (Exception $e) {
            
            }
        }
    }
}