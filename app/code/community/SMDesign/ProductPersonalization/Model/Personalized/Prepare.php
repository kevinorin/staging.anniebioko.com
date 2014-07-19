<?php 
class SMDesign_ProductPersonalization_Model_Personalized_Prepare extends Mage_Core_Model_Abstract {

	protected $postData;
	protected $_item;

	private function _preparePostData() {

		$postData = array();

		foreach ((array)Mage::app()->getRequest()->getParam('product_personalization') as $groupKey=>$groupData){
			foreach ($groupData as $elementKey=>$elementData){
				foreach ($elementData as $itemId=>$elementValue){
					$itemId = ((1 > $itemId) ? $this->getItem()->getId() : $itemId);
					$postData["$groupKey/$elementKey/$itemId"] = $elementValue;
				}
			}
		}

		if (isset($_FILES['product_personalization'])) {
			foreach ($_FILES['product_personalization']['name'] as $groupKey => $fileMethodParm) {
				foreach ($fileMethodParm as $elementKey => $fieldValues) {
					foreach ($fieldValues as $itemIdKey => $fieldValue) {
						$itemId = ((1 > $itemIdKey) ? $this->getItem()->getId() : $itemIdKey);
						$postData["$groupKey/$elementKey/$itemId"] = array(
							'name'		=> $_FILES['product_personalization']['name'][$groupKey][$elementKey][$itemIdKey],
							'type'		=> $_FILES['product_personalization']['type'][$groupKey][$elementKey][$itemIdKey],
							'tmp_name'	=> $_FILES['product_personalization']['tmp_name'][$groupKey][$elementKey][$itemIdKey],
							'error'		=> $_FILES['product_personalization']['error'][$groupKey][$elementKey][$itemIdKey],
							'size'		=> $_FILES['product_personalization']['size'][$groupKey][$elementKey][$itemIdKey],
							);
					}
				}
			}
		}
		return $postData;
	}

	public function setItem(Mage_Sales_Model_Quote_Item $_item) {
		$this->_item = $_item;
		return $this;
	}

	public function getItem() {
		return $this->_item;
	}

	public function getPostData() {
		if (empty($this->postData)) {
			$this->postData = $this->_preparePostData();
		}
		return $this->postData;
	}

	public function preparePersonalizationObjectToSave() {

		$_item = $this->getItem();
		if ($_item->getProduct()->getPersonalization()) {
			$postData = $this->getPostData();

			$personalizationItems = $_item->getPersonalizationData();
			foreach ($personalizationItems as $code=>$object) {

				if ($object->getField()->frontend_type == 'file' && is_array($postData[$code])) {

					if ($postData[$code]['error'] === 0) {

						$uploader = new Varien_File_Uploader( $postData[$code] );

						if (isset($object->getField()->allowed_file_types)) {
							$uploader->setAllowedExtensions( explode(',', str_replace(' ', '', $object->getField()->allowed_file_types)) );
						}

						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);

						$uploader->save($object->getFileSoragePath(), $postData[$code]['name'] );
						$object->setValue( $postData[$code]['name'] );
					}

				} else if ($object->getField()->frontend_type == 'multiselect' && isset($postData[$code]) && is_array($postData[$code])) {
					$object->setValue( join(',', $postData[$code]) );
				} else if ($object->getField()->frontend_type == 'checkbox') {
					$object->setValue(isset($postData[$code]) ? 'Checked' : 'Not checked');
				} else {
					$object->setValue($postData[$code]);
				}
			}
		}
		return $this;
	}
}