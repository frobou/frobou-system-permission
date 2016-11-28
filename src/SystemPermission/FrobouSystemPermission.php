<?php

namespace Frobou\SystemPermission;

use Frobou\Pdo\Db\FrobouPdoConfig;
use Frobou\Pdo\Db\FrobouPdoConnection;

class FrobouSystemPermission
{
    /**
     * @var FrobouPdoConnection
     */
    private $connection;

    public function __construct($config_file)
    {
        $config = new FrobouPdoConfig(json_decode(file_get_contents($config_file)));
        $this->connection = new FrobouPdoConnection($config);
    }

    public function createUser()
    {
        return true;
    }

    public function createGroup($name)
    {
        $params = [];
        $query = 'INSERT INTO system_group (group_name) VALUES (:name)';
        array_push($params, ['param' => ':name', 'value' => $name, 'type' => \PDO::PARAM_STR]);
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

    private function returnUser($data)
    {
        $user = new SystemUser();
        $d = $data[0];
        $perms = [];
        $u_per = $this->getUserPerms($d->id);
        if (count($u_per) === 0) {
            $u_per = $this->getGroupPerms($d->id);
        }
        if (defined('MERGE_PERMISSIONS') && MERGE_PERMISSIONS === true) {
            $g_per = $this->getGroupPerms($d->id);
            foreach ($g_per as $value) {
                $perms[$value->name] = intval($value->permission);
            }
        }
        foreach ($u_per as $value) {
            $perms[$value->name] = intval($value->permission);
        }
        $user->setActive($d->active)->setAvatar($d->avatar)->setCanEdit($d->can_edit)->setCanLogin($d->can_login)
            ->setCanUseApi($d->can_use_api)->setCanUseWeb($d->can_use_web)->setDeleteDate($d->delete_date)
            ->setEmail($d->email)->setName($d->name)->setSystemGroup($d->system_group_id)->setSystemResources($perms)
            ->setUpdateDate($d->update_date)->setUsername($d->username)->setUserType($d->user_type);
        return $user;
    }

    public function getPermissions($username, $password)
    {
        $params = [];
        $query = "SELECT id, username, password, name, email, avatar, active, can_edit, can_edit, can_login, 
can_use_web, can_use_api, delete_date, system_group_id, update_date, user_type 
from system_user where active = 1 and username = :username";
        array_push($params, ['param' => ':username', 'value' => $username, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->select($query, null, $params);

        if (count($user) === 1) {
            var_dump($this->returnUser($user));
            die;
            return $this->returnUser($user);
        }
        return false;
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