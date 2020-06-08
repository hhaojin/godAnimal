<?php

namespace App\Model;

use Core\Model\DBModel;

/**
 * Class User
 * @package App\Model
 */
class User extends DBModel
{
    protected $table = 'user';
    protected $primaryKey = 'id';

    public function userInfo()
    {
        return $this->hasOne('App\Model\UserInfo','user_id');
    }
}
