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
            return $this->mount($this->system_resources[$resource]);
        }
        $res = explode($separator, $resource);
        $the_key = '';
        for ($i = 0; $i <= count($res) - 2; $i++) {
            $the_key .= $res[$i] . $separator;
            foreach ($this->system_resources as $key => $value) {
                if ($key == '' || $value == '') {
                    continue;
                }
                if (array_key_exists(substr($the_key, 0, strlen($the_key) - 1), $this->system_resources)) {
                    return $this->mount($value);
                }
            }
        }
        return $this->mount(false);
    }

    private function mount($value)
    {
        $out = new \stdClass();
        if (is_bool($value)) {
            $out->can_select = false;
            $value = 0;
        } else {
            $out->can_select = true;
        }
        $bin = str_pad(decbin($value), 4, '0', STR_PAD_LEFT);
        $out->can_insert = boolval($bin[3]);
        $out->can_update = boolval($bin[2]);
        $out->can_delete = boolval($bin[1]);
        return $out;
    }
}