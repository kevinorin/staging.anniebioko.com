<?php
class SMDesign_ProductPersonalization_Block_Personalize_Header extends Mage_Core_Block_Template {
	
	private $links;
	
	const PAGE_CHECKOUT_CART = 'checkout/cart/';
	const PAGE_PRODUCT_PERSONALIZATION = 'product-personalization/personalize/';
	const PAGE_ONEPAGE_CHECKOUT = 'checkout/onepage/';
	
	function _prepareLayout() {
		parent::_prepareLayout();

		foreach ($this->getLinks() as $linkKey=>$link) {
			if ('/' . $linkKey == Mage::app()->getRequest()->getRequestString()) {
				$link->setCurrent(true);
			}
		}
		return $this;
		
	}
	
	function getLinks() {
		if (empty($this->links)) {
			$this->links = array(
				self::PAGE_CHECKOUT_CART => new Varien_Object( array('current'=>false, 'url'=>Mage::getUrl(self::PAGE_CHECKOUT_CART), 'label'=>$this->__('Shopping Cart')) ),
				self::PAGE_PRODUCT_PERSONALIZATION => new Varien_Object( array('current'=>false, 'url'=>Mage::getUrl(self::PAGE_PRODUCT_PERSONALIZATION), 'label'=>$this->__('Personalization')) ),
				self::PAGE_ONEPAGE_CHECKOUT => new Varien_Object( array('current'=>false, 'url'=>Mage::getUrl(self::PAGE_ONEPAGE_CHECKOUT), 'label'=>$this->__('Proceed to Checkout')) )
			);
		}
		return $this->links;
	}
}