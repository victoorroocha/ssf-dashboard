<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\DepartamentoRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class DepartamentoController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $departamentoRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, DepartamentoRepository $departamentoRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->departamentoRepository = $departamentoRepository;
    }

    public function cadastroDepartamentoAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listDepartamentosAction()
    {

        try {
            // Obtém os parâmetros de paginação e filtros
            $skip = $this->params()->fromQuery('skip', 0);
            $take = $this->params()->fromQuery('take', 500);
            $sort = $this->params()->fromQuery('sort', null);

            // Busca os usuários no repositório
            $departamentos = $this->departamentoRepository->listarDepartamentos($skip, $take, $sort);

            return new JsonModel([
                'success' => true,
                'data' => $departamentos['data'],
                'totalCount' => $departamentos['totalCount'],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao listar departamentos: ' . $e->getMessage(),
            ]);
        }
    }
    public function addOrUpdateDepartamentoAction()
    {
        // Verifica se a requisição é do tipo POST ou PUT
        if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
            return new JsonModel([
                'success' => false,
                'message' => 'Método não permitido.',
            ]);
        }

        // Obtém os dados enviados
        $data = json_decode($this->getRequest()->getContent(), true);

        try {
            // Verifica se é uma requisição PUT (atualização)
            if ($this->getRequest()->isPut()) {
                // Atualiza o departamento existente
                $this->departamentoRepository->atualizarDepartamento($data);
                $message = 'Departamento atualizado com sucesso!';
            } else {
                // Insere um novo departamento (requisição POST)
                $this->departamentoRepository->inserirDepartamento($data);
                $message = 'Departamento adicionado com sucesso!';
            }

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
        // Verifica se a requisição é do tipo DELETE
        if (!$this->getRequest()->isDelete()) {
            return new JsonModel([
                'success' => false,
                'message' => 'Método não permitido.',
            ]);
        }

        // Obtém os dados enviados no corpo da requisição
        $data = json_decode($this->getRequest()->getContent(), true);


        try {
            // Exclui o usuário
            $this->departamentoRepository->excluirDepartamento($data['id']);

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

    public function getLookupGestorDepartamentoAction()
    {
        try {
            // Recupera o valor do filtro (pesquisa) ou do código do pedido
            $filtro = $this->getRequest()->getQuery('filtro'); 
            $id_gestor_departamento = $this->getRequest()->getQuery('id_gestor_departamento'); 

            
            $result = $this->departamentoRepository ? $this->departamentoRepository->getLookupGestorDepartamento($filtro, $id_gestor_departamento) : '';

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