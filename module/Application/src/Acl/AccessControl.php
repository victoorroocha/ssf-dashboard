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
                  ->addRole(new Role('Vendedor'))
                  ->addRole(new Role('Auxiliar'))
                  ->addRole(new Role('Assistente'))
                  ->addRole(new Role('Analista'))
                  ->addRole(new Role('Coordenador'))
                  ->addRole(new Role('Encarregado'))
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
            'DepartamentoController',
            'CreditoECobrancaController',
            'ControladoriaController',
            'RecursosHumanosController',
            'ComercialController',
            'PlanejamentoControleManutencaoController',
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
        $this->acl->allow('Diretor', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Diretor', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Diretor', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Diretor', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Diretor', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Diretor', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Diretor', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);


        // Gerente 
        $this->acl->allow('Gerente', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Gerente', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Gerente', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Gerente', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Gerente', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Gerente', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Gerente', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);

        // Coordenador 
        $this->acl->allow('Coordenador', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Coordenador', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Coordenador', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Coordenador', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Coordenador', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Coordenador', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Coordenador', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);

        // Encarregado
        $this->acl->allow('Encarregado', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Encarregado', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Encarregado', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Encarregado', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Encarregado', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Encarregado', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Encarregado', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);

        // Analista
        $this->acl->allow('Analista', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Analista', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Analista', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Analista', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Analista', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Analista', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Analista', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);

        // Assistente 
        $this->acl->allow('Assistente', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Assistente', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Assistente', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Assistente', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Assistente', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Assistente', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Assistente', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);

        // Auxiliar 
        $this->acl->allow('Auxiliar', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Auxiliar', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Auxiliar', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'saveControleRecebimento',
            'deleteControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'toggleDocumentoPedido',
            'toggleGarantiaPedido',
            'toggleDuplicataBoletoPedido',
            'salvarObservacaoPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'addOrUpdateDocumento',
            'excluirDocumento',
            'cadastroGarantiasPedido',
            'listGarantias',
            'addOrUpdateGarantia',
            'excluirGarantia',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Auxiliar', 'ControladoriaController', [
            'divergenciasCentrosCustoContas', 
            'listDivergenciasCentrosCustoContas', 
            'getLookupEmpresa', 
            'getLookupFilial', 
            'estruturaContas', 
            'listarPlanoConta',
            'inserirPlanoConta',
            'atualizarPlanoConta',
            'excluirPlanoConta',
            'buscarDetalhesClacta',
            'cadastroGrupoContas',
            'listarGrupoContas',
            'salvarGrupoContas',
            'excluirGrupoContas',
            'cadastroPacoteContas',
            'listarPacoteContas',
            'salvarPacoteContas',
            'excluirPacoteContas',
            'getLookupGrupoContas',
            'getLookupPacoteContas'
        ]);
        $this->acl->allow('Auxiliar', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Auxiliar', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
        $this->acl->allow('Auxiliar', 'PlanejamentoControleManutencaoController', [
            // Cadastro Areas Técnicas
            'cadastroArea', 
            'listarAreas', 
            'salvarArea', 
            'excluirArea',
            'getLookupAreas',
            
            // Cadastro Setores
            'cadastroSetor', 
            'listarSetores',
            'salvarSetor',
            'excluirSetor',
            'getLookupSetores',
            
            // Cadastro Tipos de Manutenção
            'cadastroTipoManutencao',
            'listarTiposManutencao',
            'salvarTipoManutencao',
            'excluirTipoManutencao',
            'getLookupTiposManutencao',
            
            // Cadastro Técnicos
            'cadastroTecnico',
            'listarTecnicos',
            'salvarTecnico',
            'excluirTecnico',
            'getLookupTecnicos',
            
            // Cadastro Equipamentos
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            
            // Programação Manutenção Preventiva
            'programacaoManPreventiva',
            'listarProgramacaoPreventiva',
            'salvarProgramacaoPreventiva',
            'pausarProgramacao',
            'retomarProgramacao',
            'cancelarProgramacao',
            'gerarOsPreventiva',
            
            // Controle de Manutenção
            'controleManutencao',
            'listarControlesManutencao',
            'salvarControleManutencao',
            'excluirControleManutencao',
            'validarOsApontamentos',
            'getApontamentosOs',
            'getItensUtilizadosOs',
            'getInfoOrdemServico',
            'finalizarOs',
            'salvarApontamentoItem',
            'excluirApontamentoItem',
            'salvarApontamentoHoras',
            'excluirApontamentoHoras',
            
            // Controle Retiradas Estoque
            'retiradaEstoque',
            'listarItensPendentes',
            'marcarRetirada',
            
            // DASHBOARD Controle de Manutenção
            'dashboardControleManutencao',
            'listarDadosDashboardControleManutencao',
            'detalhesCardsControleManutencao',
            
            // Lookups
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup'
        ]);

        // Convidado
        $this->acl->allow('Convidado', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Convidado', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Convidado', 'CreditoECobrancaController', [
            'controleRecebimento',
            'getLookupSafra',
            'listControleRecebimento',
            'controleRecebimentoViewFinanceiro',
            'listControleRecebimentoEnvioFinanceiro',
            'controleDocumentosPedido',
            'listPedidos',
            'listClientesSenior',
            'listDocumentosPedido',
            'cadastroDocumentosPedido',
            'listDocumentos',
            'cadastroGarantiasPedido',
            'listGarantias',
            'dashboardMonitoramentoPedidosSafra',
            'listarDadosMonitoramentoPedidosSafra',
            'detalhesCardsMonitoramentoPedidosSafra',
            'dashboardPropostasDocumentos',
            'listarDadosPropostasDocumentos',
            'fetchPedidosStatusControleDocumentos'
        ]);
        $this->acl->allow('Convidado', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial', 'estruturaContas', 'listarPlanoConta']);
        $this->acl->allow('Convidado', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Convidado', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);

        // Vendedor 
        $this->acl->allow('Vendedor', 'IndexController', ['index', 'filtrarDados']);
        $this->acl->allow('Vendedor', 'UsuarioController', ['perfilUsuario', 'atualizaPerfil']);
        $this->acl->allow('Vendedor', 'ControladoriaController', ['divergenciasCentrosCustoContas', 'listDivergenciasCentrosCustoContas', 'getLookupEmpresa', 'getLookupFilial', 'estruturaContas', 'listarPlanoConta']);
        $this->acl->allow('Vendedor', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras']);
        $this->acl->allow('Vendedor', 'ComercialController', ['classificacaoClientesSoftsul', 'listClassificacaoClientesSoftsul', 'listPedidosCliente']);
    }

    public function getAcl()
    {
        return $this->acl;
    }
}
