<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 上午11:40
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{
    function index() {
        $this->actionNotFound('index');
    }
}