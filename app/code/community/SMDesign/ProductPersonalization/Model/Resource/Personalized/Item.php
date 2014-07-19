<?php 
class SMDesign_ProductPersonalization_Model_Resource_Personalized_Item extends Mage_Core_Model_Resource_Db_Abstract {

	public function _construct() {
		$this->_init('product_personalization/personalized_item', 'entity_id');
	}

	public function getRecordByItemIdCodeQuoteId($itemId, $code, $quoteId) {

    	$select = $this->_getWriteAdapter()->select()
    			->from($this->getMainTable())
    			->where("quote_id = ?", $quoteId)
    			->where("item_id = ?", $itemId)
    			->where("code = ?", $code)
				;

    	$result = $this->_getReadAdapter()->fetchRow($select);
		if ($result) {
			return Mage::getModel('product_personalization/personalized_item')->load($result['entity_id']);
		}
		return false;
	}
}