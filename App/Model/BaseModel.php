<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 下午2:18
 */

namespace App\Model;

use App\Utility\Pool\MysqlObject;

class BaseModel
{
    private $db;

    function __construct(MysqlObject $db)
    {
        $this->db = $db;
    }

    function getDb():MysqlObject {
        return $this->db;
    }
}