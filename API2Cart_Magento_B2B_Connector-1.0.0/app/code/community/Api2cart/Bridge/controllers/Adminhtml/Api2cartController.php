<?php

class Api2cart_Bridge_Adminhtml_Api2cartController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @var Api2cart_Bridge_Model_Worker
     */
    protected $worker;

    public function _construct()
    {
        $this->worker = Mage::getModel('api2cart_bridge/worker');
        parent::_construct();
    }

    public function connectAction()
    {
        echo Zend_Json::encode($this->_call('installBridge'));
    }

    public function disconnectAction()
    {
        echo Zend_Json::encode($this->_call('removeBridge'));
    }

    public function updateTokenAction()
    {
        echo Zend_Json::encode($this->_call('updateStoreKey'));
    }

    /**
     * @param $action
     * @return array
     */
    protected function _call($action)
    {
        try {
            $this->worker->$action();
        } catch (Mage_Core_Exception $e) {
            return array('code' => 500, 'msg' => $this->__($e->getMessage()));
        }

        return array('code' => 200, 'token' => $this->worker->readStoreKey());
    }

}