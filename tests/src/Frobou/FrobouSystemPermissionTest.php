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
        $this->perms = new FrobouSystemPermission();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(FrobouSystemPermission::class, $this->perms);
    }

    public function testSelect(){
        $user = $this->perms->getPermissions('fabio', 'pass');
        $this->assertTrue(count($user) === 1);
    }

    public function testSelectFail(){
        $user = $this->perms->getPermissions('ispti', 'pass');
        $this->assertTrue(count($user) === 0);
    }

}
