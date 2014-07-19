<?php
class VES_CheckoutDiscountCode_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
	/**
	 * Get 'one step checkout' step data
	 *
	 * @return array
	 */
	public function getSteps()
	{
		$steps = array();
		$stepCodes = $this->_getStepCodes();
	
		if ($this->isCustomerLoggedIn()) {
			$stepCodes = array_diff($stepCodes, array('login'));
		}
	
		foreach ($stepCodes as $step) {
			$steps[$step] = $this->getCheckout()->getStepData($step);
		}
		
		$steps['discountcode'] = array('label'=>'Discounts','is_show'=>true);
	
		return $steps;
	}
	
	/**
	 * Get checkout steps codes
	 *
	 * @return array
	 */
	protected function _getStepCodes()
	{
		return array('login', 'billing', 'shipping', 'discountcode', 'shipping_method', 'payment', 'review');
	}
}