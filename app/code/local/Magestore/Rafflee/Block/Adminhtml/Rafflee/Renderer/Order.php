<?php
class Magestore_Rafflee_Block_Adminhtml_Rafflee_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
        $orderId = $row->getOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        if($order){
            return '<a href="'.Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view',array('order_id'=>$orderId)).'" >'.$order->getIncrementId().'</a>';
        }
        return '';
    }
}