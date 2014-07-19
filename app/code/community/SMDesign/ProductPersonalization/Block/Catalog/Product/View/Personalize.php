<?php
class SMDesign_ProductPersonalization_Block_Catalog_Product_View_Personalize extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
    }

	function canShowPersonalization() {
		return Mage::getStoreConfig('product_personalization/general/can_show_catalog_product_view') ? true : false;
	}

	function _prepareLayout() {
		parent::_prepareLayout();

		$headBlock = $this->getLayout()->getBlock('head');
		$headBlock->addItem('js_css', 'calendar/' . Mage::getStoreConfig('product_personalization/calendar/calendar_style'));
		$headBlock->addJs('calendar/calendar.js');
		$headBlock->addJs('calendar/calendar-setup.js');
		$headBlock->addCss('css/product-personalization.css');

		$calendarBlock = $this->getLayout()->createBlock(
			'Mage_Core_Block_Html_Calendar',
			'html_calendar',
			array('template' => 'page/js/calendar.phtml')
		);
		$this->setChild('calendar', $calendarBlock);
		
		$config = Mage::getModel('product_personalization/config')->getPersonalizationFields();

		$quoteItemId = Mage::app()->getRequest()->getParam('id', -1);
		if (Mage::registry('product')->getId() != $quoteItemId) { // is edit
			$_item = Mage::getModel('sales/quote_item')->load($quoteItemId);
			$collection = Mage::getModel('product_personalization/personalized_item')->getCollection();
			$collection->getSelect()->where('main_table.quote_id=?', $_item->getQuoteId());
			$collection->getSelect()->where('main_table.item_id=?', $quoteItemId);
			$storedData = $collection->getItemsByCode();
		} else {
			$_item = Mage::getModel('sales/quote_item');
			$_item->setId(null); //need a virtual
			$quoteItemId = -1;
		}

		$personalizationItemData = array();
		foreach ($config->groups->children() as $groupKey=>$group) {
			foreach ($group->fields->children() as $key=>$e) {
				$code = isset($storedData["$groupKey/$key/$quoteItemId"]) ? "$groupKey/$key/$quoteItemId" : "$groupKey/$key/\{item_id\}";
				$object = isset($storedData[$code]) ? $storedData[$code] : Mage::getModel('product_personalization/personalized_item');

				$object->setGroupKey($groupKey);
				$object->setFieldKey($key);
				$object->setField($e);

				$personalizationItemData[$code] = $object->getId() ? $object : $object->setData( array(
					'group_label'	=>	$group->label,
					'source_model'	=>	$e->source_model,
					'label'			=>	$e->label,
					'backend_label'	=>	$e->backend_label,
					'group_key'		=>	$groupKey,
					'field_key'		=>	$key,
					'field'			=>	$e,
					'code'			=>	$code,
					'item_id'		=>	isset($storedData[$code]) ? $storedData[$code]->getItemId() : -1,
					'quote_id'		=>	isset($storedData[$code]) ? $storedData[$code]->getQuoteId() : -1
				));
			}
		}
		$_item->setData('personalization_data', $personalizationItemData);

		$blockForm = $this->getLayout()->createBlock('product_personalization/personalize_form', 'product_personalization_form', array('item'=>$_item));
		$this->setChild('product_personalization_form', $blockForm);

		return $this;
	}

}