<?php

namespace Frobou\SystemPermission;

use Frobou\SystemPermission\Exceptions\FrobouSystemPermissionUserException;

abstract class SystemUserHelper
{

    protected $fields = [];
    protected $params = [];

    public function getInsertString()
    {
        $fields = 'INSERT INTO system_user (';
        $params = '';
        if (count($this->fields) === 0) {
            throw new FrobouSystemPermissionUserException();
        }
        foreach ($this->fields as $value) {
            $fields .= "{$value},";
            $params .= ":{$value},";
        }
        return substr($fields, 0, strlen($fields) - 1) . ') VALUES (' . substr($params, 0, strlen($params) - 1) . ')';
    }

    public function getUpdateString(array $where)
    {
        if (count($where) === 0) {
            throw new FrobouSystemPermissionUserException('WHERE values can not be empty');
        }
        if (count($this->fields) === 0) {
            throw new FrobouSystemPermissionUserException();
        }
        $fields = 'UPDATE system_user set ';
        $w = ' WHERE ';
        foreach ($this->fields as $value) {
            $fields .= "{$value}=:{$value},";
        }
        foreach ($where as $key => $value) {
            $w .= "{$key} = '{$value}' AND ";
        }
        return substr($fields, 0, strlen($fields) - 1) . substr($w, 0, strlen($w) - 5);
    }

    public function getSqlParams()
    {
        if (count($this->fields) === 0) {
            throw new FrobouSystemPermissionUserException();
        }
        foreach ($this->fields as $value) {
            $this->params[$value] = $this->{$value};
        }
        return $this->params;
    }
}