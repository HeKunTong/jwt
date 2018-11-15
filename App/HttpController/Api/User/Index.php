<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 上午11:57
 */

namespace App\HttpController\Api\User;

use App\Model\User\UserBean;
use App\Model\User\UserModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\Message\Status;
use EasySwoole\Spl\SplBean;

class Index extends UserBase
{
    /**
     * @api {get|post} api/user/login
     * @apiName 登录
     * @apiDescription 帐号密码登录
     * @apiParam {String} account 帐号
     * @apiParam {String} password 密码
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "data": {}, "msg": "success"}
     * @author: blank < 1161709455@qq.com >
     */
    function login() {
        $fields = ['account', 'password'];
        if ($this->requireFiled($fields)) {
            $params = $this->request()->getRequestParam();
            $bean = new UserBean($params);
            $model = new UserModel($this->getDbConnection());
            $user = $model->getOne($bean);
            if ($user) {
                if ($params['account'] == $user->getAccount() && md5($params['password']) == $user->getPassword()) {
                    //这里有问题，当经过proxy的时候，会全部变为127.0.0.1，因此需要修改
                    $fd = $this->request()->getSwooleRequest()->fd;
                    $ip = ServerManager::getInstance()->getSwooleServer()->connection_info($fd);
                    //需要nginx 添加  proxy_set_header X-Real-IP $remote_addr;
                    if($ip['remote_ip'] == '127.0.0.1'){
                        $heads = $this->request()->getHeaders();
                        $ip['remote_ip'] = $heads['x-real-ip'] ? : '';
                    }
                    $data = [
                        'lastLoginTime' => time(),
                        'lastLoginIp' => $ip['remote_ip']
                    ];
                    $user->setLastLoginTime($data['lastLoginTime']);
                    $user->setLastLoginIp($data['lastLoginIp']);
                    $result = $model->update($user, $data);
                    if ($result) {
                        $user = $user->toArray(null, SplBean::FILTER_NOT_NULL);
                        unset($user['password']);
                        $token = $this->getToken($user);

                        $this->writeJson(Status::CODE_OK, ['token' => $token, 'expireTime' => time() + Config::getInstance()->getConf('JWT.ttl')], 'success');
                    } else {
                        $this->writeJson(Status::CODE_BAD_REQUEST, null, 'fail');
                    }
                } else {
                    $this->writeJson(Status::CODE_BAD_REQUEST, null, 'fail');
                }
            } else {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, 'fail');
            }
        }
    }

    function info() {
        $this->writeJson(Status::CODE_OK, 1, 'success');
    }
}