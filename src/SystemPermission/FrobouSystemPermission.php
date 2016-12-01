<?php

namespace Frobou\SystemPermission;

class FrobouSystemPermission extends FrobouSystemPermissionHelper
{
    /**
     * @param $username
     * @param $password
     * @return bool|SystemUser|null
     */
    public function login($username, $password, $pass_in_plain = false)
    {
        $params = [];
        $query = "SELECT id, username, password, name, email, avatar, active, can_edit, can_edit, can_login, 
can_use_web, can_use_api, delete_date, system_group_id, update_date, user_type, create_date 
from system_user where active = 1 and username = :username";
        array_push($params, ['param' => ':username', 'value' => $username, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->select($query, $this->db_name, $params);
        if (!is_bool($user) && count($user) === 1) {
            if (!defined('PASSWORD_SALT')) {
                define('PASSWORD_SALT', 'default');
            }
            if ($pass_in_plain === true){
                $password = md5($password);
            }
            if (!password_verify($username . PASSWORD_SALT . $password, $user[0]->password)) {
                return false;
            }
            return $this->getUser($user[0]);
        }
        return null;
    }

    /**
     * @param SystemUser $user
     * @return mixed
     */
    public function createUser(SystemUser $user)
    {
        $u = $this->cryptPass($user);
        $op = $this->connection->insert($u->getInsertString(), $this->db_name, $this->connection->utils->bindParams($u->getSqlParams()));
        return $op;
    }

    /**
     * @param SystemUser $user
     * @param array $where
     * @return mixed
     */
    public function updateUser(SystemUser $user, array $where)
    {
        $u = $this->cryptPass($user);
        $op = $this->connection->update($u->getUpdateString($where), $this->db_name, $this->connection->utils->bindParams($u->getSqlParams()));
        //todo: usar afected rows pra saber se foi alterado ou nao
        return $op;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function createGroup($name)
    {
        $params = [];
        $query = 'INSERT INTO system_group (group_name) VALUES (:name)';
        array_push($params, ['param' => ':name', 'value' => $name, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->insert($query, $this->db_name, $params);
        return $user;
    }

    /**
     * @param $name
     * @param $permission
     * @return mixed
     */
    public function createResource($name, $permission)
    {
        $params = [];
        $query = 'INSERT INTO system_resources (name, permission) VALUES (:name, :permission)';
        array_push($params, ['param' => ':name', 'value' => $name, 'type' => \PDO::PARAM_STR]);
        array_push($params, ['param' => ':permission', 'value' => $permission, 'type' => \PDO::PARAM_INT]);
        return $this->connection->insert($query, $this->db_name, $params);
    }

    public function registerResource()
    {
        return true;
    }

    public function unregisterResource()
    {
        return true;
    }

    public function listResources($for_user = null)
    {
        return true;
    }

}