<?php
class SMDesign_ProductPersonalization_Model_Calendar_Style {

    public function toOptionArray() {
        return array(
			array('value' => 'calendar-blue.css', 'label'=>Mage::helper('product_personalization')->__('Blue')),
			array('value' => 'calendar-blue2.css', 'label'=>Mage::helper('product_personalization')->__('Blue 2')),
			array('value' => 'calendar-brown.css', 'label'=>Mage::helper('product_personalization')->__('Brown')),
			array('value' => 'calendar-green.css', 'label'=>Mage::helper('product_personalization')->__('Green')),
            array('value' => 'calendar-system.css', 'label'=>Mage::helper('product_personalization')->__('System')),
            array('value' => 'calendar-tas.css', 'label'=>Mage::helper('product_personalization')->__('Tas')),
            array('value' => 'calendar-win2k-1.css', 'label'=>Mage::helper('product_personalization')->__('Win-2k-1')),
            array('value' => 'calendar-win2k-2.css', 'label'=>Mage::helper('product_personalization')->__('Win-2k-2')),
            array('value' => 'calendar-win2k-cold-1.css', 'label'=>Mage::helper('product_personalization')->__('Win2k-cold-1')),
            array('value' => 'calendar-win2k-cold-2.css', 'label'=>Mage::helper('product_personalization')->__('Win2k-cold-2'))          
        );
    }

    public function toArray() {
		$ret = array();
		foreach (self::toOptionArray()  as $option) {
			$ret[$option['value']] = $option['label'];
		}
        return $ret;
    }

}