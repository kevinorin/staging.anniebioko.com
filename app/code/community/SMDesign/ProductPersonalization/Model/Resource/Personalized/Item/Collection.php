<?php 
class SMDesign_ProductPersonalization_Model_Resource_Personalized_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

	private $_quote;
	private $_itemsByKey;
	private $_CodeAndQuoteItemIdToItemId;

	public function _construct() {
		$this->_init('product_personalization/personalized_item');
	}

	public function getQuote() {
		if (empty($this->_quote)) {
			$this->_quote = Mage::getModel('checkout/session')->getQuote();
		}
		return $this->_quote;
	}

	public function getItemsByCode() {
		$items = array();
		foreach ($this->getItems() as $item) {
			$items[$item->getCode()] = $item;
		}
		return $items;
	}

	public function getItemsByGroup() {
		$items = array();
		foreach ($this->getItems() as $item) {
			if (!isset($items[$item->getGroupLabel()]['label'])) {
				$items[$item->getGroupLabel()]['label'] = $item->getGroupLabel();
			}
			$items[$item->getGroupLabel()]['items'][] = $item;
		}
		return $items;
	}

	public function getItemsByIdAsKey() {
		if (empty($this->_itemsByKey)) {
			foreach ($this->getItems() as $item) {
				$this->_itemsByKey[$item->getId()] = $item;
			}
		}
		return $this->_itemsByKey;
	}

	function getItemByCodeAndQuoteItemId($code, $itemId) {
		if (isset($this->_CodeAndQuoteItemIdToItemId[$code][$itemId])) {
			$items = $this->getItemsByIdAsKey();
			return isset($items[$this->_CodeAndQuoteItemIdToItemId[$code][$itemId]]) ? $items[$this->_CodeAndQuoteItemIdToItemId[$code][$itemId]] : false;
		} else {
			foreach ($this->getItems() as $item) {
				if ($item->getCode() == $code && $item->getItemId() == $itemId) {
					$this->_CodeAndQuoteItemIdToItemId[$code][$itemId] = $item->getId();
					return $item;
				}
			}
		}
		return false;
	}

	public function filterByCodes( array $elementIdentifierCode) {
		$this->getSelect()->where('code IN (?)', $elementIdentifierCode);
		return $this;
	}

	public function filterByQuote() {
		$this->getSelect()->where('quote_id=?', $this->getQuote()->getId());
		return $this;
	}
}