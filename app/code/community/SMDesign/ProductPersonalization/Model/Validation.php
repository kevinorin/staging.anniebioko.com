<?php
class SMDesign_ProductPersonalization_Model_Validation extends Mage_Core_Model_Abstract {
	
	public function personalizationIsEnabled() {
		if (Mage::getStoreConfig('product_personalization/general/can_show_catalog_product_view') ||
			Mage::getStoreConfig('product_personalization/general/can_show_checkout_cart') ||
			Mage::getStoreConfig('product_personalization/general/can_show_before_onepage_checkout') ) {
			return true;
		}
		return false;
	}
	
	public function validateCollection() {

		$quote = Mage::getModel('checkout/session')->getQuote();
		
		foreach ($quote->getAllVisibleItems() as $_item) {

			if ($_item->getProduct()->getPersonalization()) {
				$personalizationData = $_item->getPersonalizationData();
				foreach ($personalizationData as $code=>$object) {

					if (isset($object->getField()->required)) {
						if ('' == $object->getValue()) {
							Mage::getSingleton('core/session')->addError( " {$object->getLabel()} can not be empty." );
							return false;
						}
					}

					if (isset($object->getField()->validate)) {
						Mage::dispatchEvent('product_personalization_validate', array(
							'item'			=> $_item,
							'validate'		=> $object->getField()->validate,
							'object'		=> $object
						));
						if ($object->getIsNotValid() == true) {
							Mage::getSingleton('core/session')->addError( " Validation error " );
							return fasle;
						}
					}
				}

			}
		}
		return true;
	}
}