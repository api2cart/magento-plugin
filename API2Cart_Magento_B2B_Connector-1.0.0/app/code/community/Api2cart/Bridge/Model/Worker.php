<?php

class Api2cart_Bridge_Model_Worker extends Mage_Core_Model_Abstract {

    protected $bridge_tmp;
    protected $bridge_dir;
    protected $bridge_file;
    protected $bridge_config_file;
    protected $downloadBridgeUrl = 'http://api.api2cart.com/v1.0/bridge.download.file';
    protected $extractEntities = array(
        'bridge2cart/',
        'bridge2cart/bridge.php',
        'bridge2cart/config.php'
    );

    public function __construct()
    {
        $this->bridge_tmp         = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'bridge.zip';
        $this->bridge_dir         = Mage::getBaseDir() . DIRECTORY_SEPARATOR . 'bridge2cart';
        $this->bridge_file        = $this->bridge_dir . DIRECTORY_SEPARATOR . 'bridge.php';
        $this->bridge_config_file = $this->bridge_dir . DIRECTORY_SEPARATOR . 'config.php';

        parent::__construct();
    }

    public function installBridge()
    {
        if ($this->isExistBridge()) {
            return;
        }

        $res = @file_put_contents($this->bridge_tmp, file_get_contents($this->downloadBridgeUrl));
        if (!$res) {
            Mage::throwException(Mage::helper('core/translate')->__('Can\'t write file to ' . $this->bridge_tmp));
        }
        if (!$this->_extract()) {
            Mage::throwException(Mage::helper('core/translate')->__('Can\'t extract zip file ' . $this->bridge_tmp));
        }

        $this->_removeTmpFiles();
    }

    protected function _removeTmpFiles()
    {
        @unlink($this->bridge_tmp);
    }

    protected function _extract()
    {
        $zip = new ZipArchive;
        $res = $zip->open($this->bridge_tmp);

        if ($res !== true) {
            return false;
        }

        $res = $zip->extractTo(Mage::getBaseDir() . DIRECTORY_SEPARATOR, $this->extractEntities);

        if ($res !== true) {
            return false;
        }

        $zip->close();

        return true;
    }

    public function removeBridge()
    {
        if ($this->isExistBridge()) {
            $this->_deleteDir($this->bridge_dir);
        }

        return true;
    }

    protected function _deleteDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir ."/". $object))
                        $this->_deleteDir($dir ."/". $object);
                    else
                        unlink($dir ."/". $object);
                }
            }
            rmdir($dir);
        }
    }

    public function isExistBridge()
    {
        return is_readable($this->bridge_file)
               && is_readable($this->bridge_config_file);
    }

    public function readStoreKey()
    {
        if (is_readable($this->bridge_config_file)) {
            require_once($this->bridge_config_file);
            return M1_TOKEN;
        }

        return false;
    }

    public function updateStoreKey()
    {
        if (!$this->isExistBridge()) {
            return false;
        }

        $fp = @fopen($this->bridge_config_file, 'w');

        if ($fp) {
            $token = $this->generateStoreKey();
            $res = fwrite($fp, "<?php define('M1_TOKEN', '" . addslashes($token) . "');");
            fclose($fp);
            if ($res > 0) {
                return true;
            }
        }

        return false;
    }

    public function generateStoreKey()
    {
        return md5('app_' . time());
    }
}