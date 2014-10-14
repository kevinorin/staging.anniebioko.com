<?php
class SMDesign_ProductPersonalization_Block_Personalize_Form_Abstract extends Mage_Core_Block_Template {

	private $_form;
	private $_fieldsets;

    public function __construct() {
        parent::__construct();
		$this->setTemplate('product_personalization/personalize/form.phtml');
    }

	function getFieldset($itemId, $key) {
		if (isset($this->_fieldsets[$itemId][$key])) {
			return $this->_fieldsets[$itemId][$key];
		}
		return false;
	}

	function getAllAvilableFieldset() {
		return $this->_fieldsets[$this->getItem()->getId()];
	}

	private function getForm() {
		if (empty($this->_form)) {
			$this->_form = new Varien_Data_Form();
		}
		return $this->_form;
	}

	function _prepareLayout() {
		parent::_prepareLayout();

		foreach ($this->getItem()->getPersonalizationData() as $code=> $_productPersonalizationItem) {

			$groupKey = $_productPersonalizationItem->getGroupKey();
			$fieldsetKey = $groupKey . '_' . $this->getItem()->getId();
			$element = $_productPersonalizationItem->getField();

			$fieldset = $this->getFieldset($this->getItem()->getId(), $groupKey);
			if (!$fieldset) {
				$fieldset = $this->_fieldsets[$this->getItem()->getId()][$groupKey] = $this->getForm()->addFieldset($fieldsetKey, array('legend'=>$_productPersonalizationItem->getLabel()));
			}

			$field = $fieldset->addField($_productPersonalizationItem->getFieldKey() . '_' . $this->getItem()->getId(), $element->frontend_type, array(
				'label'     => $element->label,
				'name'      => "product_personalization[{$groupKey}][{$_productPersonalizationItem->getFieldKey()}][{$this->getItem()->getId()}]",
				'value'		=> $_productPersonalizationItem->getValue()
			));

			$afterElement = $element->comment;

			if (isset($element->frontend_type) && 'file' === (string)$element->frontend_type ) { //to do need to move in elment render class
				if ($_productPersonalizationItem->getValue() && $_productPersonalizationItem->getFileUrl()) {
					$deleteURL = Mage::getUrl('*/*/delete', array('item_id'=>$_productPersonalizationItem->getId()));
					$afterElement .= "<p class='uploaded-file-options'>
<a href='{$_productPersonalizationItem->getFileUrl()}' target='_blank'>View file</a> | <a  onclick=\"return confirm('Do you really want remove file {$_productPersonalizationItem->getValue()}');\" href='$deleteURL'>Remove file</a>						
</p>";
				}
			}

			if (isset($element->required) && ((int)$element->required > 0 || 'true' == (string)$element->required) ) {
				if (isset($element->frontend_type) && 'file' === (string)$element->frontend_type) {
					if (!$_productPersonalizationItem->getFileUrl()) {
						$field->setRequired(true);
					}
				} else {
					$field->setRequired(true);
				}
			}

			if (isset($element->validate)) {
				$field->addClass($element->validate);
			}

			if (isset($element->size)) {
				$field->setSize($element->valisizedate);
			}

            if ($element->frontend_model) {
				$fieldRenderer = Mage::getBlockSingleton((string)$element->frontend_model);
				$fieldRenderer->setForm($this->getForm());
				$field->setRenderer($fieldRenderer);
			}

			if ('checkbox' === (string)$element->frontend_type) {
				$field->setValue('');
				if ('Checked' == $_productPersonalizationItem->getValue()) {
					$field->setChecked(true);
				}
			}

			if (isset($element->frontend_type) && 'multiselect' === (string)$element->frontend_type && isset($element->can_be_empty)) {
				$field->setCanBeEmpty(true);
			}

			if (!empty($afterElement)) {
				$field->setAfterElementHtml($afterElement);
			}

			if ($element->source_model) {
				// determine callback for the source model
				$factoryName = (string)$element->source_model;
				$method = false;
				if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
					array_shift($matches);
					list($factoryName, $method) = array_values($matches);
				}

				$sourceModel = Mage::getSingleton($factoryName);
				if ($sourceModel instanceof Varien_Object) {
					$sourceModel->setPath($path);
				}
				if ($method) {
					if ($fieldType == 'multiselect') {
						$optionArray = $sourceModel->$method();
					} else {
						$optionArray = array();
						foreach ($sourceModel->$method() as $value => $label) {
							$optionArray[] = array('label' => $label, 'value' => $value);
						}
					}
				} else {
					$optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
				}
				$field->setValues($optionArray);
			}

			$this->setClassName($groupKey);
			$this->setChild('fieldset_' . $groupKey . '_' . $this->getItem()->getId() , $fieldset);
		}
		return $this;
	}
}