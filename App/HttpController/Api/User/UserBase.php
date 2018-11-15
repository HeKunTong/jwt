<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 上午11:58
 */

namespace App\HttpController\Api\User;


use App\HttpController\BaseWithDb;
use App\Model\User\UserBean;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;
use Firebase\JWT\JWT;

class UserBase extends BaseWithDb
{
    private $who;
    private $whiteList = ['login'];

    function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            //白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }
            //获取登入信息
            return $this->auth();
        }
    }

    /*
     * 查找用户是否登录
     */
    function auth() {
        $header = $this->request()->getHeader('authorization');
        if (!empty($header)) {
            $authorization = $header[0];
            $token = str_replace('Bearer ', '', $authorization);

            try {
                $payload = JWT::decode($token, Config::getInstance()->getConf('JWT.secret'), ['HS256']);

                if ($payload->exp < time()) {
                    $this->writeJson(Status::CODE_BAD_REQUEST, null, 'fail');
                    return false;
                } else {
                    $this->who = new UserBean(json_decode($payload->user, true));
                    return true;
                }
            } catch (\Exception $exception) {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, 'fail');
                return false;
            }
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, 'fail');
            return false;
        }
    }


    function getWho():?UserBean {
        return $this->who;
    }

    function requireFiled(array $fields) {
        $validate = new Validate();
        foreach ($fields as $field) {
            $validate->addColumn("$field")->required('必填');
        }
        if ($this->validate($validate)) {
            return true;
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, $validate->getError()->__toString());
            return false;
        }
    }

    function getToken(array $user) {
        $jwt = Config::getInstance()->getConf('JWT');
        $time = time();
        $payload = [
            'iat' => $time,
            'exp' => $time + 60 * $jwt['ttl'],
            'user' => json_encode($user),
        ];

        return JWT::encode($payload, $jwt['secret']);
    }
}