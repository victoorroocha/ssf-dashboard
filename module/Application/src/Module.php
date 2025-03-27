<?php
namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

class Module
{
    public function getConfig(): array
    {
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $viewModel = $e->getViewModel();  // Acesse o ViewModel da requisição atual

        // Recupera a sessão e define a variável globalmente
        $session = new Container('auth');
        $nomeUsuario = isset($session->user) ? $session->user['nome'] : 'Usuário';

        // Define a variável global para todas as views
        $viewModel->setVariable('nomeUsuario', $nomeUsuario);


        // Mandar o menu para todas views.
        $serviceManager = $application->getServiceManager();
        // Obtém o MenuRepository
        $menuRepository = $serviceManager->get('Application\Repository\MenuRepository');
        $userId = isset($session->user) ? $session->user['id'] : null;
        $isAdmin = isset($session->user) ? $session->user['role'] === 'Administrador' : null;
        if (isset($session->user)) {
            $menus = $menuRepository->fetchAllowedMenus($userId, $isAdmin);
            // Compartilha os menus com todas as views
            $viewModel = $application->getMvcEvent()->getViewModel();
            $viewModel->setVariable('menus', $menus);
        }

        // ADIÇÃO: Configuração do handler de erros (novo)
        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'handleError']
        );
        
        $eventManager->attach(
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'handleError']
        );
    }

    // ADIÇÃO: Novo método para tratamento de erros
    public function handleError(MvcEvent $e)
    {
        // Ignora se for rota de login ou se já houver resposta
        if ($e->getRouteMatch() && $e->getRouteMatch()->getMatchedRouteName() === 'login') {
            return;
        }

        // Configura o response
        $response = $e->getResponse();
        if (!$response) {
            $response = new \Laminas\Http\Response();
            $e->setResponse($response);
        }

        if ($e->getError() === \Laminas\Mvc\Application::ERROR_ROUTER_NO_MATCH || $e->getError() === \Laminas\Mvc\Application::ERROR_EXCEPTION) { // erro de rota
            $response->setStatusCode(404);
            
            $errorModel = new ViewModel();
            $errorModel->setTerminal(true);
            $errorModel->setTemplate('error/error');
            
            // Passando response para a view
            $errorModel->setVariables([
                'nomeUsuario' => 'Usuário',
                'menus' => [],
                'erro' => 404 
            ]);
            
            $e->setViewModel($errorModel);
        } else { // erro 500

            $response->setStatusCode(500);
            
            $errorModel = new ViewModel();
            $errorModel->setTerminal(true);
            $errorModel->setTemplate('error/error');
            
            $errorModel->setVariables([
                'nomeUsuario' => 'Usuário',
                'menus' => [],
                'erro' => 500 
            ]);
            
            $e->setViewModel($errorModel);
        }
    }

    public function checkAuthentication(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();  // Obtém as informações de rota
        $session = new Container('auth');  // Obtém a sessão de autenticação

        // Verifica se a ação requer autenticação
        $controllerName = $routeMatch->getParam('controller'); // Nome do controlador

        // Verifica se o controlador está dentro do namespace protegido
        if (strpos($controllerName, 'Application\Controller') === 0) {
            if (!isset($session->user)) {
                // Realiza o redirecionamento diretamente com a aplicação
                $application = $e->getApplication();
                $url = $application->getServiceManager()->get('ViewHelperManager')->get('url');
                $url = $url('login');  // Gera a URL para o login

                // Redireciona para a página de login
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);  // Status para redirecionamento
                return $response;
            }
        }
    }
}
