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
            // Recebe o parâmetro de pesquisa
            $key = $this->params()->fromQuery('key', '');
            $search = $this->params()->fromQuery('search', '');
            $search = strtoupper(trim($search));
            $offset = $this->params()->fromQuery('offset', 0);
            $limit = $this->params()->fromQuery('limit', 30);

            try {
                $result = $this->PlanejamentoControleManutencaoRepository->getLookupEquipamentos($search, $key, $offset, $limit);

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
        public function pausarProgramacaoAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $id = $this->params()->fromPost('id');
            
            try {
                $this->PlanejamentoControleManutencaoRepository->atualizarStatusProgramacao($id, 'Pausada');
                return new JsonModel(['success' => true, 'message' => 'Programação pausada com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao pausar: ' . $e->getMessage()]);
            }
        }
        public function retomarProgramacaoAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $id = $this->params()->fromPost('id');
            
            try {
                $this->PlanejamentoControleManutencaoRepository->atualizarStatusProgramacao($id, 'Ativa');
                return new JsonModel(['success' => true, 'message' => 'Programação retomada com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao retomar: ' . $e->getMessage()]);
            }
        }
        public function cancelarProgramacaoAction()
        {
            if (!$this->getRequest()->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }
            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleManutencaoRepository->cancelarProgramacao($data);
                return new JsonModel(['success' => true, 'message' => 'Programação cancelada com sucesso!']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao cancelar: ' . $e->getMessage()]);
            }
        }
        public function gerarOsPreventivaAction()
        {
            date_default_timezone_set('America/Sao_Paulo');
            try {
                $this->PlanejamentoControleManutencaoRepository->gerarOsPreventiva();

                $hora = date('Y-m-d H:i:s');
                return new JsonModel([
                    'success' => true,
                    'message' => "OS preventivas geradas com sucesso em $hora."
                ]);
            } catch (\Exception $e) {
                $hora = date('Y-m-d H:i:s');
                return new JsonModel([
                    'success' => false,
                    'message' => "Erro ao gerar OS preventiva em $hora: " . $e->getMessage()
                ]);
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
        public function validarOsApontamentosAction()
        {
            $request = $this->getRequest();

            if (!$request->isGet()) {
                return new JsonModel(['success' => false, 'message' => 'Método inválido']);
            }

            $id = $this->params()->fromQuery('id');

            if (empty($id)) {
                return new JsonModel(['success' => false, 'message' => 'ID da OS não informado']);
            }

            try {
                $valido = $this->PlanejamentoControleManutencaoRepository->validarOsApontamentos($id);
                return new JsonModel($valido);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao validar: ' . $e->getMessage()]);
            }
        }
        public function getApontamentosOsAction()
        {
            $request = $this->getRequest();
            $osId = (int)$this->params()->fromQuery('id');

            if (!$osId) {
                return new JsonModel(['success' => false, 'message' => 'ID da OS não informado']);
            }

            try {
                $dados = $this->PlanejamentoControleManutencaoRepository->getApontamentosPorOS($osId);
                return new JsonModel($dados);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao buscar apontamentos: ' . $e->getMessage()]);
            }
        }
        public function getItensUtilizadosOsAction()
        {
            $request = $this->getRequest();
            $osId = (int)$this->params()->fromQuery('id');

            if (!$osId) {
                return new JsonModel(['success' => false, 'message' => 'ID da OS não informado']);
            }

            try {
                $dados = $this->PlanejamentoControleManutencaoRepository->getItensUtilizadosPorOS($osId);
                return new JsonModel($dados);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao buscar itens: ' . $e->getMessage()]);
            }
        }
        public function apontamentosManutencaoOsAction()
        {
            $request = $this->getRequest();

            if (!$request->isPost()) {
                return new JsonModel(['success' => false, 'message' => 'Método inválido']);
            }

            $dados = json_decode($request->getContent(), true);

            if (empty($dados['id_os'])) {
                return new JsonModel(['success' => false, 'message' => 'OS não informada']);
            }

            try {
                $this->PlanejamentoControleManutencaoRepository->apontamentosManutencaoOs($dados);
                return new JsonModel(['success' => true, 'message' => 'Controle finalizado com sucesso']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao finalizar: ' . $e->getMessage()]);
            }
        }
        public function getInfoOrdemServicoAction()
        {
            try {
                $id = (int) $this->params()->fromQuery('id'); 
                if (!$id) {
                    return new JsonModel(['success' => false, 'message' => 'ID não informado.']);
                }
                $data = $this->PlanejamentoControleManutencaoRepository->getInfoOrdemServico($id);
                $dataItens = $this->PlanejamentoControleManutencaoRepository->getInfoItensOrdemServico($id);

                return new JsonModel(
                    [
                        'success' => true, 
                        'data' => array(
                            'ordemServico' => $data,
                            'itens' => $dataItens
                        )
                    ]
                );

            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao buscar informações para ordem de serviço: ' . $e->getMessage()
                ]);
            }
        }
        public function finalizarOsAction()
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
                $this->PlanejamentoControleManutencaoRepository->finalizarOs($dados);
                return new JsonModel(['success' => true, 'message' => 'Controle finalizado com sucesso']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'message' => 'Erro ao finalizar: ' . $e->getMessage()]);
            }
        }
    #endRegion

    #region Controle Retiradas Estoque
        public function retiradaEstoqueAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarItensPendentesAction()
        {
            try {
                $equipamentos = $this->PlanejamentoControleManutencaoRepository->listarItensPendentes();
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
        public function marcarRetiradaAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel(['success' => false, 'message' => 'Método não permitido.']);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            if (empty($data['ids']) || !is_array($data['ids'])) {
                return new JsonModel(['success' => false, 'message' => 'Nenhum item informado para retirada.']);
            }

            try {
                $this->PlanejamentoControleManutencaoRepository->marcarRetirada($data);
                return new JsonModel([
                    'success' => true,
                    'message' => count($data['ids']) . ' item(s) marcado(s) como retirado(s) com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao marcar retirada: ' . $e->getMessage()
                ]);
            }
        }
    #endRegion

    #region DASHBOARD Controle de Manutenção
        public function dashboardControleManutencaoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel();
        }
        public function listarDadosDashboardControleManutencaoAction()
        {
            try {
                $request = $this->getRequest();
                $params = $request->getQuery()->toArray();

                $dataInicio = $params['dataInicio'] ?? null;
                $dataFim    = $params['dataFim'] ?? null;

                if (!$dataInicio || !$dataFim) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Período não informado corretamente.'
                    ]);
                }

                $repo = $this->PlanejamentoControleManutencaoRepository;

                $resumoCards       = $repo->buscarResumoCards($dataInicio, $dataFim);
                $porTipo           = $repo->buscarPorTipoManutencao($dataInicio, $dataFim);
                $porAreaTecnica    = $repo->buscarPorAreaTecnica($dataInicio, $dataFim);
                $porEquipamento    = $repo->buscarPorEquipamento($dataInicio, $dataFim);
                $porSetor          = $repo->buscarPorSetor($dataInicio, $dataFim);
                $porTecnico        = $repo->buscarPorTecnico($dataInicio, $dataFim);

                return new JsonModel([
                    'success' => true,
                    'data' => [
                        'resumoCards' => $resumoCards,
                        'porTipo' => $porTipo,
                        'porAreaTecnica' => $porAreaTecnica,
                        'porEquipamento' => $porEquipamento,
                        'porSetor' => $porSetor,
                        'porTecnico' => $porTecnico
                    ]
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar dados do dashboard: ' . $e->getMessage()
                ]);
            }
        }
        public function detalhesCardsControleManutencaoAction()
        {
            try {
                $request = $this->getRequest();
                $params = $request->getQuery()->toArray();

                $dataInicio = $params['dataInicio'] ?? null;
                $dataFim    = $params['dataFim'] ?? null;
                $tipo       = $params['tipo'] ?? null;

                if (!$dataInicio || !$dataFim) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Período não informado corretamente.'
                    ]);
                }
      

                $repo = $this->PlanejamentoControleManutencaoRepository;
                $dados = $repo->getDetalhamentoCard($tipo, $dataInicio, $dataFim);

                $colunas = [
                    ['dataField' => 'nr_ordem_servico', 'caption' => 'Ordem Serviço', 'width' => 110, 'alignment' => 'center'],
                    ['dataField' => 'nome_tecnico', 'caption' => 'Técnico'],
                    ['dataField' => 'area_tecnica', 'caption' => 'Área Técnica'],
                    ['dataField' => 'tipo_manutencao', 'caption' => 'Tipo'],
                    ['dataField' => 'data_solicitacao', 'caption' => 'Solicitado em', 'dataType' => 'date', 'format' => 'dd/MM/yyyy'],
                    ['dataField' => 'data_inicio', 'caption' => 'Inicio', 'dataType' => 'date', 'format' => 'dd/MM/yyyy'],
                    ['dataField' => 'data_final', 'caption' => 'Fim', 'dataType' => 'date', 'format' => 'dd/MM/yyyy'],
                    ['dataField' => 'status', 'caption' => 'Status'],
                    ['dataField' => 'custo_total', 'caption' => 'Custo (R$)', 'dataType' => 'number', 'format' => ['type' => 'currency', 'currency' => 'BRL']]
                ];
                return new JsonModel([
                    'success' => true,
                    'data' => [
                        'dataSource' => $dados,
                        'collumns' => $colunas
                    ]
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao buscar os detalhes: ' . $e->getMessage()
                ]);
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
                    REGEXP_REPLACE(TRIM(PRO.DESPRO), '\\s+', ' ') AS DESPRO,
                    E210EST.UNIMED,
                    E210EST.CODDEP,
                    REGEXP_REPLACE(TRIM(DEP.DESDEP), '\\s+', ' ') AS DESDEP,
                    E210EST.QTDEST,
                    (SELECT AVG(PRMEST) FROM E210MVP WHERE CODPRO = E210EST.CODPRO AND CODDEP = E210EST.CODDEP ) AS PRMEST,
                    E210EST.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(PRO.DESPRO), '\\s+', ' ') || ' - ' || REGEXP_REPLACE(TRIM(DEP.DESDEP), '\\s+', ' ') AS PRODUTO_DISPLAY
                FROM E210EST  
                LEFT JOIN E075PRO PRO ON PRO.CODEMP = E210EST.CODEMP AND PRO.CODPRO = E210EST.CODPRO
                LEFT JOIN E205DEP DEP ON DEP.CODEMP = E210EST.CODEMP AND DEP.CODDEP = E210EST.CODDEP
                WHERE E210EST.CODEMP = 5
                AND PRO.SITPRO = 'A'
                AND E210EST.QTDEST > 0
                AND E210EST.CODDEP = 1
                {$ands}
                ORDER BY E210EST.CODPRO, E210EST.CODDEP";

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






}