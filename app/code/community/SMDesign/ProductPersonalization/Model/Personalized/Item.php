<?php
class SMDesign_ProductPersonalization_Model_Personalized_Item extends Mage_Core_Model_Abstract {

	private $_quote;
	private $_fileUrl;

    public function _construct() {
        parent::_construct();
		$this->_init('product_personalization/personalized_item');
	}

	protected function getQuote() {
		if (empty($this->_quote)) {
			$this->_quote = Mage::getModel('checkout/session')->getQuote();
		}
		return $this->_quote;
	}

	function setQuote(Mage_Sales_Model_Qiote $quote) {
		$this->_quote = $quote;
	}

	function getGroupKey() {

		if (isset($this->_data['group_key'])) {
			return $this->_data['group_key'];
		}

		preg_match('@(?<key>.*)/.*/.*@', $this->getCode(), $match);

		if (isset($match['key'])) {
			return $match['key'];
		}

		return false;
	}

	function getFieldKey() {

		if (isset($this->_data['field_key'])) {
			return $this->_data['field_key'];
		}

		preg_match('@.*/(?<key>.*)/.*@', $this->getCode(), $match);
		if (isset($match['key'])) {
			return $match['key'];
		}

		return false;
	}

	function getFileSoragePath() {
		return Mage::getBaseDir('media') . DS . 'product-personalization' . DS . $this->getGroupKey() . DS . $this->getFieldKey() . DS . $this->getQuoteId() . DS . $this->getItemId();
	}

	function getFileUrl() {
		if (empty($this->_fileUrl)) {
			$this->_fileUrl = ( is_file($this->getFileSoragePath() . DS . $this->getValue()) && file_exists($this->getFileSoragePath() . DS . $this->getValue()) ) ? Mage::getBaseUrl('media') . "product-personalization/{$this->getGroupKey()}/{$this->getFieldKey()}/{$this->getQuoteId()}/{$this->getItemId()}/{$this->getValue()}" : false;
		}
		return $this->_fileUrl;
	}
}