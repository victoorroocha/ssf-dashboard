<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\PlanejamentoControleManutencaoRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class PlanejamentoControleManutencaoController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $PlanejamentoControleManutencaoRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, PlanejamentoControleManutencaoRepository $PlanejamentoControleManutencaoRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->PlanejamentoControleManutencaoRepository = $PlanejamentoControleManutencaoRepository;
    }

    #region Cadastro Areas Técnicas
        public function cadastroAreaAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarAreasAction()
        {
            try {
                $areas = $this->PlanejamentoControleManutencaoRepository->listarAreas();

                return new JsonModel([
                    'success' => true,
                    'data' => $areas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar áreas: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarAreaAction()
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
                    $this->PlanejamentoControleManutencaoRepository->salvarArea($data);
                    $message = 'Área atualizada com sucesso!';
                } else {
                    $this->PlanejamentoControleManutencaoRepository->salvarArea($data);
                    $message = 'Área adicionada com sucesso!';
                }
                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar área: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirAreaAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->excluirArea($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Área excluída com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir área: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupAreasAction()
        {
            try {
                $areas = $this->PlanejamentoControleManutencaoRepository->getLookupAreas();

                return new JsonModel([
                    'success' => true,
                    'data' => $areas,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar áreas: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion

    #region Cadastro Setores
        public function cadastroSetorAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarSetoresAction()
        {
            try {
                $setores = $this->PlanejamentoControleManutencaoRepository->listarSetores();
                return new JsonModel([
                    'success' => true,
                    'data' => $setores,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar setores: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarSetorAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->salvarSetor($data);
                $message = $this->getRequest()->isPut()
                    ? 'Setor atualizado com sucesso!'
                    : 'Setor adicionado com sucesso!';
                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar setor: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirSetorAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->excluirSetor($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Setor excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir setor: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupSetoresAction()
        {
            try {
                $setores = $this->PlanejamentoControleManutencaoRepository->getLookupSetores();
                return new JsonModel([
                    'success' => true,
                    'data' => $setores,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar setores: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion
    
    #region Cadastro Tipos de Manutenção
        public function cadastroTipoManutencaoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarTiposManutencaoAction()
        {
            try {
                $tipos = $this->PlanejamentoControleManutencaoRepository->listarTiposManutencao();
                return new JsonModel([
                    'success' => true,
                    'data' => $tipos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar tipos de manutenção: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarTipoManutencaoAction()
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
                    $this->PlanejamentoControleManutencaoRepository->salvarTipoManutencao($data);
                    $message = 'Tipo de manutenção atualizado com sucesso!';
                } else {
                    $this->PlanejamentoControleManutencaoRepository->salvarTipoManutencao($data);
                    $message = 'Tipo de manutenção adicionado com sucesso!';
                }
                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar tipo de manutenção: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirTipoManutencaoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->excluirTipoManutencao($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Tipo de manutenção excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir tipo de manutenção: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupTiposManutencaoAction()
        {
            try {
                $tipos = $this->PlanejamentoControleManutencaoRepository->getLookupTiposManutencao();
                return new JsonModel([
                    'success' => true,
                    'data' => $tipos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar tipos de manutenção: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion

    #region Cadastro Técnicos
        public function cadastroTecnicoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarTecnicosAction()
        {
            try {
                $tecnicos = $this->PlanejamentoControleManutencaoRepository->listarTecnicos();
                return new JsonModel([
                    'success' => true,
                    'data' => $tecnicos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar técnicos: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarTecnicoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->salvarTecnico($data);
                return new JsonModel([
                    'success' => true,
                    'message' => $this->getRequest()->isPut() ? 'Técnico atualizado com sucesso!' : 'Técnico adicionado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao salvar técnico: ' . $e->getMessage()]);
            }
        }
        public function excluirTecnicoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->excluirTecnico($data['id']);
                return new JsonModel(['success' => true, 'message' => 'Técnico excluído com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao excluir técnico: ' . $e->getMessage()]);
            }
        }
        public function getLookupTecnicosAction()
        {
            try {
                $tecnicos = $this->PlanejamentoControleManutencaoRepository->getLookupTecnicos();
                return new JsonModel([
                    'success' => true,
                    'data' => $tecnicos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar técnicos: ' . $e->getMessage(),
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
                $equipamentos = $this->PlanejamentoControleManutencaoRepository->listarEquipamentos();
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
                $this->PlanejamentoControleManutencaoRepository->salvarEquipamento($data);
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
                $this->PlanejamentoControleManutencaoRepository->excluirEquipamento($data['id']);
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
            try {
                $equipamentos = $this->PlanejamentoControleManutencaoRepository->getLookupEquipamentos();

                foreach ($equipamentos as $key => $equipamento) {
                    $equipamentos[$key]['nome'] = $equipamento['codigo'] . ' - ' . $equipamento['nome'];
                }

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
    #endRegion

    #region Programação Manutenção Preventiva
        public function programacaoManPreventivaAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarProgramacaoPreventivaAction()
        {
            try {
                $dados = $this->PlanejamentoControleManutencaoRepository->listarProgramacoesPreventivas();
                return new JsonModel(['success' => true, 'data' => $dados]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao listar: ' . $e->getMessage()]);
            }
        }
        public function salvarProgramacaoPreventivaAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->salvarProgramacaoPreventiva($data);
                return new JsonModel([
                    'success' => true,
                    'message' => $this->getRequest()->isPut() ? 'Atualizada com sucesso!' : 'Cadastrada com sucesso!'
                ]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
            }
        }
        public function excluirProgramacaoPreventivaAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->excluirProgramacaoPreventiva($data['id']);
                return new JsonModel(['success' => true, 'message' => 'Excluída com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
            }
        }
        public function aprovarProgramacaoPreventivaAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->aprovarProgramacaoPreventiva($data);
                return new JsonModel(['success' => true, 'message' => 'Programação aprovada e pendente criada com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao aprovar programação: ' . $e->getMessage()]);
            }
        }
        public function reprovarProgramacaoPreventivaAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->reprovarProgramacaoPreventiva($data);
                return new JsonModel(['success' => true, 'message' => 'Programação reprovada com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao reprovar programação: ' . $e->getMessage()]);
            }
        }
    #endRegion

    #region Controle de Manutenção
        public function controleManutencaoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }

            // Atualiza status dos equipamentos antes de exibir a tela
            try {
                $this->PlanejamentoControleManutencaoRepository->atualizarStatusEquipamentos();
            } catch (\Exception $e) {
            }

            return new ViewModel();
        }
        public function listarControlesManutencaoAction()
        {
            try {
                $data = $this->PlanejamentoControleManutencaoRepository->listarControlesManutencao();
                return new JsonModel(['success' => true, 'data' => $data]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao listar controles de manutenção: ' . $e->getMessage()]);
            }
        }
        public function salvarControleManutencaoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->salvarControleManutencao($data);
                return new JsonModel([
                    'success' => true,
                    'message' => $this->getRequest()->isPut() ? 'Controle atualizado com sucesso!' : 'Controle adicionado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao salvar controle: ' . $e->getMessage()]);
            }
        }
        public function excluirControleManutencaoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->excluirControleManutencao($data['id']);
                return new JsonModel(['success' => true, 'message' => 'Controle excluído com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao excluir controle: ' . $e->getMessage()]);
            }
        }
        public function finalizarManutencaoAction()
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
                $this->PlanejamentoControleManutencaoRepository->finalizarManutencao($dados);
                return new JsonModel(['success' => true, 'message' => 'Controle finalizado com sucesso']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao finalizar: ' . $e->getMessage()]);
            }
        }
    #endRegion

    #region Controle de Manutenção
        public function getInfoOrdemServicoAction()
        {
            try {
                $id = (int) $this->params()->fromQuery('id'); 
                if (!$id) {
                    return new JsonModel(['success' => false, 'message' => 'ID não informado.']);
                }
                $data = $this->PlanejamentoControleManutencaoRepository->getInfoOrdemServico($id);

                return new JsonModel(['success' => true, 'data' => $data]);

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao buscar informações para ordem de serviço: ' . $e->getMessage()
                ]);
            }
        }
    #endregion

    public function getUsuariosSeniorLookupAction()
    {
        $request = $this->getRequest();

        $sql = "SELECT DISTINCT
                    R034FUN.NUMCAD AS MATRICULA,
                    R034FUN.NOMFUN AS NOME_COLABORADOR,
                    LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0') AS CPF,
                    R024CAR.TITCAR AS DSC_CARGO,
                    CASE WHEN LENGTH(REGEXP_REPLACE(R033PES.DDDTEL, '[^0-9]', '')) = 2
                        AND LENGTH(REGEXP_REPLACE(R033PES.NUMTEL, '[^0-9]', '')) >= 8
                        THEN REGEXP_REPLACE(CONCAT(R033PES.DDDTEL, R033PES.NUMTEL), '[^0-9]', '')
                        ELSE NULL
                    END NUMTEL,
                    CASE WHEN LENGTH(REGEXP_REPLACE(R033PES.DDDCEL, '[^0-9]', '')) = 2
                        AND LENGTH(REGEXP_REPLACE(R033PES.NUMCEL, '[^0-9]', '')) >= 8
                        THEN REGEXP_REPLACE(CONCAT(R033PES.DDDCEL, R033PES.NUMCEL), '[^0-9]', '')
                        ELSE NULL
                    END NUMCEL
                FROM VETORH.R034FUN
                LEFT JOIN VETORH.R033PES ON R033PES.CADAUX = R034FUN.NUMCAD AND R033PES.EMPAUX = R034FUN.NUMEMP AND R033PES.NUMCPF = R034FUN.NUMCPF
                LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR
                WHERE R034FUN.SITAFA <> 7
                AND R034FUN.NUMEMP = 5
                ORDER BY R034FUN.NOMFUN";

        try {
            $result = $this->oracleService->executeQuery($sql, ['search' => $search]);

            foreach ($result as $key => $row) {
                $result[$key]['MATRICULA'] = intval($row['MATRICULA']);
                $result[$key]['NOME'] = utf8_encode($row['NOME']);
                $result[$key]['DSC_CARGO'] = utf8_encode($row['DSC_CARGO']);
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
    public function getCentroCustoLookupAction()
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
    public function getProdutosEstoqueLookupAction()
    {
        $request = $this->getRequest();
        $search = strtoupper($this->params()->fromQuery('search', ''));

        $ands = "";
        if (!empty($search)) {
            $ands .= "AND REGEXP_REPLACE(TRIM(MVP.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(PRO.DESPRO), '\s+', ' ')), '\s+', ' ') LIKE '%{$search}%'";
        }

        $sql = "WITH UltimaMovimentacao AS (
                    SELECT 
                        MVP.CODEMP,
                        MVP.CODPRO,
                        REGEXP_REPLACE(TRIM(PRO.DESPRO), '\s+', ' ') AS DESPRO,
                        PRO.UNIMED,
                        MVP.CODDEP,
                        REGEXP_REPLACE(TRIM(DEP.DESDEP), '\s+', ' ') AS DESDEP,
                        MVP.DATMOV,
                        MVP.SEQMOV,
                        MVP.ESTEOS,
                        MVP.PRMEST,
                        MVP.QTDEST,
                        MVP.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(PRO.DESPRO), '\s+', ' ') AS PRODUTO_DISPLAY,
                        ROW_NUMBER() OVER (
                            PARTITION BY MVP.CODPRO
                            ORDER BY MVP.DATMOV DESC, MVP.SEQMOV DESC
                        ) AS RN
                    FROM E210MVP MVP
                    LEFT JOIN E075PRO PRO ON PRO.CODEMP = MVP.CODEMP AND PRO.CODPRO = MVP.CODPRO
                    LEFT JOIN E205DEP DEP ON DEP.CODEMP = MVP.CODEMP AND DEP.CODDEP = MVP.CODDEP
                    WHERE MVP.CODEMP = 5
                    AND PRO.SITPRO = 'A'
                    AND MVP.QTDEST > 0
                    {$ands}
                )
                SELECT * FROM (
                    SELECT 
                        CODEMP,
                        CODPRO,
                        DESPRO,
                        UNIMED,
                        CODDEP,
                        DESDEP,
                        QTDEST,
                        PRMEST,
                        PRODUTO_DISPLAY
                    FROM UltimaMovimentacao
                    WHERE RN = 1
                    ORDER BY DESPRO, DESDEP
                )";

        try {
            $result = $this->oracleService->executeQuery($sql);

            foreach ($result as $key => $row) {
                $result[$key]['DESPRO'] = utf8_encode($row['DESPRO']);
                $result[$key]['DESDEP'] = utf8_encode($row['DESDEP']);
                $result[$key]['PRODUTO_DISPLAY'] = utf8_encode($row['PRODUTO_DISPLAY']);
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


}