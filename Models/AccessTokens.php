<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Utils\DateTime;

use Arikaim\Core\Traits\Db\Uuid;
use Arikaim\Core\Traits\Db\Find;
use Arikaim\Core\Traits\Db\DateCreated;

use Arikaim\Core\Db\Model as DbModel;

/**
 * Access tokens database model
*/
class AccessTokens extends Model 
{
    const PAGE_ACCESS_TOKEN  = 0;
    const LOGIN_ACCESS_TOKEN = 1;

    use Uuid,
        Find,
        DateCreated;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'token',
        'date_expired',
        'user_id',
        'type'
    ];

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Expired mutator attribute
     *
     * @return void
     */
    public function getExpiredAttribute()
    {
        if ($this->date_expired == -1) {
            return false;
        }
        return (DateTime::getCurrentTime() > $this->date_expired || empty($this->date_expired) == true) ? true : false;
    }

    /**
     * Create access token
     *
     * @param integer $user_id
     * @param integer $type
     * @param integer $expire_period
     * @return Model|false
     */
    public function createToken($user_id, $type = AccessTokens::PAGE_ACCESS_TOKEN, $expire_time = 1800, $delete_expired = true)
    {
        $expire_time = ($expire_time < 1000) ? 1000 : $expire_time;
        $date_expired = DateTime::getCurrentTime() + $expire_time;
        $token = ($type == Self::LOGIN_ACCESS_TOKEN) ? Utils::createRandomKey() : Utils::createUUID();

        if ($delete_expired == true) {          
            $result = $this->deleteExpired($user_id,$type);
        }
        
        $model = $this->getTokenByUser($user_id,$type);
        if (is_object($model) == true) {
            return $model;
        }

        $info = [
            'user_id'      => $user_id,
            'token'        => $token,
            'date_expired' => $date_expired,
            'type'         => $type
        ];
        $model = $this->create($info);

        return (is_object($model) == true) ? $model : false;
    }

    /**
     * Remove access token
     *
     * @param string $token
     * @return boolean
     */
    public function removeToken($token)
    {
        $model = $this->findByColumn($token,['uuid','token']);
        if (is_object($model) == true) {
            return $model->delete();
        }
        return true;
    }

    /**
     * Get access token
     *
     * @param  string $token
     * @return string|null
     */
    public function getToken($token)
    {      
        $model = $this->findByColumn($token,'token');
        return (is_object($model) == true) ? $model : null;
    }

    /**
     * Return true if token is expired
     *
     * @param string $token
     * @return boolean
     */
    public function isExpired($token)
    {
        $model = $this->findByColumn($token,'token');
        if (is_object($model) == false) {
            return true;
        }
        if ($model->date_expired == -1) {
            return false;
        }

        return (DateTime::getCurrentTime() > $model->date_expired || empty($model->date_expired) == true) ? true : false;
    }

    /**
     * Find token
     *
     * @param integer $user_id
     * @param integer $type
     * @return mxied
     */
    public function getTokenByUser($user_id, $type = AccessTokens::PAGE_ACCESS_TOKEN)
    {
        return $model = $this->where('user_id','=',$user_id)->where('type','=',$type)->first();
    }

    /**
     * Return true if token exist
     *
     * @param integer $user_id
     * @param integer $type
     * @return boolean
     */
    public function hasToken($user_id, $type = AccessTokens::PAGE_ACCESS_TOKEN)
    {    
        return is_object($this->getTokenByUser($user_id,$type));
    }

    /**
     * Delete expired token
     *
     * @param integer $user_id
     * @param integer|null $type
     * @return void
     */
    public function deleteExpired($user_id, $type = AccessTokens::PAGE_ACCESS_TOKEN)
    {
        $model = $this->where('date_expired','<',DateTime::getCurrentTime())
            ->where('date_expired','<>',-1)
            ->where('user_id','=', $user_id);
        
        if ($type != null) {
            $model = $model->where('type','=',$type);
        }
        return $model->delete();
    }

    /**
     * Delete all expired tokens
     *
     * @return bool
     */
    public function deleteExpiredTokens()
    {
        return $this->where('date_expired','<',DateTime::getCurrentTime())->where('date_expired','<>',-1)->delete();
    }

    /**
     * Get all tokens for user
     *
     * @param integer $user_id
     * @return null|Model
     */
    public function getUserTokens($user_id)
    {
        return $this->where('user_id','=',$user_id)->get();
    }
}