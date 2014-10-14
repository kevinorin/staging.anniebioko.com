<?php
class SMDesign_ProductPersonalization_Block_Personalize extends Mage_Core_Block_Template {

	private $_quote;

    public function __construct() {
        parent::__construct();
    }

	public function getQuote() {
		if (empty($this->_quote)) {
			$this->_quote = Mage::getModel('checkout/session')->getQuote();
		}
		return $this->_quote;
	}

	function _prepareLayout() {
		parent::_prepareLayout();

		$headBlock = $this->getLayout()->getBlock('head');
		$headBlock->addItem('js_css', 'calendar/' . Mage::getStoreConfig('product_personalization/calendar/calendar_style'));
		$headBlock->addJs('calendar/calendar.js');
		$headBlock->addJs('calendar/calendar-setup.js');

		$calendarBlock = $this->getLayout()->createBlock(
			'Mage_Core_Block_Html_Calendar',
			'html_calendar',
			array('template' => 'page/js/calendar.phtml')
		);
		$this->setChild('calendar', $calendarBlock);
		foreach ($this->getQuote()->getAllVisibleItems() as $_item) {
			$blockForm = $this->getLayout()->createBlock('product_personalization/personalize_form', 'form_' . $_item->getId(), array('item'=>$_item));
			$this->setChild('form_' . $_item->getId(), $blockForm);
		}

		$personalizationError = Mage::getModel('core/session')->getValidationError();
		Mage::getModel('core/session')->setValidationError(null);
		if ($personalizationError) {
			$globalMessageBlock = $this->getLayout()->getBlock('global_messages');
			if (is_array($personalizationError)) {
				foreach ($personalizationError as $errorMessage) {
					$globalMessageBlock->addNotice( $errorMessage );
				}
			} else {
				$globalMessageBlock->addNotice( $personalizationError );
			}
		}
		return $this;
	}

    public function getProductUrl($item) {

        if ($item->getRedirectUrl()) {
            return $item->getRedirectUrl();
        }

        $product = $item->getProduct();
        $option  = $item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
    }

}