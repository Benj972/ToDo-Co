<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;

/*
 * This class is used to link old tasks to the anonymous user
 */
class UpdateOldTaskCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UpdateOldTaskCommand constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        //create command
        $this
            ->setName('demo:load')
            ->setDescription('Updates old tasks without user')
            ->addArgument('anonymous')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usercommand = $input->getArgument('anonymous');
        $text = strtoupper('Bad Command');
        if (true == $usercommand) {
            //create anonymous and link to old tasks
            $anonymous = $this->load();
            $this->setOldTask($anonymous);
            $text = strtoupper('Old Tasks Update');
        }
        $output->writeln($text);
    }

    public function load()
    {
        //create user anonymous
        $anonymous = new User();
        $anonymous->setUsername('anonymous');
        $anonymous->setPassword('passwordanonymous');
        $anonymous->setEmail('anonymous@hotmail.fr');
        $anonymous->setRoles(['ROLE_ADMIN']);
        $this->manager->persist($anonymous);
        $this->manager->flush();
        return $anonymous;
    }

    public function setOldTask($anonymous)
    {
        //search tasks
        $tasks = $this->manager->getRepository('AppBundle:Task')->findAll();
        foreach ($tasks as $task) {
            if (null === $task->getUser()) {
                //link tasks without user to anonymous
                $task->setUser($anonymous);
                $this->manager->persist($task);
                $this->manager->flush();
            }
        }
    }
}
