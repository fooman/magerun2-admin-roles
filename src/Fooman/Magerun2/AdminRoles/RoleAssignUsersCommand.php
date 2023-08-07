<?php

namespace Fooman\Magerun2\AdminRoles;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Exception\LocalizedException;

class RoleAssignUsersCommand extends AbstractMagentoCommand
{
    const OPTION_ROLE_NAME = 'name';
    const OPTION_USERS = 'users';

    private $roleFactory;
    private $userFactory;

    protected function configure()
    {
        $this->setName('admin:role:users')
             ->setDescription('Assign users to admin role')
             ->addOption(
                 self::OPTION_ROLE_NAME,
                 '',
                 InputOption::VALUE_REQUIRED,
                 'The name of the admin role'
             )
             ->addOption(
                 self::OPTION_USERS,
                 'u',
                 InputOption::VALUE_REQUIRED,
                 'Comma separated list of users to assign to role, replaces existing list'
             );
    }

    public function inject(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->roleFactory = $roleFactory;
        $this->userFactory = $userFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption(self::OPTION_ROLE_NAME);
        if ($name === null) {
            throw new LocalizedException(__('--name is a required option'));
        }

        $users = $input->getOption(self::OPTION_USERS);
        if ($users === null) {
            throw new LocalizedException(__('--users is a required option'));
        }

        $role = $this->roleFactory->create()->load($name, 'role_name');

        if ($role->getId()) {
            foreach (explode(',', $users) as $userName) {
                $this->addUserToRole($output, $userName, $role->getId());
            }
        } else {
            $output->writeln(sprintf('Role %s doesn\'t exist.', $name));
        }
    }

    private function addUserToRole($output, $userName, $roleId)
    {
        if (is_numeric($userName)) {
            $user = $this->userFactory->create()->load($userName);
        } else {
            $user = $this->userFactory->create()->load($userName, 'username');
        }

        if ($user->getId()) {
            $user->setRoleId($roleId);
            if ($user->roleUserExists() !== true) {
                $user->save();
                $output->writeln(sprintf('Role applied to user %s', $userName));
            }
        } else {
            $output->writeln(sprintf('User %s doesn\'t exist.', $userName));
        }
    }
}
