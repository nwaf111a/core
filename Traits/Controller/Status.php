<?php
/**
 *  Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Traits\Controller;

use Arikaim\Core\Db\Model;

/**
 * Set status Controller
*/
trait Status 
{        
    /**
     * Set status
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function setStatusController($request, $response, $data)
    {
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {
            $status = $data->get('status',1);                
            $uuid = $data->get('uuid');
            $model = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);
            $result = (is_object($model) == true) ? $model->setStatus($status) : false; 
        
            $this->setResponse($result,function() use($uuid,$status) {              
                $this
                    ->message('status')
                    ->field('uuid',$uuid)
                    ->field('status',$status);
            },'errors.status');
        });
        $data
            ->addRule('status','checkList:items=0,1,toggle')
            ->validate(); 
    }
}
