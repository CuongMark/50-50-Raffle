<?php

class Magestore_Rafflee_Adminhtml_RaffleeController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('rafflee/rafflee')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}
	
	public function winnerAction(){
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('rafflee/rafflee')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('rafflee_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('rafflee/rafflee');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('rafflee/adminhtml_rafflee_edit'))
				->_addLeft($this->getLayout()->createBlock('rafflee/adminhtml_rafflee_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rafflee')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('rafflee/rafflee');
			try {
				$data['start_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp(strtotime($data['start_time'])));
				$data['end_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp(strtotime($data['end_time'])));
			} catch (Exception $e) {}

			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				$model->createProduct();
				$model->updateStatus();
				if(!$model->setIsChangedStatus()) {
					$model->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rafflee')->__('Rafflee was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rafflee')->__('Unable to find Raffle to save'));
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('rafflee/rafflee');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$raffleeIds = $this->getRequest()->getParam('rafflee');
		if(!is_array($raffleeIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($raffleeIds as $raffleeId) {
					$rafflee = Mage::getModel('rafflee/rafflee')->load($raffleeId);
					$rafflee->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($raffleeIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$raffleeIds = $this->getRequest()->getParam('rafflee');
		if(!is_array($raffleeIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($raffleeIds as $raffleeId) {
					$rafflee = Mage::getSingleton('rafflee/rafflee')
						->load($raffleeId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($raffleeIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
  
	public function exportCsvAction(){
		$fileName   = 'rafflee.csv';
		$content	= $this->getLayout()->createBlock('rafflee/adminhtml_rafflee_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'rafflee.xml';
		$content	= $this->getLayout()->createBlock('rafflee/adminhtml_rafflee_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
}