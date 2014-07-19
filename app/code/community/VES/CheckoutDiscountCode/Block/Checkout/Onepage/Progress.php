<?php
class VES_CheckoutDiscountCode_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{
	/**
	 * Get checkout steps codes
	 *
	 * @return array
	 */
	protected function _getStepCodes()
	{
		return Mage::getStoreConfig('checkoutdiscountcode/config/enable')?array('login', 'billing', 'shipping', 'discountcode', 'shipping_method', 'payment', 'review'):parent::_getStepCodes();
	}
	
	/**
	 * Get is discountcode step completed.
	 *
	 * @return bool
	 */
	public function isDiscountcodeStepComplete()
	{
		$stepsRevertIndex = array_flip($this->_getStepCodes());
	
		$toStep = $this->getRequest()->getParam('toStep');
	
		if ($stepsRevertIndex['discountcode'] >= $stepsRevertIndex[$toStep]) {
			return false;
		}
	
		return true;
	}
	
}