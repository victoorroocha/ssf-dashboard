<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\CreditoECobrancaRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class CreditoECobrancaController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $creditoECobrancaRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, CreditoECobrancaRepository $creditoECobrancaRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->creditoECobrancaRepository = $creditoECobrancaRepository;
    }

    public function controleRecebimentoAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }

    
    public function getLookupSafraAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        try {
            // Consulta dados na Softsul
            $sql = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getLookupSafraQuery() : '';
            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql);

                foreach ($result as $key => $row) {
                    $result[$key]['dsc'] = mb_convert_encoding($row['dsc'], 'UTF-8', 'Windows-1252');
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
    public function listControleRecebimentoAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        // Captura os parâmetros da requisição GET
        $codigoSafra = $this->params()->fromQuery('codigosafra', null);
        $emissao_inicio = $this->params()->fromQuery('emissao_inicio', null);
        $emissao_fim = $this->params()->fromQuery('emissao_fim', null);
        $skip = $this->params()->fromQuery('skip', null);
        $take = $this->params()->fromQuery('take', null);

        try {
            // Consulta no Softsul todos pedidos
            $sql = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDadosSoftsulQuery($codigoSafra, $emissao_inicio, $emissao_fim) : '';

            $params = [];
            if ($codigoSafra) {
                $params['codigoSafra'] = $codigoSafra;
            }
            if ($emissao_inicio && $emissao_fim) {
                $params['emissao_inicio'] = $emissao_inicio;
                $params['emissao_fim'] = $emissao_fim;
            }
            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql, $params);

                // Consulta no PostgreSQL para obter os registros da tabela controle_recebimento
                $pgSql = new Sql($this->pgAdapter);
                $selectPg = $pgSql->select('controle_recebimento');
                // Adiciona filtro de codigoSafra, caso o parâmetro tenha sido passado
                if (!empty($codigoSafra)) {
                    $selectPg->where(['codigosafra' => $codigoSafra]);
                }
                $statementPg = $pgSql->prepareStatementForSqlObject($selectPg);
                $pgResult = $statementPg->execute();

                // Mapeia os resultados do PostgreSQL em um array associativo
                $pgData = [];
                foreach ($pgResult as $pgRow) {
                    // Conversão de valores numéricos
                    $pgRow['valor_parcela'] = floatval(str_replace(',', '.', $pgRow['valor_parcela']));
                    $pgRow['valor_recebido'] = floatval(str_replace(',', '.', $pgRow['valor_recebido']));
                    $pgRow['valor_recebido_juros'] = floatval(str_replace(',', '.', $pgRow['valor_recebido_juros']));
                    $pgRow['valor_desconto'] = floatval(str_replace(',', '.', $pgRow['valor_desconto']));
                    $pgRow['valor_liquido'] = floatval(str_replace(',', '.', $pgRow['valor_liquido']));
                    $pgRow['saldo_parcela'] = floatval(str_replace(',', '.', $pgRow['saldo_parcela']));
                    $pgRow['total_germoplasma'] = floatval(str_replace(',', '.', $pgRow['total_germoplasma']));
                    $pgRow['recebido_germoplasma'] = floatval(str_replace(',', '.', $pgRow['recebido_germoplasma']));
                    $pgRow['total_royalties'] = floatval(str_replace(',', '.', $pgRow['total_royalties']));
                    $pgRow['recebido_royalties'] = floatval(str_replace(',', '.', $pgRow['recebido_royalties']));
                    $pgRow['total_tsi'] = floatval(str_replace(',', '.', $pgRow['total_tsi']));
                    $pgRow['recebido_tsi'] = floatval(str_replace(',', '.', $pgRow['recebido_tsi']));
                    $pgRow['total_frete'] = floatval(str_replace(',', '.', $pgRow['total_frete']));
                    $pgRow['recebido_frete'] = floatval(str_replace(',', '.', $pgRow['recebido_frete']));

                    // Cria uma chave única com base nas colunas relevantes
                    $chave = $pgRow['codigo']
                            . '-' . $pgRow['id']
                            . '-' . $pgRow['vencimento_parcela']
                            . '-' . (!empty($pgRow['id_recebimento']) ? $pgRow['id_recebimento'] : 'x')
                            . '-' . (!empty($pgRow['total_germoplasma']) && $pgRow['total_germoplasma'] !== '0.00' ? $pgRow['total_germoplasma'] : 'x')
                            . '-' . (!empty($pgRow['total_tsi']) && $pgRow['total_tsi'] !== '0.00' ? $pgRow['total_tsi'] : 'x')
                            . '-' . (!empty($pgRow['total_frete']) && $pgRow['total_frete'] !== '0.00' ? $pgRow['total_frete'] : 'x')
                            . '-' . (!empty($pgRow['total_royalties']) && $pgRow['total_royalties'] !== '0.00' ? $pgRow['total_royalties'] : 'x');
                    $pgData[$chave] = $pgRow;
                }

                // Processa os dados do Oracle
                foreach ($result as $key => $row) {
                    // Convertendo a codificação para UTF-8
                    $result[$key]['status'] = mb_convert_encoding($row['status'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_cliente'] = mb_convert_encoding($row['nome_cliente'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_vendedor'] = mb_convert_encoding($row['nome_vendedor'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_agente'] = mb_convert_encoding($row['nome_agente'], 'UTF-8', 'Windows-1252');
                    $result[$key]['tipo_venda'] = mb_convert_encoding($row['tipo_venda'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_grupo_compra'] = mb_convert_encoding($row['nome_grupo_compra'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_tipo_desmembramento'] = mb_convert_encoding($row['nome_tipo_desmembramento'], 'UTF-8', 'Windows-1252');
                    $result[$key]['tipo_parcela'] = mb_convert_encoding($row['tipo_parcela'], 'UTF-8', 'Windows-1252');

                    // Conversão de valores numéricos
                    $result[$key]['valor_parcela'] = floatval(str_replace(',', '.', $result[$key]['valor_parcela']));
                    $result[$key]['valor_recebido'] = floatval(str_replace(',', '.', $result[$key]['valor_recebido']));
                    $result[$key]['valor_recebido_juros'] = floatval(str_replace(',', '.', $result[$key]['valor_recebido_juros']));
                    $result[$key]['valor_desconto'] = floatval(str_replace(',', '.', $result[$key]['valor_desconto']));
                    $result[$key]['valor_liquido'] = floatval(str_replace(',', '.', $result[$key]['valor_liquido']));
                    $result[$key]['saldo_parcela'] = floatval(str_replace(',', '.', $result[$key]['saldo_parcela']));
                    $result[$key]['total_germoplasma'] = floatval(str_replace(',', '.', $result[$key]['total_germoplasma']));
                    $result[$key]['recebido_germoplasma'] = floatval(str_replace(',', '.', $result[$key]['recebido_germoplasma']));
                    $result[$key]['total_royalties'] = floatval(str_replace(',', '.', $result[$key]['total_royalties']));
                    $result[$key]['recebido_royalties'] = floatval(str_replace(',', '.', $result[$key]['recebido_royalties']));
                    $result[$key]['total_tsi'] = floatval(str_replace(',', '.', $result[$key]['total_tsi']));
                    $result[$key]['recebido_tsi'] = floatval(str_replace(',', '.', $result[$key]['recebido_tsi']));
                    $result[$key]['total_frete'] = floatval(str_replace(',', '.', $result[$key]['total_frete']));
                    $result[$key]['recebido_frete'] = floatval(str_replace(',', '.', $result[$key]['recebido_frete']));

                    // Cria a chave única para buscar no array associativo do PostgreSQL
                    $chave = $result[$key]['codigo']
                            . '-' . $result[$key]['id']
                            . '-' . $result[$key]['vencimento_parcela']
                            . '-' . (!empty($result[$key]['id_recebimento']) ? $result[$key]['id_recebimento'] : 'x')
                            . '-' . (!empty($result[$key]['total_germoplasma']) ? $result[$key]['total_germoplasma'] : 'x')
                            . '-' . (!empty($result[$key]['total_tsi']) ? $result[$key]['total_tsi'] : 'x')
                            . '-' . (!empty($result[$key]['total_frete']) ? $result[$key]['total_frete'] : 'x')
                            . '-' . (!empty($result[$key]['total_royalties']) ? $result[$key]['total_royalties'] : 'x');

                    // Verifica se há correspondência no PostgreSQL
                    if (isset($pgData[$chave])) {
                        // Adiciona os campos do PostgreSQL ao resultado
                        $result[$key]['id_controle_recebimento'] = $pgData[$chave]['id_controle_recebimento'];
                        $result[$key]['custom_forma_pgto'] = $pgData[$chave]['custom_forma_pgto'];
                        $result[$key]['custom_valor_devolvido'] = $pgData[$chave]['custom_valor_devolvido'];
                        $result[$key]['custom_vencimento_boleto'] = $pgData[$chave]['custom_vencimento_boleto'];
                        $result[$key]['custom_observacao'] = $pgData[$chave]['custom_observacao'];

                        // Remove a chave do PostgreSQL para que não seja inserida novamente mais tarde
                        unset($pgData[$chave]);
                    } else {
                        // Se não tiver no PostgreSQL, gera um ID virtual (GUID)
                        $result[$key]['id_controle_recebimento'] = (int)substr(uniqid('', true), -8);
                    }
                }

                // Agora adiciona os registros que estão apenas no PostgreSQL pros casos que são devolvidos por completo e deletados do softsul também aparecer.
                foreach ($pgData as $chave => $pgRow) {
                    // Adiciona no resultado apenas se a chave não existir no Oracle
                    $result[] = $pgRow;
                }
            }

            $totalCount = count($result); // Contagem total de registros
            $pagedData = $result; // Aplica paginação

            // Retorna os dados como JSON
            return new JsonModel([
                'success' => true,
                'data' => $pagedData,
                'totalCount' => $totalCount
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function saveControleRecebimentoAction()
    {
        // Obtém os dados em formato JSON no corpo da requisição
        $data = json_decode($this->getRequest()->getContent(), true);

        // Verifica se os dados foram recebidos corretamente
        if (empty($data)) {
            return new JsonModel([
                'success' => false,
                'message' => 'Nenhum dado recebido'
            ]);
        }

        $sql = new Sql($this->pgAdapter);
        $table = 'controle_recebimento';  // Nome da tabela no banco

        try {
            // Formata os valores numéricos conforme necessário
            foreach ($data as $key => $value) {
                if (is_string($value) && preg_match('/^\d+,\d+$/', $value)) {
                    $data[$key] = number_format(floatval(str_replace(',', '.', $value)), 2, '.', '');
                }
            }

            // Se 'id_controle_recebimento' está presente e não está vazio, tenta realizar um UPDATE
            if (isset($data['id_controle_recebimento']) && !empty($data['id_controle_recebimento'])) {
                $select = $sql->select();
                $select->from($table)->where(['id_controle_recebimento' => $data['id_controle_recebimento']]);
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();

                // Se a linha existir, realiza o UPDATE
                if ($result->count() > 0) {
                    $update = $sql->update($table);
                    $update->set($data);
                    $update->where(['id_controle_recebimento' => $data['id_controle_recebimento']]);

                    $updateStatement = $sql->prepareStatementForSqlObject($update);
                    $updateStatement->execute();

                    return new JsonModel([
                        'success' => true,
                        'message' => 'Dados atualizados com sucesso!'
                    ]);
                }
            }

            // Se o 'id_controle_recebimento' não existir ou a linha não for encontrada, faz um INSERT
            // Remove 'id_controle_recebimento' para que o banco o atribua automaticamente, se necessário
            unset($data['id_controle_recebimento']);

            $insert = $sql->insert($table);
            $insert->values($data);

            $insertStatement = $sql->prepareStatementForSqlObject($insert);
            $insertStatement->execute();

            return new JsonModel([
                'success' => true,
                'message' => 'Dados inseridos com sucesso!'
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao executar consulta: ' . $e->getMessage()
            ]);
        }
    }
    public function deleteControleRecebimentoAction()
    {
        // Obtém o ID da query string
        $id = intval($this->getRequest()->getQuery('id'));  // Recupera o ID da URL

        // Verifica se o ID foi fornecido
        if (empty($id)) {
            return new JsonModel([
                'success' => false,
                'message' => 'ID não fornecido'
            ]);
        }

        $sql = new Sql($this->pgAdapter);
        $table = 'controle_recebimento';  // Nome da tabela no banco

        try {
            // Verifica se o registro existe antes de tentar excluir
            $select = $sql->select();
            $select->from($table)
                ->where(['id_controle_recebimento' => $id]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();

            // Se o registro existe, realiza a exclusão
            if ($result->count() > 0) {
                $delete = $sql->delete($table);
                $delete->where(['id_controle_recebimento' => $id]);

                $deleteStatement = $sql->prepareStatementForSqlObject($delete);
                $deleteStatement->execute();

                return new JsonModel([
                    'success' => true,
                    'message' => 'Registro excluído com sucesso!'
                ]);
            } else {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Registro não encontrado para exclusão.'
                ]);
            }
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao executar consulta: ' . $e->getMessage()
            ]);
        }
    }

    public function controleRecebimentoViewFinanceiroAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }
        
        return new ViewModel();
    }
    public function listControleRecebimentoEnvioFinanceiroAction()
    {
        // Verifica se o serviço Oracle está disponível
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }
    
        // Captura os parâmetros da requisição GET
        $codigoSafra = $this->params()->fromQuery('codigosafra', null);
        $pagamento_inicio = $this->params()->fromQuery('pagamento_inicio', null);
        $pagamento_fim = $this->params()->fromQuery('pagamento_fim', null);
         
        try {
            // Consulta no Softsul todos pedidos
            $sql = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDadosSoftsulDataPagamentoQuery($codigoSafra, $pagamento_inicio, $pagamento_fim) : '';

            $params = [];
            if ($codigoSafra) {
                $params['codigoSafra'] = $codigoSafra;
            }
            if ($pagamento_inicio && $pagamento_fim) {
                $params['pagamento_inicio'] = $pagamento_inicio;
                $params['pagamento_fim'] = $pagamento_fim;
            }

            $result = [];
            if ($sql) {
                // Executa a consulta Oracle, caso tenha uma consulta
                $result = $this->oracleService->executeQuery($sql, $params);

                // Consulta no PostgreSQL para obter os registros da tabela controle_recebimento
                $pgSql = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDadosControlRecebimentoDataPagamentoQuery($codigoSafra, $pagamento_inicio, $pagamento_fim) : '';
                $statementPg = $this->pgAdapter->query($pgSql);
                $pgResult = $statementPg->execute();
                
    
                // Mapeia os resultados do PostgreSQL em um array associativo
                $pgData = [];
                foreach ($pgResult as $pgRow) {
                    // Conversão de valores numéricos
                    $pgRow['valor_parcela'] = floatval(str_replace(',', '.', $pgRow['valor_parcela']));
                    $pgRow['valor_recebido'] = floatval(str_replace(',', '.', $pgRow['valor_recebido']));
                    $pgRow['valor_recebido_juros'] = floatval(str_replace(',', '.', $pgRow['valor_recebido_juros']));
                    $pgRow['valor_desconto'] = floatval(str_replace(',', '.', $pgRow['valor_desconto']));
                    $pgRow['valor_liquido'] = floatval(str_replace(',', '.', $pgRow['valor_liquido']));
                    $pgRow['saldo_parcela'] = floatval(str_replace(',', '.', $pgRow['saldo_parcela']));
                    $pgRow['total_germoplasma'] = floatval(str_replace(',', '.', $pgRow['total_germoplasma']));
                    $pgRow['recebido_germoplasma'] = floatval(str_replace(',', '.', $pgRow['recebido_germoplasma']));
                    $pgRow['total_royalties'] = floatval(str_replace(',', '.', $pgRow['total_royalties']));
                    $pgRow['recebido_royalties'] = floatval(str_replace(',', '.', $pgRow['recebido_royalties']));
                    $pgRow['total_tsi'] = floatval(str_replace(',', '.', $pgRow['total_tsi']));
                    $pgRow['recebido_tsi'] = floatval(str_replace(',', '.', $pgRow['recebido_tsi']));
                    $pgRow['total_frete'] = floatval(str_replace(',', '.', $pgRow['total_frete']));
                    $pgRow['recebido_frete'] = floatval(str_replace(',', '.', $pgRow['recebido_frete']));


                    // Cria uma chave única com base nas colunas relevantes
                    $chave = $pgRow['codigo'] 
                    . '-' . $pgRow['id'] 
                    . '-' . $pgRow['vencimento_parcela'] 
                    . '-' . (!empty($pgRow['id_recebimento']) ? $pgRow['id_recebimento'] : 'x')
                    . '-' . (!empty($pgRow['total_germoplasma']) && $pgRow['total_germoplasma'] !== '0.00' ? $pgRow['total_germoplasma'] : 'x')
                    . '-' . (!empty($pgRow['total_tsi']) && $pgRow['total_tsi'] !== '0.00'  ? $pgRow['total_tsi'] : 'x')
                    . '-' . (!empty($pgRow['total_frete']) && $pgRow['total_frete'] !== '0.00'  ? $pgRow['total_frete'] : 'x')
                    . '-' . (!empty($pgRow['total_royalties']) && $pgRow['total_royalties'] !== '0.00'  ? $pgRow['total_royalties'] : 'x');
                    
                    $pgData[$chave] = $pgRow;
                }

                // Processa os dados do Oracle
                foreach ($result as $key => $row) {
                    // Convertendo a codificação para UTF-8
                    $result[$key]['status'] = mb_convert_encoding($row['status'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_cliente'] = mb_convert_encoding($row['nome_cliente'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_vendedor'] = mb_convert_encoding($row['nome_vendedor'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_agente'] = mb_convert_encoding($row['nome_agente'], 'UTF-8', 'Windows-1252');
                    $result[$key]['tipo_venda'] = mb_convert_encoding($row['tipo_venda'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_grupo_compra'] = mb_convert_encoding($row['nome_grupo_compra'], 'UTF-8', 'Windows-1252');
                    $result[$key]['nome_tipo_desmembramento'] = mb_convert_encoding($row['nome_tipo_desmembramento'], 'UTF-8', 'Windows-1252');
                    $result[$key]['tipo_parcela'] = mb_convert_encoding($row['tipo_parcela'], 'UTF-8', 'Windows-1252');
    
                    // Conversão de valores numéricos
                    $result[$key]['valor_parcela'] = floatval(str_replace(',', '.', $result[$key]['valor_parcela']));
                    $result[$key]['valor_recebido'] = floatval(str_replace(',', '.', $result[$key]['valor_recebido']));
                    $result[$key]['valor_recebido_juros'] = floatval(str_replace(',', '.', $result[$key]['valor_recebido_juros']));
                    $result[$key]['valor_desconto'] = floatval(str_replace(',', '.', $result[$key]['valor_desconto']));
                    $result[$key]['valor_liquido'] = floatval(str_replace(',', '.', $result[$key]['valor_liquido']));
                    $result[$key]['saldo_parcela'] = floatval(str_replace(',', '.', $result[$key]['saldo_parcela']));
                    $result[$key]['total_germoplasma'] = floatval(str_replace(',', '.', $result[$key]['total_germoplasma']));
                    $result[$key]['recebido_germoplasma'] = floatval(str_replace(',', '.', $result[$key]['recebido_germoplasma']));
                    $result[$key]['total_royalties'] = floatval(str_replace(',', '.', $result[$key]['total_royalties']));
                    $result[$key]['recebido_royalties'] = floatval(str_replace(',', '.', $result[$key]['recebido_royalties']));
                    $result[$key]['total_tsi'] = floatval(str_replace(',', '.', $result[$key]['total_tsi']));
                    $result[$key]['recebido_tsi'] = floatval(str_replace(',', '.', $result[$key]['recebido_tsi']));
                    $result[$key]['total_frete'] = floatval(str_replace(',', '.', $result[$key]['total_frete']));
                    $result[$key]['recebido_frete'] = floatval(str_replace(',', '.', $result[$key]['recebido_frete']));
    
                    // Cria a chave única para buscar no array associativo do PostgreSQL
                    $chave = $result[$key]['codigo'] 
                             . '-' . $result[$key]['id'] 
                             . '-' . $result[$key]['vencimento_parcela']
                             . '-' . (!empty($result[$key]['id_recebimento']) ? $result[$key]['id_recebimento'] : 'x')

                             . '-' . (!empty($result[$key]['total_germoplasma']) ? $result[$key]['total_germoplasma'] : 'x')
                             . '-' . (!empty($result[$key]['total_tsi']) ? $result[$key]['total_tsi'] : 'x')
                             . '-' . (!empty($result[$key]['total_frete']) ? $result[$key]['total_frete'] : 'x')
                             . '-' . (!empty($result[$key]['total_royalties']) ? $result[$key]['total_royalties'] : 'x');
    
                    // Verifica se há correspondência no PostgreSQL
                    if (isset($pgData[$chave])) {
                        // Adiciona os campos do PostgreSQL ao resultado
                        $result[$key]['id_controle_recebimento'] = $pgData[$chave]['id_controle_recebimento'];
                        $result[$key]['custom_forma_pgto'] = $pgData[$chave]['custom_forma_pgto'];
                        $result[$key]['custom_valor_devolvido'] = $pgData[$chave]['custom_valor_devolvido'];
                        $result[$key]['custom_vencimento_boleto'] = $pgData[$chave]['custom_vencimento_boleto'];
                        $result[$key]['custom_observacao'] = $pgData[$chave]['custom_observacao'];

                        // Remove a chave do PostgreSQL para que não seja inserida novamente mais tarde
                        unset($pgData[$chave]);
                    }
                }

                // Agora adiciona os registros que estão apenas no PostgreSQL pros casos que são devolvidos por completo e deletados do softsul também aparecer.
                foreach ($pgData as $chave => $pgRow) {
                    // Adiciona no resultado apenas se a chave não existir no Oracle
                    $result[] = $pgRow;
                }
            }

            // Inicializa array agrupado
            $resultadoAgrupado = [];
            foreach ($result as $item) {
                if (isset($item['custom_forma_pgto']) && !empty($item['custom_forma_pgto'])) {

                    $chave = $item['codigo'] . '|' . $item['numero_parcela'] . '|' . $item['cliente_id'] . '|' . $item['nome_cliente'] . '|' . $item['custom_forma_pgto'] . '|' . $item['data_pagamento'];
                    
                    if (!isset($resultadoAgrupado[$chave])) {
                        $resultadoAgrupado[$chave] = [
                            'codigo' => $item['codigo'],
                            'numero_parcela' => $item['numero_parcela'],
                            'cliente_id' => $item['cliente_id'],
                            'nome_cliente' => $item['nome_cliente'],
                            'custom_forma_pgto' => $item['custom_forma_pgto'],
                            'data_pagamento' => $item['data_pagamento'],
                            'valor_recebido' => 0,
                            'valor_desconto' => 0,
                            'valor_liquido' => 0,
                            'recebido_germoplasma' => 0,
                            'recebido_royalties' => 0,
                            'recebido_tsi' => 0,
                            'recebido_frete' => 0
                        ];
                    }
                    
                    $resultadoAgrupado[$chave]['valor_recebido'] += $item['valor_recebido'];
                    $resultadoAgrupado[$chave]['valor_desconto'] += $item['valor_desconto'];
                    $resultadoAgrupado[$chave]['valor_liquido'] += $item['valor_liquido'];
                    $resultadoAgrupado[$chave]['recebido_germoplasma'] += $item['recebido_germoplasma'];
                    $resultadoAgrupado[$chave]['recebido_royalties'] += $item['recebido_royalties'];
                    $resultadoAgrupado[$chave]['recebido_tsi'] += $item['recebido_tsi'];
                    $resultadoAgrupado[$chave]['recebido_frete'] += $item['recebido_frete'];
                }

            }

            // Converte para array final
            $resultadoFinal = array_values($resultadoAgrupado);

            // Retorna os dados como JSON
            return new JsonModel([
                'success' => true,
                'data' => $resultadoFinal
            ]);
        } catch (\Exception $e) {

            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    #region Controle Documentos Pedido
        public function controleDocumentosPedidoAction()
        {
            $session = new Container('auth');

            if (!isset($session->user)) {
                // Redireciona o usuário para o login caso não esteja autenticado
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel();
        }
        public function listPedidosAction()
        {
            // Verifica se o serviço Oracle está disponível
            if (!$this->oracleService) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Serviço Oracle não disponível'
                ]);
            }

            // Captura os parâmetros da requisição GET
            $codigoSafra = $this->params()->fromQuery('codigosafra', null);
            $emissao_inicio = $this->params()->fromQuery('emissao_inicio', null);
            $emissao_fim = $this->params()->fromQuery('emissao_fim', null);
            $skip = $this->params()->fromQuery('skip', null);
            $take = $this->params()->fromQuery('take', null);

            try {
                // Consulta no Softsul todos pedidos
                $sql = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDadosSoftsulPedidoQuery($codigoSafra, $emissao_inicio, $emissao_fim) : '';

                $params = [];
                if ($codigoSafra) {
                    $params['codigoSafra'] = $codigoSafra;
                }
                if ($emissao_inicio && $emissao_fim) {
                    $params['emissao_inicio'] = $emissao_inicio;
                    $params['emissao_fim'] = $emissao_fim;
                }
                $result = [];
                if ($sql) {
                    // Executa a consulta Oracle, caso tenha uma consulta
                    $result = $this->oracleService->executeQuery($sql, $params);

                    // Processa os dados do Oracle
                    foreach ($result as $key => $row) {
                        $idPedido = $row['ID_PEDIDO'];
                        $tipoPessoa = $row['TIPO_PESSOA'];

                        // Busca Status Documentos Pedido
                        $sqlStatusDocumentos = $this->creditoECobrancaRepository->getStatusDocumentoQuery($idPedido, $tipoPessoa);
                        if ($sqlStatusDocumentos) {
                            $stmtStatusDoc = $this->pgAdapter->query($sqlStatusDocumentos);
                            $resStatusDoc = $stmtStatusDoc->execute();
                            $statusDocumentosPedido = $resStatusDoc->current();
                            
                            $totalDocumentos = $statusDocumentosPedido['qtd_documentos_obrigatorios']; 
                            $documentosRecebidos = $statusDocumentosPedido['qtd']; 

                            if ($documentosRecebidos === 0) {
                                $result[$key]['STATUS_DOCUMENTO_PEDIDO'] = 'Pendente';
                            } elseif ($documentosRecebidos < $totalDocumentos) {
                                $result[$key]['STATUS_DOCUMENTO_PEDIDO'] = 'Recebido Parcial';
                            } else {
                                $result[$key]['STATUS_DOCUMENTO_PEDIDO'] = 'Recebido';
                            }
                        }

                        // Busca Status Duplicatas
                        $sqlStatusDuplicatas = $this->creditoECobrancaRepository->getStatusDuplicatasQuery($idPedido);
                        if ($sqlStatusDuplicatas) {
                            $stmtStatusDuplicata = $this->pgAdapter->query($sqlStatusDuplicatas);
                            $resStatusDuplicata = $stmtStatusDuplicata->execute();
                            $statusDuplicatasPedido = $resStatusDuplicata->current();

                            // Busca Total Duplicatas e Boletos
                            $sqlDuplicataBoleto = $this->creditoECobrancaRepository->getDuplicatasBoletosPedidoOracleQuery($idPedido);
                            if ($sqlDuplicataBoleto) {
                                $resDuplicataBoleto = $this->oracleService->executeQuery($sqlDuplicataBoleto);
                            }
                            $totalDuplicatas = count($resDuplicataBoleto);
                            $duplicatasRecebidos = isset($statusDuplicatasPedido['qtd']) ? $statusDuplicatasPedido['qtd'] : 0; 
                            
                            if ($duplicatasRecebidos === 0) {
                                $result[$key]['STATUS_DUPLICATA_PEDIDO'] = 'Pendente';
                            } elseif ($duplicatasRecebidos < $totalDuplicatas) {
                                $result[$key]['STATUS_DUPLICATA_PEDIDO'] = 'Recebido Parcial';
                            } else {
                                $result[$key]['STATUS_DUPLICATA_PEDIDO'] = 'Recebido';
                            }
                        }

                        // Busca Status CPR Pedido
                        $sqlStatusCPR = $this->creditoECobrancaRepository->getStatusCPRQuery($idPedido, $tipoPessoa);
                        if ($sqlStatusCPR) {
                            $stmtStatusCPR = $this->pgAdapter->query($sqlStatusCPR);
                            $resStatusCPR = $stmtStatusCPR->execute();
                            $statusCPRPedido = $resStatusCPR->current();
                            
                            $CPRRecebidos = $statusCPRPedido['qtd']; 

                            if ($CPRRecebidos === 0) {
                                $result[$key]['STATUS_CPR_PEDIDO'] = '-';
                            } else {
                                $result[$key]['STATUS_CPR_PEDIDO'] = 'Recebido';
                            }
                        }

                        // Busca Status Instrumento Fiança Pedido
                        $sqlStatusInstrumentoFianca = $this->creditoECobrancaRepository->getStatusInstrumentoFiancaQuery($idPedido, $tipoPessoa);
                        if ($sqlStatusInstrumentoFianca) {
                            $stmtStatusInstrumentoFianca = $this->pgAdapter->query($sqlStatusInstrumentoFianca);
                            $resStatusInstrumentoFianca = $stmtStatusInstrumentoFianca->execute();
                            $statusInstrumentoFiancaPedido = $resStatusInstrumentoFianca->current();
                            
                            $instrumentoFiancaRecebidos = $statusInstrumentoFiancaPedido['qtd']; 

                            if ($instrumentoFiancaRecebidos === 0) {
                                $result[$key]['STATUS_INST_FIANCA_PEDIDO'] = '-';
                            } else {
                                $result[$key]['STATUS_INST_FIANCA_PEDIDO'] = 'Recebido';
                            }
                        }
                        
                        // Busca Status Confissão Divida Pedido
                        $sqlConfissaoDivida = $this->creditoECobrancaRepository->getStatusConfissaoDividaQuery($idPedido, $tipoPessoa);
                        if ($sqlConfissaoDivida) {
                            $stmtStatusConfissaoDivida = $this->pgAdapter->query($sqlConfissaoDivida);
                            $resStatusConfissaoDivida = $stmtStatusConfissaoDivida->execute();
                            $statusConfissaoDividaPedido = $resStatusConfissaoDivida->current();
                            
                            $confissaoDividaRecebidos = $statusConfissaoDividaPedido['qtd']; 

                            if ($confissaoDividaRecebidos === 0) {
                                $result[$key]['STATUS_CONFISSAO_DIVIDA_PEDIDO'] = '-';
                            } else {
                                $result[$key]['STATUS_CONFISSAO_DIVIDA_PEDIDO'] = 'Recebido';
                            }
                        }

                        // Busca Status Recebimentos Garantias Pedido
                        $sqlGarantias = $this->creditoECobrancaRepository->getStatusGarantiasQuery($idPedido, $tipoPessoa);
                        if ($sqlGarantias) {
                            $stmtStatusGarantias = $this->pgAdapter->query($sqlGarantias);
                            $resStatusGarantias = $stmtStatusGarantias->execute();
                            $statusGarantiasPedido = $resStatusGarantias->current();
                            
                            $totalGarantias = $statusGarantiasPedido['qtd_garantias']; 
                            $garantiasRecebidos = $statusGarantiasPedido['qtd']; 

                            if ($garantiasRecebidos === 0) {
                                $result[$key]['STATUS_GARANTIA_PEDIDO'] = 'Pendente';
                            } elseif ($garantiasRecebidos < $totalGarantias) {
                                $result[$key]['STATUS_GARANTIA_PEDIDO'] = 'Recebido Parcial';
                            } else {
                                $result[$key]['STATUS_GARANTIA_PEDIDO'] = 'Recebido';
                            }
                        }

                        // Convertendo a codificação para UTF-8
                        $result[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                        $result[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                        $result[$key]['PRECO_TOTAL_GERMOPLASMA'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_GERMOPLASMA']));
                        $result[$key]['PRECO_TOTAL_TSI'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_TSI']));
                    }
                }

                $totalCount = count($result); // Contagem total de registros
                $pagedData = $result; // Aplica paginação

                // Retorna os dados como JSON
                return new JsonModel([
                    'success' => true,
                    'data' => $pagedData,
                    'totalCount' => $totalCount
                ]);
            } catch (\Exception $e) {

                return new JsonModel([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        public function listDocumentosPedidoAction()
        {
            if (!$this->oracleService) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Serviço Oracle não disponível'
                ]);
            }

            if (!$this->pgAdapter) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Adaptador PostgreSQL não disponível'
                ]);
            }

            $idPedido = $this->params()->fromQuery('idPedido', null);
            $tipoPessoa = $this->params()->fromQuery('tipoPessoa', null);
            $grupoClienteID = $this->params()->fromQuery('grupoClienteID', null);
            $codigoSafra = $this->params()->fromQuery('codigoSafra', null);

            try {
                $result = [
                    'documentos' => [],
                    'garantias' => [],
                    'duplicatasBoletos' => [],
                    'observacaoPedido' => [],
                ];

                if ($idPedido) {
                    // Busca documentos
                    $sqlDocumentos = $this->creditoECobrancaRepository->getDocumentosPedidoQuery($idPedido, $tipoPessoa);
                    if ($sqlDocumentos) {
                        $stmtDoc = $this->pgAdapter->query($sqlDocumentos);
                        $resDoc = $stmtDoc->execute();
                        foreach ($resDoc as $row) {
                            $result['documentos'][] = $row;
                        }
                    }

                    // Busca garantias
                    $sqlGarantias = $this->creditoECobrancaRepository->getGarantiasPedidoQuery($idPedido, $tipoPessoa);
                    if ($sqlGarantias) {
                        $stmtGar = $this->pgAdapter->query($sqlGarantias);
                        $resGar = $stmtGar->execute();
                        foreach ($resGar as $row) {
                            $result['garantias'][] = $row;
                        }
                    }

                    // Busca Duplicatas e Boletos
                    $sqlDuplicataBoleto = $this->creditoECobrancaRepository->getDuplicatasBoletosPedidoOracleQuery($idPedido);
                    if ($sqlDuplicataBoleto) {
                        $resDuplicataBoleto = $this->oracleService->executeQuery($sqlDuplicataBoleto);
                        
                        foreach ($resDuplicataBoleto as $row) {
                            $idParcelaPedido = $row['ID'];

                            // Inicializa valores padrão
                            $boletoRecebido = false;
                            $duplicataRecebido = false;

                            // Busca Duplicatas e Boletos no PostgreSQL
                            $sqlDuplicatasBoletos = $this->creditoECobrancaRepository->getDuplicatasBoletosPedidoPostgresQuery($idPedido, $idParcelaPedido);
                            if ($sqlDuplicatasBoletos) {
                                $stmtDulpBol = $this->pgAdapter->query($sqlDuplicatasBoletos);
                                $resDupBol = $stmtDulpBol->execute();
                                $pgRow = $resDupBol->current();
                                
                                if ($pgRow) {
                                    $boletoRecebido = $pgRow['boleto_recebido'] ?? false;
                                    $duplicataRecebido = $pgRow['duplicata_recebido'] ?? false;
                                }
                            }

                            // Converte campos Oracle
                            $row['DUPLICATA_EMITIDA'] = intval($row['DUPLICATA_EMITIDA']) === 1;
                            $row['BOLETO_EMITIDO'] = intval($row['BOLETO_EMITIDO']) === 1;

                            // Adiciona campos do PostgreSQL
                            $row['BOLETO_RECEBIDO'] = $boletoRecebido;
                            $row['DUPLICATA_RECEBIDO'] = $duplicataRecebido;

                            $result['duplicatasBoletos'][] = $row;
                        }
                    }

                    // Busca observações (última inserida, por exemplo)
                    $sqlObservacoes = $this->creditoECobrancaRepository->getObservacoesPedidoQuery($idPedido);
                    if ($sqlObservacoes) {
                        $stmtObs = $this->pgAdapter->query($sqlObservacoes);
                        $resObs = $stmtObs->execute();
                        $pgRowObs = $resObs->current();

                        if ($pgRowObs) {
                            $result['observacaoPedido'] = $pgRowObs;
                        }
                    }
                }

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
        public function toggleDocumentoPedidoAction()
        {
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $body = json_decode($this->getRequest()->getContent(), true);

            $idPedido = $body['id_pedido'] ?? null;
            $idDocumento = $body['id_documento'] ?? null;
            $checked = $body['checked'] ?? null;
            $grupoClienteID = $body['grupoClienteID'] ?? null;
            $codigoSafra = $body['codigoSafra'] ?? null;

            if (!$idDocumento || !isset($checked)) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Parâmetros obrigatórios ausentes.'
                ]);
            }

            try {
                if (!$this->pgAdapter) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Adaptador PostgreSQL não disponível.'
                    ]);
                }
                 // Verifica se o serviço Oracle está disponível
                if (!$this->oracleService) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Serviço Oracle não disponível'
                    ]);
                }

                if (intval($grupoClienteID) > 0 && intval($codigoSafra) > 0) {
                    // Busca Pedidos GrupoCliente e Safra
                    $params = [];
                    $sqlPedidosGrupoCliente = $this->creditoECobrancaRepository->getPedidosGrupoClienteSafra($grupoClienteID, $codigoSafra);
                    if ($sqlPedidosGrupoCliente) {
                    // Executa a consulta Oracle, caso tenha uma consulta
                        $resPedidosGrupoCliente = $this->oracleService->executeQuery($sqlPedidosGrupoCliente, $params);
                    }
                    
                    if (count($resPedidosGrupoCliente) > 0) {
                        foreach ($resPedidosGrupoCliente as $key => $pedido) {
                            if ($checked) {
                                // Insere ou atualiza para ativo = true
                                $sql = "
                                    INSERT INTO documentos_pedido (id_pedido, id_documento, ativo)
                                    VALUES (:id_pedido, :id_documento, true)
                                    ON CONFLICT (id_pedido, id_documento) DO UPDATE
                                    SET ativo = EXCLUDED.ativo
                                ";
                            } else {
                                // Atualiza para ativo = false
                                $sql = "
                                    UPDATE documentos_pedido
                                    SET ativo = false
                                    WHERE id_pedido = :id_pedido AND id_documento = :id_documento
                                ";
                            }

                            $statement = $this->pgAdapter->query($sql);
                            $statement->execute([
                                'id_pedido' => $pedido['ID_PEDIDO'],
                                'id_documento' => $idDocumento
                            ]);
                        }
                    } 
                } else {
                    if ($checked) {
                        // Insere ou atualiza para ativo = true
                        $sql = "
                            INSERT INTO documentos_pedido (id_pedido, id_documento, ativo)
                            VALUES (:id_pedido, :id_documento, true)
                            ON CONFLICT (id_pedido, id_documento) DO UPDATE
                            SET ativo = EXCLUDED.ativo
                        ";
                    } else {
                        // Atualiza para ativo = false
                        $sql = "
                            UPDATE documentos_pedido
                            SET ativo = false
                            WHERE id_pedido = :id_pedido AND id_documento = :id_documento
                        ";
                    }

                    $statement = $this->pgAdapter->query($sql);
                    $statement->execute([
                        'id_pedido' => $idPedido,
                        'id_documento' => $idDocumento
                    ]);
                }

                return new JsonModel([
                    'success' => true,
                    'message' => $checked ? 'Documento vinculado com sucesso.' : 'Documento desvinculado com sucesso.'
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao processar requisição: ' . $e->getMessage()
                ]);
            }
        }
        public function toggleGarantiaPedidoAction()
        {
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $body = json_decode($this->getRequest()->getContent(), true);

            $idPedido = $body['id_pedido'] ?? null;
            $idGarantia = $body['id_garantia'] ?? null;
            $checked = $body['checked'] ?? null;
            $grupoClienteID = $body['grupoClienteID'] ?? null;
            $codigoSafra = $body['codigoSafra'] ?? null;

            if (!$idGarantia || !isset($checked)) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Parâmetros obrigatórios ausentes.'
                ]);
            }

            try {
                if (!$this->pgAdapter) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Adaptador PostgreSQL não disponível.'
                    ]);
                }
                // Verifica se o serviço Oracle está disponível
                if (!$this->oracleService) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Serviço Oracle não disponível'
                    ]);
                }

                if (intval($grupoClienteID) > 0 && intval($codigoSafra) > 0) {
                    // Busca Pedidos GrupoCliente e Safra
                    $params = [];
                    $sqlPedidosGrupoCliente = $this->creditoECobrancaRepository->getPedidosGrupoClienteSafra($grupoClienteID, $codigoSafra);
                    if ($sqlPedidosGrupoCliente) {
                        // Executa a consulta Oracle, caso tenha uma consulta
                        $resPedidosGrupoCliente = $this->oracleService->executeQuery($sqlPedidosGrupoCliente, $params);
                    }

                    if (count($resPedidosGrupoCliente) > 0) {
                        foreach ($resPedidosGrupoCliente as $key => $pedido) {
                            if ($checked) {
                                // Insere ou atualiza para ativo = true
                                $sql = "
                                    INSERT INTO garantias_pedido (id_pedido, id_garantia, ativo)
                                    VALUES (:id_pedido, :id_garantia, true)
                                    ON CONFLICT (id_pedido, id_garantia) DO UPDATE
                                    SET ativo = EXCLUDED.ativo
                                ";
                            } else {
                                // Atualiza para ativo = false
                                $sql = "
                                    UPDATE garantias_pedido
                                    SET ativo = false
                                    WHERE id_pedido = :id_pedido AND id_garantia = :id_garantia
                                ";
                            }

                            $statement = $this->pgAdapter->query($sql);
                            $statement->execute([
                                'id_pedido' => $pedido['ID_PEDIDO'],
                                'id_garantia' => $idGarantia
                            ]);
                        }
                    } 
                } else {
                    if ($checked) {
                        // Insere ou atualiza para ativo = true
                        $sql = "
                            INSERT INTO garantias_pedido (id_pedido, id_garantia, ativo)
                            VALUES (:id_pedido, :id_garantia, true)
                            ON CONFLICT (id_pedido, id_garantia) DO UPDATE
                            SET ativo = EXCLUDED.ativo
                        ";
                    } else {
                        // Atualiza para ativo = false
                        $sql = "
                            UPDATE garantias_pedido
                            SET ativo = false
                            WHERE id_pedido = :id_pedido AND id_garantia = :id_garantia
                        ";
                    }
                    $statement = $this->pgAdapter->query($sql);
                    $statement->execute([
                        'id_pedido' => $idPedido,
                        'id_garantia' => $idGarantia
                    ]);
                }

                return new JsonModel([
                    'success' => true,
                    'message' => $checked ? 'Garantia vinculada com sucesso.' : 'Garantia desvinculada com sucesso.'
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao processar requisição: ' . $e->getMessage()
                ]);
            }
        }
        public function salvarObservacaoPedidoAction()
        {
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $body = json_decode($this->getRequest()->getContent(), true);
            
            
            $idPedido = $body['idPedido'] ?? null;
            $observacao = $body['observacao'] ?? null;
            $grupoClienteID = $body['grupoClienteID'] ?? null;
            $codigoSafra = $body['codigoSafra'] ?? null;

            if ($observacao === null) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Parâmetros obrigatórios ausentes.'
                ]);
            }

            try {
                if (!$this->pgAdapter) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Adaptador PostgreSQL não disponível.'
                    ]);
                }

                if (!$this->oracleService) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Serviço Oracle não disponível'
                    ]);
                }

                if (intval($grupoClienteID) > 0 && intval($codigoSafra) > 0) {
                    // Busca Pedidos GrupoCliente e Safra
                    $params = [];
                    $sqlPedidosGrupoCliente = $this->creditoECobrancaRepository->getPedidosGrupoClienteSafra($grupoClienteID, $codigoSafra);
                    if ($sqlPedidosGrupoCliente) {
                        $resPedidosGrupoCliente = $this->oracleService->executeQuery($sqlPedidosGrupoCliente, $params);
                    }

                    if (count($resPedidosGrupoCliente) > 0) {
                        foreach ($resPedidosGrupoCliente as $pedido) {
                            $sql = "
                                INSERT INTO observacoes_pedido (id_pedido, observacao)
                                VALUES (:id_pedido, :observacao)
                                ON CONFLICT (id_pedido) DO UPDATE
                                SET observacao = EXCLUDED.observacao
                            ";

                            $statement = $this->pgAdapter->query($sql);
                            $statement->execute([
                                'id_pedido' => $pedido['ID_PEDIDO'],
                                'observacao' => $observacao
                            ]);
                        }
                    }

                    return new JsonModel([
                        'success' => true,
                        'message' => 'Observações salvas com sucesso.'
                    ]);
                } else {
                    $sql = "
                        INSERT INTO observacoes_pedido (id_pedido, observacao)
                        VALUES (:id_pedido, :observacao)
                        ON CONFLICT (id_pedido) DO UPDATE
                        SET observacao = EXCLUDED.observacao
                    ";
                    $statement = $this->pgAdapter->query($sql);
                    $statement->execute([
                        'id_pedido' => $idPedido,
                        'observacao' => $observacao
                    ]);

                    return new JsonModel([
                        'success' => true,
                        'message' => 'Observação salva com sucesso.'
                    ]);
                }

                
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar observações: ' . $e->getMessage()
                ]);
            }
        }
        public function toggleDuplicataBoletoPedidoAction()
        {
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $body = json_decode($this->getRequest()->getContent(), true);

            $idPedido = $body['PEDIDO_ID'] ?? null;
            $idParcela = $body['ID'] ?? null;

            $boletoRecebido = $body['BOLETO_RECEBIDO'] ?? null;
            $duplicataRecebido = $body['DUPLICATA_RECEBIDO'] ?? null;

            unset($body['BOLETO_EMITIDO']);
            unset($body['DUPLICATA_EMITIDA']);
            unset($body['VENCIMENTO_PARCELA']);

            if (!$idPedido || !$idParcela) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Parâmetros obrigatórios ausentes.'
                ]);
            }

            try {
                if (!$this->pgAdapter) {
                    return new JsonModel([
                        'success' => false,
                        'message' => 'Adaptador PostgreSQL não disponível.'
                    ]);
                }

                $sql = "INSERT INTO duplicata_boleto_pedido (
                            id_pedido,
                            id_parcela_pedido,
                            boleto_recebido,
                            duplicata_recebido
                        )
                        VALUES (
                            :id_pedido,
                            :id_parcela_pedido,
                            :boleto_recebido,
                            :duplicata_recebido
                        )
                        ON CONFLICT (id_pedido, id_parcela_pedido) DO UPDATE SET
                            boleto_recebido = EXCLUDED.boleto_recebido,
                            duplicata_recebido = EXCLUDED.duplicata_recebido
                ";

                $statement = $this->pgAdapter->query($sql);
                $statement->execute([
                    'id_pedido' => $idPedido,
                    'id_parcela_pedido' => $idParcela,
                    'boleto_recebido' => $boletoRecebido,
                    'duplicata_recebido' => $duplicataRecebido,
                ]);

                return new JsonModel([
                    'success' => true,
                    'message' => 'Dados atualizados com sucesso.',
                    'data' => [
                        'ID' => $idParcela,
                        'BOLETO_RECEBIDO' => $boletoRecebido,
                        'DUPLICATA_RECEBIDO' => $duplicataRecebido,
                    ]
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao processar requisição: ' . $e->getMessage()
                ]);
            }
        }
    #endregion

    #region Cadastro Documento
        public function cadastroDocumentosPedidoAction()
        {
            $session = new Container('auth');

            if (!isset($session->user)) {
                // Redireciona o usuário para o login caso não esteja autenticado
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel();
        }
        public function listDocumentosAction()
        {
            try {
                $skip = $this->params()->fromQuery('skip', 0);
                $take = $this->params()->fromQuery('take', 500);
                $sort = $this->params()->fromQuery('sort', null);

                $documentos = $this->creditoECobrancaRepository->listarDocumentos($skip, $take, $sort);

                return new JsonModel([
                    'success' => true,
                    'data' => $documentos['data'],
                    'totalCount' => $documentos['totalCount'],
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar documentos: ' . $e->getMessage(),
                ]);
            }
        }
        public function addOrUpdateDocumentoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                if ($this->getRequest()->isPut()) {
                    $this->creditoECobrancaRepository->atualizarDocumento($data);
                    $message = 'Documento atualizado com sucesso!';
                } else {
                    $this->creditoECobrancaRepository->inserirDocumento($data);
                    $message = 'Documento adicionado com sucesso!';
                }

                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar documento: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirDocumentoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->creditoECobrancaRepository->excluirDocumento($data['id']);

                return new JsonModel([
                    'success' => true,
                    'message' => 'Documento excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir documento: ' . $e->getMessage(),
                ]);
            }
        }
    #endregion

    #region Cadastro Garantias
        public function cadastroGarantiasPedidoAction()
        {
            $session = new Container('auth');

            if (!isset($session->user)) {
                // Redireciona o usuário para o login caso não esteja autenticado
                return $this->redirect()->toRoute('login');
            }

            return new ViewModel();
        }
        public function listGarantiasAction()
        {
            try {
                $skip = $this->params()->fromQuery('skip', 0);
                $take = $this->params()->fromQuery('take', 500);
                $sort = $this->params()->fromQuery('sort', null);

                $garantias = $this->creditoECobrancaRepository->listarGarantias($skip, $take, $sort);

                return new JsonModel([
                    'success' => true,
                    'data' => $garantias['data'],
                    'totalCount' => $garantias['totalCount'],
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar garantias: ' . $e->getMessage(),
                ]);
            }
        }
        public function addOrUpdateGarantiaAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                if ($this->getRequest()->isPut()) {
                    $this->creditoECobrancaRepository->atualizarGarantia($data);
                    $message = 'Garantia atualizada com sucesso!';
                } else {
                    $this->creditoECobrancaRepository->inserirGarantia($data);
                    $message = 'Garantia adicionada com sucesso!';
                }

                return new JsonModel([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar garantia: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirGarantiaAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->creditoECobrancaRepository->excluirGarantia($data['id']);

                return new JsonModel([
                    'success' => true,
                    'message' => 'Garantia excluída com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir garantia: ' . $e->getMessage(),
                ]);
            }
        }
    #endregion

    #region Dashboard Monitoramento Pedidos Safra

    public function dashboardMonitoramentoPedidosSafraAction()
    {
        $session = new Container('auth');

        if (!isset($session->user)) {
            // Redireciona o usuário para o login caso não esteja autenticado
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
    public function listarDadosMonitoramentoPedidosSafraAction()
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
        $codigoSafra = $this->params()->fromQuery('codigoSafra', null);

        try {
            // Define o cabeçalho corretamente antes de qualquer saída
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                              ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                              ->addHeaderLine('Pragma', 'no-cache')
                                              ->addHeaderLine('Expires', '0');

            $infoCardsResult = null;
            if (!empty($apuracao_inicio) && !empty($apuracao_fim)) {
                #region CARDS MES ATUAL
                    // Informações Cards
                    $infoCardsSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoCardsMonitoramentoPedidos($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    // echo '<pre>';print_r($infoCardsSQL);exit;
                    if ($infoCardsSQL) {
                        $infoCardsMonitoramentoPedidos = $this->oracleService->executeQuery($infoCardsSQL)[0];
                    }

                    $infoCardsMonitoramentoPedidos['TOTAL_EMITIDO'] = floatval(str_replace(',', '.', $infoCardsMonitoramentoPedidos['TOTAL_EMITIDO']));
                    $infoCardsMonitoramentoPedidos['TOTAL_PAGO'] = floatval(str_replace(',', '.', $infoCardsMonitoramentoPedidos['TOTAL_PAGO']));
                    $infoCardsMonitoramentoPedidos['TOTAL_VENCIDO'] = floatval(str_replace(',', '.', $infoCardsMonitoramentoPedidos['TOTAL_VENCIDO']));
                    $infoCardsMonitoramentoPedidos['TOTAL_A_VENCER'] = floatval(str_replace(',', '.', $infoCardsMonitoramentoPedidos['TOTAL_A_VENCER']));
                    $infoCardsMonitoramentoPedidos['TOTAL_PERMUTA'] = floatval(str_replace(',', '.', $infoCardsMonitoramentoPedidos['TOTAL_PERMUTA']));
                    $infoCardsMonitoramentoPedidos['TOTAL_SAFRA'] = floatval(str_replace(',', '.', $infoCardsMonitoramentoPedidos['TOTAL_SAFRA']));
                #endregion

                #region CARDS TIPO PRAZO    
                    // Germoplasma Tipo Prazo
                    $infoGermoplasmaTipoPrazoSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoGermoplasmaTipoPrazo($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoGermoplasmaTipoPrazoSQL) {
                        $infoGermoplasmaTipoPrazo = $this->oracleService->executeQuery($infoGermoplasmaTipoPrazoSQL)[0];
                    }
                    $infoGermoplasmaTipoPrazo['PRAZO_ANO_GERMOPLASMA'] = floatval(str_replace(',', '.', $infoGermoplasmaTipoPrazo['PRAZO_ANO_GERMOPLASMA']));
                    $infoGermoplasmaTipoPrazo['PRAZO_SAFRA_GERMOPLASMA'] = floatval(str_replace(',', '.', $infoGermoplasmaTipoPrazo['PRAZO_SAFRA_GERMOPLASMA']));


                    // Royalties Tipo Prazo
                    $infoRoyaltiesTipoPrazoSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoRoyaltiesTipoPrazo($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoRoyaltiesTipoPrazoSQL) {
                        $infoRoyaltiesTipoPrazo = $this->oracleService->executeQuery($infoRoyaltiesTipoPrazoSQL)[0];
                    }
                    $infoRoyaltiesTipoPrazo['PRAZO_ANO_ROYALTIES'] = floatval(str_replace(',', '.', $infoRoyaltiesTipoPrazo['PRAZO_ANO_ROYALTIES']));
                    $infoRoyaltiesTipoPrazo['PRAZO_SAFRA_ROYALTIES'] = floatval(str_replace(',', '.', $infoRoyaltiesTipoPrazo['PRAZO_SAFRA_ROYALTIES']));

                    // TSI Tipo Prazo
                    $infoTSITipoPrazoSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoTSITipoPrazo($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoTSITipoPrazoSQL) {
                        $infoTSITipoPrazo = $this->oracleService->executeQuery($infoTSITipoPrazoSQL)[0];
                    }
                    $infoTSITipoPrazo['PRAZO_ANO_TSI'] = floatval(str_replace(',', '.', $infoTSITipoPrazo['PRAZO_ANO_TSI']));
                    $infoTSITipoPrazo['PRAZO_SAFRA_TSI'] = floatval(str_replace(',', '.', $infoTSITipoPrazo['PRAZO_SAFRA_TSI']));

                    // Frete Tipo Prazo
                    $infoFreteTipoPrazoSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoFreteTipoPrazo($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoFreteTipoPrazoSQL) {
                        $infoFreteTipoPrazo = $this->oracleService->executeQuery($infoFreteTipoPrazoSQL)[0];
                    }
                    $infoFreteTipoPrazo['PRAZO_ANO_FRETE'] = floatval(str_replace(',', '.', $infoFreteTipoPrazo['PRAZO_ANO_FRETE']));
                    $infoFreteTipoPrazo['PRAZO_SAFRA_FRETE'] = floatval(str_replace(',', '.', $infoFreteTipoPrazo['PRAZO_SAFRA_FRETE']));

                #endregion

                #region Grafico Recebimento por Data de Pagamento
                    $infoRecebimentoPgtoSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoRecebimentoPorDataPagamento($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoRecebimentoPgtoSQL) {
                        $infoRecebimentoDataPagamento = $this->oracleService->executeQuery($infoRecebimentoPgtoSQL);
                    }
                    foreach ($infoRecebimentoDataPagamento as $key => $rowRecebimento) {
                        $infoRecebimentoDataPagamento[$key]['VALOR_RECEBIDO'] = floatval(str_replace(',', '.', $rowRecebimento['VALOR_RECEBIDO']));
                    }
                #endregion

                #region Top Clientes
                    $infoTopClientesSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoTopClientes($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoTopClientesSQL) {
                        $infoTopClientes = $this->oracleService->executeQuery($infoTopClientesSQL);
                    }
                    foreach ($infoTopClientes as $key => $rowTopClientes) {
                        $infoTopClientes[$key]['NOME_CLIENTE'] = utf8_encode($rowTopClientes['NOME_CLIENTE']);
                        $infoTopClientes[$key]['VALOR'] = floatval(str_replace(',', '.', $rowTopClientes['VALOR']));
                    }
                #endregion

                #region Top Vendedores
                    $infoTopVendedoresSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoTopVendedores($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoTopVendedoresSQL) {
                        $infoTopVendedores = $this->oracleService->executeQuery($infoTopVendedoresSQL);
                    }
                    foreach ($infoTopVendedores as $key => $rowTopVendedores) {
                        $infoTopVendedores[$key]['NOME_VENDEDOR'] = utf8_encode($rowTopVendedores['NOME_VENDEDOR']);
                        $infoTopVendedores[$key]['VALOR'] = floatval(str_replace(',', '.', $rowTopVendedores['VALOR']));
                    }
                #endregion

                #region Grafico a Receber e Recebido
                    $infoReceberRecebidoSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getInfoAReceberRecebido($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                    if ($infoReceberRecebidoSQL) {
                        $infoReceberRecebido = $this->oracleService->executeQuery($infoReceberRecebidoSQL)[0];
                    }
                     $infoReceberRecebido['VALOR_A_RECEBER'] = floatval(str_replace(',', '.', $infoReceberRecebido['VALOR_A_RECEBER']));
                     $infoReceberRecebido['VALOR_RECEBIDO'] = floatval(str_replace(',', '.', $infoReceberRecebido['VALOR_RECEBIDO']));
                #endregion
            }

            // Retorna os dados em JSON
            return new JsonModel([
                'success' => true,
                'data' => array(
                    'infoCardsMonitoramentoPedidos' => $infoCardsMonitoramentoPedidos,
                    'infoGermoplasmaTipoPrazo' => $infoGermoplasmaTipoPrazo,
                    'infoRoyaltiesTipoPrazo' => $infoRoyaltiesTipoPrazo,
                    'infoTSITipoPrazo' => $infoTSITipoPrazo,
                    'infoFreteTipoPrazo' => $infoFreteTipoPrazo,
                    'infoRecebimentoDataPagamento' => $infoRecebimentoDataPagamento,
                    'infoReceberRecebido' => $infoReceberRecebido,
                    'infoTopClientes' => $infoTopClientes,
                    'infoTopVendedores' => $infoTopVendedores,
                ),
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function detalhesCardsMonitoramentoPedidosSafraAction()
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
        $codigoSafra = $this->params()->fromQuery('codigoSafra', null);
        $tipo = $this->params()->fromQuery('tipo', null);

        try {
            // Define o cabeçalho corretamente antes de qualquer saída
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8')
                                              ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate')
                                              ->addHeaderLine('Pragma', 'no-cache')
                                              ->addHeaderLine('Expires', '0');

            $dataSource = null;
            $collumns = [];
            if (!empty($apuracao_inicio) && !empty($apuracao_fim)) {

                if ($tipo == "EMITIDOS") {
                    #region Detalhamento Emitidos
                        $dataSourceSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDetalhesPedidosEmitidosPedidos($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                        if ($dataSourceSQL) {
                            $dataSource = $this->oracleService->executeQuery($dataSourceSQL);
                        }
                        foreach ($dataSource as $key => $row) {
                            $dataSource[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                            $dataSource[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                            $dataSource[$key]['PRECO_TOTAL_GERMOPLASMA'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_GERMOPLASMA']));
                            $dataSource[$key]['PRECO_TOTAL_ROYALTIES'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_ROYALTIES']));
                            $dataSource[$key]['PRECO_TOTAL_TSI'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_TSI']));
                            $dataSource[$key]['PRECO_TOTAL_FRETE'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_FRETE']));
                            $dataSource[$key]['PRECO_TOTAL'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL']));
                        }

                        $collumns = [
                            [
                                'caption' => 'Abrir Pedido Softsul',
                                'allowEditing' => false,
                                'allowExporting'=> false,
                                'fixed' => true,
                                'fixedPosition' => "left",
                                'width' => 'auto',
                                'alignment' => 'center',
                            ],
                            [
                                'dataField' => 'ID_PEDIDO',
                                'caption' => 'Pedido ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 60,
                                'visible' => false
                            ],
                            [
                                'dataField' => 'CODIGO_PEDIDO',
                                'caption' => 'Pedido',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 150,
                                'fixed' => true,
                                'fixedPosition' => "left"
                            ],
                            [
                                'dataField' => 'DATA_PEDIDO',
                                'caption' => 'Emissão',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'ID_CLIENTE',
                                'caption' => 'Cliente ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_CLIENTE',
                                'caption' => 'Cliente',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'VENDEDOR_ID',
                                'caption' => 'Vendedor ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_VENDEDOR',
                                'caption' => 'Vendedor',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'TIPO_PESSOA',
                                'caption' => 'Tipo Pessoa',
                                'alignment' => 'center',
                                'lookup' => [
                                    'dataSource' => [
                                        ['value' => 'PF', 'name' => 'Pessoa Física'],
                                        ['value' => 'PJ', 'name' => 'Pessoa Jurídica']
                                    ],
                                    'valueExpr' => 'value',
                                    'displayExpr' => 'name'
                                ],
                                'validationRules' => [
                                    ['type' => 'required', 'message' => 'Tipo Pessoa é obrigatório']
                                ]
                            ],
                            [
                                'dataField' => 'QUANTIDADE',
                                'caption' => 'Quantidade',
                                'allowEditing' => false,
                                'alignment' => 'center'
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_GERMOPLASMA',
                                'caption' => 'Germoplasma',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_GERMOPLASMA',
                                'caption' => 'Venc. Germoplasma',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_ROYALTIES',
                                'caption' => 'Royalties',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_ROYALTIES',
                                'caption' => 'Venc. Royalties',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_TSI',
                                'caption' => 'TSI',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_TSI',
                                'caption' => 'Venc. TSI',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_FRETE',
                                'caption' => 'Frete',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_FRETE',
                                'caption' => 'Venc. Frete',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL',
                                'caption' => 'Valor Total Pedido',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ]
                        ];
                    #endregion
                } else if ($tipo == "PAGOS") {
                    #region Detalhamento Pagos
                        $dataSourceSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDetalhesPedidosPagosPedidos($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                        if ($dataSourceSQL) {
                            $dataSource = $this->oracleService->executeQuery($dataSourceSQL);
                        }
                        foreach ($dataSource as $key => $row) {
                            $dataSource[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                            $dataSource[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                            $dataSource[$key]['DESCONTO'] = floatval(str_replace(',', '.', $row['DESCONTO']));
                            $dataSource[$key]['JUROS'] = floatval(str_replace(',', '.', $row['JUROS']));
                            $dataSource[$key]['VALOR_RECEBIDO'] = floatval(str_replace(',', '.', $row['VALOR_RECEBIDO']));
                        }

                        $collumns = [
                            [
                                'caption' => 'Abrir Pedido Softsul',
                                'allowEditing' => false,
                                'allowExporting'=> false,
                                'fixed' => true,
                                'fixedPosition' => "left",
                                'width' => 'auto',
                                'alignment' => 'center',
                            ],
                            [
                                'dataField' => 'ID_PEDIDO',
                                'caption' => 'Pedido ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 60,
                                'visible' => false
                            ],
                            [
                                'dataField' => 'CODIGO_PEDIDO',
                                'caption' => 'Pedido',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 150,
                                'fixed' => true,
                                'fixedPosition' => "left"
                            ],
                            [
                                'dataField' => 'DATA_PEDIDO',
                                'caption' => 'Emissão',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'ID_CLIENTE',
                                'caption' => 'Cliente ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_CLIENTE',
                                'caption' => 'Cliente',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'VENDEDOR_ID',
                                'caption' => 'Vendedor ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_VENDEDOR',
                                'caption' => 'Vendedor',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'TIPO_PESSOA',
                                'caption' => 'Tipo Pessoa',
                                'alignment' => 'center',
                                'lookup' => [
                                    'dataSource' => [
                                        ['value' => 'PF', 'name' => 'Pessoa Física'],
                                        ['value' => 'PJ', 'name' => 'Pessoa Jurídica']
                                    ],
                                    'valueExpr' => 'value',
                                    'displayExpr' => 'name'
                                ],
                                'validationRules' => [
                                    ['type' => 'required', 'message' => 'Tipo Pessoa é obrigatório']
                                ]
                            ],
                            [
                                'dataField' => 'RECEBIDO_EM',
                                'caption' => 'Recebido Em',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'DESCONTO',
                                'caption' => 'Desconto',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'JUROS',
                                'caption' => 'Juros',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VALOR_RECEBIDO',
                                'caption' => 'Vlr. Recebido',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ]
                        ];
                    #endregion
                } else if ($tipo == "VENCIDOS") {
                    #region Detalhamento Vencidos
                        $dataSourceSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDetalhesPedidosVencidosPedidos($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                        if ($dataSourceSQL) {
                            $dataSource = $this->oracleService->executeQuery($dataSourceSQL);
                        }
                        foreach ($dataSource as $key => $row) {
                            $dataSource[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                            $dataSource[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                            $dataSource[$key]['PRECO_PARCELA'] = floatval(str_replace(',', '.', $row['PRECO_PARCELA']));
                        }

                        $collumns = [
                            [
                                'caption' => 'Abrir Pedido Softsul',
                                'allowEditing' => false,
                                'allowExporting'=> false,
                                'fixed' => true,
                                'fixedPosition' => "left",
                                'width' => 'auto',
                                'alignment' => 'center',
                            ],
                            [
                                'dataField' => 'ID_PEDIDO',
                                'caption' => 'Pedido ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 60,
                                'visible' => false
                            ],
                            [
                                'dataField' => 'CODIGO_PEDIDO',
                                'caption' => 'Pedido',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 150,
                                'fixed' => true,
                                'fixedPosition' => "left"
                            ],
                            [
                                'dataField' => 'DATA_PEDIDO',
                                'caption' => 'Emissão',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'ID_CLIENTE',
                                'caption' => 'Cliente ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_CLIENTE',
                                'caption' => 'Cliente',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'VENDEDOR_ID',
                                'caption' => 'Vendedor ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_VENDEDOR',
                                'caption' => 'Vendedor',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'TIPO_PESSOA',
                                'caption' => 'Tipo Pessoa',
                                'alignment' => 'center',
                                'lookup' => [
                                    'dataSource' => [
                                        ['value' => 'PF', 'name' => 'Pessoa Física'],
                                        ['value' => 'PJ', 'name' => 'Pessoa Jurídica']
                                    ],
                                    'valueExpr' => 'value',
                                    'displayExpr' => 'name'
                                ],
                                'validationRules' => [
                                    ['type' => 'required', 'message' => 'Tipo Pessoa é obrigatório']
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_PARCELA',
                                'caption' => 'Vencimento da Parcela',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_PARCELA',
                                'caption' => 'Vlr. Parcela',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ]
                        ];
                    #endregion
                }  else if ($tipo == "A_VENCER") {
                    #region Detalhamento A Vencer
                        $dataSourceSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDetalhesPedidosAVencerPedidos($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                        if ($dataSourceSQL) {
                            $dataSource = $this->oracleService->executeQuery($dataSourceSQL);
                        }
                        foreach ($dataSource as $key => $row) {
                            $dataSource[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                            $dataSource[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                            $dataSource[$key]['PRECO_PARCELA'] = floatval(str_replace(',', '.', $row['PRECO_PARCELA']));
                        }

                        $collumns = [
                            [
                                'caption' => 'Abrir Pedido Softsul',
                                'allowEditing' => false,
                                'allowExporting'=> false,
                                'fixed' => true,
                                'fixedPosition' => "left",
                                'width' => 'auto',
                                'alignment' => 'center',
                            ],
                            [
                                'dataField' => 'ID_PEDIDO',
                                'caption' => 'Pedido ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 60,
                                'visible' => false
                            ],
                            [
                                'dataField' => 'CODIGO_PEDIDO',
                                'caption' => 'Pedido',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 150,
                                'fixed' => true,
                                'fixedPosition' => "left"
                            ],
                            [
                                'dataField' => 'DATA_PEDIDO',
                                'caption' => 'Emissão',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'ID_CLIENTE',
                                'caption' => 'Cliente ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_CLIENTE',
                                'caption' => 'Cliente',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'VENDEDOR_ID',
                                'caption' => 'Vendedor ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_VENDEDOR',
                                'caption' => 'Vendedor',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'TIPO_PESSOA',
                                'caption' => 'Tipo Pessoa',
                                'alignment' => 'center',
                                'lookup' => [
                                    'dataSource' => [
                                        ['value' => 'PF', 'name' => 'Pessoa Física'],
                                        ['value' => 'PJ', 'name' => 'Pessoa Jurídica']
                                    ],
                                    'valueExpr' => 'value',
                                    'displayExpr' => 'name'
                                ],
                                'validationRules' => [
                                    ['type' => 'required', 'message' => 'Tipo Pessoa é obrigatório']
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_PARCELA',
                                'caption' => 'Vencimento da Parcela',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_PARCELA',
                                'caption' => 'Vlr. Parcela',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ]
                        ];
                    #endregion
                } else if ($tipo == "PERMUTA") {
                    #region Detalhamento Permuta
                        $dataSourceSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDetalhesPedidosPermuta($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                        if ($dataSourceSQL) {
                            $dataSource = $this->oracleService->executeQuery($dataSourceSQL);
                        }
                        foreach ($dataSource as $key => $row) {
                            $dataSource[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                            $dataSource[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                            $dataSource[$key]['PRECO_TOTAL_GERMOPLASMA'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_GERMOPLASMA']));
                            $dataSource[$key]['PRECO_TOTAL_ROYALTIES'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_ROYALTIES']));
                            $dataSource[$key]['PRECO_TOTAL_TSI'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_TSI']));
                            $dataSource[$key]['PRECO_TOTAL_FRETE'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_FRETE']));
                            $dataSource[$key]['PRECO_TOTAL'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL']));
                        }

                        $collumns = [
                            [
                                'caption' => 'Abrir Pedido Softsul',
                                'allowEditing' => false,
                                'allowExporting'=> false,
                                'fixed' => true,
                                'fixedPosition' => "left",
                                'width' => 'auto',
                                'alignment' => 'center',
                            ],
                            [
                                'dataField' => 'ID_PEDIDO',
                                'caption' => 'Pedido ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 60,
                                'visible' => false
                            ],
                            [
                                'dataField' => 'CODIGO_PEDIDO',
                                'caption' => 'Pedido',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 150,
                                'fixed' => true,
                                'fixedPosition' => "left"
                            ],
                            [
                                'dataField' => 'DATA_PEDIDO',
                                'caption' => 'Emissão',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'ID_CLIENTE',
                                'caption' => 'Cliente ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_CLIENTE',
                                'caption' => 'Cliente',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'VENDEDOR_ID',
                                'caption' => 'Vendedor ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_VENDEDOR',
                                'caption' => 'Vendedor',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'TIPO_PESSOA',
                                'caption' => 'Tipo Pessoa',
                                'alignment' => 'center',
                                'lookup' => [
                                    'dataSource' => [
                                        ['value' => 'PF', 'name' => 'Pessoa Física'],
                                        ['value' => 'PJ', 'name' => 'Pessoa Jurídica']
                                    ],
                                    'valueExpr' => 'value',
                                    'displayExpr' => 'name'
                                ],
                                'validationRules' => [
                                    ['type' => 'required', 'message' => 'Tipo Pessoa é obrigatório']
                                ]
                            ],
                            [
                                'dataField' => 'QUANTIDADE',
                                'caption' => 'Quantidade',
                                'allowEditing' => false,
                                'alignment' => 'center'
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_GERMOPLASMA',
                                'caption' => 'Germoplasma',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_GERMOPLASMA',
                                'caption' => 'Venc. Germoplasma',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL',
                                'caption' => 'Valor Total Pedido',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ]
                        ];
                    #endregion
                } else if ($tipo == "TOTAL_PEDIDOS_SAFRA") {
                    #region Detalhamento Todos Pedidos Safra
                        $dataSourceSQL = $this->creditoECobrancaRepository ? $this->creditoECobrancaRepository->getDetalhesTodosPedidosSafra($apuracao_inicio, $apuracao_fim, $codigoSafra) : null;
                        if ($dataSourceSQL) {
                            $dataSource = $this->oracleService->executeQuery($dataSourceSQL);
                        }
                        foreach ($dataSource as $key => $row) {
                            $dataSource[$key]['NOME_CLIENTE'] = utf8_encode($row['NOME_CLIENTE']);
                            $dataSource[$key]['NOME_VENDEDOR'] = utf8_encode($row['NOME_VENDEDOR']);
                            $dataSource[$key]['PRECO_TOTAL_GERMOPLASMA'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_GERMOPLASMA']));
                            $dataSource[$key]['PRECO_TOTAL_ROYALTIES'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_ROYALTIES']));
                            $dataSource[$key]['PRECO_TOTAL_TSI'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_TSI']));
                            $dataSource[$key]['PRECO_TOTAL_FRETE'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL_FRETE']));
                            $dataSource[$key]['PRECO_TOTAL'] = floatval(str_replace(',', '.', $row['PRECO_TOTAL']));
                        }

                        $collumns = [
                            [
                                'caption' => 'Abrir Pedido Softsul',
                                'allowEditing' => false,
                                'allowExporting'=> false,
                                'fixed' => true,
                                'fixedPosition' => "left",
                                'width' => 'auto',
                                'alignment' => 'center',
                            ],
                            [
                                'dataField' => 'ID_PEDIDO',
                                'caption' => 'Pedido ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 60,
                                'visible' => false
                            ],
                            [
                                'dataField' => 'CODIGO_PEDIDO',
                                'caption' => 'Pedido',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 150,
                                'fixed' => true,
                                'fixedPosition' => "left"
                            ],
                            [
                                'dataField' => 'DATA_PEDIDO',
                                'caption' => 'Emissão',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'ID_CLIENTE',
                                'caption' => 'Cliente ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_CLIENTE',
                                'caption' => 'Cliente',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'VENDEDOR_ID',
                                'caption' => 'Vendedor ID',
                                'allowEditing' => false,
                                'alignment' => 'center',
                                'width' => 100
                            ],
                            [
                                'dataField' => 'NOME_VENDEDOR',
                                'caption' => 'Vendedor',
                                'allowEditing' => false,
                                'width' => 'auto'
                            ],
                            [
                                'dataField' => 'TIPO_PESSOA',
                                'caption' => 'Tipo Pessoa',
                                'alignment' => 'center',
                                'lookup' => [
                                    'dataSource' => [
                                        ['value' => 'PF', 'name' => 'Pessoa Física'],
                                        ['value' => 'PJ', 'name' => 'Pessoa Jurídica']
                                    ],
                                    'valueExpr' => 'value',
                                    'displayExpr' => 'name'
                                ],
                                'validationRules' => [
                                    ['type' => 'required', 'message' => 'Tipo Pessoa é obrigatório']
                                ]
                            ],
                            [
                                'dataField' => 'QUANTIDADE',
                                'caption' => 'Quantidade',
                                'allowEditing' => false,
                                'alignment' => 'center'
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_GERMOPLASMA',
                                'caption' => 'Germoplasma',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_GERMOPLASMA',
                                'caption' => 'Venc. Germoplasma',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_ROYALTIES',
                                'caption' => 'Royalties',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_ROYALTIES',
                                'caption' => 'Venc. Royalties',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_TSI',
                                'caption' => 'TSI',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_TSI',
                                'caption' => 'Venc. TSI',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL_FRETE',
                                'caption' => 'Frete',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ],
                            [
                                'dataField' => 'VENCIMENTO_FRETE',
                                'caption' => 'Venc. Frete',
                                'allowEditing' => true,
                                'alignment' => 'center',
                                'width' => 130,
                                'dataType' => 'date',
                                'format' => 'dd/MM/yyyy',
                                'editorOptions' => [
                                    'displayFormat' => 'dd/MM/yyyy'
                                ]
                            ],
                            [
                                'dataField' => 'PRECO_TOTAL',
                                'caption' => 'Valor Total Pedido',
                                'allowEditing' => false,
                                'alignment' => 'right',
                                'width' => 'auto',
                                'dataType' => 'number',
                                'format' => [
                                    'type' => 'currency',
                                    'currency' => 'BRL',
                                    'precision' => 2
                                ]
                            ]
                        ];
                    #endregion
                }
            }

            // Retorna os dados em JSON
            return new JsonModel([
                'success' => true,
                'data' => array(
                    'dataSource' => $dataSource,
                    'collumns' => $collumns,
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

}