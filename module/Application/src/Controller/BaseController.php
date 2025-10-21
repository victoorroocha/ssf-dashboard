<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Permissions\Acl\Acl;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

abstract class BaseController extends AbstractActionController
{
    protected $acl;
    protected $session;

    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
        $this->session = new Container('auth');
    }

    public function onDispatch(\Laminas\Mvc\MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('action');
        $controller = $e->getRouteMatch()->getParam('controller');
        $controllerName = substr($controller, strrpos($controller, '\\') + 1);

        // Libera essa action específica
        if ($action === 'gerar-os-preventiva') {
            return parent::onDispatch($e);
        }

        // Libera o CompressController inteiro para todos
        if ($controllerName === 'CompressController') {
            return parent::onDispatch($e);
        }

        if (!isset($this->session)) {
            $this->session = new \Laminas\Session\Container('auth');
        }

        $request = $this->getRequest();
        $acceptsJson =
            $request->isXmlHttpRequest() ||
            ($request->getHeaders()->has('Accept') &&
            strpos($request->getHeaders()->get('Accept')->getFieldValue(), 'application/json') !== false);

        // 1) Sessão inexistente/expirada
        if (!isset($this->session->user)) {
            if ($acceptsJson) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Sessão expirada. Faça login novamente.'
                ], 401); // Unauthorized
            }
            return $this->redirect()->toRoute('login');
        }

        // 2) Validação por MENU/ROTA (não admin)
        if ($this->session->user['role'] !== 'Administrador') {
            $currentPath = $request->getUri()->getPath();
            if (
                !$this->usuarioTemAcessoMenu($this->session->user['id'], $currentPath) &&
                !in_array($currentPath, ['/', '/login', '/logout', '/usuario/perfil-usuario'], true)
            ) {
                if ($acceptsJson) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Acesso negado ao menu/rota.'
                    ], 403);
                }
                return $this->redirect()->toRoute('error', ['action' => 'unauthorized']);
            }
        }

        // 3) ACL por controller/action
        $role = $this->session->user['role'];
        $controller = $e->getRouteMatch()->getParam('controller');
        $controller = substr($controller, strrpos($controller, '\\') + 1);
        $actionCamel = str_replace('Action', '', $this->kebabToCamelCase($action));

        if (!$this->acl->isAllowed($role, $controller, $actionCamel)) {
            if ($acceptsJson) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar este recurso.'
                ], 403);
            }
            return $this->redirect()->toRoute('error', ['action' => 'unauthorized']);
        }

        return parent::onDispatch($e);
    }


    /**
     * Verifica se a action retorna JSON.
     */
    protected function isJsonAction()
    {
        // Verifica o cabeçalho 'Accept' da requisição
        $acceptHeader = $this->getRequest()->getHeader('Accept');
        if ($acceptHeader && strpos($acceptHeader->getFieldValue(), 'application/json') !== false) {
            return true;
        }

        // Verifica se a action retorna um JsonModel
        $actionResponse = parent::onDispatch($this->getEvent());
        return $actionResponse instanceof JsonModel;
    }

    /**
     * Retorna uma resposta JSON.
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        return $response;
    }

    /**
     * Converte kebab-case para camelCase.
     */
    protected function kebabToCamelCase($string)
    {
        // Remove hífens e converte a primeira letra após cada hífen para maiúscula
        $string = str_replace('-', '', ucwords($string, '-'));
        // Converte a primeira letra para minúscula (camelCase)
        $string = lcfirst($string);
        return $string;
    }
    protected function usuarioTemAcessoMenu($usuarioId, $currentPath)
    {
        /** @var \Laminas\Db\Adapter\Adapter $db */
        $db = $this->getEvent()
                ->getApplication()
                ->getServiceManager()
                ->get('Laminas\Db\Adapter\Adapter');

        // Pega a primeira parte da rota (ex: "/credito-e-cobranca/")
        $pathParts = explode('/', trim($currentPath, '/'));
        $moduloPrincipal = '/' . $pathParts[0] . '/'; // Formata como "/credito-e-cobranca/"

        // Verifica se o usuário tem acesso a qualquer menu dentro desse módulo
        $sql = "
            SELECT COUNT(*) AS total
            FROM usuario_menu um
            INNER JOIN menu m ON um.menu_id = m.id
            WHERE um.usuario_id = ?
            AND m.link LIKE ? || '%'
        ";
        $stmt = $db->createStatement($sql, [$usuarioId, $moduloPrincipal]);
        $result = $stmt->execute()->current();

        return !empty($result['total']);
    }
}