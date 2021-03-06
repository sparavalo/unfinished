<?php
declare(strict_types = 1);
namespace Test\Core\Service;

class AdminUserServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testUserLoginShouldReturnUserDataIfUserIsProperlyLoggedIn()
    {
        $userData = new class {
            public $email = 'admin@example.org';
            public $password = 'secret';
            public $admin_user_uuid = 'uuid';
        };
        $bcrypt = $this->getMockBuilder('Zend\Crypt\Password\Bcrypt')
            ->setMethods(['verify'])
            ->getMock();
        $bcrypt->expects(static::once())
            ->method('verify')
            ->will(static::returnValue(true));
        $adminUsersMapper = $this->getMockBuilder('Core\Mapper\AdminUsersMapper')
            ->setMethods(['getByEmail', 'updateLogin'])
            ->getMock();
        $adminUsersMapper->expects(static::once())
            ->method('updateLogin')
            ->will(static::returnValue(1));
        $adminUsersMapper->expects(static::once())
            ->method('getByEmail')
            ->will(static::returnValue($userData));
        $adminUserService = new \Core\Service\AdminUserService($bcrypt, $adminUsersMapper);
        static::assertSame($userData, $adminUserService->loginUser('admin@example.org', 'secret'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Password does not match.
     */
    public function testUserLoginShouldThrowExceptionIfCredentialsVerificationFails()
    {
        $userData = new class {
            public $email = 'admin@example.org';
            public $password = 'secret';
            public $admin_user_uuid = 'uuid';
        };
        $bcrypt = $this->getMockBuilder('Zend\Crypt\Password\Bcrypt')
            ->setMethods(['verify'])
            ->getMock();
        $bcrypt->expects(static::once())
            ->method('verify')
            ->will(static::returnValue(false));
        $adminUsersMapper = $this->getMockBuilder('Core\Mapper\AdminUsersMapper')
            ->setMethods(['getByEmail'])
            ->getMock();
        $adminUsersMapper->expects(static::once())
            ->method('getByEmail')
            ->will(static::returnValue($userData));
        $adminUserService = new \Core\Service\AdminUserService($bcrypt, $adminUsersMapper);
        $adminUserService->loginUser('admin@example.org', 'secret');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage User does not exist.
     */
    public function testUserLoginShouldThrowExceptionIfUserEmailDoesNotExist()
    {
        $userData = null;
        $bcrypt = $this->getMockBuilder('Zend\Crypt\Password\Bcrypt')
            ->getMock();
        $adminUsersMapper = $this->getMockBuilder('Core\Mapper\AdminUsersMapper')
            ->setMethods(['getByEmail'])
            ->getMock();
        $adminUsersMapper->expects(static::once())
            ->method('getByEmail')
            ->will(static::returnValue($userData));
        $adminUserService = new \Core\Service\AdminUserService($bcrypt, $adminUsersMapper);
        $adminUserService->loginUser('admin@example.org', 'secret');
    }
}
