<?php

namespace Frobou\SystemPermission;

use Frobou\Pdo\Db\FrobouPdoConfig;
use Frobou\Pdo\Db\FrobouPdoConnection;

class FrobouSystemPermission
{

    public function __construct()
    {
        $config = new FrobouPdoConfig(json_decode(file_get_contents(__DIR__ . './../database.json')));
        $this->connection = new FrobouPdoConnection($config);
    }

    public function createUser()
    {

        return true;
    }

    public function createGroup()
    {

    }

    public function getPermissions($username, $password)
    {
        $params = [];
        $query = "SELECT username, password, name, email, avatar, active, can_edit, can_edit, can_login, can_use_web, can_use_api from system_user where active = 1 and username = :username";
        array_push($params, ['param' => ':username', 'value' => $username, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->select($query, null, $params);
        return $user;
    }

    public function getPermissionResource($resource)
    {

    }

    public function registerResource()
    {
        return true;
    }

    public function unregisterResource()
    {

    }

    public function listResources($for_user = null)
    {

    }

}