<?php

namespace Fooman\Magerun2\AdminRoles;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Exception\LocalizedException;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

class RoleCreateCommand extends AbstractMagentoCommand
{
    const OPTION_ROLE_NAME = 'name';

    private $roleFactory;

    protected function configure()
    {
        $this->setName('admin:role:create')
            ->setDescription('Create a new admin role by name')
            ->addOption(
                self::OPTION_ROLE_NAME,
                '',
                InputOption::VALUE_REQUIRED,
                'The name of the admin role'
            );
    }

    public function inject(
        \Magento\Authorization\Model\RoleFactory $roleFactory
    ) {
        $this->roleFactory = $roleFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption(self::OPTION_ROLE_NAME);
        if ($name === null) {
            throw new LocalizedException(__('--name is a required option'));
        }

        $role = $this->roleFactory->create()->load($name, 'role_name');

        if (!$role->getId()) {
            $role->setName($name)
                 ->setPid(0)
                 ->setRoleType(RoleGroup::ROLE_TYPE)
                 ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
            $role->save();

            $output->writeln(sprintf('Role %s created.', $name));
        } else {
            $output->writeln(sprintf('Role %s already exists.', $name));
        }
    }
}
