<?php

namespace Frobou\SystemPermission;

use Frobou\Db\FrobouDbConfig;
use Frobou\Db\FrobouDbConnection;

class FrobouSystemPermissionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FrobouSystemPermission
     */
    private $perms;

    public function setUp()
    {
        if (!defined('MERGE_PERMISSIONS')) {
            define('MERGE_PERMISSIONS', true);
        }

        $config = new FrobouDbConfig(json_decode(file_get_contents(__DIR__ . './../database.json')));
        $connection = new FrobouDbConnection($config);
        $this->perms = new FrobouSystemPermission($connection);

        $connection->delete('delete from group_resources;delete from user_resources;delete from system_resources;delete from system_user;');

        $user = new SystemUser();
        $user->setActive(1)->setAvatar('imagem.png')->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('eu@email.com')->setName('Novo Usuario')
            ->setPassword('pass')->setSystemGroup(1)->setUserType('T')->setUsername('test');
        $this->perms->createUser($user);
        $u_id = $connection->stats();

        $connection->insert('insert into system_resources (name, permission) values ("admin.teste", 7)');
        $id = $connection->stats();
        $connection->insert("insert into group_resources values (1,{$id['last_id']})");
        $connection->insert("insert into user_resources values ({$u_id['last_id']},{$id['last_id']})");
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(FrobouSystemPermission::class, $this->perms);
    }

    public function testLoginFail()
    {
        $user = $this->perms->login('test', 'passt');
        $this->assertFalse($user);
    }

    public function testLoginOk()
    {
        $user = $this->perms->login('test', 'pass');
        $this->assertInstanceOf(SystemUser::class, $user);
    }

    public function testPermissionForResourceAdminDotTeste()
    {
        $user = $this->perms->login('test', 'pass');
        $this->assertEquals($this->perms->getResourcePermission($user, 'admin.teste'), 7);
    }

    public function testPermissionForResourceFail()
    {
        $user = $this->perms->login('test', 'pass');
        $this->assertEquals($this->perms->getResourcePermission($user, 'admin'), null);
    }

    /**
     * @expectedException Frobou\Db\Exceptions\FrobouDbSgdbErrorException
     */
    public function testeInsertGroupExists()
    {
        $this->perms->createGroup('SuperUser');
    }

    public function testInsertGroup()
    {
        $this->assertTrue($this->perms->createGroup('grp_' . rand(0, 15988)));
    }

    public function testInsertResource()
    {
        $this->assertTrue($this->perms->createResource('admin.test', 0));
    }

    public function testInsertUser()
    {
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('capitao@caverna.com')->setName('Novo Usuario')
            ->setPassword('senhanha')->setSystemGroup(1)->setUsername('username_' . rand(0, 12345))->setUserType('T');
        $this->assertTrue($this->perms->createUser($user));
    }

    /**
     * @expectedException Frobou\Db\Exceptions\FrobouDbSgdbErrorException
     */
    public function testInsertUserError()
    {
        $user = new SystemUser();
        $user->setActive(1)->setAvatar('imagem.img')->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('eu@email.com')->setName('Novo Usuario')
            ->setPassword('asdfasdfsd')->setSystemGroup(1)->setUserType('T');
        $this->perms->createUser($user);
    }

    public function testUpdateUser()
    {
        $user = new SystemUser();
        $user->setActive(0)->setCanEdit(0)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setUpdateDate()->setEmail('stacio@email.com')->setName('Novo Usuario');
        $this->assertTrue($this->perms->updateUser($user,['email' => 'eu@email.com', 'active' => 1]));
    }

    /**
     * @expectedException Frobou\SystemPermission\Exceptions\FrobouSystemPermissionUserException
     */
    public function testUpdateUserError()
    {
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setUpdateDate('2016-12-22 12:30:22')->setEmail('eles@email.com')->setName('Novo Usuario');
        $this->perms->updateUser($user,[]);
    }

    public function testUpdateUserNothingToDo()
    {
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setUpdateDate('2016-12-22 12:30:22')->setEmail('eles@email.com')->setName('Novo Usuario');
        $this->assertFalse($this->perms->updateUser($user,['email' => 'tatu@email.com', 'active' => 1]));
    }

}
