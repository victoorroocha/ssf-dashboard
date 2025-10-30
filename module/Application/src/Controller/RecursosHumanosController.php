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

class RecursosHumanosController extends BaseController
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

    
    #region Controle Apuração de Pontos
    public function apuracoesColaboradoresAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listLancamentosApuracoesColaboradoresAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }


        // Captura os parâmetros da requisição GET
        $apuracao_inicio = $this->params()->fromQuery('dataInicial', null);
        $apuracao_fim = $this->params()->fromQuery('dataFinal', null);
        $datReferencia = $this->params()->fromQuery('datReferencia', null);
        $codColaborador = $this->params()->fromQuery('colaborador', null);
        $codSupervisor = $this->params()->fromQuery('supervisor', null);
        $codCentroCusto = $this->params()->fromQuery('centroCusto', null);
        $codEscala = $this->params()->fromQuery('escala', null);
        $codFilial = $this->params()->fromQuery('filial', null);
        $tipoApuracoes = $this->params()->fromQuery('tipoApuracao', null);


        try {
            // Define o cabeçalho corretamente antes de qualquer saída
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                              ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                              ->addHeaderLine('Pragma', 'no-cache')
                                              ->addHeaderLine('Expires', '0');

            // Consulta no Softsul todos pedidos
            $result = [];
            foreach ($tipoApuracoes as $key => $tipoApuracao) {
                $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLancamentosApuracoesColaboradores($apuracao_inicio, $apuracao_fim, $codColaborador,$codSupervisor, $codCentroCusto, $codEscala, $codFilial, $tipoApuracao, $datReferencia) : '';

                if ($sql) {
                    // Executa a consulta Oracle
                    $chunkResult = $this->oracleService->executeQuery($sql);
                    
                    // Processa os dados do Oracle
                    foreach ($chunkResult  as $key => $row) {
                        // Convertendo apenas as colunas de texto para UTF-8
                        $textColumns = ['NOMEMP', 'NOMFUN', 'TITCAR', 'NOMLOC', 'NOME_SUPERVISOR', 'ESCALA_CADASTRO', 'ESCALA_TROCA', 'TIPO_COMPENSACAO', 'DSC_SITUACAO_COLABORADOR'];
                        foreach ($textColumns as $col) {
                            if (isset($row[$col])) {
                                $row[$col] = utf8_encode($row[$col]);
                            }
                        }

                        // Adiciona cada linha ao array result final
                        $result[] = $row;
                    }
                }
            }

            
            // Garantir que os dados estão em UTF-8
            array_walk_recursive($result, function(&$value) {
                if (is_string($value)) {
                    // Converter para UTF-8
                    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            });
          

            $totalCount = count($result);


            return new JsonModel([
                'success' => true,
                'data' => $result,
                'totalCount' => $totalCount
            ]);
            
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    #endregion

    #region Controle Banco de Horas
        public function bancoHorasAction()
        {
            $session = new Container('auth');
    
            if (!isset($session->user)) {
                // Redireciona o usuário para o login caso não esteja autenticado
                return $this->redirect()->toRoute('login');
            }
    
            return new ViewModel();
        }
        public function listBancoHorasAction()
        {
            // Verifica se o serviço Oracle está disponível
            if (!$this->oracleService) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Serviço Oracle não disponível'
                ]);
            }
    
    
            // Captura os parâmetros da requisição GET
            $dataInicial = $this->params()->fromQuery('dataInicial', null);
            $dataFinal = $this->params()->fromQuery('dataFinal', null);
            $codColaborador = $this->params()->fromQuery('colaborador', null);
            $codSupervisor = $this->params()->fromQuery('supervisor', null);
            $codCentroCusto = $this->params()->fromQuery('centroCusto', null);
            $codFilial = $this->params()->fromQuery('filial', null);
    
    
            try {
                // Define o cabeçalho corretamente antes de qualquer saída
                $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                                  ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                                  ->addHeaderLine('Pragma', 'no-cache')
                                                  ->addHeaderLine('Expires', '0');
    
                // Consulta no Softsul todos pedidos
                $result = [];
                $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getBancoHorasColaboradores($dataInicial, $dataFinal, $codColaborador, $codSupervisor, $codCentroCusto, $codFilial) : '';
           
    
                if ($sql) {
                    // Executa a consulta Oracle
                    $chunkResult = $this->oracleService->executeQuery($sql);
    
                    // Processa os dados do Oracle
                    foreach ($chunkResult  as $key => $row) {
                        // Convertendo apenas as colunas de texto para UTF-8
                        $textColumns = ['NOMEMP', 'NOMFIL', 'NOMFUN', 'TITCAR', 'NOMLOC', 'NOME_SUPERVISOR'];
                        foreach ($textColumns as $col) {
                            if (isset($row[$col])) {
                                $row[$col] = utf8_encode($row[$col]);
                            }
                        }
    
                        // Adiciona cada linha ao array result final
                        $result[] = $row;
                    }
                }
    
                
                // Garantir que os dados estão em UTF-8
                array_walk_recursive($result, function(&$value) {
                    if (is_string($value)) {
                        // Converter para UTF-8
                        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                });
    
                $totalCount = count($result);
    
                return new JsonModel([
                    'success' => true,
                    'data' => $result,
                    'totalCount' => $totalCount
                ]);
                
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    #endregion

    #region Lookups Filtros
    public function getLookupColaboradorAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupColaboradorQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);
                
                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    public function getLookupSupervisorAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupSupervisorQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    public function getLookupCentroCustoAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupCentroCustoQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    public function getLookupEscalaAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupEscalaQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    public function getLookupFilialAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupFilialQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    public function getLookupLocalAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupLocalQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    public function getLookupCargoAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            $sql = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getLookupCargoQuery() : '';

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['DSC'] = utf8_encode($row['DSC']);
                }
            }

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
    #endregion

    #region Controle TurnOver
        public function dashboardTurnoverAction()
        {
            $session = new Container('auth');
    
            if (!isset($session->user)) {
                // Redireciona o usuário para o login caso não esteja autenticado
                return $this->redirect()->toRoute('login');
            }
    
            return new ViewModel();
        }
        public function listInfoDashboardTurnoverAction()
        {
            // Verifica se o serviço Oracle está disponível
            if (!$this->oracleService) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Serviço Oracle não disponível'
                ]);
            }

            // Captura os parâmetros da requisição GET
            $dataInicio = $this->params()->fromQuery('dataInicio', null);
            $dataFim = $this->params()->fromQuery('dataFim', null);

            try {
                // Define o cabeçalho corretamente antes de qualquer saída
                $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                                ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                                ->addHeaderLine('Pragma', 'no-cache')
                                                ->addHeaderLine('Expires', '0');

                // Infos Cards Turnover
                $infoCardsTurnoverSQL = $this->RecursosHumanosRepository ? $this->RecursosHumanosRepository->getCardsDashboardTurnover($dataInicio, $dataFim) : null;
                if ($infoCardsTurnoverSQL) {
                    $infoCardsTurnover = $this->oracleService->executeQuery($infoCardsTurnoverSQL);
                }

                // $turnoverPorMes = [];

                // // Datas como objetos
                // $start = new \DateTime($dataInicio);
                // $end = new \DateTime($dataFim);
                // // Para garantir que o fim seja o último dia do mês
                // $end->modify('last day of this month');

                // if ($start->format('Y-m') === $end->format('Y-m')) {
                //     // Caso apenas um mês selecionado, já formata no padrão ISO para Oracle
                //     $sql = $this->RecursosHumanosRepository->getCardsDashboardTurnover(
                //         $start->format('Y-m-d'), 
                //         $end->format('Y-m-d')
                //     );
                //     $dados = $this->oracleService->executeQuery($sql);
                //     $turnoverPorMes[] = [
                //         'mes' => $start->format('Y-m-01'),
                //         'perc_turnover' => isset($dados[0]['PERC_TURNOVER']) ? (float) $dados[0]['PERC_TURNOVER'] : null
                //     ];
                // } else {
                //     // Percorre mês a mês
                //     $interval = new \DateInterval('P1M');
                //     // Clona o $end para não modificar o original no DatePeriod
                //     $periodEnd = (clone $end)->modify('+1 day'); 

                //     $period = new \DatePeriod($start, $interval, $periodEnd);

                //     foreach ($period as $mes) {
                //         // Primeiro dia do mês
                //         $inicioMes = (clone $mes)->modify('first day of this month')->format('Y-m-d');
                //         // Último dia do mês
                //         $fimMes = (clone $mes)->modify('last day of this month')->format('Y-m-d');

                //         $sql = $this->RecursosHumanosRepository->getCardsDashboardTurnover($inicioMes, $fimMes);
                //         $dados = $this->oracleService->executeQuery($sql);

                //         $turnoverPorMes[] = [
                //             'mes' => $mes->format('Y-m-01'),
                //             'perc_turnover' => isset($dados[0]['PERC_TURNOVER']) ? (float) $dados[0]['PERC_TURNOVER'] : null
                //         ];
                //     }
                // }

                $turnoverPorMes = [];

                // Datas como objetos
                $start = new \DateTime($dataInicio);
                $end = new \DateTime($dataFim);
                // Garante que o fim seja o último dia do mês
                $end->modify('last day of this month');

                if ($start->format('Y-m') === $end->format('Y-m')) {
                    // Apenas um mês selecionado
                    $sql = $this->RecursosHumanosRepository->getCardsDashboardTurnover(
                        $start->format('Y-m-d'),
                        $end->format('Y-m-d')
                    );
                    $dados = $this->oracleService->executeQuery($sql);

                    $turnoverPorMes[] = [
                        'mes'           => $start->format('Y-m-01'),
                        'perc_turnover' => isset($dados[0]['PERC_TURNOVER']) ? (float) $dados[0]['PERC_TURNOVER'] : null,
                        'ativos'        => isset($dados[0]['ATIVOS']) ? (int) $dados[0]['ATIVOS'] : 0,
                        'admitidos'     => isset($dados[0]['ADMITIDOS']) ? (int) $dados[0]['ADMITIDOS'] : 0,
                        'demitidos'     => isset($dados[0]['DEMITIDOS']) ? (int) $dados[0]['DEMITIDOS'] : 0,
                    ];
                } else {
                    // Percorre mês a mês
                    $interval = new \DateInterval('P1M');
                    $periodEnd = (clone $end)->modify('+1 day');
                    $period = new \DatePeriod($start, $interval, $periodEnd);

                    foreach ($period as $mes) {
                        $inicioMes = (clone $mes)->modify('first day of this month')->format('Y-m-d');
                        $fimMes = (clone $mes)->modify('last day of this month')->format('Y-m-d');

                        $sql = $this->RecursosHumanosRepository->getCardsDashboardTurnover($inicioMes, $fimMes);
                        $dados = $this->oracleService->executeQuery($sql);

                        $turnoverPorMes[] = [
                            'mes'           => $mes->format('Y-m-01'),
                            'perc_turnover' => isset($dados[0]['PERC_TURNOVER']) ? (float) $dados[0]['PERC_TURNOVER'] : null,
                            'ativos'        => isset($dados[0]['ATIVOS']) ? (int) $dados[0]['ATIVOS'] : 0,
                            'admitidos'     => isset($dados[0]['ADMITIDOS']) ? (int) $dados[0]['ADMITIDOS'] : 0,
                            'demitidos'     => isset($dados[0]['DEMITIDOS']) ? (int) $dados[0]['DEMITIDOS'] : 0,
                        ];
                    }
                }


                // Retorna os dados em JSON
                return new JsonModel([
                    'success' => true,
                    'data' => array(
                        'infoCardsTurnover' => $infoCardsTurnover[0] ?? null,
                        'turnoverPorMes' => $turnoverPorMes
                    ),
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    #endregion






    // // action com paginação
    // public function listLancamentosApuracoesColaboradoresAction()
    // {
    //     // Verifica se o serviço Oracle está disponível
    //     if (!$this->oracleService) {
    //         return new JsonModel([
    //             'success' => false,
    //             'message' => 'Serviço Oracle não disponível'
    //         ]);
    //     }


    //     // Captura os parâmetros da requisição GET
    //     $apuracao_inicio = $this->params()->fromQuery('dataInicial', null);
    //     $apuracao_fim = $this->params()->fromQuery('dataFinal', null);
    //     $codColaborador = $this->params()->fromQuery('colaborador', null);
    //     $codSupervisor = $this->params()->fromQuery('supervisor', null);
    //     $codCentroCusto = $this->params()->fromQuery('centroCusto', null);
    //     $codEscala = $this->params()->fromQuery('escala', null);
    //     $codFilial = $this->params()->fromQuery('filial', null);
    //     $tipoApuracao = $this->params()->fromQuery('tipoApuracao', null);
        
    //     // Novos parâmetros para otimização
    //     $page = (int) $this->params()->fromQuery('page', 1);
    //     $pageSize = (int) $this->params()->fromQuery('pageSize', 200);
    //     $columns = $this->params()->fromQuery('columns', null); // Colunas específicas

    //     try {
    //         // Define o cabeçalho corretamente antes de qualquer saída
    //         $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
    //                                           ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
    //                                           ->addHeaderLine('Pragma', 'no-cache')
    //                                           ->addHeaderLine('Expires', '0');

    //         // Consulta no Softsul todos pedidos
    //         $sql = $this->RecursosHumanosRepository ? 
    //             $this->RecursosHumanosRepository->getLancamentosApuracoesColaboradores(
    //                 $apuracao_inicio, $apuracao_fim, $codColaborador, 
    //                 $codSupervisor, $codCentroCusto, $codEscala, 
    //                 $codFilial, $tipoApuracao
    //             ) : '';

    //         $result = [];
    //         if ($sql) {
    //             // Executa a consulta Oracle
    //             $result = $this->oracleService->executeQuery($sql);
                
    //             // Processa os dados do Oracle
    //             foreach ($result as $key => $row) {
    //                 // Convertendo apenas as colunas de texto para UTF-8
    //                 $textColumns = ['NOMEMP', 'NOMFUN', 'TITCAR', 'NOMLOC', 'NOME_SUPERVISOR'];
    //                 foreach ($textColumns as $col) {
    //                     if (isset($row[$col])) {
    //                         $result[$key][$col] = utf8_encode($row[$col]);
    //                     }
    //                 }
                    
    //                 // Se foram solicitadas colunas específicas, filtrar
    //                 if ($columns) {
    //                     $requestedColumns = explode(',', $columns);
    //                     $filteredRow = [];
    //                     foreach ($requestedColumns as $requestedCol) {
    //                         if (isset($result[$key][$requestedCol])) {
    //                             $filteredRow[$requestedCol] = $result[$key][$requestedCol];
    //                         }
    //                     }
    //                     $result[$key] = $filteredRow;
    //                 }
    //             }
    //         }

    //         $totalCount = count($result);
            
    //         // Aplicar paginação
    //         $pagedData = array_slice(
    //             $result, 
    //             ($page - 1) * $pageSize, 
    //             $pageSize
    //         );
            
    //         // Garantir que os dados estão em UTF-8
    //         array_walk_recursive($pagedData, function(&$value) {
    //             if (is_string($value)) {
    //                 // Converter para UTF-8
    //                 $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    //             }
    //         });
            
    //         // // Validar se a conversão para JSON deu certo
    //         // $json = json_encode($responseArray, JSON_UNESCAPED_UNICODE);
    //         // if (json_last_error() !== JSON_ERROR_NONE) {
    //         //     echo 'JSON Error: ' . json_last_error_msg();
    //         //     exit;
    //         // }

    //         return new JsonModel([
    //             'success' => true,
    //             'data' => $pagedData,
    //             'totalCount' => $totalCount,
    //             'currentPage' => $page,
    //             'pageSize' => $pageSize,
    //             'totalPages' => ceil($totalCount / $pageSize)
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return new JsonModel([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
}