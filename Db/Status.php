<?php
/**
 *  Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Db;

/**
 * Update Status field
*/
trait Status 
{        
    public static function ACTIVE()
    {
        return 1;
    }

    public static function DISABLED()
    {
        return 0;
    }

    public function getActive()
    {
        return parent::where('status','=',Self::ACTIVE());
    }
    
    public function getDisabled()
    {
        return parent::where('status','=',Self::DISABLED());
    }
}