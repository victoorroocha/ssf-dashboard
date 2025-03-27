<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ErrorController extends AbstractActionController
{
    public function unauthorizedAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true); // Isso desativa o layout
        return $viewModel;
    }
}