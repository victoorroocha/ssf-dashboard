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

    #region Vincular Conta x Centro de Custo
        public function vincularContaCentroCustoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarGestoresAction()
        {
            try {
                $sql = "
                    SELECT 
                        id, 
                        (id || ' - ' || nome) as dsc 
                    FROM usuario 
                    WHERE ativo = true
                    ORDER BY nome
                ";

                $statement = $this->pgAdapter->createStatement($sql);
                $result = $statement->execute();

                $data = [];
                foreach ($result as $row) {
                    $data[] = [
                        'id'   => (int)$row['id'],
                        'nome' => $row['dsc']
                    ];
                }

                return new JsonModel([
                    'success' => true,
                    'data' => $data
                ]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar gestores: ' . $e->getMessage()
                ]);
            }
        }
        public function listarCentrosCustoAction()
        {
            if (!$this->oracleService) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Serviço Oracle não disponível'
                ]);
            }

            try {
                $sql = "SELECT
                            CODCCU as ID
                            ,CODCCU || ' - ' || DESCCU AS DSC
                        FROM sapiens.E044CCU
                        WHERE CODEMP = 1000
                        ORDER BY CODCCU";

                $result = $this->oracleService->executeQuery($sql);
                foreach ($result as $key => $row) {
                    $result[$key]['ID'] = intval($row['ID']);
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }

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
        public function listarGruposContasAction()
        {
            try {
                $sql = "SELECT id, nome FROM ctr_grupo_contas WHERE flg_ativo = true ORDER BY nome";
                $statement = $this->pgAdapter->createStatement($sql);
                $result = $statement->execute();

                $data = [];
                foreach ($result as $row) {
                    $data[] = [
                        'id'   => (int)$row['id'],
                        'nome' => $row['nome']
                    ];
                }

                return new JsonModel([
                    'success' => true,
                    'data' => $data
                ]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar Grupos Contas: ' . $e->getMessage()
                ]);
            }
        }
        public function listarVinculoContaCcuAction()
        {
            try {
                $codccu = $this->params()->fromQuery('codccu', null);
                $referencia = $this->params()->fromQuery('referencia', null);

                if (!$codccu) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Código do Centro de Custo não informado.'
                    ]);
                }

                $vinculos = $this->ControladoriaRepository->listarVinculoContaCcu($codccu, $referencia);

                return new JsonModel([
                    'success' => true,
                    'data' => $vinculos
                ]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar vínculos: ' . $e->getMessage(),
                ]);
            }
        }
        public function listarPlanoContaAnaliticasAction()
        {
            try {
                $planoContas = $this->ControladoriaRepository->listarPlanoContaAnaliticas();

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
        public function buscarGestorPorCcuAction()
        {
            try {
                $codccu = $this->params()->fromQuery('codccu');

                $sql = "
                    SELECT DISTINCT id_usuario_gestor AS gestor
                    FROM ctr_vinculo_contas_ccu
                    WHERE codccu = :ccu AND id_usuario_gestor IS NOT NULL
                    LIMIT 1
                ";

                $result = $this->pgAdapter->createStatement($sql, [
                    'ccu' => $codccu
                ])->execute()->current();

                return new JsonModel([
                    'success' => true,
                    'gestor' => $result ? intval($result['gestor']) : null
                ]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        public function salvarVinculoContaCcuAction()
        {
            try {
                $data = json_decode($this->getRequest()->getContent(), true);

                if (!$data || 
                    !isset($data['codccu']) || 
                    !isset($data['id_plano_contas']) || 
                    !isset($data['id_usuario_gestor']) ||
                    !isset($data['ctared'])) 
                {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Parâmetros incompletos.'
                    ]);
                }

                if (!isset($data['referencia'])) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Referência não informada.'
                    ]);
                }

                $resp = $this->ControladoriaRepository->salvarVinculoContaCcu(
                    $data['codccu'],
                    $data['id_plano_contas'],
                    $data['ctared'],
                    $data['id_usuario_gestor'],
                    $data['referencia']
                );

                return new JsonModel($resp);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar vínculo: ' . $e->getMessage()
                ]);
            }
        }
        public function salvarGrupoContaCcuAction()
        {
            try {
                $data = json_decode($this->getRequest()->getContent(), true);

                if (!$data || !isset($data['id']) || !isset($data['id_grupo'])) {
                    return new JsonModel(['success' => false, 'message' => 'Parâmetros inválidos.']);
                }

                if (!isset($data['referencia'])) {
                    return new JsonModel(['success' => false, 'message' => 'Referência não informada.']);
                }

                $resp = $this->ControladoriaRepository->atualizarGrupoVinculo(
                    $data['id'],
                    $data['id_grupo'],
                    $data['referencia']
                );

                return new JsonModel($resp);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao atualizar: ' . $e->getMessage()
                ]);
            }
        }
        public function atualizarGestorCcuAction()
        {
            try {
                $data = json_decode($this->getRequest()->getContent(), true);

                if (!$data || 
                    !isset($data['codccu']) || 
                    !isset($data['id_usuario_gestor']) ||
                    !isset($data['referencia'])) 
                {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Parâmetros inválidos.'
                    ]);
                }

                $resp = $this->ControladoriaRepository->atualizarGestorCcu(
                    $data['codccu'],
                    $data['id_usuario_gestor'],
                    $data['referencia']
                );

                return new JsonModel($resp);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao atualizar gestor: ' . $e->getMessage()
                ]);
            }
        }
        public function excluirVinculoContaCcuAction()
        {
            try {
                $id = $this->params()->fromRoute('id');
                $referencia = $this->params()->fromQuery('referencia') 
                    ?? $this->params()->fromPost('referencia');

                if (!$id) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'ID do vínculo não informado.'
                    ]);
                }

                $resp = $this->ControladoriaRepository->excluirVinculoContaCcu($id, $referencia);

                return new JsonModel($resp);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir vínculo: ' . $e->getMessage(),
                ]);
            }
        }
        public function importarValorOrcadoAction()
        {
            try {
                $request = $this->getRequest();

                if (!$request->isPost()) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Método inválido.'
                    ]);
                }

                // Dados vindos do FormData
                $codccu     = $this->params()->fromPost('codccu');
                $referencia = $this->params()->fromPost('referencia');
                $gestor     = $this->params()->fromPost('gestor');

                if (!$codccu || !$referencia || !$gestor) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Parâmetros obrigatórios não informados.'
                    ]);
                }

                // Arquivo
                $files = $request->getFiles()->toArray();
                if (!isset($files['file']) || $files['file']['error'] !== UPLOAD_ERR_OK) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Arquivo não enviado ou inválido.'
                    ]);
                }

                $uploaded = $files['file'];
                $ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, ['csv', 'xlsx'])) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Formato inválido. Utilize CSV ou XLSX.'
                    ]);
                }

                // Ler o arquivo
                $dadosImportados = [];

                if ($ext === 'csv') {
                    $handle = fopen($uploaded['tmp_name'], 'r');
                    if (!$handle) {
                        return new JsonModel([
                            'success' => false,
                            'message' => 'Erro ao ler arquivo CSV.'
                        ]);
                    }

                    // Pular header
                    fgetcsv($handle, 0, ';');

                    while (($row = fgetcsv($handle, 0, ';')) !== false) {
                        if (count($row) < 2) continue;

                        $dadosImportados[] = [
                            'ctared'       => trim($row[0]),
                            'valor_orcado' => floatval(str_replace(',', '.', $row[1]))
                        ];
                    }
                    fclose($handle);

                } else {
                    // XLSX
                    require_once __DIR__ . '/../../../../vendor/autoload.php';
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $spreadsheet = $reader->load($uploaded['tmp_name']);
                    $sheet = $spreadsheet->getSheet(0);
                    $rows = $sheet->toArray();

                    // Pular header
                    array_shift($rows);

                    foreach ($rows as $r) {
                        if (empty($r[0])) continue;

                        $dadosImportados[] = [
                            'ctared'       => trim($r[0]),
                            'valor_orcado' => floatval(str_replace(',', '.', $r[1]))
                        ];
                    }
                }

                // Chama REPOSITORY para atualizar
                $resultado = $this->ControladoriaRepository->importarValorOrcado(
                    $codccu,
                    $referencia,
                    $gestor,
                    $dadosImportados
                );

                return new JsonModel($resultado);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro na importação: ' . $e->getMessage()
                ]);
            }
        }
        public function importarVinculoCompletoAction()
        {
            try {
                $request = $this->getRequest();

                if (!$request->isPost()) {
                    return new JsonModel(['success' => false, 'message' => 'Método inválido']);
                }

                $files = $request->getFiles()->toArray();
                if (!isset($files['file']) || $files['file']['error'] !== UPLOAD_ERR_OK) {
                    return new JsonModel(['success' => false, 'message' => 'Arquivo inválido']);
                }

                $dados = [];

                $handle = fopen($files['file']['tmp_name'], 'r');
                fgetcsv($handle, 0, ';'); // header

                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    $dados[] = [
                        'codccu'     => trim($row[0]),
                        'ctared'     => trim($row[1]),
                        'referencia' => trim($row[2]),
                        'gestor'     => (int)$row[3],
                        'grupo'      => (int)$row[4],
                        'valor'      => floatval(str_replace(',', '.', $row[5]))
                    ];
                }
                fclose($handle);

                $resp = $this->ControladoriaRepository->importarVinculoCompleto($dados);

                return new JsonModel($resp);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }


        public function prepararDownloadModeloImportacaoOrcadoAction()
        {
            return new JsonModel([
                'success' => true,
                'url' => '/controladoria/download-modelo-importacao-orcado'
            ]);
        }
        public function downloadModeloImportacaoOrcadoAction()
        {
            $file = getcwd() . '/data/controladoria/modeloImportacaoOrcado.csv';

            if (!file_exists($file)) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Arquivo modelo não encontrado',
                    'debug_path' => $file
                ]);
            }

            $response = new \Laminas\Http\Response();
            $response->setContent(file_get_contents($file));

            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'text/csv; charset=UTF-8');
            $headers->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="modeloImportacaoOrcado.csv"'
            );
            $headers->addHeaderLine('Content-Length', filesize($file));

            return $response;
        }
        public function prepararDownloadModeloImportacaoVinculoCompletoAction()
        {
            return new JsonModel([
                'success' => true,
                'url' => '/controladoria/download-modelo-importacao-vinculo-completo'
            ]);
        }
        public function downloadModeloImportacaoVinculoCompletoAction()
        {
            $file = getcwd() . '/data/controladoria/modeloImportacaoVinculoCompleto.csv';

            if (!file_exists($file)) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Arquivo modelo não encontrado',
                    'debug_path' => $file
                ]);
            }

            $response = new \Laminas\Http\Response();
            $response->setContent(file_get_contents($file));

            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'text/csv; charset=UTF-8');
            $headers->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="modeloImportacaoVinculoCompleto.csv"'
            );
            $headers->addHeaderLine('Content-Length', filesize($file));

            return $response;
        }
    #endRegion

    #region Painel de Justificativas
        public function painelJustificativasOrcadoRealizadoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel([
                'usuario' => [
                    'id'              => $session->user['id'],
                    'nome'            => $session->user['nome'],
                    'email'           => $session->user['email'],
                    'role'            => $session->user['role'],
                    'id_departamento' => $session->user['id_departamento'],
                ]
            ]);
        }
        public function listarGestoresPainelJustificativaAction()
        {
            try {
                $sql = "
                    SELECT DISTINCT u.id, (u.id || ' - ' || u.nome) as dsc
                    FROM ctr_vinculo_contas_ccu cvcc
                    LEFT JOIN usuario u ON u.id = cvcc.id_usuario_gestor 
                    WHERE u.id IS NOT NULL
                    ORDER BY (u.id || ' - ' || u.nome) ASC
                ";

                $statement = $this->pgAdapter->createStatement($sql);
                $result = $statement->execute();

                $data = [];
                foreach ($result as $row) {
                    $data[] = [
                        'id'   => (int)$row['id'],
                        'nome' => $row['dsc']
                    ];
                }

                return new JsonModel([
                    'success' => true,
                    'data'    => $data
                ]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar gestores: ' . $e->getMessage()
                ]);
            }
        }
        public function listarPainelJustificativasAction() 
        {
            try {
                $idGestor   = (int) $this->params()->fromQuery('gestor');
                $referencia = $this->params()->fromQuery('referencia');

                if (!$idGestor || !$referencia) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Gestor e referência são obrigatórios.'
                    ]);
                }

                /**
                 * =========================================================
                 * FASE 1 — PAINEL BASE (Postgres)
                 * Retorna linhas por: CTARED + CCU
                 * =========================================================
                 */
                $painel = $this->ControladoriaRepository->listarPainelJustificativasOrcadoRealizado($idGestor, $referencia);

                /**
                 * =========================================================
                 * FASE 2 — REALIZADO (Oracle)
                 * Retorna por: CTARED + CCU
                 * =========================================================
                 */
                $sqlOracle  = $this->ControladoriaRepository->getRealizadoPorContaCentroCustoQuery(1000, $referencia);
                $realizados = $this->oracleService->executeQuery($sqlOracle);

                /**
                 * =========================================================
                 * FASE 3 — MAPA REALIZADO POR CCU + CTARED
                 * =========================================================
                 */
                $mapRealizado = [];
                foreach ($realizados as $r) {
                    $key = $r['CODCCU'] . '|' . $r['CTARED'];
                    $mapRealizado[$key] = (float)$r['VALOR_REALIZADO'];
                }

                /**
                 * =========================================================
                 * FASE 4 — MERGE (CCU + CTARED)
                 * =========================================================
                 */
                foreach ($painel as &$row) {
                    $key = $row['codccu'] . '|' . $row['ctared'];
                    $row['valor_realizado'] = $mapRealizado[$key] ?? 0;
                }
                unset($row);

                /**
                 * =========================================================
                 * FASE 5 — AGRUPAMENTO FINAL POR CTARED
                 * =========================================================
                 */
                $agrupadoPorConta = [];

                foreach ($painel as $row) {
                    $ctared = $row['ctared'];

                    if (!isset($agrupadoPorConta[$ctared])) {
                        $agrupadoPorConta[$ctared] = [
                            'row_key'               => $ctared . '-' . $row['referencia'],
                            'id_plano_contas'       => $row['id_plano_contas'],
                            'id_grupo_contas'       => $row['id_grupo_contas'],
                            'ctared'                => $row['ctared'],
                            'referencia'            => $row['referencia'],
                            'id_usuario_gestor'     => $row['id_usuario_gestor'],

                            'pacote_contas'         => $row['pacote_contas'],
                            'conta_descricao'       => $row['conta_descricao'],

                            // inicia zerado para somar
                            'valor_orcado'          => 0,
                            'valor_realizado'       => 0,

                            // justificativas são da CONTA
                            'valor_pressao'         => $row['valor_pressao'],
                            'justificativa_pressao' => $row['justificativa_pressao'],
                            'valor_tempo'           => $row['valor_tempo'],
                            'justificativa_tempo'   => $row['justificativa_tempo'],
                            'mes_orcado_realizar'   => $row['mes_orcado_realizar'],
                             
                            'centro_custos' => []
                        ];
                    }

                    // SOMA FINAL IGNORANDO CCU
                    $agrupadoPorConta[$ctared]['valor_orcado'] += (float)$row['valor_orcado'];
                    $agrupadoPorConta[$ctared]['valor_realizado'] += (float)$row['valor_realizado'];

                    // Guarda Centro de Custos para o Master Detail
                    $agrupadoPorConta[$ctared]['centro_custos'][] = [
                        'codccu' => (int)$row['codccu'],
                        'valor_orcado'    => (float)$row['valor_orcado'],
                        'valor_realizado' => (float)$row['valor_realizado']
                    ];
                }

                /**
                 * =========================================================
                 * FASE 6 — RETORNO FINAL PARA A TELA
                 * =========================================================
                 */
                return new JsonModel([
                    'success' => true,
                    'data'    => array_values($agrupadoPorConta)
                ]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar painel de justificativas: ' . $e->getMessage()
                ]);
            }
        }
        public function salvarJustificativaOrcadoRealizadoAction() 
        {
            try {
                $request = $this->getRequest();
                if (!$request->isPost()) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Método inválido.'
                    ]);
                }

                $data = json_decode($request->getContent(), true);
                if (!$data) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Payload inválido.'
                    ]);
                }

                $required = ['id_plano_contas','ctared','id_usuario_gestor','referencia'];
                foreach ($required as $field) {
                    if (!isset($data[$field])) {
                        return new JsonModel([
                            'success' => false,
                            'message' => "Campo obrigatório não informado: {$field}"
                        ]);
                    }
                }

                $session = new Container('auth');
                $idUsuarioAlteracao = $session->user['id'] ?? null;

                $resp = $this->ControladoriaRepository->salvarJustificativaOrcadoRealizado($data, $idUsuarioAlteracao);

                return new JsonModel($resp);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar justificativa: ' . $e->getMessage()
                ]);
            }
        }
    #endRegion



}