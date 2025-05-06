<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\ComercialRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class ComercialController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $ComercialRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, ComercialRepository $ComercialRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->ComercialRepository = $ComercialRepository;
    }

    #region Classificação Base Clientes Softsul
    public function classificacaoClientesSoftsulAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listClassificacaoClientesSoftsulAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        // Captura os parâmetros da requisição GET
        // $classificacaoId = $this->params()->fromQuery('classificacaoId', null);

        try {
            // Define o cabeçalho corretamente antes de qualquer saída
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                              ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                              ->addHeaderLine('Pragma', 'no-cache')
                                              ->addHeaderLine('Expires', '0');

            // Consulta no Softsul todos pedidos
            $result = [];
            $sqlBaseClientes = $this->ComercialRepository ? $this->ComercialRepository->getClassificacaoClientesSoftsul() : '';

            if ($sqlBaseClientes) {
                // Executa a consulta Oracle
                $listaClientes = $this->oracleService->executeQuery($sqlBaseClientes);
                
                // Processa os dados do Oracle
                foreach ($listaClientes  as $key => $rowCliente) {
                    // Convertendo apenas as colunas de texto para UTF-8
                    $textColumns = ['NOME_CLIENTE', 'CIDADE_CLIENTE', 'ESTADO_CLIENTE', 'CLIENTE_REGIAO'];
                    foreach ($textColumns as $col) {
                        if (isset($rowCliente[$col])) {
                            $rowCliente[$col] = utf8_encode($rowCliente[$col]);
                        }
                    }

                    $rowCliente['CATEGORIA_CLIENTE_ID'] = intval($rowCliente['CATEGORIA_CLIENTE_ID']);

                    // Conversão de valores numéricos
                    $rowCliente['QTD_PEDIDOS'] = intval($rowCliente['QTD_PEDIDOS']);
                    $rowCliente['QTD_PEDIDO_ANO_ANTERIOR'] = intval($rowCliente['QTD_PEDIDO_ANO_ANTERIOR']);
                    $rowCliente['QTD_PEDIDO_ANO_ATUAL'] = intval($rowCliente['QTD_PEDIDO_ANO_ATUAL']);
                    $rowCliente['QTD_B10'] = intval($rowCliente['QTD_B10']);
                    $rowCliente['QTD_B50'] = intval($rowCliente['QTD_B50']);
                    $rowCliente['QTD_SK200'] = intval($rowCliente['QTD_SK200']);
                    $rowCliente['QTD_TOTAL'] = intval($rowCliente['QTD_TOTAL']);
                    $rowCliente['ULTIMO_PEDIDO_DIAS'] = intval($rowCliente['ULTIMO_PEDIDO_DIAS']);
                    $rowCliente['PRIMEIRO_PEDIDO_DIAS'] = intval($rowCliente['PRIMEIRO_PEDIDO_DIAS']);
                    $rowCliente['QTD_SAFRAS_PARTICIPADAS'] = intval($rowCliente['QTD_SAFRAS_PARTICIPADAS']);


                    $rowCliente['PRECO_TOTAL_ANO_ANTERIOR'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL_ANO_ANTERIOR']));
                    $rowCliente['PRECO_TOTAL_ANO_ATUAL'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL_ANO_ATUAL']));
                    $rowCliente['PRECO_TOTAL'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL']));
                    $rowCliente['PRECO_TOTAL_GERMOPLASMA'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL_GERMOPLASMA']));
                    $rowCliente['PRECO_TOTAL_ROYALTIES'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL_ROYALTIES']));
                    $rowCliente['PRECO_TOTAL_TSI'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL_TSI']));
                    $rowCliente['PRECO_TOTAL_FRETE'] = floatval(str_replace(',', '.', $rowCliente['PRECO_TOTAL_FRETE']));
                    $rowCliente['PERC_TSI'] = floatval(str_replace(',', '.', $rowCliente['PERC_TSI']));
                    $rowCliente['TICKET_MEDIO'] = floatval(str_replace(',', '.', $rowCliente['TICKET_MEDIO']));
                    $rowCliente['PERC_CRESCIMENTO_QUEDA'] = floatval(str_replace(',', '.', $rowCliente['PERC_CRESCIMENTO_QUEDA']));
                    $rowCliente['MEDIA_PEDIDOS_P_SAFRA'] = floatval(str_replace(',', '.', $rowCliente['MEDIA_PEDIDOS_P_SAFRA']));
                    $rowCliente['MEDIA_CULTIVARES_POR_PEDIDO'] = floatval(str_replace(',', '.', $rowCliente['MEDIA_CULTIVARES_POR_PEDIDO']));
                    $rowCliente['MEDIA_DIAS_ENTRE_PEDIDOS'] = floatval(str_replace(',', '.', $rowCliente['MEDIA_DIAS_ENTRE_PEDIDOS']));

                    // Adiciona cada linha ao array result final
                    $result[] = $rowCliente;
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
    public function listPedidosClienteAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        // Captura os parâmetros da requisição GET
        $clienteId = $this->params()->fromQuery('clienteId', null);

        try {
            // Define o cabeçalho corretamente antes de qualquer saída
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                              ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                              ->addHeaderLine('Pragma', 'no-cache')
                                              ->addHeaderLine('Expires', '0');

            // Consulta no Softsul todos pedidos
            $result = [];
            $sqlPedidosCliente = $this->ComercialRepository ? $this->ComercialRepository->getPedidosCliente($clienteId) : '';

            if ($sqlPedidosCliente) {
                // Executa a consulta Oracle
                $listaPedidosCliente = $this->oracleService->executeQuery($sqlPedidosCliente);
                
                // Processa os dados do Oracle
                foreach ($listaPedidosCliente  as $key => $rowPedidoCliente) {
                    // Convertendo apenas as colunas de texto para UTF-8
                    $textColumns = ['NOME_VENDEDOR'];
                    foreach ($textColumns as $col) {
                        if (isset($rowPedidoCliente[$col])) {
                            $rowPedidoCliente[$col] = utf8_encode($rowPedidoCliente[$col]);
                        }
                    }

                    // Conversão de valores numéricos
                    $rowPedidoCliente['QTD_B10'] = intval($rowPedidoCliente['QTD_B10']);
                    $rowPedidoCliente['QTD_B50'] = intval($rowPedidoCliente['QTD_B50']);
                    $rowPedidoCliente['QTD_SK200'] = intval($rowPedidoCliente['QTD_SK200']);
                    $rowPedidoCliente['QTD_CULTIVAR_POR_PEDIDO'] = intval($rowPedidoCliente['QTD_CULTIVAR_POR_PEDIDO']);
                    $rowPedidoCliente['QTD_TOTAL'] = intval($rowPedidoCliente['QTD_TOTAL']);

                    $rowPedidoCliente['PRECO_TOTAL'] = floatval(str_replace(',', '.', $rowPedidoCliente['PRECO_TOTAL']));
                    $rowPedidoCliente['PRECO_TOTAL_GERMOPLASMA'] = floatval(str_replace(',', '.', $rowPedidoCliente['PRECO_TOTAL_GERMOPLASMA']));
                    $rowPedidoCliente['PRECO_TOTAL_ROYALTIES'] = floatval(str_replace(',', '.', $rowPedidoCliente['PRECO_TOTAL_ROYALTIES']));
                    $rowPedidoCliente['PRECO_TOTAL_TSI'] = floatval(str_replace(',', '.', $rowPedidoCliente['PRECO_TOTAL_TSI']));
                    $rowPedidoCliente['PRECO_TOTAL_FRETE'] = floatval(str_replace(',', '.', $rowPedidoCliente['PRECO_TOTAL_FRETE']));
                    $rowPedidoCliente['PERC_TSI'] = floatval(str_replace(',', '.', $rowPedidoCliente['PERC_TSI']));

                    // Adiciona cada linha ao array result final
                    $result[] = $rowPedidoCliente;
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

}