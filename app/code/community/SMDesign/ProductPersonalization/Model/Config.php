<?php
class SMDesign_ProductPersonalization_Model_Config extends Mage_Core_Model_Abstract {

	private $_fields;
	private $_xmlConfig;

	private $_personalization;

	const XML_PATH_PRODUCT_PERSONALIZATION_FIELDS = 'personalization';
	
	public function getPersonalizationFields() {
		if (empty($this->_personalization)) {
			$this->_initConfig();
		}
		return $this->_personalization;
	}

    protected function _initConfig() {
        $config = Mage::getConfig()->loadModulesConfiguration('product_personalization.xml');
        $this->_personalization = $config->getNode( self::XML_PATH_PRODUCT_PERSONALIZATION_FIELDS );

    }
	
    public function hasChildren($node, $websiteCode=null, $storeCode=null, $isField=false) {

		if (isset($node->groups)) {
			foreach ($node->groups->children() as $children){
				if ($this->hasChildren($children, $websiteCode, $storeCode)) {
					return true;
				}

			}
		} elseif (isset($node->fields)) {

			foreach ($node->fields->children() as $children){
//				if ($this->hasChildren ($children, $websiteCode, $storeCode, true)) {
					return true;
//				}
			}
		} else {
//                return true;
		}
        
        return false;

    }

    function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null) {
        $moduleName = 'adminhtml';
        if (is_object($sectionNode) && method_exists($sectionNode, 'attributes')) {
            $sectionAttributes = $sectionNode->attributes();
            $moduleName = isset($sectionAttributes['module']) ? (string)$sectionAttributes['module'] : $moduleName;
        }
        if (is_object($groupNode) && method_exists($groupNode, 'attributes')) {
            $groupAttributes = $groupNode->attributes();
            $moduleName = isset($groupAttributes['module']) ? (string)$groupAttributes['module'] : $moduleName;
        }
        if (is_object($fieldNode) && method_exists($fieldNode, 'attributes')) {
            $fieldAttributes = $fieldNode->attributes();
            $moduleName = isset($fieldAttributes['module']) ? (string)$fieldAttributes['module'] : $moduleName;
        }

        return $moduleName;
    }	
}