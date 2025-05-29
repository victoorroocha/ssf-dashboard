<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\RecursosHumanosRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class IndexController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $RecursosHumanosRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, RecursosHumanosRepository $RecursosHumanosRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->RecursosHumanosRepository = $RecursosHumanosRepository;
    }

    public function indexAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            return $this->redirect()->toRoute('login');
        }
        $dashboard = null;
        // Determina qual dashboard carregar baseado em departamento
        if ((isset($session->user['id_departamento']) && $session->user['id_departamento'] == 5)) { // dashboard Recursos Humanos || $session->user['role'] == 'Administrador'
            $dashboard = 'recursos-humanos1';
        } 

        return new ViewModel([
            'idUsuario' => $session->user['id'],
            'nomeUsuario' => $session->user['nome'],
            'dashboard' => $dashboard,
        ]);
    }

    public function filtrarDadosAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        // Captura os parâmetros da requisição GET
        $apuracao_inicio = $this->params()->fromQuery('dataInicio', null);
        $apuracao_fim = $this->params()->fromQuery('dataFim', null);
        $colaborador = $this->params()->fromQuery('colaborador', null);
        $supervisor = $this->params()->fromQuery('supervisor', null);
        $local = $this->params()->fromQuery('local', null);
        $cargo = $this->params()->fromQuery('cargo', null);

        try {
            // Define o cabeçalho corretamente antes de qualquer saída
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                              ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                              ->addHeaderLine('Pragma', 'no-cache')
                                              ->addHeaderLine('Expires', '0');

            $infoCardsAtualResult = null;
            $infoGrafico1Result = null;
            $infoGrafico2Result = null;
            $infoGrafico6Result = null;
            if (!empty($apuracao_inicio) && !empty($apuracao_fim)) {
                #region CARDS MES ATUAL
                    // Informações Cards
                    $infoCardsSQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getInformacoesCards($apuracao_inicio, $apuracao_fim, $colaborador, $supervisor, $local, $cargo) : null;
                    if ($infoCardsSQL) {
                        $infoCardsAtualResult = $this->oracleService->executeQuery($infoCardsSQL)[0];
                        $infoCardsAtualResult['MEDIA_SALARIAL'] = floatval(str_replace(',', '.', $infoCardsAtualResult['MEDIA_SALARIAL']));
                    }
                    // SALDO DE HORAS
                    $infoCardSaldoHorasSQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getInfoCardSaldoBancoHoras($apuracao_inicio, $apuracao_fim, $colaborador, $supervisor, $local, $cargo) : null;
                    if ($infoCardSaldoHorasSQL) {
                        $infoCardSaldoHoras = $this->oracleService->executeQuery($infoCardSaldoHorasSQL);
                        $infoCardsAtualResult['SALDO_HORAS'] = isset($infoCardSaldoHoras[0]['SALDO_FORMAT']) ? $infoCardSaldoHoras[0]['SALDO_FORMAT'] : null;
                    }
                #endregion

                #region Grafico Ocorrencias Dia da Semana
                    $infoGrafico1SQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getGraficoOcorrenciasDiaSemana($apuracao_inicio, $apuracao_fim, $colaborador, $supervisor, $local, $cargo) : null;
                    if ($infoGrafico1SQL) {
                        $chunkResult = $this->oracleService->executeQuery($infoGrafico1SQL);
                        
                        // Processa os dados do Oracle
                        foreach ($chunkResult  as $key => $row) {
                            // Convertendo apenas as colunas de texto para UTF-8
                            $textColumns = ['DIA_SEMANA_APU'];
                            foreach ($textColumns as $col) {
                                if (isset($row[$col])) {
                                    $row[$col] = utf8_encode($row[$col]);
                                }
                            }
                            $row['QTD_FALTAS'] = intval($row['QTD_FALTAS']);
                            $row['QTD_ADICIONAL_NOTURNO'] = intval($row['QTD_ADICIONAL_NOTURNO']);
                            $row['QTD_HORAS_EXTRAS_2HRS'] = intval($row['QTD_HORAS_EXTRAS_2HRS']);
                            $row['QTD_INTERJORNADA'] = intval($row['QTD_INTERJORNADA']);
                            $row['QTD_INTRAJORNADA'] = intval($row['QTD_INTRAJORNADA']);
                            $row['TOTAL'] = intval($row['TOTAL']);

                            // Adiciona cada linha ao array result final
                            $infoGrafico1Result[] = $row;
                        }
                    }
                #endregion

                #region Grafico Ocorrencias Supervisor
                $infoGrafico2SQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getGraficoOcorrenciasSupervisor($apuracao_inicio, $apuracao_fim, $colaborador, $supervisor, $local, $cargo) : null;
                if ($infoGrafico2SQL) {
                    $chunkResult2 = $this->oracleService->executeQuery($infoGrafico2SQL);
                    
                    // Processa os dados do Oracle
                    foreach ($chunkResult2  as $key => $row) {
                        // Convertendo apenas as colunas de texto para UTF-8
                        $textColumns = ['NOME_SUPERVISOR'];
                        foreach ($textColumns as $col) {
                            if (isset($row[$col])) {
                                $row[$col] = utf8_encode($row[$col]);
                            }
                        }
                        $row['QTD_FUNCIONARIO'] = intval($row['QTD_FUNCIONARIO']);
                        $row['QTD_FALTAS'] = intval($row['QTD_FALTAS']);
                        $row['QTD_ADICIONAL_NOTURNO'] = intval($row['QTD_ADICIONAL_NOTURNO']);
                        $row['QTD_HORAS_EXTRAS_2HRS'] = intval($row['QTD_HORAS_EXTRAS_2HRS']);
                        $row['QTD_INTERJORNADA'] = intval($row['QTD_INTERJORNADA']);
                        $row['QTD_INTRAJORNADA'] = intval($row['QTD_INTRAJORNADA']);
                        $row['TOTAL'] = intval($row['TOTAL']);

                        // Adiciona cada linha ao array result final
                        $infoGrafico2Result[] = $row;
                    }
                }


                $infoGrafico6SQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getInfoSaldoBancoHorasSupervisor($apuracao_inicio, $apuracao_fim, $colaborador, $supervisor, $local, $cargo) : null;
                if ($infoGrafico6SQL) {
                    $chunkResult6 = $this->oracleService->executeQuery($infoGrafico6SQL);
                    
                    // Processa os dados do Oracle
                    foreach ($chunkResult6  as $key => $row) {
                        // Convertendo apenas as colunas de texto para UTF-8
                        $textColumns = ['NOME_SUPERVISOR'];
                        foreach ($textColumns as $col) {
                            if (isset($row[$col])) {
                                $row[$col] = utf8_encode($row[$col]);
                            }
                        }
                        $row['SALDO'] = intval($row['SALDO']);

                        // Adiciona cada linha ao array result final
                        $infoGrafico6Result[] = $row;
                    }
                }

                // GRAFICO 7 - Horas Positivas e Negativas por Supervisor
                $infoGrafico7SQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getInfoBalancoDeHorasSupervisor($apuracao_inicio, $apuracao_fim, $colaborador, $supervisor, $local, $cargo) : null;

                if ($infoGrafico7SQL) {
                    $chunkResult7 = $this->oracleService->executeQuery($infoGrafico7SQL);

                    // Processa os dados do Oracle
                    foreach ($chunkResult7 as $key => $row) {
                        // Convertendo as colunas de texto para UTF-8
                        $textColumns = ['NOME_SUPERVISOR', 'SALDO_POSITIVO_FORMAT', 'SALDO_NEGATIVO_FORMAT'];
                        foreach ($textColumns as $col) {
                            if (isset($row[$col])) {
                                $row[$col] = utf8_encode($row[$col]);
                            }
                        }

                        // Convertendo as colunas numéricas
                        $row['HORAS_POSITIVAS'] = isset($row['SALDO_POSITIVO']) ? (float)$row['SALDO_POSITIVO'] : 0;
                        $row['HORAS_NEGATIVAS'] = isset($row['SALDO_NEGATIVO']) ? (float)abs($row['SALDO_NEGATIVO']) : 0;
                        $row['QTD_FUNCIONARIO'] = isset($row['QTD_FUNCIONARIO']) ? (int)$row['QTD_FUNCIONARIO'] : 0;
                        $row['QTD_POSITIVO'] = isset($row['QTD_FUNCIONARIO_POSITIVO']) ? (int)$row['QTD_FUNCIONARIO_POSITIVO'] : 0;
                        $row['QTD_NEGATIVO'] = isset($row['QTD_FUNCIONARIO_NEGATIVO']) ? (int)$row['QTD_FUNCIONARIO_NEGATIVO'] : 0;

                        // Adiciona cada linha ao array result final
                        $infoGrafico7Result[] = $row;
                    }
                }
                #endregion
            }

            // Retorna os dados em JSON
            return new JsonModel([
                'success' => true,
                'data' => array(
                    'infoCardsAtualResult' => $infoCardsAtualResult,
                    'infoGrafico1Result' => $infoGrafico1Result,
                    'infoGrafico2Result' => $infoGrafico2Result,
                    'infoGrafico6Result' => $infoGrafico6Result,
                    'infoGrafico7Result' => $infoGrafico7Result
                ),
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}