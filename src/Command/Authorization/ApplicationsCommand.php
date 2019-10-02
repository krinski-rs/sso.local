<?php
namespace App\Command\Authorization;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Authorization\Application;

class ApplicationsCommand extends Command
{
    private $objApplication = NULL;
    
    public function __construct(Application $objApplication)
    {
        $this->objApplication = $objApplication;
        parent::__construct();
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('sso:application-create')
             ->setDescription('Register an application.')
             ->setHelp("This command is used to register a new application and its access key.")
             ->addArgument('name', InputArgument::REQUIRED, 'Application name')
             ->addArgument('host', InputArgument::REQUIRED, 'Application host')
             ->addArgument('acronym', InputArgument::REQUIRED, 'Application acronym')
             ->addArgument('expirationDate', InputArgument::OPTIONAL, 'Application expiration date');
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $objInputInterface, OutputInterface $objOutputInterface)
    {
        $acronym = $objInputInterface->getArgument('acronym');
        $expirationDate = $objInputInterface->getArgument('expirationDate');
        $host = $objInputInterface->getArgument('host');
        $name = $objInputInterface->getArgument('name');
        
        $objRequest = new Request();
        $objRequest->attributes->set('acronym', $acronym);
        $objRequest->attributes->set('expirationDate', $expirationDate);
        $objRequest->attributes->set('host', $host);
        $objRequest->attributes->set('name', $name);
        $this->objApplication->create($objRequest);
    }
}
