<?php

namespace Application\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;

class ControladoriaRepository
{

    private $tableGateway;
    private $userMenuTableGateway;

    public function __construct(Adapter $adapter)
    {
        $this->tableGateway = new TableGateway('menu', $adapter);
        $this->userMenuTableGateway = new TableGateway('usuario_menu', $adapter);
        $this->adapter = $adapter;
    }


    public function getLancamentosCentrosCustoContaContas($codempresa = null, $codfilial = null, $lancamento_inicio = null, $lancamento_fim = null)
    {
        $wheresContabilizados = "";
        if (!empty($codfilial)) {
            $wheresContabilizados .= " AND lc.CODFIL = {$codfilial}";
        }
        if (!empty($lancamento_inicio)) {
            $lancamento_fim = !empty($lancamento_fim) ? $lancamento_fim : date('Y-m-d');
            $wheresContabilizados .= " AND LC.DATLCT BETWEEN TO_DATE('{$lancamento_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$lancamento_fim}', 'YYYY-MM-DD')";
        }


        $wheresNaoContabilizados = "";
        if (!empty($codfilial)) {
            $wheresNaoContabilizados .= " AND LC.CODFIL = {$codfilial}";
        }
        if (!empty($lancamento_inicio)) {
            $lancamento_fim = !empty($lancamento_fim) ? $lancamento_fim : date('Y-m-d');
            $wheresNaoContabilizados .= " AND RT.DATMOV BETWEEN TO_DATE('{$lancamento_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$lancamento_fim}', 'YYYY-MM-DD')";
        }
       
        
        return "SELECT 
                    RT.CODEMP, 
                    EMP.NOMEMP,   
                    RT.FILRAT,
                    FI.SIGFIL,  
                    TO_CHAR(LC.DATLCT, 'YYYY-MM-DD') AS DATLCT,
                    TO_CHAR(LC.DATLCT, 'FMMonth') AS MES_LANCAMENTO,
                    PL.CLACTA AS CONTA_CONTABIL,      
                    PL.DESPAR AS DSC_CONTA_CONTABIL,       
                    PL.CTARED AS CONTA_REDUZIDA,       
                    PL.ABRCTA AS DSC_CONTA_REDUZIDA,     
                    CU.CLACCU AS CONTA_CENTRO_CUSTO,       
                    CU.CODCCU AS CENTRO_CUSTO,       
                    CU.DESCCU AS DSC_CENTRO_CUSTO,     
                    CASE 
                        WHEN RT.DEBCRE = 'C' THEN LC.CTADEB
                        WHEN RT.DEBCRE = 'D' THEN LC.CTACRE
                        ELSE NULL
                    END AS CONTRA_PARTIDA,    
                    LC.NUMLOT AS LOTE,      
                    RT.NUMLCT AS NUM_LANCAMENTO,      
                    LC.CODHPD AS CODIGO_HISTORICO,  
                    CASE
                        WHEN (LENGTH (HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) >= 4 THEN 
                        SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) ||
                        -- primeiro *
                        SUBSTR(lc.cpllct, 1, INSTR(lc.cpllct, ',', 1, 1) - 1) ||
                        --primeiro list
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 1) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                        --segundo *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 1) + 1, INSTR(lc.cpllct || ',', ',', 1, 2) - INSTR(lc.cpllct, ',', 1, 1) - 1)||
                        -- segundo list
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 2) + 4, INSTR(HP.DESHPD, '*', 1, 3) - INSTR(HP.DESHPD, '*', 1, 2) - 4) ||
                        --terceiro *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 2) + 1, INSTR(lc.cpllct || ',', ',', 1, 3) - INSTR(lc.cpllct, ',', 1, 2) - 1)||
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 3) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                        -- quarto *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 3) + 1, INSTR(lc.cpllct || ',', ',', 1, 4) - INSTR(lc.cpllct, ',', 1, 3) - 1 )||
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 4) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 5) ||
                        -- quinto *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 4) + 1, INSTR(lc.cpllct || ',', ',', 1, 5) - INSTR(lc.cpllct, ',', 1, 4) - 1 )
                        -- quinto list
                        WHEN (LENGTH (HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) >= 3 THEN 
                        SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) ||
                        -- primeiro *
                        SUBSTR(lc.cpllct, 1, INSTR(lc.cpllct, ',', 1, 1) - 1) ||
                        --primeiro list
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 1) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                        --segundo *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 1) + 1, INSTR(lc.cpllct || ',', ',', 1, 2) - INSTR(lc.cpllct, ',', 1, 1) - 1)||
                        -- segundo list
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 2) + 4, INSTR(HP.DESHPD, '*', 1, 3) - INSTR(HP.DESHPD, '*', 1, 2) - 4) ||
                        --terceiro *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 2) + 1, INSTR(lc.cpllct || ',', ',', 1, 3) - INSTR(lc.cpllct, ',', 1, 2) - 1)
                        WHEN (LENGTH (HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) > 1
                        AND (LENGTH (HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) < 3 THEN 
                        SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) ||
                        -- primeiro *
                        SUBSTR(lc.cpllct, 1, INSTR(lc.cpllct, ',', 1, 1) - 1) ||
                        --primeiro list
                        SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 1) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                        --segundo *
                        SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 1) + 1, INSTR(lc.cpllct || ',', ',', 1, 2) - INSTR(lc.cpllct, ',', 1, 1) - 1)
                        -- segundo list
                        WHEN (LENGTH (HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) <= 1 THEN 
                        SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) || LC.CPLLCT || HP.DESHPD
                        ELSE LC.CPLLCT
                    END HISTORICO, 
                    CASE WHEN RT.DEBCRE = 'D' THEN RT.VLRRAT ELSE 0 END AS DEBITO,       
                    CASE WHEN RT.DEBCRE = 'C' THEN RT.VLRRAT ELSE 0 END AS CREDITO,    
                    (CASE WHEN RT.DEBCRE = 'D' THEN RT.VLRRAT ELSE 0 END) - (CASE WHEN RT.DEBCRE = 'C' THEN RT.VLRRAT ELSE 0 END) AS TOTAL,
                    RT.DEBCRE AS TIPO, 
                    CASE WHEN PL.GRUCTA = '1' THEN '1-ATIVO' WHEN PL.GRUCTA = '2' THEN '2-PASIVO' WHEN PL.GRUCTA = '3' THEN '3-RECEITAS' WHEN PL.GRUCTA = '5' THEN '5-CUSTOS E DESPESAS' WHEN PL.GRUCTA = '6' THEN '6-CONTAS GERENCIAIS' END CLASSIFICACAO,
                    CASE WHEN CU.TIPCCU = 1 THEN '1 - Produtivo/Operacional Indireto' WHEN CU.TIPCCU = 2 THEN '2 - Produtivo/Operacional Direto' WHEN CU.TIPCCU = 3 THEN '3 - Administrativo' WHEN CU.TIPCCU = 4 THEN '4 - Comercial' WHEN CU.TIPCCU = 5 THEN '5 - Financeiro' END AS TIPO_CENTRO_CUSTO,
                    CASE WHEN CU.TIPCCU IN (1,2) AND PL.CLACTA LIKE '5020102%' THEN '1 - CC Produtivo em Conta ADM' WHEN CU.TIPCCU IN (3) AND PL.CLACTA LIKE '5020101%' THEN '2 - CC ADM em Conta Produtiva' ELSE NULL END LANCAMENTO_INCONSIST, -- 1 = CC Produtivo em Conta ADM; 2 = CC ADM em Conta Produtiva;
                    NULL AS NUMDOC,
                    'Contabilizados' AS FLG_CONTABILIZADO
                FROM Sapiens.E640RAT RT  
                LEFT JOIN Sapiens.E640LCT LC ON RT.CODEMP = LC.CODEMP AND RT.NUMLCT = LC.NUMLCT AND RT.FILRAT = LC.CODFIL  
                LEFT JOIN Sapiens.E045PLA PL ON RT.CODEMP = PL.CODEMP AND RT.CTARED = PL.CTARED
                LEFT JOIN Sapiens.E046HPD HP ON HP.CODHPD = LC.CODHPD
                INNER JOIN Sapiens.E044CCU CU ON RT.CODEMP = CU.CODEMP AND RT.CODCCU = CU.CODCCU 
                INNER JOIN Sapiens.E070EMP EMP ON RT.CODEMP = EMP.CODEMP 
                INNER JOIN Sapiens.E070FIL FI ON RT.CODEMP = FI.CODEMP AND RT.FILRAT = FI.CODFIL 
                WHERE LC.SITLCT = '2' 
                AND lc.CODEMP = 1000
                AND CASE WHEN CU.TIPCCU IN (1,2) AND PL.CLACTA LIKE '5020102%' THEN 1 WHEN CU.TIPCCU IN (3) AND PL.CLACTA LIKE '5020101%' THEN 2 ELSE NULL END IN (1,2)
                {$wheresContabilizados}

                UNION ALL

                -- TRAZ CASOS NÃO CONTABILIZADOS AINDA.
                SELECT 
                    RT.CODEMP, 
                    EMP.NOMEMP,   
                    RT.CODFIL as FILRAT,
                    FI.SIGFIL,  
                    TO_CHAR(RT.DATMOV, 'YYYY-MM-DD') AS DATLCT,
                    TO_CHAR(RT.DATMOV, 'FMMonth') AS MES_LANCAMENTO,
                    PL.CLACTA AS CONTA_CONTABIL,      
                    PL.DESPAR AS DSC_CONTA_CONTABIL,       
                    PL.CTARED AS CONTA_REDUZIDA,       
                    PL.ABRCTA AS DSC_CONTA_REDUZIDA,     
                    CU.CLACCU AS CONTA_CENTRO_CUSTO,       
                    CU.CODCCU AS CENTRO_CUSTO,       
                    CU.DESCCU AS DSC_CENTRO_CUSTO,     
                    NULL AS CONTRA_PARTIDA,    
                    LC.NUMLOT AS LOTE,      
                    null AS NUM_LANCAMENTO,      
                    null AS CODIGO_HISTORICO, 
                    NULL AS HISTORICO,
                    CASE WHEN LC.ESTEOS = 'E' THEN RT.VLRRAT ELSE 0 END AS DEBITO,       
                    CASE WHEN LC.ESTEOS = 'S' THEN RT.VLRRAT ELSE 0 END AS CREDITO,    
                    (CASE WHEN LC.ESTEOS = 'E' THEN RT.VLRRAT ELSE 0 END) - (CASE WHEN LC.ESTEOS = 'S' THEN RT.VLRRAT ELSE 0 END) AS TOTAL,
                    CASE WHEN LC.ESTEOS = 'E' THEN 'D' WHEN LC.ESTEOS = 'S' THEN 'C' ELSE NULL END AS TIPO, 
                    CASE WHEN PL.GRUCTA = '1' THEN '1-ATIVO' WHEN PL.GRUCTA = '2' THEN '2-PASIVO' WHEN PL.GRUCTA = '3' THEN '3-RECEITAS' WHEN PL.GRUCTA = '5' THEN '5-CUSTOS E DESPESAS' WHEN PL.GRUCTA = '6' THEN '6-CONTAS GERENCIAIS' END CLASSIFICACAO,
                    CASE WHEN CU.TIPCCU = 1 THEN '1 - Produtivo/Operacional Indireto' WHEN CU.TIPCCU = 2 THEN '2 - Produtivo/Operacional Direto' WHEN CU.TIPCCU = 3 THEN '3 - Administrativo' WHEN CU.TIPCCU = 4 THEN '4 - Comercial' WHEN CU.TIPCCU = 5 THEN '5 - Financeiro' END AS TIPO_CENTRO_CUSTO,
                    CASE WHEN CU.TIPCCU IN (1,2) AND PL.CLACTA LIKE '5020102%' THEN '1 - CC Produtivo em Conta ADM' WHEN CU.TIPCCU IN (3) AND PL.CLACTA LIKE '5020101%' THEN '2 - CC ADM em Conta Produtiva' ELSE NULL END LANCAMENTO_INCONSIST, -- 1 = CC Produtivo em Conta ADM; 2 = CC ADM em Conta Produtiva;
                    LC.NUMDOC,
                    'Não contabilizados' AS FLG_CONTABILIZADO
                FROM Sapiens.E210RAT RT
                LEFT JOIN Sapiens.E210MVP LC ON LC.CODEMP = RT.CODEMP AND LC.CODPRO = RT.CODPRO AND LC.CODDER = RT.CODDER AND LC.CODDEP = RT.CODDEP AND LC.DATMOV = RT.DATMOV AND LC.SEQMOV = RT.SEQMOV
                LEFT JOIN Sapiens.E045PLA PL ON RT.CODEMP = PL.CODEMP AND RT.CTARED = PL.CTARED
                INNER JOIN Sapiens.E070EMP EMP ON RT.CODEMP = EMP.CODEMP 
                INNER JOIN Sapiens.E070FIL FI ON RT.CODEMP = FI.CODEMP AND RT.CODFIL = FI.CODFIL 
                INNER JOIN Sapiens.E044CCU CU ON RT.CODEMP = CU.CODEMP AND RT.CODCCU = CU.CODCCU
                WHERE 1 = 1
                AND CASE WHEN CU.TIPCCU IN (1,2) AND PL.CLACTA LIKE '5020102%' THEN 1 WHEN CU.TIPCCU IN (3) AND PL.CLACTA LIKE '5020101%' THEN 2 ELSE NULL END IN (1,2)
                AND (LC.NUMLOT = 0 OR LC.NUMLOT IS null)
                AND RT.CODEMP = 1000
                {$wheresNaoContabilizados}
        ";  
    }
    public function getLookupEmpresaQuery()
    {
        return "SELECT 
                    CODEMP as id, 
                    CODEMP || ' - ' || UPPER(NOMEMP) AS dsc 
                FROM sapiens.E070EMP
                ORDER BY CODEMP"; 
    }
    public function getLookupFilialQuery($codempresa = null)
    {
        return "SELECT 
                    CODFIL as id,
                    CODFIL || ' - ' || UPPER(SIGFIL) || ' - ' || REGEXP_REPLACE(SUBSTR(NUMCGC, 1, 2) || '.' || SUBSTR(NUMCGC, 3, 3) || '.' || SUBSTR(NUMCGC, 6, 3) || '/' || SUBSTR(NUMCGC, 9, 4) || '-' || SUBSTR(NUMCGC, 13, 2), '[^0-9./-]', '') || ' - ' || CIDFIL AS dsc 
                FROM sapiens.E070FIL
                WHERE 1 = 1 
                AND CODEMP = 1000
                ORDER BY CODEMP, CODFIL";
    }


    #region Estrutura Contas
        public function listarPlanoContas()
        {
            $sql = '
                 SELECT 
                    cpc.id, 
                    cpc.parent_id, 
                    cpc.clacta, 
                    cpc.descta, 
                    cpc.ctared, 
                    cpc.natcta, 
                    cpc.anasin, 
                    cpc.id_pacote_contas,
                    cpc2.nome as dsc_pacote_contas
                FROM ctr_plano_contas cpc
                left join ctr_pacote_contas cpc2 on cpc2.id = cpc.id_pacote_contas
                ORDER BY cpc.clacta
                ';
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $planoContas = [];
            foreach ($result as $row) {
                $planoContas[] = $row;
            }

            return $planoContas;
        }
        public function inserirPlanoConta(array $data)
        {
            $sql = 'INSERT INTO ctr_plano_contas 
                    (parent_id, clacta, descta, ctared, natcta, anasin, id_pacote_contas) 
                    VALUES 
                    (:parent_id, :clacta, :descta, :ctared, :natcta, :anasin, :id_pacote_contas)';
            
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([
                ':parent_id' => $data['parent_id'] ?? null,
                ':clacta' => $data['clacta'] ?? null,
                ':descta' => $data['descta'] ?? null,
                ':ctared' => !empty($data['ctared']) ? $data['ctared'] : null,
                ':natcta' => $data['natcta'] ?? null,
                ':anasin' => $data['anasin'] ?? null,
                ':id_pacote_contas' => $data['id_pacote_contas'] ?? null
            ]);
        }
        public function atualizarPlanoConta(array $data)
        {
            $sql = 'UPDATE ctr_plano_contas 
                    SET 
                        parent_id = :parent_id, 
                        clacta = :clacta, 
                        descta = :descta, 
                        ctared = :ctared, 
                        natcta = :natcta, 
                        anasin = :anasin, 
                        id_pacote_contas = :id_pacote_contas
                    WHERE id = :id';
            
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([
                ':id' => $data['id'],
                ':parent_id' => $data['parent_id'] ?? null,
                ':clacta' => $data['clacta'],
                ':descta' => $data['descta'] ?? null,
                ':ctared' => !empty($data['ctared']) ? $data['ctared'] : null,
                ':natcta' => $data['natcta'] ?? null,
                ':anasin' => $data['anasin'] ?? null,
                ':id_pacote_contas' => $data['id_pacote_contas'] ?? null
            ]);
        }
        public function excluirPlanoConta($id)
        {
            $connection = $this->adapter->getDriver()->getConnection();
            $connection->beginTransaction();

            try {
               $sql = 'WITH RECURSIVE contas_a_excluir AS (
                            SELECT id FROM ctr_plano_contas WHERE id = :id
                            UNION ALL
                            SELECT p.id 
                            FROM ctr_plano_contas p
                            INNER JOIN contas_a_excluir c ON p.parent_id = c.id
                        )
                        DELETE FROM ctr_plano_contas WHERE id IN (SELECT id FROM contas_a_excluir);';
                $statementFilhos = $this->adapter->createStatement($sql);
                $statementFilhos->execute([':id' => $id]);

                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollback();
                throw $e; // Deixa a exceção propagar para ser tratada no controller
            }
        }
        public function getBuscarDetalhesClactaQuery($clacta = null)
        {
            $ands = "";
             if (!empty($clacta)) {
                $ands .= " AND CLACTA = '{$clacta}'";
            }

            return "SELECT 
                        CLACTA,
                        CTARED,
                        DESCTA,
                        NATCTA,
                        ANASIN
                    FROM sapiens.E045PLA
                    WHERE CODEMP = 1000
                    {$ands}
                    ORDER BY CLACTA"; 
        }
    #endRegion

    #region Cadastro Grupos Contas
        public function listarGrupoContas()
        {
            $sql = 'SELECT id, nome, descricao, flg_ativo FROM ctr_grupo_contas ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function salvarGrupoContas(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do Grupo Contas é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : false;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE ctr_grupo_contas SET 
                            nome = :nome, 
                            descricao = :descricao,
                            flg_ativo = :flg_ativo
                        WHERE id = :id';
                $params = [
                    ':nome' => $data['nome'],
                    ':descricao' => $data['descricao'] ?? null,
                    ':flg_ativo' => $flgAtivo,
                    ':id' => $data['id'],
                ];
            } else {
                // Inserir
                $sql = 'INSERT INTO ctr_grupo_contas (nome, descricao, flg_ativo) 
                        VALUES (:nome, :descricao, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':descricao' => $data['descricao'] ?? null,
                    ':flg_ativo' => $flgAtivo,
                ];
            }

            $statement = $this->adapter->createStatement($sql);
            $statement->execute($params);
        }
        public function excluirGrupoContas($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do Grupo Contas não fornecido.');
            }

            $sql = 'UPDATE ctr_grupo_contas SET flg_ativo = false WHERE id = :id';
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([':id' => $id]);
        }
        public function getLookupGrupoContas()
        {
            $sql = 'SELECT id, nome, descricao FROM ctr_grupo_contas WHERE flg_ativo = true ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
    #endregion

    #region Cadastro Pacote Contas
        public function listarPacoteContas()
        {
            $sql = 'SELECT id, nome, descricao, flg_ativo FROM ctr_pacote_contas ORDER BY nome';
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function salvarPacoteContas(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do Pacote de Contas é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : false;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE ctr_pacote_contas SET 
                            nome = :nome, 
                            descricao = :descricao,
                            flg_ativo = :flg_ativo
                        WHERE id = :id';
                $params = [
                    ':nome' => $data['nome'],
                    ':descricao' => $data['descricao'] ?? null,
                    ':flg_ativo' => $flgAtivo,
                    ':id' => $data['id'],
                ];
            } else {
                // Inserir
                $sql = 'INSERT INTO ctr_pacote_contas (nome, descricao, flg_ativo) 
                        VALUES (:nome, :descricao, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':descricao' => $data['descricao'] ?? null,
                    ':flg_ativo' => $flgAtivo,
                ];
            }

            $statement = $this->adapter->createStatement($sql);
            $statement->execute($params);
        }
        public function excluirPacoteContas($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do Pacote de Contas não fornecido.');
            }

            $sql = 'UPDATE ctr_pacote_contas SET flg_ativo = false WHERE id = :id';
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([':id' => $id]);
        }
        public function getLookupPacoteContas()
        {
            $sql = 'SELECT id, nome, descricao FROM ctr_pacote_contas WHERE flg_ativo = true ORDER BY nome';
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
    #endregion

    #region Vinculos Conta X Centro de Custo
        public function listarVinculoContaCcu($codccu, $referencia)
        {
            $sql = "
                SELECT
                    v.id,
                    v.codccu,
                    v.id_plano_contas,
                    v.ctared,
                    v.id_grupo_contas,  
                    v.referencia,
                    v.valor_orcado,  
                    p.ctared AS conta_codigo,
                    p.descta AS conta_descricao,
                    g.nome AS gestor_nome,
                    gc.nome AS grupo_nome
                FROM ctr_vinculo_contas_ccu v
                JOIN ctr_plano_contas p ON p.id = v.id_plano_contas
                LEFT JOIN usuario g ON g.id = v.id_usuario_gestor
                LEFT JOIN ctr_grupo_contas gc ON gc.id = v.id_grupo_contas
                WHERE v.codccu = :codccu
                AND v.referencia = :referencia
                ORDER BY p.ctared
            ";

            $statement = $this->adapter->createStatement($sql, [
                'codccu' => $codccu,
                'referencia' => $referencia
            ]);

            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function listarPlanoContaAnaliticas()
        {
            $sql = "
                SELECT 
                    cpc.id, 
                    cpc.parent_id, 
                    cpc.clacta, 
                    cpc.descta, 
                    cpc.ctared, 
                    cpc.natcta, 
                    cpc.anasin, 
                    cpc.id_pacote_contas,
                    cpc2.nome as dsc_pacote_contas
                FROM ctr_plano_contas cpc
                LEFT JOIN ctr_pacote_contas cpc2 ON cpc2.id = cpc.id_pacote_contas
                WHERE anasin = 'A'
                ORDER BY cpc.clacta
                ";
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $planoContas = [];
            foreach ($result as $row) {
                $planoContas[] = $row;
            }

            return $planoContas;
        }
        public function salvarVinculoContaCcu($codccu, $idPlano, $ctared, $idGestor, $referencia)
        {
            $sql = "
                INSERT INTO ctr_vinculo_contas_ccu 
                    (codccu, id_plano_contas, ctared, id_usuario_gestor, id_grupo_contas, referencia)
                VALUES 
                    (:codccu, :id_plano_contas, :ctared, :id_usuario_gestor, :id_grupo_contas, :referencia)
            ";

            $statement = $this->adapter->createStatement($sql, [
                'codccu' => $codccu,
                'id_plano_contas' => $idPlano,
                'ctared' => $ctared,
                'id_usuario_gestor' => $idGestor,
                'id_grupo_contas' => null,
                'referencia' => $referencia
            ]);

            try {
                $statement->execute();
                return [
                    'success' => true,
                    'message' => 'Vínculo criado com sucesso.'
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar vínculo: ' . $e->getMessage()
                ];
            }
        }
        public function atualizarGrupoVinculo($idVinculo, $idGrupo, $referencia)
        {
            $sql = "
                UPDATE ctr_vinculo_contas_ccu
                SET id_grupo_contas = :grupo
                WHERE id = :id AND referencia = :referencia
            ";

            try {
                $this->adapter->createStatement($sql, [
                    'id'    => $idVinculo,
                    'grupo' => $idGrupo,
                    'referencia' => $referencia
                ])->execute();

                return ['success' => true];

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        public function atualizarGestorCcu($codccu, $idGestor, $referencia)
        {
            $sql = "
                UPDATE ctr_vinculo_contas_ccu
                SET id_usuario_gestor = :gestor
                WHERE codccu = :ccu
                AND referencia = :referencia
            ";

            try {
                $this->adapter->createStatement($sql, [
                    'gestor' => $idGestor,
                    'ccu'    => $codccu,
                    'referencia' => $referencia
                ])->execute();

                return ['success' => true];

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        public function excluirVinculoContaCcu($id, $referencia)
        {
            $sql = "
                DELETE FROM ctr_vinculo_contas_ccu
                WHERE id = :id AND referencia = :referencia
            ";

            $statement = $this->adapter->createStatement($sql, [
                'id' => $id,
                'referencia' => $referencia
            ]);

            try {
                $statement->execute();

                return [
                    'success' => true,
                    'message' => 'Vínculo removido com sucesso.'
                ];

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Erro ao remover vínculo: ' . $e->getMessage()
                ];
            }
        }
        public function importarValorOrcado($codccu, $referencia, $gestor, $dados)
        {
            try {
                $atualizados = 0;
                $naoEncontrados = [];

                foreach ($dados as $item) {

                    $ctared = $item['ctared'];
                    $valor  = $item['valor_orcado'];

                    // Busca vínculo pelo CTARED
                    $sqlBusca = "
                        SELECT id 
                        FROM ctr_vinculo_contas_ccu
                        WHERE codccu = :ccu
                        AND referencia = :ref
                        AND ctared = :ctared
                        LIMIT 1
                    ";

                    $row = $this->adapter->createStatement($sqlBusca, [
                        'ccu'     => $codccu,
                        'ref'     => $referencia,
                        'ctared'  => $ctared
                    ])->execute()->current();

                    if (!$row) {
                        $naoEncontrados[] = $ctared;
                        continue;
                    }

                    $idVinculo = $row['id'];

                    // Atualiza valor orçado
                    $sqlUpdate = "
                        UPDATE ctr_vinculo_contas_ccu
                        SET valor_orcado = :valor,
                            id_usuario_gestor = :gestor
                        WHERE id = :id
                    ";

                    $this->adapter->createStatement($sqlUpdate, [
                        'valor'  => $valor,
                        'gestor' => $gestor,
                        'id'     => $idVinculo
                    ])->execute();

                    $atualizados++;
                }

                $listaNao = empty($naoEncontrados) 
                    ? 'Nenhum.' 
                    : implode(', ', $naoEncontrados);

                return [
                    'success' => true,
                    'atualizados' => $atualizados,
                    'nao_encontrados' => $naoEncontrados,
                    'mensagem' => "Importação concluída. Atualizados: $atualizados. Contas não  encontradas: $listaNao"
                ];

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Erro ao importar valores: ' . $e->getMessage()
                ];
            }
        }

    #endRegion


}
