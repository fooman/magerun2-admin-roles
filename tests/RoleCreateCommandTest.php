<?php

namespace Fooman\Magerun2\AdminRolesTest;

use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Magento\Framework\Exception\LocalizedException;

class RoleCreateCommandTest extends TestCase
{
    public function testNameIsRequired()
    {
        /**
         * Load module config for unit test. In this case the relative
         * path from current test case.
         */
        $this->loadConfigFile(__DIR__ . '/../n98-magerun2.yaml');

        /**
         * Test if command could be found
         */
        $command = $this->getApplication()->find('admin:role:create');

        $this->setExpectedException(LocalizedException::class, '--name is a required option');
        /**
         * Call command
         */
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
            ]
        );
    }

    public function testRoleIsCreated()
    {
        /**
         * Load module config for unit test. In this case the relative
         * path from current test case.
         */
        $this->loadConfigFile(__DIR__ . '/../n98-magerun2.yaml');

        /** @var \Fooman\Magerun2\AdminRoles\RoleCreateCommand $command */
        $command = $this->getApplication()->find('admin:role:create');

        $roleFactoryMock = $this->prepareRoleFactoryMock();

        /**
         * @TODO the run command overwrites our own injected mocks with the real adapters
         */
        $command->inject($roleFactoryMock);
        /**
         * Call command
         */
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--name'=>'test'
            ]
        );
    }

    private function prepareRoleFactoryMock()
    {
        $roleMock = $this->getMockBuilder(\Magento\Authorization\Model\Role::class)
                                ->disableOriginalConstructor()
                                ->disableOriginalClone()
                                ->disableArgumentCloning()
                                ->setMethods(['load','getId','setName','setPid','setRoleType','setUserType','save'])
                                ->getMock();

        $roleMock->expects($this->any())
                        ->method('load')
                        ->willReturnSelf();

        $roleMock->expects($this->any())
                 ->method('getId')
                 ->willReturn('');

        $roleMock->expects($this->any())
                 ->method('setName')
                 ->willReturnSelf();

        $roleMock->expects($this->any())
                 ->method('setPid')
                 ->willReturnSelf();

        $roleMock->expects($this->any())
                 ->method('setRoleType')
                 ->willReturnSelf();

        $roleMock->expects($this->any())
                 ->method('setUserType')
                 ->willReturnSelf();

        $roleMock->expects($this->never())
                 ->method('save')
                 ->willReturnSelf();

        $roleFactoryMock = $this->getMockBuilder(\Magento\Authorization\Model\RoleFactory::class)
                                ->disableOriginalConstructor()
                                ->disableOriginalClone()
                                ->disableArgumentCloning()
                                ->setMethods(['create'])
                                ->getMock();
        $roleFactoryMock->expects($this->any())
                        ->method('create')
                        ->willReturn($roleMock);
        return $roleFactoryMock;
    }
}
