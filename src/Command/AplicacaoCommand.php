<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Autorizacao\Aplicacao;

class AplicacaoCommand extends Command
{
    private $objAplicacao = NULL;
    
    public function __construct(Aplicacao $objAplicacao)
    {
        $this->objAplicacao = $objAplicacao;
        parent::__construct();
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('sso:aplicacao-create')
             ->setDescription('Registra uma aplicação.')
             ->setHelp("Este comando é utilizado para cadastrar uma nova aplicação e sua chave de acesso.")
             ->addArgument('nome', InputArgument::REQUIRED, 'Nome da aplicação')
             ->addArgument('apelido', InputArgument::REQUIRED, 'Apelido da aplicação');
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $objInputInterface, OutputInterface $objOutputInterface)
    {
        $nome = $objInputInterface->getArgument('nome');
        $apelido = $objInputInterface->getArgument('apelido');
        
        $objRequest = new Request();
        $objRequest->attributes->set('nome', $nome);
        $objRequest->attributes->set('apelido', $apelido);
        $this->objAplicacao->create($objRequest);
    }

}

