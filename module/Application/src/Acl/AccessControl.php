<?php

namespace Application\Acl;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

class AccessControl
{
    private $acl;

    public function __construct()
    {
        $this->acl = new Acl();

        // 1️⃣ Definir as Roles
        $this->defineRoles();

        // 2️⃣ Definir as Controllers como Resources
        $this->defineResources();

        // 3️⃣ Definir as Permissões das Roles para cada Controller
        $this->definePermissions();
    }

    private function defineRoles()
    {
        $this->acl->addRole(new Role('Convidado'))
                  ->addRole(new Role('Auxiliar'))
                  ->addRole(new Role('Assistente'))
                  ->addRole(new Role('Analista'))
                  ->addRole(new Role('Coordenador'))
                  ->addRole(new Role('Gerente'))
                  ->addRole(new Role('Diretor'))
                  ->addRole(new Role('Administrador')); 
    }

    private function defineResources()
    {
        $controllers = [
            'BaseController',
            'DbController',
            'IndexController',
            'LoginController',
            'UsuarioController',
            'MenuController',
            'CreditoECobrancaController',
            'ControladoriaController',
        ];

        foreach ($controllers as $controller) {
            $this->acl->addResource(new Resource($controller));
        }
    }

    private function definePermissions()
    {
        // Administrador
        $this->acl->allow('Administrador');

        // Diretor 
        $this->acl->allow('Diretor', 'CreditoECobrancaController', ['controleRecebimento','getLookupSafra','listControleRecebimento','saveControleRecebimento','deleteControleRecebimento','controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']);
        $this->acl->allow('Diretor', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
        $this->acl->allow('Diretor', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);

        // Gerente 
        $this->acl->allow('Gerente', 'CreditoECobrancaController', ['controleRecebimento','getLookupSafra','listControleRecebimento','saveControleRecebimento','deleteControleRecebimento','controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']);
        $this->acl->allow('Gerente', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
        $this->acl->allow('Gerente', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);

        // Coordenador 
        $this->acl->allow('Coordenador', 'CreditoECobrancaController', ['controleRecebimento','getLookupSafra','listControleRecebimento','saveControleRecebimento','deleteControleRecebimento','controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']);
        $this->acl->allow('Coordenador', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
        $this->acl->allow('Coordenador', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);

        // Analista
        $this->acl->allow('Analista', 'CreditoECobrancaController', ['controleRecebimento', 'getLookupSafra', 'listControleRecebimento','saveControleRecebimento','deleteControleRecebimento','controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']);
        $this->acl->allow('Analista', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
        $this->acl->allow('Analista', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);

        // Assistente 
        $this->acl->allow('Assistente', 'CreditoECobrancaController', ['controleRecebimento', 'getLookupSafra', 'listControleRecebimento','saveControleRecebimento','deleteControleRecebimento','controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']); 
        $this->acl->allow('Assistente', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
        $this->acl->allow('Assistente', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);

        // Auxiliar 
        $this->acl->allow('Auxiliar', 'CreditoECobrancaController', ['controleRecebimento', 'getLookupSafra', 'listControleRecebimento','saveControleRecebimento','deleteControleRecebimento','controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']); 
        $this->acl->allow('Auxiliar', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
        $this->acl->allow('Auxiliar', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);

        // Convidado
        $this->acl->allow('Convidado', 'CreditoECobrancaController', ['controleRecebimento', 'getLookupSafra', 'listControleRecebimento', 'controleRecebimentoViewFinanceiro','listControleRecebimentoEnvioFinanceiro']);
        $this->acl->allow('Convidado', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial']);
    }

    public function getAcl()
    {
        return $this->acl;
    }
}
