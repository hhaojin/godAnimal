<?php
/**
 * Created by PhpStorm.
 * User: haojin
 * Date: 2019/9/4
 * Time: 15:58
 */


namespace App\Model;


use Core\Model\DBModel;

class UserInfo extends DBModel
{
    protected $table = 'user_info';
    protected $primaryKey = 'id';

    public function userInfo()
    {
        return $this->hasOne('user','id','user_id');
    }
}