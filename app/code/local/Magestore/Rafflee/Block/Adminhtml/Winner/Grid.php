<?php

class Magestore_Rafflee_Block_Adminhtml_Winner_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection->getSelect()->joinLeft(array('ticket'=>Mage::getSingleton('core/resource')->getTableName('rafflee_tickets')),'ticket.rafflee_id = main_table.rafflee_id', array('win_price'=>'MAX(ticket.num_end) * main_table.price / 2', 'customer_id' => 'ticket.customer_id'))
                    ->group('main_table.rafflee_id');
        $collection->addFieldToFilter('main_table.status',Magestore_Rafflee_Model_Rafflee::STATUS_FINISHED);
        $collection->addFieldToFilter('ticket.status',Magestore_Rafflee_Model_Tickets::STATUS_WIN);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('rafflee_id', array(
            'header'    => Mage::helper('rafflee')->__('ID'),
            'align'  =>'right',
            'width'  => '50px',
            'index'  => 'rafflee_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('rafflee')->__('Name'),
            'align'  =>'left',
            'index'  => 'name',
        ));

        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('rafflee')->__('Customers'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'renderer'  => 'rafflee/adminhtml_rafflee_renderer_customer'
        ));

        $this->addColumn('win_number', array(
            'header'    => Mage::helper('rafflee')->__('Win Number'),
            'align'     =>'left',
            'index'     => 'win_number',
        ));

        $this->addColumn('win_price', array(
            'header'    => Mage::helper('rafflee')->__('Win Price'),
            'width'     => '150px',
            'type'      => 'price',
            'index'     => 'win_price',
            'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('finished_time', array(
            'header' => Mage::helper('rafflee')->__('Finished Time'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'finished_time',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('rafflee')->__('Status'),
            'align'  => 'left',
            'width'  => '80px',
            'index'  => 'status',
            'type'      => 'options',
            'options'    => Magestore_Rafflee_Model_Rafflee::getOptionArray(),
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('rafflee')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('rafflee')->__('View'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('rafflee')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('rafflee')->__('XML'));
        return parent::_prepareColumns();
    }

    // protected function _prepareMassaction(){
    //     $this->setMassactionIdField('rafflee_id');
    //     $this->getMassactionBlock()->setFormFieldName('rafflee');
    //     $statuses = Mage::getSingleton('rafflee/status')->getOptionArray();

    //     array_unshift($statuses, array('label'=>'', 'value'=>''));
    //     $this->getMassactionBlock()->addItem('status', array(
    //         'label'=> Mage::helper('rafflee')->__('Change status'),
    //         'url'   => $this->getUrl('*/*/massStatus', array('_current'=>true)),
    //         'additional' => array(
    //             'visibility' => array(
    //                 'name'  => 'status',
    //                 'type'  => 'select',
    //                 'class' => 'required-entry',
    //                 'label' => Mage::helper('rafflee')->__('Status'),
    //                 'values'=> $statuses
    //             ))
    //     ));
    //     return $this;
    // }

    public function getRowUrl($row){
        return $this->getUrl('*/adminhtml_rafflee/edit', array('id' => $row->getId()));
    }
}