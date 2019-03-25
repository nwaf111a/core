<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Packages\Template;

use Arikaim\Core\Db\Model;
use Arikaim\Core\System\Path;
use Arikaim\Core\Packages\PackageManager;
use Arikaim\Core\Packages\Template\TemplatePackage;
use Arikaim\Core\Arikaim;

/**
 * Manage templates
*/
class TemplatesManager extends PackageManager
{
    public function __construct()
    {
       parent::__construct(Path::TEMPLATES_PATH,'template.json');
    }

    public function getPackages($cached = false, $filter = null)
    {
        $result = ($cached == true) ? Arikaim::cache()->fetch('templates.list') : null;
        
        if (is_array($result) == false) {
            $result = $this->scan($filter);
            Arikaim::cache()->save('templates.list',$result,5);
        } 
        return $result;
    }

    public function createPackage($template_name)
    {
        $propertes = $this->loadPackageProperties($template_name);
        return new TemplatePackage($propertes);
    }

    /**
     * Return template routes
     *
     * @param string $template_name Template name
     * @return void
     */
    public function getRoutesList($template_name)
    {
        $model = Model::Routes();
        $package = $this->createPackage($template_name);
        $routes = $package->getRoutes();

        if (is_array($routes) == false) {
            return [];
        }

        foreach ($routes as $key => $item) {
            $routes[$key]['method'] = "GET";
            $route = $model->getTemplateRoute($routes[$key]['path'],$template_name);
            if ($route != false) {
                $routes[$key]['status'] = $route->status;
                $routes[$key]['auth'] = $route->auth;
            } else {
                $routes[$key]['status'] = 0;
                $routes[$key]['auth'] = 0;
            }
        }
        return $routes;
    }
}