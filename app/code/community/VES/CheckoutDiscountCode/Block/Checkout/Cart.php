<?php
class VES_CheckoutDiscountCode_Block_Checkout_Cart extends Mage_Checkout_Block_Onepage
{
	protected function _prepareLayout(){
		if(Mage::getStoreConfig('checkoutdiscountcode/config/remove_discount_box')){
			$this->getLayout()->getBlock('checkout.cart')->unsetChild('coupon');
		}
	}
}