<?php

namespace Frobou\SystemPermission;

class FrobouSystemPermissionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FrobouSystemPermission
     */
    private $perms;

    public function setUp()
    {
        $this->perms = new FrobouSystemPermission(__DIR__ . './../database.json');
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(FrobouSystemPermission::class, $this->perms);
    }

    public function testSelect()
    {
        define('MERGE_PERMISSIONS', false);
        $user = $this->perms->getPermissions('test', 'pass');
        $this->assertTrue(count($user) === 1);
    }

//    public function testSelectFail()
//    {
//        $user = $this->perms->getPermissions('ispti', 'pass');
//        $this->assertTrue(count($user) === 0);
//    }

//    public function testInsertGroup()
//    {
//        $this->assertTrue($this->perms->createGroup('grp_' . rand(0, 15988)));
//    }

//    /**
//     * @expectedException Frobou\Pdo\Exceptions\FrobouSgdbErrorException
//     */
//    public function testeInsertGroupExists()
//    {
//        $this->perms->createGroup('SuperUser');
//    }

}
