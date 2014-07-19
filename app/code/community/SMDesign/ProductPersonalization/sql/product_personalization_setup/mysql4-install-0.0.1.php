<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('sales_flat_quote_item_personalization')};
CREATE TABLE {$this->getTable('sales_flat_quote_item_personalization')} (
	`entity_id` int(11) unsigned NOT NULL auto_increment,
	`item_id` int(11) NOT NULL default '0',
	`quote_id` int(11) NOT NULL default '0',
	`source_model` varchar(255) NOT NULL default '',
	`backend_label` varchar(255) NOT NULL default '',
	`group_label` varchar(255) NOT NULL default '',
	`label` varchar(255) NOT NULL default '',
	`code` varchar(255) NOT NULL default '',
	`value` text NOT NULL default '',
	PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

# [ INIT NEW ATTRIBUTES ]
$installer->addAttribute('catalog_product', 'personalization', array(
        'group'             => 'General',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Product needs to be personalized',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '1',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'simple,virtual,grouped,configurable,bundle',
        'is_configurable'   => false
    ));

$fieldList = array('personalization');
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (count($applyTo) > 0) {
		$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
}

$productIds = Mage::getModel('catalog/product')->getCollection()->getAllIds();
Mage::getSingleton('catalog/product_action')
	->updateAttributes($productIds, array('personalization' => true), Mage::app()->getStore()->getId());

$installer->endSetup(); 