<?php

class Magestore_Rafflee_Block_Adminhtml_Rafflee_Edit_Tab_Tickets extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct(){
        parent::__construct();
        $this->setId('ticketsGrid');
        $this->setDefaultSort('ticket_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('rafflee/tickets')->getCollection()
                ->addFieldToFilter('rafflee_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('ticket_id', array(
            'header'    => Mage::helper('rafflee')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'ticket_id',
        ));
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('rafflee')->__('Customers'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'renderer'  => 'rafflee/adminhtml_rafflee_renderer_customer'
        ));
        $this->addColumn('num_start', array(
            'header'    => Mage::helper('rafflee')->__('Start Number'),
            'align'     =>'left',
            'index'     => 'num_start',
        ));
        $this->addColumn('num_end', array(
            'header'    => Mage::helper('rafflee')->__('End Number'),
            'align'     =>'left',
            'index'     => 'num_end',
        ));
        $this->addColumn('order_id', array(
            'header'    => Mage::helper('rafflee')->__('Orders'),
            'align'     =>'left',
            'index'     => 'order_id',
            'renderer'  => 'rafflee/adminhtml_rafflee_renderer_order'
        ));
        $this->addColumn('created_time', array(
              'header'    => Mage::helper('rafflee')->__('Created Date'),
              'width'     => '250px',
              'index'     => 'created_time',
              'type'      => 'datetime',
        ));
        $this->addColumn('ticket.status', array(
                'header'	=> Mage::helper('rafflee')->__('Status'),
                'align'	 => 'left',
                'width'	 => '80px',
                'index'	 => 'status',
                'type'      => 'options',
                'options' => Magestore_Rafflee_Model_Tickets::status(),
        ));
        $this->addColumn('action',
            array(
                'header'	=> Mage::helper('rafflee')->__('Action'),
                'width'		=> '100',
                'type'		=> 'action',
                'getter'	=> 'getId',
                'actions'	=> array(
                    array(
                        'caption'	=> Mage::helper('rafflee')->__('Edit'),
                        'url'		=> array('base'=> '*/tickets/edit'),
                        'field'		=> 'id'
                    )),
                'filter'	=> false,
                'sortable'	=> false,
                'index'		=> 'stores',
                'is_system'	=> true,
        ));		$this->addExportType('*/*/exportCsv', Mage::helper('rafflee')->__('CSV'));
        return parent::_prepareColumns();
  }


	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl(){
            return $this->getData('grid_url')
                ? $this->getData('grid_url')
                : $this->getUrl('*/*/ticketGrid', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
        }
}