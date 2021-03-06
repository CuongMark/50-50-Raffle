<?php

class Magestore_Rafflee_Model_System_Template_Loser
{
    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();
            Mage::register('config_system_email_template', $collection);
        }

        $options = $collection->toOptionArray();
        
        array_unshift(
            $options,
            array(
                'value'=> 'magestore_raffle_email_notifie_finished_to_loser',
                'label' => 'Mail to Loser (Default)'
            ),
            array(
                'value'=> '0',
                'label' => 'None'
            )
        );		
		
		return $options;
    }
}