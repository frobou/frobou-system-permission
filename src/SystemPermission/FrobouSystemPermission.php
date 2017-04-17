<?php

namespace Frobou\SystemPermission;

use Frobou\Db\FrobouDbConnection;
use Frobou\Db\FrobouDbUtils;
use Frobou\SystemPermission\Exceptions\FrobouSystemPermissionUserException;

class FrobouSystemPermission extends FrobouSystemPermissionHelper
{

    private $utils;

    public function __construct(FrobouDbConnection $connection, $db_name = null)
    {
        $this->utils = new FrobouDbUtils();
        parent::__construct($connection, $db_name);
    }

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
FROM system_user WHERE deleted = 0 AND active = 1 and username = :username";
        array_push($params, ['param' => ':username', 'value' => $username, 'type' => \PDO::PARAM_STR]);
        $user = $this->connection->select($query, $this->db_name, $params);
        if (!is_bool($user) && count($user) === 1) {
            if (!defined('PASSWORD_SALT')) {
                define('PASSWORD_SALT', 'default');
            }
            if ($pass_in_plain === true) {
                $password = md5($password);
            }
            if (!password_verify($username . PASSWORD_SALT . $password, $user[0]->password)) {
                return false;
            }
            return $this->getUser($user[0]);
        }
        return null;
    }

    public function getUserList(){
        $query = "SELECT id, username, name, email, avatar, active, can_edit, can_edit, can_login, 
can_use_web, can_use_api, system_group_id, user_type 
FROM system_user WHERE deleted = 0 AND active = 1";
        $list = $this->connection->select($query, $this->db_name);
        if (!is_bool($list) && count($list) > 0) {
            return $list;
        }
        return null;
    }

    public function getUserTypes(){
        $query = "SELECT cod, name, description FROM system_types";
        $list = $this->connection->select($query, $this->db_name);
        if (!is_bool($list) && count($list) > 0) {
            return $list;
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
        $params = $this->utils->bindParams($u->getSqlParams());
        array_shift($params);
        array_shift($params);
        $op = $this->connection->insert($u->getInsertString(), $this->db_name, $params);
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
        $params = $this->utils->bindParams($u->getSqlParams());
        array_shift($params);
        array_shift($params);
        $op = $this->connection->update($u->getUpdateString($where), $this->db_name, $params);
        //todo: usar afected rows pra saber se foi alterado ou nao
        return $op;
    }

    public function deleteUser($username)
    {
        $user = $this->getUserGroupId($username);
        if (count($user) !== 1){
            throw new FrobouSystemPermissionUserException('User not found');
        }
        if (defined('TRUE_DELETE') && TRUE_DELETE === true){
            $query = 'DELETE FROM system_user ';
        } else {
            $date = date_format(new \DateTime('now', new \DateTimeZone("America/Sao_Paulo")), "Y-m-d H:i:s");
            $query = "UPDATE system_user SET deleted = 1, active = 0, delete_date = '{$date}' ";
        }
        $query .= " WHERE id = {$user[0]->id}";
        return $this->connection->delete($query, $this->db_name);
    }

    public function undeleteUser($username)
    {
        $user = $this->getUserGroupId($username, true);
        if (count($user) !== 1){
            throw new FrobouSystemPermissionUserException('User not found');
        }
        $query = "UPDATE system_user SET deleted = 0, active = 1, delete_date = null WHERE id = {$user[0]->id}";
        return $this->connection->delete($query, $this->db_name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getGroupList($name)
    {
        $query = 'SELECT id, group_name FROM system_group';
        $list = $this->connection->select($query, $this->db_name);
        return $list;
    }

    /**
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
        if (intval($permission) < 0 || intval($permission) > 7) {
            throw new FrobouSystemPermissionUserException('registerGroupResource failed for permission value');
        }
        $params = [];
        $query = 'INSERT INTO system_resources (name, permission) VALUES (:name, :permission)';
        array_push($params, ['param' => ':name', 'value' => $name, 'type' => \PDO::PARAM_STR]);
        array_push($params, ['param' => ':permission', 'value' => $permission, 'type' => \PDO::PARAM_INT]);
        return $this->connection->insert($query, $this->db_name, $params);
    }

    private function toReg($username, $resourcename)
    {
        $user = $this->getUserGroupId($username);
        if (count($user) !== 1) {
            throw new FrobouSystemPermissionUserException('registerGroupResource failed for username');
        }
        $res = $this->getResourceId($resourcename);
        if (count($res) !== 1) {
            throw new FrobouSystemPermissionUserException('registerGroupResource failed for resourcename');
        }
        return ['user' => $user[0], 'res' => $res[0]];
    }

    public function registerGroupResource($username, $resourcename)
    {
        $val = $this->toReg($username, $resourcename);
        return $this->linkGroupResource($val['user']->system_group_id, $val['res']->id);
    }

    public function unregisterGroupResource($username, $resourcename)
    {
        $val = $this->toReg($username, $resourcename);
        return $this->unlinkGroupResource($val['user']->system_group_id, $val['res']->id);
    }

    public function registerUserResource($username, $resourcename)
    {
        $val = $this->toReg($username, $resourcename);
        return $this->linkUserResource($val['user']->id, $val['res']->id);
    }

    public function unregisterUserResource($username, $resourcename)
    {
        $val = $this->toReg($username, $resourcename);
        return $this->unlinkUserResource($val['user']->id, $val['res']->id);
    }

}