<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserPromoteCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'user:promote';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Наделить юзера правами')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('role', InputArgument::REQUIRED, 'Role')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->lock();

        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
//        $user->removeRole($role);
        $user->addRole($role);

        $this->em->flush();

        $this->release();

        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
