<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\ControladoriaRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class ControladoriaController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $ControladoriaRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, ControladoriaRepository $ControladoriaRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->ControladoriaRepository = $ControladoriaRepository;
    }

    public function divergenciasCentrosCustoContasAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listDivergenciasCentrosCustoContasAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        // Captura os parâmetros da requisição GET
        $codempresa = $this->params()->fromQuery('codempresa', null);
        $codfilial = $this->params()->fromQuery('codfilial', null);
        $lancamento_inicio = $this->params()->fromQuery('lancamento_inicio', null);
        $lancamento_fim = $this->params()->fromQuery('lancamento_fim', null);
        $skip = $this->params()->fromQuery('skip', null);
        $take = $this->params()->fromQuery('take', null);

        try {
            // Consulta no Softsul todos pedidos
            $sql = $this->ControladoriaRepository ? $this->ControladoriaRepository->getLancamentosCentrosCustoContaContas($codempresa, $codfilial, $lancamento_inicio, $lancamento_fim) : '';

            $params = [];
            if ($lancamento_inicio && $lancamento_fim) {
                $params['lancamento_inicio'] = $lancamento_inicio;
                $params['lancamento_fim'] = $lancamento_fim;
            }
            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql, $params);


                // Processa os dados do Oracle
                foreach ($result as $key => $row) {
                    // Convertendo a codificação para UTF-8
                    $result[$key]['NOMEMP'] = utf8_encode($row['NOMEMP']);
                    $result[$key]['SIGFIL'] = utf8_encode($row['SIGFIL']);
                    $result[$key]['MES_LANCAMENTO'] = utf8_encode($row['MES_LANCAMENTO']);
                    $result[$key]['DSC_CONTA_CONTABIL'] = utf8_encode($row['DSC_CONTA_CONTABIL']);
                    $result[$key]['DSC_CONTA_REDUZIDA'] = utf8_encode($row['DSC_CONTA_REDUZIDA']);
                    $result[$key]['DSC_CENTRO_CUSTO'] = utf8_encode($row['DSC_CENTRO_CUSTO']);
                    $result[$key]['HISTORICO'] = utf8_encode($row['HISTORICO']);
                    $result[$key]['CLASSIFICACAO'] = utf8_encode($row['CLASSIFICACAO']);
                    $result[$key]['TIPO_CENTRO_CUSTO'] = utf8_encode($row['TIPO_CENTRO_CUSTO']);

                    // Conversão de valores numéricos
                    $result[$key]['DEBITO'] = floatval(str_replace(',', '.', $result[$key]['DEBITO']));
                    $result[$key]['CREDITO'] = floatval(str_replace(',', '.', $result[$key]['CREDITO']));
                    $result[$key]['TOTAL'] = floatval(str_replace(',', '.', $result[$key]['TOTAL']));
                }
            }

            $totalCount = count($result);
            $pagedData = array_slice($result, $skip, 9999); 


            // Retorna os dados como JSON
            return new JsonModel([
                'success' => true,
                'data' => $pagedData,
                'totalCount' => $totalCount
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getLookupEmpresaAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            // Consulta dados na Softsul
            $sql = $this->ControladoriaRepository ? $this->ControladoriaRepository->getLookupEmpresaQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

            // Retorna os dados como JSON
            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getLookupFilialAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        // Recupera o ID da empresa enviado pela requisição
        $codempresa = $this->getRequest()->getQuery('codempresa');

        try {
            // Consulta dados na Softsul
            $sql = $this->ControladoriaRepository ? $this->ControladoriaRepository->getLookupFilialQuery($codempresa) : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

            // Retorna os dados como JSON
            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}