<?php

class Display_Allmembers_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    protected $_orderedLinks = array();
    protected $_customLoaded = false;

    public function addOrderedLink($name, $path, $label, $urlParams = array())
    {
        if (isset($this->_orderedLinks[$name])) {
            throw new Exception('Link with name ' . $name . ' exists already.');
        }
        $this->_orderedLinks[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ));
        return $this;
    }

    public function getLinks()
    {
        $this->_loadCustomLinks();
        return count($this->_orderedLinks) ? $this->_orderedLinks : $this->_links;
    }

    protected function _loadCustomLinks()
    {
        if ($this->_customLoaded) {
            return $this;
        }

        $update = Mage::getStoreConfig('cms/my_account_menu/links_config');
        $update = '<reference name="customer_account_navigation">' . $update
                . '</reference>';
        $update = Mage::getModel('core/layout_element', $update);
        $this->getLayout()->generateBlocks($update);

        $this->_customLoaded = true;
        return $this;
    }
}
