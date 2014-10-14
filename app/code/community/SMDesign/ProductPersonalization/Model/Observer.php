<?php
class SMDesign_ProductPersonalization_Model_Observer {
	
	protected $_config;
	
	private function getConfig() {
		if (empty($this->_config)) {
			$this->_config = Mage::getModel('product_personalization/config')->getPersonalizationFields();
		}
		return $this->_config;
	}


	public function addPersonalizationDataAfterProductSavedToCart(Varien_Event_Observer $observer) {
		
		$request = Mage::app()->getRequest();
		$_item = $observer->getEvent()->getItem();

		/*
		 * need to run only in case when enabled personalization on catalog product view and action is checkout/cart/add/
		 */
		if (Mage::getStoreConfig('product_personalization/general/can_show_catalog_product_view') && 'cart' == $request->getControllerName() && 'add' == $request->getActionName() && is_null($_item->getParentItemId())) {
		
			
			foreach ($this->getConfig()->groups->children() as $groupKey=>$group) {
				foreach ($group->fields->children() as $key=>$e) {
					$code = "$groupKey/$key/{$_item->getId()}";
					$personalizationItemData[$code] = Mage::getModel('product_personalization/personalized_item')->setData( array(
							'group_label'	=>	$group->label,
							'source_model'	=>	$e->source_model,
							'label'			=>	$e->label,
							'backend_label'	=>	$e->backend_label,
							'group_key'		=>	$groupKey,
							'field_key'		=>	$key,
							'field'			=>	$e,
							'code'			=>	$code,
							'item_id'		=>	$_item->getId(),
							'quote_id'		=>	$_item->getQuoteId()
						));
				}
			}
			$totalItemForPersonalization++;
			$_item->setData('personalization_data', $personalizationItemData);
			Mage::getModel('product_personalization/personalized_prepare')->setItem($_item)->preparePersonalizationObjectToSave();

			foreach ($_item->getPersonalizationData() as $code=>$object) {
				$object->save();
			}
			
		}
	}
	
	/*
	 * This function will run when updaed in cart product
	 */
	public function updateProductPersonalization(Varien_Event_Observer $observer) {
		
		$_item = $observer->getEvent()->getItem();
		$collection = Mage::getModel('product_personalization/personalized_item')->getCollection();
		$collection->getSelect()->where('main_table.quote_id=?', $_item->getQuoteId());
		$collection->getSelect()->where('main_table.item_id=?', $_item->getId());
		$storedData = $collection->getItemsByCode();
		
		foreach ($this->getConfig()->groups->children() as $groupKey=>$group) {
			foreach ($group->fields->children() as $key=>$e) {

				$code = "$groupKey/$key/{$_item->getId()}";
				$object = isset($storedData[$code]) ? $storedData[$code] : Mage::getModel('product_personalization/personalized_item');

				$object->setGroupKey($groupKey);
				$object->setFieldKey($key);
				$object->setField($e);

				$personalizationItemData[$code] = $object->getId() ? $object : $object->setData( array(
						'group_label'	=>	$group->label,
						'source_model'	=>	$e->source_model,
						'label'			=>	$e->label,
						'backend_label'	=>	$e->backend_label,
						'group_key'		=>	$groupKey,
						'field_key'		=>	$key,
						'field'			=>	$e,
						'code'			=>	$code,
						'item_id'		=>	$_item->getId(),
						'quote_id'		=>	$_item->getQuoteId()
					));
			}
		}
		
		
		$_item->setData('personalization_data', $personalizationItemData);
		Mage::getModel('product_personalization/personalized_prepare')->setItem($_item)->preparePersonalizationObjectToSave();
//		dump($_item->getPersonalizationData());
//		exit();
		
		foreach ($_item->getPersonalizationData() as $code=>$object) {
			$object->save();
		}
			
	}
	
	
	
	public function validateProductPersonalization(Varien_Event_Observer $observer) {
		$controllerAction = $observer->getEvent()->getControllerAction();
		
		if (Mage::getModel('product_personalization/validation')->personalizationIsEnabled() && !Mage::getModel('product_personalization/validation')->validateCollection()) {
			if ( Mage::getStoreConfig('product_personalization/general/can_show_before_onepage_checkout') ) {
				$controllerAction->getResponse()->setRedirect(Mage::getUrl('product-personalization/personalize/'))->sendResponse();
			} else {
				Mage::getModel('core/session')->addError($this->__('Please personalize your product before you go in checkout process'));
				$controllerAction->getResponse()->setRedirect(Mage::getUrl('checkout/cart/'))->sendResponse();
			}
		}
	}
	
	function assingPersonalizationDataToQuoteItems(Varien_Event_Observer $observer) {
		$quote = $observer->getEvent()->getQuote();
		$totalItemForPersonalization = $quote->getNumberOfItemToPersonalize();

		$collection = Mage::getModel('product_personalization/personalized_item')->getCollection();
		$collection->getSelect()->where('main_table.quote_id=?', $quote->getId());
		$storedData = $collection->getItemsByCode();


	    foreach ($quote->getAllVisibleItems() as $item) {
			if ($item->getProduct()->getPersonalization()) {
				$personalizationItemData = array();
				foreach ($this->getConfig()->groups->children() as $groupKey=>$group) {
					foreach ($group->fields->children() as $key=>$e) {

						$code = "$groupKey/$key/{$item->getId()}";
						$object = isset($storedData[$code]) ? $storedData[$code] : Mage::getModel('product_personalization/personalized_item');

						$object->setGroupKey($groupKey);
						$object->setFieldKey($key);
						$object->setField($e);

						$personalizationItemData[$code] = $object->getId() ? $object : $object->setData( array(
								'group_label'	=>	$group->label,
								'source_model'	=>	$e->source_model,
								'label'			=>	$e->label,
								'backend_label'	=>	$e->backend_label,
								'group_key'		=>	$groupKey,
								'field_key'		=>	$key,
								'field'			=>	$e,
								'code'			=>	$code,
								'item_id'		=>	$item->getId(),
								'quote_id'		=>	$quote->getId()
							));
					}
				}
				$totalItemForPersonalization++;
				$item->setData('personalization_data', $personalizationItemData);
			}
		}
		$quote->setNumberOfItemToPersonalize($totalItemForPersonalization);
	}

}