<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

class Simple_Forum_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_LAYOUT = 'forum/designsettings/layout';

	public function getLayout()
    {
        return Mage::getStoreConfig( self::XML_PATH_LAYOUT );
    }
}

?>