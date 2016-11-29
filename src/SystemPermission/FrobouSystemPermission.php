<?php

namespace Frobou\SystemPermission;

use Frobou\Db\FrobouDbConfig;
use Frobou\Db\FrobouDbConnection;

class FrobouSystemPermission
{
    /**
     * @var FrobouDbConnection
     */
    private $connection;

    public function __construct($config_file)
    {
        $config = new FrobouDbConfig(json_decode(file_get_contents($config_file)));
        $this->connection = new FrobouDbConnection($config);
    }

    public function createUser(SystemUser $user)
    {
        $user = $this->connection->insert($user->getInsertString(), null, $this->connection->utils->bindParams($user->getSqlParams()));
        return $user;
    }

    public function updateUser(SystemUser $user, array $where)
    {
        $user = $this->connection->update($user->getUpdateString($where), null, $this->connection->utils->bindParams($user->getSqlParams()));
        //todo: usar afected rows pra saber se foi alterado ou nao
        return $user;
    }

    public function createGroup($name)
    {
        $params = [];
        $query = 'INSERT INTO system_group (group_name) VALUES (:name)';
        array_push($params, ['param' => ':name', 'value' => $name, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->insert($query, null, $params);
        return $user;
    }

    public function createResource($name, $permission)
    {
        $params = [];
        $query = 'INSERT INTO system_resources (name, permission) VALUES (:name, :permission)';
        array_push($params, ['param' => ':name', 'value' => $name, 'type' => \PDO::PARAM_STR]);
        array_push($params, ['param' => ':permission', 'value' => $permission, 'type' => \PDO::PARAM_INT]);
        $user = $this->connection->insert($query, null, $params);
        return $user;
    }

    private function getGroupPerms($id)
    {
        $query = "SELECT sr.name, sr.permission
FROM group_resources gr
INNER JOIN system_resources sr ON gr.system_resources_id = sr.id
WHERE gr.system_group_id = {$id} group by sr.name";
        return $this->connection->select($query);
    }

    private function getUserPerms($id)
    {
        $query = "SELECT sr.name, sr.permission
FROM user_resources ur
INNER JOIN system_resources sr ON ur.system_resources_id = sr.id
WHERE ur.system_user_id = {$id} group by sr.name";
        return $this->connection->select($query);
    }

    private function getUser($data)
    {
        $user = new SystemUser();
        $perms = [];
        $u_per = $this->getUserPerms($data->id);
        if (count($u_per) === 0) {
            $u_per = $this->getGroupPerms($data->id);
        }
        if (defined('MERGE_PERMISSIONS') && MERGE_PERMISSIONS === true) {
            $g_per = $this->getGroupPerms($data->id);
            foreach ($g_per as $value) {
                $perms[$value->name] = intval($value->permission);
            }
        }
        foreach ($u_per as $value) {
            $perms[$value->name] = intval($value->permission);
        }
        $user->setActive($data->active)->setAvatar($data->avatar)->setCanEdit($data->can_edit)->setCanLogin($data->can_login)
            ->setCanUseApi($data->can_use_api)->setCanUseWeb($data->can_use_web)->setDeleteDate($data->delete_date)
            ->setEmail($data->email)->setName($data->name)->setSystemGroup($data->system_group_id)->setSystemResources($perms)
            ->setUpdateDate($data->update_date)->setUsername($data->username)->setUserType($data->user_type)
            ->setCreateDate($data->create_date);
        return $user;
    }

    public function login($username, $password)
    {
        $params = [];
        $query = "SELECT id, username, password, name, email, avatar, active, can_edit, can_edit, can_login, 
can_use_web, can_use_api, delete_date, system_group_id, update_date, user_type, create_date 
from system_user where active = 1 and username = :username";
        array_push($params, ['param' => ':username', 'value' => $username, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->select($query, null, $params);
        if (count($user) === 1) {
            if (!defined('PASSWORD_SALT')) {
                define('PASSWORD_SALT', 'default');
            }
            if (!password_verify(md5($username) . PASSWORD_SALT . $password, $user[0]->password)) {
                return false;
            }
            return $this->getUser($user[0]);
        }
        return null;
    }

    public function getResourcePermission(SystemUser $user, $resource)
    {
        $perms = $user->getSystemResources();
        if (array_key_exists($resource, $perms)) {
            return $perms[$resource];
        }
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