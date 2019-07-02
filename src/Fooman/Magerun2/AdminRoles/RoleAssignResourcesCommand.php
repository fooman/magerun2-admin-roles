<?php

namespace Fooman\Magerun2\AdminRoles;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Exception\LocalizedException;

class RoleAssignResourcesCommand extends AbstractMagentoCommand
{
    const OPTION_ROLE_NAME = 'name';
    const OPTION_RESOURCES = 'resources';

    private $roleFactory;
    private $rulesFactory;

    protected function configure()
    {
        $this->setName('admin:role:resources')
             ->setDescription('Assign resources to admin role')
             ->addOption(
                 self::OPTION_ROLE_NAME,
                 '',
                 InputOption::VALUE_REQUIRED,
                 'The name of the admin role'
             )
             ->addOption(
                 self::OPTION_RESOURCES,
                 'r',
                 InputOption::VALUE_REQUIRED,
                 'Comma separated list of resources to assign to role, replaces existing list'
             );
    }

    public function inject(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory
    ) {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption(self::OPTION_ROLE_NAME);
        if ($name === null) {
            throw new LocalizedException(__('--name is a required option'));
        }

        $resources = $input->getOption(self::OPTION_RESOURCES);
        if ($resources === null) {
            throw new LocalizedException(__('--resources is a required option'));
        }
        $role = $this->roleFactory->create()->load($name, 'role_name');

        if ($role->getId()) {
            $this->rulesFactory->create()->setRoleId($role->getId())->setResources(explode(',', $resources))->saveRel();
            $output->writeln(sprintf('Role %s updated.', $name));
        } else {
            $output->writeln(sprintf('Role %s doesn\'t exist.', $name));
        }
    }
}
