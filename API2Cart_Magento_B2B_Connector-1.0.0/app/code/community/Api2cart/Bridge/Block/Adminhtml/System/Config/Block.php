<?php

class Api2cart_Bridge_Block_Adminhtml_System_Config_Block extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('api2cart/bridge/config/block.phtml');
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Include css and js in head if section is bridge
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'api2cart') {
            $this->getLayout()
                ->getBlock('head')
                ->addCss('api2cart_bidge/styles.css')
                ->addJs('api2cart_bidge/jquery-1.10.2.min.js')
                ->addJs('api2cart_bidge/noconflict.js')
                ->addJs('api2cart_bidge/script.js');
        }
        parent::_prepareLayout();
    }

    public function storeKey()
    {
        /**
         * @var Api2cart_Bridge_Model_Worker $worker
         */
        $worker = Mage::getModel('api2cart_bridge/worker');
        return $worker->readStoreKey();
    }

    public function isExistBridge()
    {
        /**
         * @var Api2cart_Bridge_Model_Worker $worker
         */
        $worker = Mage::getModel('api2cart_bridge/worker');
        return $worker->isExistBridge();
    }

}
