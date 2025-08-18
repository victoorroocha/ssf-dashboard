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
            $pagedData = $result; 


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


    #region Estrutura Contas
        public function estruturaContasAction()
        {
            $session = new Container('auth');
        
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel();
        }
        public function listarPlanoContaAction()
        {
            try {
                $planoContas = $this->ControladoriaRepository->listarPlanoContas();

                return new JsonModel([
                    'success' => true,
                    'data' => $planoContas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar contas: ' . $e->getMessage(),
                ]);
            }
        }
        public function inserirPlanoContaAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            $data['parent_id'] = $data['parent_id'] == 0 ? null : $data['parent_id'];

            try {
                $this->ControladoriaRepository->inserirPlanoConta($data);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Plano Conta inserido com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao inserir Plano Conta: ' . $e->getMessage(),
                ]);
            }
        }
        public function atualizarPlanoContaAction()
        {
            if (!$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->ControladoriaRepository->atualizarPlanoConta($data);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Plano Conta atualizado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao atualizar Plano Conta: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirPlanoContaAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->ControladoriaRepository->excluirPlanoConta($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Plano Conta excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir Plano Conta: ' . $e->getMessage(),
                ]);
            }
        }
        public function buscarDetalhesClactaAction()
        {
            // Verifica se o serviço Oracle está disponível
            if (!$this->oracleService) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Serviço Oracle não disponível'
                ]);
            }

            // Captura o parâmetro da requisição
            $clacta = $this->params()->fromQuery('clacta', null);

            if (!$clacta) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Classificação não informada.'
                ]);
            }

            try {
                // Consulta no Softsul todos pedidos
                $sql = $this->ControladoriaRepository ? $this->ControladoriaRepository->getBuscarDetalhesClactaQuery($clacta) : '';
                $result = [];
                if ($sql) {
                    // Executa a consulta Oracle, caso tenha uma consulta
                    $result = $this->oracleService->executeQuery($sql);

                    if (count($result)) {
                        $result[0]['DESCTA'] = utf8_encode($result[0]['DESCTA']);
                    }
                }


                // Retorna os dados como JSON
                return new JsonModel([
                    'success' => true,
                    'data' => count($result) > 0 ? $result[0] : array()
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    #endRegion

    #region Cadastro Grupos Contas
        public function cadastroGrupoContasAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarGrupoContasAction()
        {
            try {
                $grupoContas = $this->ControladoriaRepository->listarGrupoContas();

                return new JsonModel([
                    'success' => true,
                    'data' => $grupoContas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar áreas: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarGrupoContasAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                if ($this->getRequest()->isPut()) {
                    $this->ControladoriaRepository->salvarGrupoContas($data);
                    $message = 'Grupo Contas atualizada com sucesso!';
                } else {
                    $this->ControladoriaRepository->salvarGrupoContas($data);
                    $message = 'Grupo Contas adicionada com sucesso!';
                }
                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar Grupo Contas: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirGrupoContasAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->ControladoriaRepository->excluirGrupoContas($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Grupo Contas excluída com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir Grupo Contas: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupGrupoContasAction()
        {
            try {
                $grupoContas = $this->ControladoriaRepository->getLookupGrupoContas();

                return new JsonModel([
                    'success' => true,
                    'data' => $grupoContas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar áreas: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion

    #region Cadastro Pacote Contas
        public function cadastroPacoteContasAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarPacoteContasAction()
        {
            try {
                $pacoteContas = $this->ControladoriaRepository->listarPacoteContas();

                return new JsonModel([
                    'success' => true,
                    'data' => $pacoteContas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar Pacotes de Contas: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarPacoteContasAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->ControladoriaRepository->salvarPacoteContas($data);
                $message = $this->getRequest()->isPut()
                    ? 'Pacote de Contas atualizado com sucesso!'
                    : 'Pacote de Contas adicionado com sucesso!';

                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar Pacote de Contas: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirPacoteContasAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->ControladoriaRepository->excluirPacoteContas($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Pacote de Contas excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir Pacote de Contas: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupPacoteContasAction()
        {
            try {
                $pacoteContas = $this->ControladoriaRepository->getLookupPacoteContas();

                return new JsonModel([
                    'success' => true,
                    'data' => $pacoteContas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar áreas: ' . $e->getMessage(),
                ]);
            }
        }
    #endregion



}