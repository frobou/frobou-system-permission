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

    public function getPermission($resource, $separator = '.')
    {
        if (!is_string($resource)) {
            throw new FrobouSystemPermissionUserException('Invalid resource format');
        }
        if (array_key_exists($resource, $this->system_resources)) {
            return $this->mount($resource);
        }
        $has = false;
        $res = explode($separator, $resource);
        array_walk($this->system_resources, function ($value, $key) use (&$has, $res) {
            if ($has === false && (strpos($key, $res[0]) === 0)) {
                $has = true;
            }
        });
        if ($has === false) {
            return $this->mount(false);
        }
        return $this->mount(0);
    }

    private function mount($value)
    {
        return $value;
    }
}