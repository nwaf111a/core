<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Template;

use Arikaim\Core\Utils\File;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Http\Session;

/**
 * Html Template
*/
class Template
{
    const SYSTEM_TEMPLATE_NAME = 'system';
    const DEFAULT_TEMPLATE_NAME = 'blog';
    
    /**
     * Return template files
     *
     * @return array
     */
    public static function getTemplateFiles()
    {
        return  Arikaim::page()->properties()->get('template.files');
    }
    
    /**
     * Return theme files
     *
     * @return array
     */
    public static function getThemeFiles()
    {      
        return Arikaim::page()->properties()->get('template.theme');
    }
    
    /**
     * Return library files
     *
     * @return array
     */
    public static function getLibraryFiles()
    {
        return Arikaim::page()->properties()->get('ui.library.files',[]);
    }

    /**
     * Return libraries
     *
     * @return array
     */
    public static function getLibraries()    
    {
        return Session::get("ui.included.libraries");
    }

    /**
     * Get template loader component name
     *
     * @return string|null
     */
    public static function getLoader()
    {
        return Session::get("template.loader");
    }

    /**
     * Return UI frameworks
     *
     * @return array
     */
    public static function getFrameworks()    
    {
        return Session::get("ui.included.frameworks");
    }

    /**
     * Return current template name
     *
     * @return void
     */
    public static function getTemplateName()     
    {           
        try {            
            return Arikaim::options()->get('current.template',Self::DEFAULT_TEMPLATE_NAME);               
        } catch(\Exception $e) {
            return Self::DEFAULT_TEMPLATE_NAME;
        }
    }
    
    /**
     * Return current language
     *
     * @return string
     */
    public static function getLanguage() 
    {  
        $language = Session::get('language');
        if (empty($language) == true) { 
            try {
                $language = Arikaim::config('settings/defaultLanguage');
                $language = (empty($language) == true) ? "en" : $language;

                Session::set('language',$language);
            } catch(\Exception $e) {
                $$language = 'en';
                Session::set('language',$language);
            }                 
        }       

        return $language;
    }
    
    /**
     * Set current language
     *
     * @param string $language Language code
     * @return string
     */
    public static function setLanguage($language) 
    {
        Session::set('language',$language);
    
        return $language;
    }

    /**
     * Set current front end framework.
     *
     * @param string $library UI library name
     * @return void
     */
    public static function setCurrentFramework($library)
    {
        Session::set("current.framework",$library);
    }

    /**
     * Return current front end framework used in page
     *
     * @return string
     */
    public static function getCurrentFramework()
    {
        $framework = Session::get("current.framework");
        if (empty($framework) == true || $framework == null) {
            $frameworks = json_decode(Self::getFrameworks());
            $frameworks = (is_array($frameworks) == true) ? $frameworks : [];
            $framework = end($frameworks);
            Self::setCurrentFramework($framework);
        }

        return $framework;
    }

    /**
     * Scan directory and return components list
     *
     * @param string $path
     * @return array
     */
    public static function getComponents($path)
    {       
        if (File::exists($path) == false) {
            return [];
        }
        $items = [];
        $dir = new \RecursiveDirectoryIterator($path,\RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dir,\RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if ($file->isDir() == true) {
                $item['name'] = $file->getFilename();   
                $item['path'] = $file->getPathname();
                
                $componentPath = str_replace($path,'',$file->getRealPath());                
                $componentPath = str_replace(DIRECTORY_SEPARATOR,'.',$componentPath);
               
                $item['full_name'] = $componentPath;
                array_push($items,$item);
            }
        }

        return $items;
    }

    /**
     * Scan directory and return pages list
     *
     * @param string $path
     * @return array
     */
    public static function getPages($path)
    {
        if (File::exists($path) == false) {
            return [];
        }
        $items = [];
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() == true) continue;
            if ($file->isDir() == true) {
                $item['name'] = $file->getFilename();
                array_push($items,$item);
            }
        }

        return $items;
    }

    /**
     * Scan directory and return macros list
     *
     * @param string $path
     * @return array
     */
    public static function getMacros($path)
    {       
        if (File::exists($path) == false) {
            return [];
        }
        $items = [];
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() == true || $file->isDir() == true) continue;
            
            $fileExt = $file->getExtension();
            if ($fileExt != "html" && $fileExt != "htm") continue;           
            
            $item['name'] = str_replace(".$fileExt",'',$file->getFilename());
            array_push($items,$item);            
        }

        return $items;
    }
}
