<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 下午2:21
 */

namespace App\Model\User;


use App\Model\BaseModel;
use EasySwoole\Spl\SplBean;

class UserModel extends BaseModel
{
    private $table = 'easyswoole_user';

    function getOne(UserBean $userBean):?UserBean {
        $user = $this->getDb()
            ->where('account', $userBean->getAccount())
            ->where('password', md5($userBean->getPassword()))
            ->getOne($this->table);
        return empty($user) ? null : new UserBean($user);
    }

    function add(UserBean $userBean):bool {
        return $this->getDb()->insert($this->table, $userBean->toArray(null, SplBean::FILTER_NOT_NULL));
    }

    function update(UserBean $userBean, $data):bool {
        return $this->getDb()->where('id', $userBean->getId())->update($this->table, $data);
    }
}