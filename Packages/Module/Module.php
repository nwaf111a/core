<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
 */
namespace Arikaim\Core\Packages\Module;

use Arikaim\Core\Interfaces\ModuleInterface;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Arikaim;

/**
 * Base class for Arikaim modules.
 */
class Module implements ModuleInterface
{
    /**
     * Module config
     *
     * @var array
     */
    protected $config = [];

    /**
     * Service container item name
     *
     * @var string|null
     */
    protected $service_name;
    
    /**
     * Bootable
     *
     * @var bool
     */
    protected $bootable;

    /**
     * test error
     *
     * @var string|null
     */
    protected $error = null;

    /**
     * Install module
     *
     * @return bool
     */
    public function install()
    {
        return true;        
    }

    /**
      * Install driver
      *
      * @param string|object $name Driver name, full class name or driver object ref
      * @param string|null $class
      * @param string|null $category
      * @param string|null $title
      * @param string|null $description
      * @param string|null $version
      * @param array $config
      * @return boolean|Model
    */
    public function installDriver($name, $class = null, $category = null, $title = null, $description = null, $version = null, $config = [])
    {
        return Arikaim::driver()->install($name,$class,$category,$title,$description,$version,$config);
    }

    /**
     * Boot module
     *
     * @return bool
     */
    public function boot()
    {        
        return true;
    }
    
    /**
     * Get service container item name
     *
     * @return string|null
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set service container item name
     *
     * @param string $name
     * @return void
     */
    public function setServiceName($name)
    {
        return $this->service_name = $name;
    }

    /**
     * Return true if module is bootable
     *
     * @return boolean
     */
    public function isBootable()
    {
        return ($this->bootable == true) ? true : false; 
    }

    /**
     * Set module bootable
     *
     * @param boolean $bootable
     * @return void
     */
    public function setBootable($bootable = true)
    {
        $this->bootable = $bootable;
    }

    /**
     * Test module function
     * 
     * @return bool
     */
    public function test()
    {        
        return true;
    }

    /**
     * Get test error
     *
     * @return string
     */
    public function getTestError()
    {
        return $this->error;
    }

    /**
     * Set module config
     * @param array $config
     * @return void
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    /**
     * Get module config
     *
     * @param string|null $key
     * @return array
     */
    public function getConfig($key = null)
    {
        if (empty($key) == true) {
            return $this->config;
        }
        return (isset($this->config[$key]) == true) ? $this->config[$key] : null;
    }

    /**
     * Load module config
     *
     * @param string $name
     * @return bool
     */
    protected function loadConfig($name)
    {
        $model = Model::Modules()->findByColumn($name,'name');
        if (is_object($model) == true) {
            $this->setConfig($model->config);
            return true;
        } 
        return false;
    }
}