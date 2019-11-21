<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Library;

use Arikaim\Core\App\Path;
use Arikaim\Core\App\Url;
use Arikaim\Core\Packages\PackageManager;
use Arikaim\Core\Packages\Library\LibraryPackage;
use Arikaim\Core\Arikaim;

/**
 * Ui library package manager
*/
class LibraryManager extends PackageManager
{
    /**
     * Constructor
     */
    public function __construct()
    {
       parent::__construct(Path::LIBRARY_PATH);
    }

    /**
     * Create ui library package
     *
     * @param string $name
     * @return LibraryPackage
     */
    public function createPackage($name)
    {
        $propertes = $this->loadPackageProperties($name);
       
        return new LibraryPackage($propertes);
    }

    /**
     * Get packages
     *
     * @param boolean $cached
     * @param mixed $filter
     * @return array
     */
    public function getPackages($cached = false, $filter = null)
    {
        $result = ($cached == true) ? Arikaim::cache()->fetch('library.list') : null;
        
        if (is_array($result) == false) {
            $result = $this->scan($filter);
            Arikaim::cache()->save('library.list',$result,5);
        } 

        return $result;
    }

    /**
     * Get library theme file Url
     *
     * @param string $library
     * @param string $theme
     * @return string
     */
    public function getThemeFileUrl($library, $theme)
    {
        $library = $this->createPackage($library);
        if (is_object($library) == false) {
            return false;
        }
        $file = $library->getThemeFile($theme);
        if (empty($file) == true) {
            return false;
        }

        return Url::getLibraryThemeUrl($library,$theme) . $file;
    }
}
