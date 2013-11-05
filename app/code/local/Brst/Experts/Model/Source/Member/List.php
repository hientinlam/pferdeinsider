<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Brst_Experts_Model_Source_Member_List
{
    //labels
    const Member_List_ALL_LABEL = 'All groups';

    /**
     * @static
     * @return
     */
     public static function toOptionArray()
    {
        $attribute_code=137;
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute_code);
        $attributeOptions = $attribute ->getSource()->getAllOptions();
        foreach ($attributeOptions as $value)
        {
            if($value['label']!='')
            {
              $data[] =array('value' => $value['label'], 'label' => $value['label']);
            }
        }
        return $data;
    }
   

   
}
