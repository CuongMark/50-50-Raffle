<?php

class Magestore_Rafflee_Block_Adminhtml_Rafflee_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('raffleeGrid');
		$this->setDefaultSort('rafflee_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('rafflee/rafflee')->getCollection();
		$collection->getSelect()->joinLeft(array('ticket'=>Mage::getSingleton('core/resource')->getTableName('rafflee_tickets')),'ticket.rafflee_id = main_table.rafflee_id',array('total'=>'Max(ticket.num_end)'))
                    ->group('main_table.rafflee_id');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('rafflee_id', array(
			'header'	=> Mage::helper('rafflee')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'rafflee_id',
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('rafflee')->__('Name'),
			'align'	 =>'left',
			'index'	 => 'name',
		));

		$this->addColumn('total', array(
			'header'	=> Mage::helper('rafflee')->__('Total'),
			'width'	 => '150px',
			'index'	 => 'total',
		));

		$this->addColumn('description', array(
			'header'	=> Mage::helper('rafflee')->__('Description'),
			'width'	 => '150px',
			'index'	 => 'description',
		));

		$this->addColumn('start_time', array(
			'header' => Mage::helper('rafflee')->__('Start Date'),
			'align' => 'left',
			'type' => 'datetime',
			'index' => 'start_time',
		));

		$this->addColumn('end_time', array(
			'header' => Mage::helper('rafflee')->__('End Date'),
			'align' => 'left',
			'type' => 'datetime',
			'index' => 'end_time',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('rafflee')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => Magestore_Rafflee_Model_Rafflee::getOptionArray(),
		));

		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('rafflee')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('rafflee')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('rafflee')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('rafflee')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('rafflee_id');
		$this->getMassactionBlock()->setFormFieldName('rafflee');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('rafflee')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('rafflee')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('rafflee/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('rafflee')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('rafflee')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}