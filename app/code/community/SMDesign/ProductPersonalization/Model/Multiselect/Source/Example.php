<?php
class SMDesign_ProductPersonalization_Model_Multiselect_Source_Example {

    public function toOptionArray() {
        return array(
            array('value' => 1, 'label'=>Mage::helper('product_personalization')->__('Select an option 1)')),
            array('value' => 2, 'label'=>Mage::helper('product_personalization')->__('Select an option 2)')),
            array('value' => 3, 'label'=>Mage::helper('product_personalization')->__('Select an option 3)')),
            array('value' => 4, 'label'=>Mage::helper('product_personalization')->__('Select an option 4)')),
        );
    }

    public function toArray() {
        return array(
            1 => Mage::helper('product_personalization')->__('Select an option 1)'),
            2 => Mage::helper('product_personalization')->__('Select an option 2)'),
            3 => Mage::helper('product_personalization')->__('Select an option 3)'),
            4 => Mage::helper('product_personalization')->__('Select an option 4)'),
        );
    }

}