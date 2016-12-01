<?php

namespace Frobou\SystemPermission;

use Frobou\Db\FrobouDbConnection;

class FrobouSystemPermissionHelper
{
    /**
     * @var FrobouDbConnection
     */
    protected $connection;
    protected $db_name;

    public function __construct(FrobouDbConnection $connection, $db_name = null)
    {
        $this->connection = $connection;
        $this->db_name = $db_name;
    }

    protected function getGroupPerms($id)
    {
        $query = "SELECT sr.name, sr.permission
FROM group_resources gr
INNER JOIN system_resources sr ON gr.system_resources_id = sr.id
WHERE gr.system_group_id = {$id} group by sr.name";
        return $this->connection->select($query, $this->db_name);
    }

    protected function getUserPerms($id)
    {
        $query = "SELECT sr.name, sr.permission
FROM user_resources ur
INNER JOIN system_resources sr ON ur.system_resources_id = sr.id
WHERE ur.system_user_id = {$id} group by sr.name";
        return $this->connection->select($query, $this->db_name);
    }

    protected function getUser($data)
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

    protected function cryptPass(SystemUser $user){
        $pass = $user->getPassword();
        $user->setPassword(password_hash($user->getUsername() . PASSWORD_SALT . md5($pass), PASSWORD_DEFAULT));
        return $user;
    }
}