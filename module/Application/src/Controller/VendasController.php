<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\VendasRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class VendasController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $VendasRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, VendasRepository $VendasRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->VendasRepository = $VendasRepository;
    }

    public function RtvPerfomanceAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }

    #endregion

}