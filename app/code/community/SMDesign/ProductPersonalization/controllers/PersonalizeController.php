<?php

class SMDesign_ProductPersonalization_PersonalizeController extends Mage_Core_Controller_Front_Action {
  
	private function _getQuote() {
		if (empty($this->_quote)) {
			$this->_quote = Mage::getModel('checkout/session')->getQuote();
		}
		return $this->_quote;
	}

	private function _preparePostData() {
		$postData = array();
		
		foreach ((array)$this->getRequest()->getParam('product_personalization') as $groupKey=>$groupData){
			foreach ($groupData as $elementKey=>$elementData){
				foreach ($elementData as $itemId=>$elementValue){
					$postData["$groupKey/$elementKey/$itemId"] = $elementValue;
				}
			}
		}
		
		if (isset($_FILES['product_personalization'])) {
			foreach ($_FILES['product_personalization']['name'] as $groupKey => $fileMethodParm) {
				foreach ($fileMethodParm as $elementKey => $fieldValues) {
					foreach ($fieldValues as $itemId => $fieldValue) {
						$postData["$groupKey/$elementKey/$itemId"] = array(
							'name'		=> $_FILES['product_personalization']['name'][$groupKey][$elementKey][$itemId],
							'type'		=> $_FILES['product_personalization']['type'][$groupKey][$elementKey][$itemId],
							'tmp_name'	=> $_FILES['product_personalization']['tmp_name'][$groupKey][$elementKey][$itemId],
							'error'		=> $_FILES['product_personalization']['error'][$groupKey][$elementKey][$itemId],
							'size'		=> $_FILES['product_personalization']['size'][$groupKey][$elementKey][$itemId],
							);
					}
				}
			}
		}
		return $postData;
	}

	public function indexAction() {
		if (count($this->_getQuote()->getAllVisibleItems()) == 0) {
			$this->_redirect('checkout/cart');
		}
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction() {

		if ($this->_getQuote()->getNumberOfItemToPersonalize() == 0) {
			$this->_redirect('checkout/onepage');
			return;
		}

		try {
			$postData = $this->_preparePostData();

			foreach ($this->_getQuote()->getAllVisibleItems() as $_item) {

				if ($_item->getProduct()->getPersonalization()) {
					$personalizationItems = $_item->getPersonalizationData();
					foreach ($personalizationItems as $code=>$object) {

							if ($object->getField()->frontend_type == 'file' && is_array($postData[$code])) {

								if ($postData[$code]['error'] == 0) {

									$uploader = new Varien_File_Uploader( $postData[$code] );

									if (isset($object->getField()->allowed_file_types)) {
										$uploader->setAllowedExtensions( explode(',', str_replace(' ', '', $object->getField()->allowed_file_types)) );
									}

									$uploader->setAllowRenameFiles(false);
									$uploader->setFilesDispersion(false);

									$uploader->save($object->getFileSoragePath(), $postData[$code]['name'] );
									$object->setValue( $postData[$code]['name'] );
								}
								
							} else if ($object->getField()->frontend_type == 'multiselect' && isset($postData[$code]) && is_array($postData[$code])) {
								$object->setValue( join(',', $postData[$code]) );
							} else if ($object->getField()->frontend_type == 'checkbox') {
								$object->setValue(isset($postData[$code]) ? 'Checked' : 'Not checked');
							} else {
								$object->setValue($postData[$code]);
							}

							$object->save();

					}
				}
			}
			$this->_redirect('checkout/onepage');
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addException($e, $this->__($e->getMessage()));
			Mage::logException($e);
			$this->_redirect('product-personalization/personalize');
		}
	}

	function deleteAction() {
		$itemId = $this->getRequest()->getParam('item_id');

		try {
			$item = Mage::getModel('product_personalization/personalized_item')->load($itemId);

			if ($item->getFileUrl()) {

				if ($this->_getQuote()->getId() != $item->getQuoteId()) {
					Mage::getModel('core/session')->addError("Permission denied. You only can managment your files.");
					$this->_redirect('product-personalization/personalize/');
					return;
				}
				$fileName = $item->getValue();
				unlink($item->getFileSoragePath());
				$item->setValue('');
				$item->save();
				Mage::getModel('core/session')->addNotice( "File \"$fileName\" is deleted." );

			} else {
				throw new Exception("Requested id is not element type file.");
			}
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::getModel('core/session')->addError("Unable to delete file.");
		}

		$this->_redirect('product-personalization/personalize/');
	}
}