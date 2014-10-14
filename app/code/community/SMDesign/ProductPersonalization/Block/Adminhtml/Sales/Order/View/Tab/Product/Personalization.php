<?php 
class SMDesign_ProductPersonalization_Block_Adminhtml_Sales_Order_View_Tab_Product_Personalization extends Mage_Adminhtml_Block_Sales_Order_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface {

	public function getTabLabel() {
		return Mage::helper('sales')->__('Product Personalization');
	}

	public function getTabTitle() {
		return Mage::helper('sales')->__('Order Information');
	}

	public function canShowTab() {
		return true;
	}

	public function isHidden() {
		return false;
	}

	function getPersonalizationDataCollectionByGroup($_item) {
		$collection = Mage::getModel('product_personalization/personalized_item')->getCollection();
		$collection->getSelect()->where('main_table.quote_id=?', $this->getOrder()->getQuoteId());
		$collection->getSelect()->where('main_table.item_id=?', $_item->getQuoteItemId());
		return $collection->getItemsByGroup();
	}

}