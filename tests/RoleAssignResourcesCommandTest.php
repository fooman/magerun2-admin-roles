<?php

namespace Fooman\Magerun2\AdminRolesTest;

use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Magento\Framework\Exception\LocalizedException;

class RoleAssignResourcesCommandTest extends TestCase
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
        $command = $this->getApplication()->find('admin:role:resources');

        $this->setExpectedException(LocalizedException::class, '--name is a required option');
        /**
         * Call command
         */
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--resources'=>'test'
            ]
        );
    }

    public function testResourcesAreRequired()
    {
        /**
         * Load module config for unit test. In this case the relative
         * path from current test case.
         */
        $this->loadConfigFile(__DIR__ . '/../n98-magerun2.yaml');

        /**
         * Test if command could be found
         */
        $command = $this->getApplication()->find('admin:role:resources');

        $this->setExpectedException(LocalizedException::class, '--resources is a required option');
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

    public function testOutput()
    {
        /**
         * Load module config for unit test. In this case the relative
         * path from current test case.
         */
        $this->loadConfigFile(__DIR__ . '/../n98-magerun2.yaml');

        /**
         * Test if command could be found
         */
        $command = $this->getApplication()->find('admin:role:resources');

        /**
         * Call command
         */
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--name'=>'test',
                '--resources'=>'test'
            ]
        );
    }
}
