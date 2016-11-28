<?php

namespace Frobou\SystemPermission;

use Frobou\Validator\FrobouValidation;

class SystemUser
{
    private $id;
    private $username;
    private $password;
    private $name;
    private $email;
    private $avatar;
    private $create_date;
    private $update_date = null;
    private $delete_date = null;
    private $user_type;
    private $active;
    private $can_edit;
    private $can_login;
    private $can_use_web;
    private $can_use_api;
    private $system_group;
    private $system_resources = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return SystemUser
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return SystemUser
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return SystemUser
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return SystemUser
     */
    public function setEmail($email)
    {
        if (FrobouValidation::validateEmail($email)) {
            $this->email = $email;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     * @return SystemUser
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @return null
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @param null $update_date
     * @return SystemUser
     */
    public function setUpdateDate($update_date)
    {
        $this->update_date = $update_date;
        return $this;
    }

    /**
     * @return null
     */
    public function getDeleteDate()
    {
        return $this->delete_date;
    }

    /**
     * @param null $delete_date
     * @return SystemUser
     */
    public function setDeleteDate($delete_date)
    {
        $this->delete_date = $delete_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserType()
    {
        return $this->user_type;
    }

    /**
     * @param mixed $user_type
     * @return SystemUser
     */
    public function setUserType($user_type)
    {
        $this->user_type = $user_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     * @return SystemUser
     */
    public function setActive($active)
    {
        $this->active = intval($active);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanEdit()
    {
        return $this->can_edit;
    }

    /**
     * @param mixed $can_edit
     * @return SystemUser
     */
    public function setCanEdit($can_edit)
    {
        $this->can_edit = intval($can_edit);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanLogin()
    {
        return $this->can_login;
    }

    /**
     * @param mixed $can_login
     * @return SystemUser
     */
    public function setCanLogin($can_login)
    {
        $this->can_login = intval($can_login);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanUseWeb()
    {
        return $this->can_use_web;
    }

    /**
     * @param mixed $can_use_web
     * @return SystemUser
     */
    public function setCanUseWeb($can_use_web)
    {
        $this->can_use_web = intval($can_use_web);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanUseApi()
    {
        return $this->can_use_api;
    }

    /**
     * @param mixed $can_use_api
     * @return SystemUser
     */
    public function setCanUseApi($can_use_api)
    {
        $this->can_use_api = intval($can_use_api);
        return $this;
    }

    /**
     * @return array
     */
    public function getSystemGroup()
    {
        return $this->system_group;
    }

    /**
     * @param array $system_group
     * @return SystemUser
     */
    public function setSystemGroup($system_group)
    {
        $this->system_group = intval($system_group);
        return $this;
    }

    /**
     * @return array
     */
    public function getSystemResources()
    {
        return $this->system_resources;
    }

    /**
     * @param array $system_resources
     * @return SystemUser
     */
    public function setSystemResources($system_resources)
    {
        $this->system_resources = $system_resources;
        return $this;
    }

}