<?php

namespace Frobou\SystemPermission;

use Frobou\Validator\FrobouValidation;

class SystemUser extends SystemUserAbstract
{

    protected $id = '';
    protected $username = '';
    protected $password = '';
    protected $name = '';
    protected $email = '';
    protected $avatar = '';
    protected $create_date = '';
    protected $update_date = null;
    protected $delete_date = null;
    protected $user_type = 'S';
    protected $active = 1;
    protected $can_edit = 1;
    protected $can_login = 0;
    protected $can_use_web = 0;
    protected $can_use_api = 0;
    protected $system_group_id;
    protected $system_resources = [];

    public function __construct()
    {
        if (!defined('PASSWORD_SALT')) {
            define('PASSWORD_SALT', 'default');
        }
        if (!defined('TRUE_DELETE')) {
            define('TRUE_DELETE', false);
        }
    }

    public function getId()
    {
        if (trim($this->id) == '') {
            return null;
        }
        return $this->id;
    }

    public function getUsername()
    {
        if (trim($this->username) == '') {
            return null;
        }
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        array_push($this->fields, 'username');
        return $this;
    }

    public function getPassword()
    {
        if (trim($this->password) == '') {
            return null;
        }
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $this->password = password_hash(md5($this->username . PASSWORD_SALT . $password), PASSWORD_DEFAULT);
        array_push($this->fields, 'password');
        return $this;
    }

    public function getName()
    {
        if (trim($this->name) == '') {
            return null;
        }
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        array_push($this->fields, 'name');
        return $this;
    }

    public function getEmail()
    {
        if (trim($this->email) == '') {
            return null;
        }
        return $this->email;
    }

    public function setEmail($email)
    {
        if (FrobouValidation::validateEmail($email)) {
            $this->email = $email;
        }
        array_push($this->fields, 'email');
        return $this;
    }

    public function getAvatar()
    {
        if (trim($this->avatar) == '') {
            return null;
        }
        return $this->avatar;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        array_push($this->fields, 'avatar');
        return $this;
    }

    public function getCreateDate()
    {
        if (trim($this->create_date) == '') {
            return null;
        }
        return $this->create_date;
    }

    public function setCreateDate()
    {
        $this->create_date = date_format(new \DateTime('now', new \DateTimeZone("America/Sao_Paulo")),"Y-m-d H:i:s");
        array_push($this->fields, 'create_date');
        return $this;
    }

    public function getUpdateDate()
    {
        if (trim($this->update_date) == '') {
            return null;
        }
        return $this->update_date;
    }

    public function setUpdateDate()
    {
        $this->update_date = date_format(new \DateTime('now', new \DateTimeZone("America/Sao_Paulo")),"Y-m-d H:i:s");
        array_push($this->fields, 'update_date');
        return $this;
    }

    public function getDeleteDate()
    {
        if (trim($this->delete_date) == '') {
            return null;
        }
        return $this->delete_date;
    }

    public function setDeleteDate()
    {
        $this->delete_date = date_format(new \DateTime('now', new \DateTimeZone("America/Sao_Paulo")),"Y-m-d H:i:s");
        array_push($this->fields, 'delete_date');
        return $this;
    }

    public function getUserType()
    {
        if (trim($this->user_type) == '') {
            return null;
        }
        return $this->user_type;
    }

    public function setUserType($user_type)
    {
        $this->user_type = $user_type;
        array_push($this->fields, 'user_type');
        return $this;
    }

    public function getActive()
    {
        if (trim($this->active) == '') {
            return null;
        }
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = intval($active);
        array_push($this->fields, 'active');
        return $this;
    }

    public function getCanEdit()
    {
        if (trim($this->can_edit) == '') {
            return null;
        }
        return $this->can_edit;
    }

    public function setCanEdit($can_edit)
    {
        $this->can_edit = intval($can_edit);
        array_push($this->fields, 'can_edit');
        return $this;
    }

    public function getCanLogin()
    {
        if (trim($this->can_login) == '') {
            return null;
        }
        return $this->can_login;
    }

    public function setCanLogin($can_login)
    {
        $this->can_login = intval($can_login);
        array_push($this->fields, 'can_login');
        return $this;
    }

    public function getCanUseWeb()
    {
        if (trim($this->can_use_web) == '') {
            return null;
        }
        return $this->can_use_web;
    }

    public function setCanUseWeb($can_use_web)
    {
        $this->can_use_web = intval($can_use_web);
        array_push($this->fields, 'can_use_web');
        return $this;
    }

    public function getCanUseApi()
    {
        if (trim($this->can_use_api) == '') {
            return null;
        }
        return $this->can_use_api;
    }

    public function setCanUseApi($can_use_api)
    {
        $this->can_use_api = intval($can_use_api);
        array_push($this->fields, 'can_use_api');
        return $this;
    }

    public function getSystemGroup()
    {
        if (trim($this->system_group_id) == '') {
            return null;
        }
        return $this->system_group_id;
    }

    public function setSystemGroup($system_group_id)
    {
        $this->system_group_id = intval($system_group_id);
        array_push($this->fields, 'system_group_id');
        return $this;
    }

    public function getSystemResources()
    {
        return $this->system_resources;
    }

    public function setSystemResources($system_resources)
    {
        $this->system_resources = $system_resources;
        return $this;
    }

}