<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Db\Prototypes\Column;

use Arikaim\Core\Interfaces\BlueprintPrototypeInterface;

/**
 * Id column prototype class
*/
class UserId implements BlueprintPrototypeInterface
{
    /**
     * Build column
     *
     * @param Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options
     * @return void
     */
    public function build($table,...$options)
    {
        $table->bigInteger('user_id')->unsigned()->nullable(true);     
        $table->foreign('user_id')->references('id')->on('users'); 
        $table->index('user_id');
    }
}