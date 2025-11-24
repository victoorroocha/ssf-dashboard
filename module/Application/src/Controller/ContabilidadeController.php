<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\ContabilidadeRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class ContabilidadeController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $ContabilidadeRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, ContabilidadeRepository $ContabilidadeRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->ContabilidadeRepository = $ContabilidadeRepository;
    }

    public function conferenciaEntradasCteAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listConferenciaEntradasCteAction()
    {
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        $empresa = $this->params()->fromQuery('empresa', null);
        $filial = $this->params()->fromQuery('filial', null);
        $dataInicio = $this->params()->fromQuery('dataInicio', null);
        $dataFim = $this->params()->fromQuery('dataFim', null);
        $chavenfe = $this->params()->fromQuery('chavenfe', null);

        try {
            $sql = $this->ContabilidadeRepository ? $this->ContabilidadeRepository->getConferenciaEntradasCte($empresa, $filial, $dataInicio, $dataFim, $chavenfe) : '';

            $result = [];
            if ($sql) {
                $result = $this->oracleService->executeQuery($sql);
            }

            $colunasIgnorarUtf8 = ['SITNFENTRADA_DESCR', 'CTE_TPORI']; // adicione aqui as colunas que não quer converter
            $colunasNumericas = [
                'VLRBRU', 'VLRDSC', 'VLRLIQ', 'VLRLIQ_DIF',
                'VLRBASE_ICM', 'PERICM', 'VLRICM', 'VLRBASE_ICMCREDEFET',
                'VLRIMP_ICMCREDEFET', 'VLRISEN_ICM', 'VLROUTR_ICM', 'VLRFRETE',
                'PERPIS_REC', 'VLRBASE_PIS', 'VLRIMP_PISREC', 'PERCOF_REC',
                'VLRIMP_COFREC', 'VLRRET_COF', 'PERISS', 'VLRBASE_ISS',
                'VLRIMP_ISS', 'PERINS'
            ];

            foreach ($result as $key => $row) {
                foreach ($row as $col => $val) {
                    // Converte colunas numéricas
                    if (in_array($col, $colunasNumericas)) {
                        $result[$key][$col] = floatval($val);
                    }
                    // Converte texto para UTF-8, exceto colunas ignoradas
                    elseif (is_string($val) && !in_array($col, $colunasIgnorarUtf8)) {
                        $result[$key][$col] = utf8_encode($val);
                    }
                }
            }


            return new JsonModel([
                'success' => true,
                'data' => $result,
                'totalCount' => count($result)
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function conferenciaNotasSaidaAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listConferenciaNotasSaidaProdutoAction()
    {
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        $empresa = $this->params()->fromQuery('empresa', null);
        $filial = $this->params()->fromQuery('filial', null);
        $dataInicio = $this->params()->fromQuery('dataInicio', null);
        $dataFim = $this->params()->fromQuery('dataFim', null);
        $chavenfe = $this->params()->fromQuery('chavenfe', null);

        try {
            $sql = $this->ContabilidadeRepository ? $this->ContabilidadeRepository->getConferenciaSaidasProduto($empresa, $filial, $dataInicio, $dataFim, $chavenfe) : '';

            $result = [];
            if ($sql) {
                $result = $this->oracleService->executeQuery($sql);
            }

            $colunasIgnorarUtf8 = ['SITNFENTRADA_DESCR', 'CTE_TPORI']; // adicione aqui as colunas que não quer converter
            $colunasNumericas = [
                'VLRBRU', 'VLRDSC', 'VLRLIQ', 'VLRLIQ_DIF',
                'VLRBASE_ICM', 'PERICM', 'VLRICM', 'VLRBASE_ICMCREDEFET',
                'VLRIMP_ICMCREDEFET', 'VLRISEN_ICM', 'VLROUTR_ICM', 'VLRFRETE',
                'PERPIS_REC', 'VLRBASE_PIS', 'VLRIMP_PISREC', 'PERCOF_REC',
                'VLRIMP_COFREC', 'VLRRET_COF', 'PERISS', 'VLRBASE_ISS',
                'VLRIMP_ISS', 'PERINS'
            ];

            foreach ($result as $key => $row) {
                foreach ($row as $col => $val) {
                    // Converte colunas numéricas
                    if (in_array($col, $colunasNumericas)) {
                        $result[$key][$col] = floatval($val);
                    }
                    // Converte texto para UTF-8, exceto colunas ignoradas
                    elseif (is_string($val) && !in_array($col, $colunasIgnorarUtf8)) {
                        $result[$key][$col] = utf8_encode($val);
                    }
                }
            }


            return new JsonModel([
                'success' => true,
                'data' => $result,
                'totalCount' => count($result)
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function listConferenciaNotasSaidaServicoAction()
    {
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        $empresa = $this->params()->fromQuery('empresa', null);
        $filial = $this->params()->fromQuery('filial', null);
        $dataInicio = $this->params()->fromQuery('dataInicio', null);
        $dataFim = $this->params()->fromQuery('dataFim', null);
        $chavenfe = $this->params()->fromQuery('chavenfe', null);

        try {
            $sql = $this->ContabilidadeRepository ? $this->ContabilidadeRepository->getConferenciaSaidasServico($empresa, $filial, $dataInicio, $dataFim, $chavenfe) : '';

            $result = [];
            if ($sql) {
                $result = $this->oracleService->executeQuery($sql);
            }

            $colunasIgnorarUtf8 = ['SITNFENTRADA_DESCR', 'CTE_TPORI']; // adicione aqui as colunas que não quer converter
            $colunasNumericas = [
                'VLRBRU', 'VLRDSC', 'VLRLIQ', 'VLRLIQ_DIF',
                'VLRBASE_ICM', 'PERICM', 'VLRICM', 'VLRBASE_ICMCREDEFET',
                'VLRIMP_ICMCREDEFET', 'VLRISEN_ICM', 'VLROUTR_ICM', 'VLRFRETE',
                'PERPIS_REC', 'VLRBASE_PIS', 'VLRIMP_PISREC', 'PERCOF_REC',
                'VLRIMP_COFREC', 'VLRRET_COF', 'PERISS', 'VLRBASE_ISS',
                'VLRIMP_ISS', 'PERINS'
            ];

            foreach ($result as $key => $row) {
                foreach ($row as $col => $val) {
                    // Converte colunas numéricas
                    if (in_array($col, $colunasNumericas)) {
                        $result[$key][$col] = floatval($val);
                    }
                    // Converte texto para UTF-8, exceto colunas ignoradas
                    elseif (is_string($val) && !in_array($col, $colunasIgnorarUtf8)) {
                        $result[$key][$col] = utf8_encode($val);
                    }
                }
            }


            return new JsonModel([
                'success' => true,
                'data' => $result,
                'totalCount' => count($result)
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    

}