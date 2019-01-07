<?php

class Magestore_Rafflee_Block_Adminhtml_Rafflee_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getRaffleeData()){
			$data = Mage::getSingleton('adminhtml/session')->getRaffleeData();
			Mage::getSingleton('adminhtml/session')->setRaffleeData(null);
		}elseif(Mage::registry('rafflee_data'))
			$data = Mage::registry('rafflee_data');
		
		$fieldset = $form->addFieldset('rafflee_form', array('legend'=>Mage::helper('rafflee')->__('Item information')));

		$fieldset->addField('name', 'text', array(
			'label'		=> Mage::helper('rafflee')->__('Name'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'name',
			'note'      => ($data->getProductId())?'<a id="view_raffle_product" target="_blank" href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $data->getProductId())) . '">' . $this->__('View product information.') . '</a>':''
		));

		$fieldset->addField('product_id', 'hidden', array(
			'label'		=> Mage::helper('rafflee')->__('product_id'),
			'name'		=> 'product_id',
		));

		$fieldset->addField('limit_time', 'select', array(
			'label'		=> Mage::helper('rafflee')->__('Limit Time'),
			'name'		=> 'limit_time',
			'values'	=> Magestore_Rafflee_Model_Rafflee::getLimitOptionArray(),
			// 'disabled'  => (boolean)$data->getProductId()
		));

		$fieldset->addField('total_ticket', 'text', array(
			'label'		=> Mage::helper('rafflee')->__('Total Ticket'),
			// 'class'		=> 'required-entry',
			// 'required'	=> true, 
			'name'		=> 'total_ticket',
			'disabled'  => (boolean)$data->getWinNumber(),
			'note'		=> Mage::helper('rafflee')->__('Current Number is: %s',$data->getCurrentNumber())
		));
		
		$fieldset->addField('price', 'text', array(
			'label'		=> Mage::helper('rafflee')->__('Price'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'price',
			'disabled'  => (boolean)$data->getProductId()
		));
		if($data->getWinNumber()) {
			$fieldset->addField('win_number', 'text', array(
				'label' => Mage::helper('rafflee')->__('Wining Number'),
				'name' => 'win_number_1',
				'disabled' => true
			));
		}

		try {
			$data['start_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(strtotime($data['start_time'])));
			$data['end_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(strtotime($data['end_time'])));
		} catch (Exception $e) {

		}

		$image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';
		$note = $this->__('The current server time is').': '.$this->formatTime(now(),Varien_Date::DATETIME_INTERNAL_FORMAT,true);
		$fieldset->addField('start_time', 'date', array(
			'label'     => Mage::helper('rafflee')->__('Start time'),
			'name'      => 'start_time',
			'input_format'  => Varien_Date::DATETIME_INTERNAL_FORMAT,
			'image' => $image_calendar,
			'format'    =>Varien_Date::DATETIME_INTERNAL_FORMAT,
			'time' => true,
			'note'=>$note,
			'required'  => false,
			'disabled'  => (boolean)$data->getWinNumber()
		));

		$fieldset->addField('end_time', 'date', array(
			'label'     => Mage::helper('rafflee')->__('End time'),
			'name'      => 'end_time',
			'input_format'  => Varien_Date::DATETIME_INTERNAL_FORMAT,
			'image' => $image_calendar,
			'format'    =>Varien_Date::DATETIME_INTERNAL_FORMAT,
			'time' => true,
			'required'  => false,
			// 'note'=>$note,
			'disabled'  => (boolean)$data->getWinNumber()
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('rafflee')->__('Status'),
			'name'		=> 'status',
			'values'	=> Magestore_Rafflee_Model_Rafflee::getOptionArray(),
			'disabled'  => (boolean)$data->getWinNumber(),
			'note'		=> "<script>
				$('limit_time').observe('change',function(){
					if($('limit_time').value=='0'){
						$('total_ticket').up('tr').show();
						$('end_time').up('tr').hide();
					}else{
						$('total_ticket').up('tr').hide();
						$('end_time').up('tr').show();
					}
				});
				if($('limit_time').value=='0'){
						$('total_ticket').up('tr').show();
						$('end_time').up('tr').hide();
					}else{
						$('total_ticket').up('tr').hide();
						$('end_time').up('tr').show();
					}
			</script>"
		));

		$fieldset->addField('description', 'editor', array(
			'name'		=> 'description',
			'label'		=> Mage::helper('rafflee')->__('Description'),
			'title'		=> Mage::helper('rafflee')->__('Description'),
			'style'		=> 'width:700px; height:250px;',
			'wysiwyg'	=> false,
			'required'	=> false,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}