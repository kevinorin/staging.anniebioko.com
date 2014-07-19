<?php 

class SMDesign_ProductPersonalization_Block_Personalize_Form extends SMDesign_ProductPersonalization_Block_Personalize_Form_Abstract {
	
	function _prepareLayout() {
		parent::_prepareLayout();

		// example how to forece an element in each item and fieldset
		/*
		foreach ($this->getAllAvilableFieldset() as $fieldset) {
			
			$field = $fieldset->addField('testing_' . $this->getItem()->getId(), 'text', array(
				'label'     => 'aloha',
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => "product_personalization[general][testing][{$this->getItem()->getId()}]",

			));
			
		}
		*/

        Mage::dispatchEvent('prepare_product_personalization_form', array(
            'item'			=> $this->getItem(),
            'form'			=> $this->getForm(),
            'fieldsets'		=> $this->getAllAvilableFieldset()
        ));
		
		return $this;
	}
	

}