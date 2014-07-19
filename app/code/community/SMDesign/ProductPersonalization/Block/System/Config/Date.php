<?php
class SMDesign_ProductPersonalization_Block_System_Config_Date extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$date = new Varien_Data_Form_Element_Date;
		$format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

		$data = array(
			'name'      => $element->getName(),
			'html_id'   => $element->getId(),
			'image'     => $this->getSkinUrl('images/grid-cal.gif')
		);
		$date->setData($data);
		$date->setValue($element->getValue(), $format);
		$date->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
		$date->setForm($element->getForm());
		
		if ($element->validate) {
			$date->addClass($element->validate);
		}

		if ( $element->required && ((int)$element->required > 0 || 'true' == (string)$element->required) ) {
			$date->addClass('required-entry');
		}
	
		$html ="<div id=\"{$element->getId()}-container\" class=\"date-entry date-entry-{$element->getId()}\">{$date->getElementHtml()}";
		if ( $element->validate || $element->required ) {
			$html .= "<div id=\"{$element->getId()}-container-advice\"></div>";
			$html .= "<script type=\"text/javascript\">$('{$element->getId()}').advaiceContainer = '{$element->getId()}-container-advice'</script>";
		}
		$html .= "</div>";
        return $html;
    }
}
