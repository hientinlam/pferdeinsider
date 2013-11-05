<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Brst_Member_Block_Adminhtml_Grid_Renderer_Mail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $emailaddress = "subesh@subesh.com";
        return '<a href="mailto:'.$emailaddress.'">Mail</a>';
    }
}
?>
