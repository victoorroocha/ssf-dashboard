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
                  ->addRole(new Role('RTV'))
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
            'ContabilidadeController',
            'RecursosHumanosController',
            'ComercialController',
            'PlanejamentoControleManutencaoController',
            'PlanejamentoControleProducaoController',
            'TiInfraController',
            'CompressController',
            'VendasController'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'fetchPedidosStatusControleDocumentos',
            'marcarDocumentosEnviados'
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);

        $this->acl->allow('Diretor', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Diretor', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Diretor', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Diretor', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'fetchPedidosStatusControleDocumentos',
            'marcarDocumentosEnviados'
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);
        $this->acl->allow('Gerente', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Gerente', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Gerente', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Gerente', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'fetchPedidosStatusControleDocumentos',
            'marcarDocumentosEnviados'
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);
        $this->acl->allow('Coordenador', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Coordenador', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Coordenador', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Coordenador', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'fetchPedidosStatusControleDocumentos',
            'marcarDocumentosEnviados'
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);
        $this->acl->allow('Encarregado', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Encarregado', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Encarregado', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Encarregado', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'fetchPedidosStatusControleDocumentos',
            'marcarDocumentosEnviados'
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);
        $this->acl->allow('Analista', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Analista', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Analista', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Analista', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'fetchPedidosStatusControleDocumentos',
            'marcarDocumentosEnviados'
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);
        $this->acl->allow('Assistente', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Assistente', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Assistente', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Assistente', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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
            'toggleDocumentoEnviadoPedido',
            'toggleGarantiaEnviadoPedido',
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
            'getLookupPacoteContas',

            'vincularContaCentroCusto',
            'listarGestores',
            'listarCentrosCusto',
            'listarGruposContas',
            'listarVinculoContaCcu',
            'listarPlanoContaAnaliticas',
            'buscarGestorPorCcu',
            'salvarVinculoContaCcu',
            'salvarGrupoContaCcu',
            'atualizarGestorCcu',
            'excluirVinculoContaCcu'
        ]);
        $this->acl->allow('Auxiliar', 'RecursosHumanosController', ['apuracoesColaboradores', 'listLancamentosApuracoesColaboradores', 'getLookupColaborador', 'getLookupSupervisor', 'getLookupCentroCusto', 'getLookupEscala', 'getLookupFilial', 'getLookupLocal', 'bancoHoras', 'listBancoHoras', 'dashboardTurnover', 'listInfoDashboardTurnover']);
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
        $this->acl->allow('Auxiliar', 'PlanejamentoControleProducaoController', [
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',
            'cadastroFuncionario',
            'listarFuncionarios',
            'salvarFuncionario',
            'excluirFuncionario',
            'getLookupFuncionarios',
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getCentroCustoLookup',
            'getProdutosEstoqueLookup',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Auxiliar', 'TiInfraController', [
            // ===== CADASTRO DEPARTAMENTOS =====
            'cadastroDepartamento',
            'listarDepartamentos',
            'salvarDepartamento',
            'excluirDepartamento',
            'getLookupDepartamentos',

            // ===== TIPO EQUIPAMENTO =====
            'cadastroTipoEquipamento',
            'listarTipoEquipamento',
            'salvarTipoEquipamento',
            'excluirTipoEquipamento',
            'getLookupTipoEquipamento',

            // ===== ACESSÓRIOS =====
            'cadastroAcessorio',
            'listarAcessorios',
            'salvarAcessorio',
            'excluirAcessorio',
            'getLookupAcessorios',

            // ===== EQUIPAMENTOS =====
            'cadastroEquipamento',
            'listarEquipamentos',
            'salvarEquipamento',
            'excluirEquipamento',
            'getLookupEquipamentos',
            'carregarImagensEquipamento',
            'removerImagemEquipamento',
            'clonarEquipamento',

            // ===== CONTROLE DE EMPRÉSTIMO =====
            'controleEmprestimo',
            'listarControlesEmprestimo',
            'salvarControleEmprestimo',
            'excluirControleEmprestimo',
            'marcarDevolucaoEquipamento',
            'getInfoTermoEmprestimo',

            // ===== LOOKUPS ORACLE =====
            'getUsuariosSeniorLookup',
            'listarCentrosCusto',
            'getLookupCentrosCusto',
            'getProdutosEstoqueLookup', // ainda não existe no controller, mas já estava listado
            'getLookupEmpresa',
            'getLookupFilial',

            // ===== SESSÃO =====
            'getUsuarioSessao'
        ]);
        $this->acl->allow('Auxiliar', 'ContabilidadeController', [
           'conferenciaEntradasCte',
           'listConferenciaEntradasCte'
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

        // RTV 
        $this->acl->allow('RTV', 'VendasController', ['RtvPerfomance']);
    }

    public function getAcl()
    {
        return $this->acl;
    }
}
