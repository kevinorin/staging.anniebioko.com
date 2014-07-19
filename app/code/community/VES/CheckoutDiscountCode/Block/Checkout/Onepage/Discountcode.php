<?php
class VES_CheckoutDiscountCode_Block_Checkout_Onepage_Discountcode extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function isShow(){
     	return Mage::getStoreConfig('checkoutdiscountcode/config/enable');
     }
}