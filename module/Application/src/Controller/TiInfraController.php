<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\TiInfraRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class TiInfraController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $TiInfraRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, TiInfraRepository $TiInfraRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->TiInfraRepository = $TiInfraRepository;
    }

    // Método para obter usuário da sessão (você precisa implementar conforme sua aplicação)
    private function getUsuarioSessao()
    {
        // Exemplo de como obter o usuário da sessão
        $session = new Container('auth');
        if ($session->offsetExists('user')) {
            return $session->offsetGet('user');
        }
        
        return null;
    }

    #region Cadastro Departamentos
        public function cadastroDepartamentoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarDepartamentosAction()
        {
            try {
                $departamentos = $this->TiInfraRepository->listarDepartamentos();

                return new JsonModel([
                    'success' => true,
                    'data' => $departamentos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar departamentos: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarDepartamentoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->salvarDepartamento($data);
                $message = $this->getRequest()->isPut() ? 
                    'Departamento atualizado com sucesso!' : 
                    'Departamento adicionado com sucesso!';

                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar departamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirDepartamentoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->excluirDepartamento($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Departamento excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir departamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupDepartamentosAction()
        {
            try {
                $departamentos = $this->TiInfraRepository->getLookupDepartamentos();

                return new JsonModel([
                    'success' => true,
                    'data' => $departamentos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar departamentos: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion

    #region Cadastro Tipo Equipamento
        public function cadastroTipoEquipamentoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarTipoEquipamentoAction()
        {
            try {
                $tipoEquipamentos = $this->TiInfraRepository->listarTipoEquipamento();

                return new JsonModel([
                    'success' => true,
                    'data' => $tipoEquipamentos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar os Tipos de Equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarTipoEquipamentoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->salvarTipoEquipamento($data);
                $message = $this->getRequest()->isPut() ? 
                    'Tipo Equipamento atualizado com sucesso!' : 
                    'Tipo Equipamento adicionado com sucesso!';

                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar Tipo Equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirTipoEquipamentoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->excluirTipoEquipamento($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Tipo Equipamento excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir Tipo Equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupTipoEquipamentoAction()
        {
            try {
                $tipoEquipamento = $this->TiInfraRepository->getLookupTipoEquipamento();

                return new JsonModel([
                    'success' => true,
                    'data' => $tipoEquipamento,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar Tipo Equipamento: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion
    
    #region Cadastro Acessorios
        public function cadastroAcessorioAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarAcessoriosAction()
        {
            try {
                $acessorios = $this->TiInfraRepository->listarAcessorios();

                return new JsonModel([
                    'success' => true,
                    'data' => $acessorios,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar os Acessórios: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarAcessorioAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->salvarAcessorio($data);
                $message = $this->getRequest()->isPut() ? 
                    'Acessório atualizado com sucesso!' : 
                    'Acessório adicionado com sucesso!';

                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar Acessório: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirAcessorioAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->excluirAcessorio($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Acessório excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir Acessório: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupAcessoriosAction()
        {
            try {
                $acessorios = $this->TiInfraRepository->getLookupAcessorios();

                return new JsonModel([
                    'success' => true,
                    'data' => $acessorios,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar Acessórios: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion

    #region Cadastro Equipamentos
        public function cadastroEquipamentoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarEquipamentosAction()
        {
            try {
                $equipamentos = $this->TiInfraRepository->listarEquipamentos();
                return new JsonModel([
                    'success' => true,
                    'data' => $equipamentos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar equipamentos: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarEquipamentoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->salvarEquipamento($data);
                return new JsonModel([
                    'success' => true,
                    'message' => $this->getRequest()->isPut() ? 'Equipamento atualizado com sucesso!' : 'Equipamento adicionado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirEquipamentoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->excluirEquipamento($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Equipamento excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupEquipamentosAction() // Esse Lookup para os controles, não lista equipamentos inativos!
        {
            // Recebe o parâmetro de pesquisa
            $key = $this->params()->fromQuery('key', '');
            $search = $this->params()->fromQuery('search', '');
            $search = strtoupper(trim($search));
            $offset = $this->params()->fromQuery('offset', 0);
            $limit = $this->params()->fromQuery('limit', 30);

            try {
                $result = $this->TiInfraRepository->getLookupEquipamentos($search, $key, $offset, $limit);

                return new JsonModel([
                    'success' => true,
                    'data' => $result['data'],
                    'totalCount' => $result['totalCount']
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar equipamentos: ' . $e->getMessage(),
                ]);
            }
        }
        public function carregarImagensEquipamentoAction()
        {
            $equipamentoId = $this->params()->fromQuery('id');
            
            if (empty($equipamentoId)) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'ID do equipamento não informado.',
                ]);
            }

            try {
                $imagens = $this->TiInfraRepository->carregarImagensEquipamento($equipamentoId);
                return new JsonModel([
                    'success' => true,
                    'data' => $imagens,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao carregar imagens: ' . $e->getMessage(),
                ]);
            }
        }
        public function removerImagemEquipamentoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);
            $idImagem = $data['id_imagem'] ?? null;

            if (empty($idImagem)) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'ID da imagem não informado.',
                ]);
            }

            try {
                $this->TiInfraRepository->removerImagemEquipamento($idImagem);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Imagem removida com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao remover imagem: ' . $e->getMessage(),
                ]);
            }
        }
        public function clonarEquipamentoAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->clonarEquipamento($data);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Equipamento clonado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao clonar equipamento: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion    

    #region Controle de Emprestimo
        public function controleEmprestimoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel();
        }
        public function listarControlesEmprestimoAction()
        {
            try {
                $data = $this->TiInfraRepository->listarControlesEmprestimo();
                return new JsonModel(['success' => true, 'data' => $data]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao listar controles de empréstimo: ' . $e->getMessage()]);
            }
        }
        public function salvarControleEmprestimoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->salvarControleEmprestimo($data);
                return new JsonModel([
                    'success' => true,
                    'message' => $this->getRequest()->isPut() ? 'Atualizado com sucesso!' : 'Adicionado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
            }
        }
        public function excluirControleEmprestimoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->TiInfraRepository->excluirControleEmprestimo($data['id']);
                return new JsonModel(['success' => true, 'message' => 'Excluído com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
            }
        }
        public function getInfoTermoEmprestimoAction()
        {
            try {
                $id = (int) $this->params()->fromQuery('id'); 
                if (!$id) {
                    return new JsonModel(['success' => false, 'message' => 'ID não informado.']);
                }
                $data = $this->TiInfraRepository->getInfoTermoEmprestimo($id);

                return new JsonModel(
                    [
                        'success' => true, 
                        'data' => $data
                    ]
                );

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao buscar informações para termo de empréstimo: ' . $e->getMessage()
                ]);
            }
        }
        public function marcarDevolucaoEquipamentoAction()
        {
            $request = $this->getRequest();
           
            if (!$request->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método inválido']);
            }

            $dados = json_decode($request->getContent(), true);

            if (empty($dados['id'])) {
                return new JsonModel(['success' => false, 'message' => 'ID não informado']);
            }

            try {
                $this->TiInfraRepository->marcarDevolucaoEquipamento($dados);
                return new JsonModel(['success' => true, 'message' => 'Devolução do equipamento realizada com sucesso']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao realizar Devolução: ' . $e->getMessage()]);
            }
        }
    #endRegion


    public function getUsuariosSeniorLookupAction()
    {
        $request = $this->getRequest();

        $sql = "SELECT 
                    R034FUN.NUMEMP 
                    ,R034FUN.TIPCOL 
                    ,R034FUN.NUMCAD AS MATRICULA
                    ,R034FUN.NOMFUN AS NOME_COLABORADOR
                    ,R034FUN.NUMCAD || ' - ' || R034FUN.NOMFUN AS NUMCAD_NOME_COLABORADOR
                    ,LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0') AS  CPF
                    ,R034FUN.DATADM
                    ,TRUNC(MONTHS_BETWEEN(SYSDATE, R034FUN.DATNAS ) / 12) AS IDADE 
                    ,CASE WHEN LENGTH(REGEXP_REPLACE(R033PES.DDDTEL, '[^0-9]', '')) = 2 AND LENGTH(REGEXP_REPLACE(R033PES.NUMTEL, '[^0-9]', '')) >= 8 THEN REGEXP_REPLACE(CONCAT(R033PES.DDDTEL, R033PES.NUMTEL), '[^0-9]', '') ELSE NULL END NUMTEL            
                    ,CASE WHEN LENGTH(REGEXP_REPLACE(R033PES.DDDCEL, '[^0-9]', '')) = 2 AND LENGTH(REGEXP_REPLACE(R033PES.NUMCEL, '[^0-9]', '')) >= 8 THEN REGEXP_REPLACE(CONCAT(R033PES.DDDTEL, R033PES.NUMCEL), '[^0-9]', '') ELSE NULL END NUMCEL            
                    ,R024CAR.TITCAR AS DSC_CARGO
                FROM VETORH.R034FUN
                INNER JOIN VETORH.R010SIT ON R010SIT.CODSIT = R034FUN.SITAFA
                LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR
                LEFT JOIN VETORH.R033PES ON R033PES.CADAUX = R034FUN.NUMCAD AND R033PES.EMPAUX = R034FUN.NUMEMP AND R033PES.NUMCPF = R034FUN.NUMCPF 
                WHERE R034FUN.TIPCOL = 1
                AND R034FUN.SITAFA <> 7
                AND R034FUN.NUMEMP IN (5,12)
                ORDER BY R034FUN.NOMFUN";

        try {
            $result = $this->oracleService->executeQuery($sql, ['search' => $search]);

            foreach ($result as $key => $row) {
                $result[$key]['MATRICULA'] = intval($row['MATRICULA']);
                $result[$key]['NOME'] = utf8_encode($row['NOME']);
                $result[$key]['DSC_CARGO'] = utf8_encode($row['DSC_CARGO']);
                $result[$key]['NUMCAD_NOME_COLABORADOR'] = utf8_encode($row['NUMCAD_NOME_COLABORADOR']);
                $result[$key]['NOME_COLABORADOR'] = utf8_encode($row['NOME_COLABORADOR']);
            }

            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao buscar responsáveis: ' . $e->getMessage()
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
                    FROM E044CCU
                    WHERE CODEMP = 5
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
    public function getLookupCentrosCustoAction()
    {
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        $search = strtoupper(trim($this->params()->fromQuery('search', '')));
        $key = $this->params()->fromQuery('key', '');

        try {
            $where = "WHERE CODEMP = 5";
            if (!empty($search)) {
                $where .= " AND (CODCCU LIKE '%$search%' OR DESCCU LIKE '%$search%')";
            }
            if (!empty($key)) {
                $where .= " AND CODCCU = $key";
            }

            $sql = "SELECT
                        CODCCU as ID,
                        CODCCU || ' - ' || DESCCU AS DSC
                    FROM E044CCU
                    $where
                    ORDER BY CODCCU";

            $result = $this->oracleService->executeQuery($sql);
            foreach ($result as $key => $row) {
                $result[$key]['ID'] = intval($row['ID']);
                $result[$key]['DSC'] = utf8_encode($row['DSC']);
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
    public function getProdutosEstoqueLookupAction()
    {
        $request = $this->getRequest();
        $search = strtoupper($this->params()->fromQuery('search', ''));
        $key = $this->params()->fromQuery('key', '');

        $ands = "";
        if (!empty($key)) {
            $ands .= "AND E210EST.CODPRO = '{$key}'";
        }
        if (!empty($search)) {
            $ands .= "AND E210EST.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(PRO.DESPRO), '\\s+', ' ') || ' - ' || REGEXP_REPLACE(TRIM(DEP.DESDEP), '\\s+', ' ') LIKE '%{$search}%'";
        }

        $sql = "SELECT 
                    E210EST.CODEMP,
                    E210EST.CODPRO,
                    REGEXP_REPLACE(TRIM(upper(PRO.DESPRO)), '\\s+', ' ') AS DESPRO,
                    E210EST.UNIMED,
                    E210EST.CODDEP,
                    REGEXP_REPLACE(TRIM(upper(DEP.DESDEP)), '\\s+', ' ') AS DESDEP,
                    E210EST.QTDEST,
                    nvl((SELECT AVG(PRMEST) FROM E210MVP WHERE CODPRO = E210EST.CODPRO AND CODDEP = E210EST.CODDEP ),0) AS PRMEST,
                    E210EST.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(upper(PRO.DESPRO)), '\\s+', ' ') || ' - ' || REGEXP_REPLACE(TRIM(upper(DEP.DESDEP)), '\\s+', ' ') AS PRODUTO_DISPLAY,
                    CASE WHEN PRO.CLAPRO = 1 THEN 'Estoque' WHEN PRO.CLAPRO = 2 THEN 'Passagem Direta' END AS CLAPRO,
                    E210EST.CODEND
                FROM E210EST  
                LEFT JOIN E075PRO PRO ON PRO.CODEMP = E210EST.CODEMP AND PRO.CODPRO = E210EST.CODPRO
                LEFT JOIN E205DEP DEP ON DEP.CODEMP = E210EST.CODEMP AND DEP.CODDEP = E210EST.CODDEP
                WHERE E210EST.CODEMP = 5
                AND E210EST.CODDEP = 1
                AND PRO.SITPRO = 'A'
                AND PRO.CLAPRO IN (1,2)
                {$ands}
                ORDER BY PRO.DESPRO
                FETCH FIRST 30 ROWS ONLY";

        try {
            $result = $this->oracleService->executeQuery($sql);

            foreach ($result as $key => $row) {
                $result[$key]['DESPRO'] = utf8_encode($row['DESPRO']);
                $result[$key]['DESDEP'] = utf8_encode($row['DESDEP']);
                $result[$key]['PRODUTO_DISPLAY'] = utf8_encode($row['PRODUTO_DISPLAY']);
                $result[$key]['QTDEST'] = floatval(str_replace(',', '.', $row['QTDEST']));
                $result[$key]['PRMEST'] = floatval(str_replace(',', '.', $row['PRMEST']));
            }

            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao buscar produtos: ' . $e->getMessage()
            ]);
        }
    }
    public function getLookupEmpresaAction()
    {
        $request = $this->getRequest();
        $search = strtoupper($this->params()->fromQuery('search', ''));
        $key = $this->params()->fromQuery('key', '');

        $sql = "SELECT 
                    CODEMP as id, 
                    CODEMP || ' - ' || UPPER(NOMEMP) AS dsc 
                FROM SAPIENS.E070EMP
                WHERE CODEMP IN (5,1000)
                ORDER BY CODEMP";

        try {
            $result = $this->oracleService->executeQuery($sql, ['search' => $search]);
                        
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
                'message' => 'Erro ao buscar empresas: ' . $e->getMessage()
            ]);
        }
    }
    public function getLookupFilialAction()
    {
        $request = $this->getRequest();
        $search = strtoupper($this->params()->fromQuery('search', ''));
        $key = $this->params()->fromQuery('key', '');

        $sql = "SELECT 
                    CODEMP, 
                    CODFIL as id,
                    CODFIL || ' - ' || UPPER(SIGFIL) || ' - ' || REGEXP_REPLACE(SUBSTR(NUMCGC, 1, 2) || '.' || SUBSTR(NUMCGC, 3, 3) || '.' || SUBSTR(NUMCGC, 6, 3) || '/' || SUBSTR(NUMCGC, 9, 4) || '-' || SUBSTR(NUMCGC, 13, 2), '[^0-9./-]', '') || ' - ' || CIDFIL AS dsc 
                FROM SAPIENS.E070FIL
                WHERE 1 = 1 
                ORDER BY CODEMP, CODFIL";

        try {
            $result = $this->oracleService->executeQuery($sql, ['search' => $search]);

            foreach ($result as $key => $row) {
                $result[$key]['ID'] = intval($row['ID']);
                $result[$key]['DSC'] = utf8_encode($row['DSC']);
                $result[$key]['CODEMP'] = intval($row['CODEMP']); 
            }

            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao buscar filiais: ' . $e->getMessage()
            ]);
        }
    }

}