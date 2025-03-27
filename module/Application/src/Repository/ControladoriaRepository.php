<?php

namespace Application\Repository;

class ControladoriaRepository
{


    public function getLancamentosCentrosCustoContaContas($codempresa = null, $codfilial = null, $lancamento_inicio = null, $lancamento_fim = null)
    {
        $wheresContabilizados = "";
        // if (!empty($codempresa)) {
        //     $wheresContabilizados .= " AND lc.CODEMP = {$codempresa}";
        // }
        if (!empty($codfilial)) {
            $wheresContabilizados .= " AND lc.CODFIL = {$codfilial}";
        }
        if (!empty($lancamento_inicio)) {
            $lancamento_fim = !empty($lancamento_fim) ? $lancamento_fim : date('Y-m-d');
            $wheresContabilizados .= " AND LC.DATLCT BETWEEN TO_DATE('{$lancamento_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$lancamento_fim}', 'YYYY-MM-DD')";
        }


        $wheresNaoContabilizados = "";
        // if (!empty($codempresa)) {
        //     $wheresNaoContabilizados .= " AND RT.CODEMP = {$codempresa}";
        // }
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
                        WHEN (LENGTH(HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) >= 4 THEN 
                            SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) ||    
                            SUBSTR(lc.cpllct, 1, INSTR(lc.cpllct, ',\"\"', 1, 1) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 1) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',\"\"', 1, 1) + 1, INSTR(lc.cpllct || ',\"\"', ',\"\"', 1, 2) - INSTR(lc.cpllct, ',\"\"', 1, 1) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 2) + 4, INSTR(HP.DESHPD, '*', 1, 3) - INSTR(HP.DESHPD, '*', 1, 2) - 4) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',\"\"', 1, 2) + 1, INSTR(lc.cpllct || ',\"\"', ',\"\"', 1, 3) - INSTR(lc.cpllct, ',\"\"', 1, 2) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 3) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',\"\"', 1, 3) + 1, INSTR(lc.cpllct || ',\"\"', ',\"\"', 1, 4) - INSTR(lc.cpllct, ',\"\"', 1, 3) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 4) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 5) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',\"\"', 1, 4) + 1, INSTR(lc.cpllct || ',\"\"', ',\"\"', 1, 5) - INSTR(lc.cpllct, ',\"\"', 1, 4) - 1)
                        WHEN (LENGTH(HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) >= 3 THEN 
                            SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) ||    
                            SUBSTR(lc.cpllct, 1, INSTR(lc.cpllct, ',\"\"', 1, 1) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 1) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',\"\"', 1, 1) + 1, INSTR(lc.cpllct || ',\"\"', ',\"\"', 1, 2) - INSTR(lc.cpllct, ',\"\"', 1, 1) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 2) + 4, INSTR(HP.DESHPD, '*', 1, 3) - INSTR(HP.DESHPD, '*', 1, 2) - 4) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',\"\"', 1, 2) + 1, INSTR(lc.cpllct || ',\"\"', ',\"\"', 1, 3) - INSTR(lc.cpllct, ',\"\"', 1, 2) - 1)
                        WHEN (LENGTH(HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) > 1 AND (LENGTH(HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) < 3 THEN 
                            SUBSTR(HP.DESHPD, 1, INSTR(HP.DESHPD, '*', 1, 1) - 1) ||    
                            SUBSTR(lc.cpllct, 1, INSTR(lc.cpllct, ',', 1, 1) - 1) ||
                            SUBSTR(HP.DESHPD, INSTR(HP.DESHPD, '*', 1, 1) + 4, INSTR(HP.DESHPD, '*', 1, 2) - INSTR(HP.DESHPD, '*', 1, 1) - 4) ||
                            SUBSTR(lc.cpllct, INSTR(lc.cpllct, ',', 1, 1) + 1, INSTR(lc.cpllct || ',', ',', 1, 2) - INSTR(lc.cpllct, ',', 1, 1) - 1)
                        WHEN (LENGTH(HP.DESHPD) - LENGTH(REPLACE(HP.DESHPD, '*', ''))) <= 1 THEN HP.DESHPD || LC.CPLLCT
                        ELSE LC.CPLLCT
                    END AS HISTORICO,  
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
                AND lc.CODEMP = 5
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
                AND RT.CODEMP = 5
                {$wheresNaoContabilizados}
        ";  
    }
    public function getLookupEmpresaQuery()
    {
        return "SELECT 
                    CODEMP as id, 
                    CODEMP || ' - ' || UPPER(NOMEMP) AS dsc 
                FROM E070EMP
                ORDER BY CODEMP"; 
    }
    public function getLookupFilialQuery($codempresa = null)
    {
        return "SELECT 
                    CODFIL as id,
                    CODFIL || ' - ' || UPPER(SIGFIL) || ' - ' || NUMCGC || ' - ' || CIDFIL AS dsc
                FROM E070FIL
                WHERE 1 = 1 
                AND CODEMP = 5
                ORDER BY CODEMP, CODFIL";
    }
}
