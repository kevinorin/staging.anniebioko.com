<?php

class VES_CheckoutDiscountCode_Model_Observer
{
	/**
	 * Make sure after save billing step it go to vendor step
	 * @param unknown_type $observer
	 */
	public function controller_action_postdispatch_checkout_onepage_saveBilling($observer){
		if(!Mage::getStoreConfig('checkoutdiscountcode/config/enable')) return;
		
		$controller = $observer->getData('controller_action');
		$body = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody());
		if($body['goto_section']=='shipping_method') $body['goto_section'] = 'discountcode';
		$body['update_section'] = array(
				'name' => 'shipping-method',
				'html' => $this->_getVendorHtml($controller)
		);
		$body['allow_sections'] = array('billing','shipping','discountcode');
		$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($body));
	}
	/**
	 * Make sure after save shipping step it go to vendor step
	 * @param unknown_type $observer
	 */
	public function controller_action_postdispatch_checkout_onepage_saveShipping($observer){
		if(!Mage::getStoreConfig('checkoutdiscountcode/config/enable')) return;
		
		$controller = $observer->getData('controller_action');
		$body = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody());
		if($body['goto_section']=='shipping_method') $body['goto_section'] = 'discountcode';
		$body['update_section'] = array(
				'name' => 'shipping-method',
				'html' => $this->_getVendorHtml($controller)
		);
		$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($body));
	}
	
	/**
	 * Get shipping method step html
	 *
	 * @return string
	 */
	protected function _getVendorHtml($controller)
	{
		$layout = $controller->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_discountcode');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}
}