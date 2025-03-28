<?php

namespace Application\Repository;

class RecursosHumanosRepository
{
    public function getLancamentosApuracoesColaboradores($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codCentroCusto = null, $codEscala = null, $codFilial = null, $tipoApuracao = null)
    {
        $wheres = "";
        if (!empty($apuracao_inicio)) {
            $apuracao_fim = !empty($apuracao_fim) ? $apuracao_fim : date('Y-m-d');
            $wheres .= " AND dadosApuracao.DATAPU BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')";
        }
        if (!empty($codColaborador)) {
            $wheres .= " AND dadosApuracao.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheres .= " AND dadosApuracao.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codCentroCusto)) {
            $wheres .= " AND dadosApuracao.CODCCU = {$codCentroCusto}";
        }
        if (!empty($codEscala)) {
            $wheres .= " AND dadosApuracao.COD_ESCALA_APURACAO = {$codEscala}";
        }
        if (!empty($codFilial)) {
            $wheres .= " AND dadosApuracao.CODFIL = {$codFilial}";
        }

        if (!empty($tipoApuracao)) {
            switch($tipoApuracao) {
                case 1: // Faltas
                    $wheres .= " AND dadosApuracao.FALTA = 1 AND dadosApuracao.FLG_MARCACAO = 0";
                break;
                case 2: // Intrajornada
                    $wheres .= " AND dadosApuracao.INTRAJORNADA = 1";
                break;
                case 3: // Interjornada
                    $wheres .= " AND dadosApuracao.INTERJORNADA = 1";
                break;
                case 4: // Horas Extras (Acima de 2h)
                    $wheres .= " AND dadosApuracao.HORAS_EXTRAS_2HRS = 1";
                break;
                case 5: // Adicional Noturno
                    $wheres .= " AND dadosApuracao.ADICIONAL_NOTURNO = 1";
                break;
                case 6: // Trabalho em Folga
                    $wheres .= " AND dadosApuracao.FLG_TRABALHOU_FOLGA = 1";
                break;
                case 7: // Trabalho em Feriado
                    $wheres .= " AND dadosApuracao.FLG_TRABALHOU_FERIADO = 1";
                break;
                case 8: // Justificativa Marcação - Treinamento
                    $wheres .= " AND NVL(dadosApuracao.TREINAMENTO,0) > 0";
                break;
                case 9: // Justificativa Marcação - Serviço Externo
                    $wheres .= " AND NVL(dadosApuracao.SERVICO_EXTERNO,0) > 0";
                break;
                case 10: // Justificativa Marcação - Home Office
                    $wheres .= " AND NVL(dadosApuracao.HOME_OFFICE,0) > 0";
                break;
                case 11: // Justificativa Marcação - Registro Duplicado
                    $wheres .= " AND NVL(dadosApuracao.REGISTRO_DUPLICADO,0) > 0";
                break;
                case 12: // Justificativa Marcação - Falha Equipamento
                    $wheres .= " AND NVL(dadosApuracao.FALHA_EQUIPAMENTO,0) > 0";
                break;
                case 13: // Justificativa Marcação - Esquecimento
                    $wheres .= " AND NVL(dadosApuracao.ESQUECIMENTO,0) > 0";
                break;
            }
        }
        
        return "SELECT dadosApuracao.* FROM (
                    SELECT 
                        ROW_NUMBER() OVER (ORDER BY R066APU.NUMEMP, R066APU.TIPCOL, R034FUN.CODFIL, R066APU.NUMCAD, R066APU.DATAPU) AS ID
                        ,R066APU.NUMEMP 
                        ,R066APU.TIPCOL 
                        ,R030EMP.NOMEMP 
                        ,R034FUN.CODFIL 
                        ,R066APU.NUMCAD 
                        ,R034FUN.NUMCRA 
                        ,R034FUN.NOMFUN 
                        ,R034FUN.DATADM
                        ,R034FUN.CODCCU 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC 
                        ,R016ORN.NOMLOC 
                        ,R034CPL.USU_NUMCAD
                        ,SUPERV.NOMFUN AS NOME_SUPERVISOR
                        ,R066APU.HORDAT 
                        ,R004HOR.DESHOR
                        ,CASE WHEN R066APU.HORDAT IN (9999,9996) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FOLGA
                        ,CASE WHEN R066APU.HORDAT IN (9997) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FERIADO
                        ,R034FUN.CODESC AS CODIGO_ESCALA_CADASTRO
                        ,R006ESC.NOMESC AS COD_ESCALA_CADASTRO
                        ,R066APU.CODESC AS COD_ESCALA_APURACAO
                        ,ESCALA_TROCA.NOMESC AS ESCALA_TROCA
                        ,(ESCALA_TROCA.HORSEM/5) AS JORNADA_DIA
                        ,CASE WHEN R034FUN.CODESC <> R066APU.CODESC THEN 1 ELSE 0 END FLG_TROCA_ESCALA
                        ,R066APU.DATAPU
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 'Não Houve Marcações' ELSE MARCACAO.MARCACOES END MARCACOES
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 0 ELSE 1 END FLG_MARCACAO
                        ,NVL(SITUACAO.FALTA,0) AS FALTA
                        ,NVL(SITUACAO.INTRAJORNADA,0) AS INTRAJORNADA
                        ,NVL(SITUACAO.INTERJORNADA,0) AS INTERJORNADA
                        ,NVL(SITUACAO.HORAS_EXTRAS_2HRS,0) AS HORAS_EXTRAS_2HRS
                        ,NVL(SITUACAO.ADICIONAL_NOTURNO,0) AS ADICIONAL_NOTURNO
                        ,NVL(JUSTIFICATIVAS.TREINAMENTO,0) AS TREINAMENTO
                        ,NVL(JUSTIFICATIVAS.SERVICO_EXTERNO,0) AS SERVICO_EXTERNO
                        ,NVL(JUSTIFICATIVAS.HOME_OFFICE,0) AS HOME_OFFICE
                        ,NVL(JUSTIFICATIVAS.REGISTRO_DUPLICADO,0) AS REGISTRO_DUPLICADO
                        ,NVL(JUSTIFICATIVAS.TESTE,0) AS TESTE
                        ,NVL(JUSTIFICATIVAS.FALHA_EQUIPAMENTO,0) AS FALHA_EQUIPAMENTO
                        ,NVL(JUSTIFICATIVAS.ESQUECIMENTO,0) AS ESQUECIMENTO
                        ,'Faltas' AS TIPO_APURACAO
                    FROM VETORH.R066APU
                    LEFT JOIN VETORH.R034FUN ON R034FUN.NUMEMP = R066APU.NUMEMP AND R034FUN.TIPCOL = R066APU.TIPCOL AND R034FUN.NUMCAD = R066APU.NUMCAD 
                    LEFT JOIN VETORH.R006ESC ON R006ESC.CODESC = R034FUN.CODESC
                    LEFT JOIN VETORH.R034CPL ON R034CPL.NUMEMP = R034FUN.NUMEMP AND R034CPL.TIPCOL = R034FUN.TIPCOL AND R034CPL.NUMCAD = R034FUN.NUMCAD 
                    LEFT JOIN VETORH.R004HOR ON R004HOR.CODHOR = R066APU.HORDAT 
                    LEFT JOIN VETORH.R030EMP ON R030EMP.NUMEMP = R066APU.NUMEMP 
                    LEFT JOIN VETORH.R034FUN SUPERV ON SUPERV.NUMEMP = R034CPL.USU_NUMEMP AND SUPERV.TIPCOL = R034CPL.USU_TIPCOL AND SUPERV.NUMCAD = R034CPL.USU_NUMCAD 
                    LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR   
                    LEFT JOIN VETORH.R016ORN ON R016ORN.TABORG = R034FUN.TABORG AND R016ORN.NUMLOC = R034FUN.NUMLOC
                    LEFT JOIN VETORH.R004HOR ON R004HOR.CODHOR = R066APU.HORDAT 
                    LEFT JOIN VETORH.R006ESC ESCALA_TROCA ON ESCALA_TROCA.CODESC = R066APU.CODESC 
                    LEFT JOIN (
                        SELECT 
                            NUMEMP,
                            TIPCOL,
                            NUMCAD, 
                            NUMCRA,
                            DATAPU,
                            '[' || LISTAGG(TO_CHAR(HORACC), '] [') WITHIN GROUP (ORDER BY DATACC, HORACC) || ']' AS MARCACOES_MIN,
                            '[' || LISTAGG(TO_CHAR(DATACC + (HORACC / 1440), 'HH24:MI'), '] [') WITHIN GROUP (ORDER BY DATACC, HORACC) || ']' AS MARCACOES
                        FROM VETORH.R070ACC
                        GROUP BY NUMEMP, TIPCOL, NUMCAD, NUMCRA, DATAPU
                    ) MARCACAO ON MARCACAO.NUMEMP = R066APU.NUMEMP AND MARCACAO.TIPCOL = R066APU.TIPCOL AND MARCACAO.NUMCAD = R066APU.NUMCAD AND MARCACAO.DATAPU = R066APU.DATAPU
                    LEFT JOIN (
                        SELECT
                             R066SIT.NUMEMP
                            ,R066SIT.TIPCOL 
                            ,R066SIT.NUMCAD
                            ,R066SIT.DATAPU
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (15,65) THEN 1 ELSE 0 END) FALTA
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (68) THEN 1 ELSE 0 END) INTRAJORNADA
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (69) THEN 1 ELSE 0 END) INTERJORNADA
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (16,66,301,302,303,304) AND R066SIT.QTDHOR > 120 THEN 1 ELSE 0 END) HORAS_EXTRAS_2HRS
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (50) THEN 1 ELSE 0 END) ADICIONAL_NOTURNO
                        FROM VETORH.R066SIT 
                        WHERE R066SIT.CODSIT IN (15,65,68,69,16,66,301,302,303,304,50)
                        GROUP BY R066SIT.NUMEMP,R066SIT.TIPCOL,R066SIT.NUMCAD,R066SIT.DATAPU
                    ) SITUACAO ON SITUACAO.NUMEMP = R066APU.NUMEMP AND SITUACAO.TIPCOL = R066APU.TIPCOL AND SITUACAO.NUMCAD = R066APU.NUMCAD AND SITUACAO.DATAPU = R066APU.DATAPU 
                    LEFT JOIN (
                        SELECT *
                        FROM (
                        SELECT 
                            R070JUS.NUMCRA,
                            R070JUS.DATACC,
                            R076JMA.DESJMA
                        FROM VETORH.R070JUS 
                        INNER JOIN VETORH.R076JMA ON R076JMA.CODJMA = R070JUS.CODJMA 
                        )
                        PIVOT (
                            COUNT(DESJMA)
                            FOR DESJMA IN (
                                'TREINAMENTO' AS TREINAMENTO,
                                'SERVIÇO EXTERNO' AS SERVICO_EXTERNO,
                                'HOME OFFICE' AS HOME_OFFICE,
                                'REGISTRO DUPLICADO' AS REGISTRO_DUPLICADO,
                                'TESTE' AS TESTE,
                                'FALHA NO EQUIPAMENTO' AS FALHA_EQUIPAMENTO,
                                'ESQUECIMENTO' AS ESQUECIMENTO
                            )
                        )
                        ORDER BY DATACC
                    ) JUSTIFICATIVAS ON JUSTIFICATIVAS.NUMCRA = R034FUN.NUMCRA AND JUSTIFICATIVAS.DATACC = R066APU.DATAPU 
                WHERE R066APU.NUMEMP = 5
                ) dadosApuracao
                WHERE 1 = 1
                {$wheres}";  
    }














    // LOOKUPS FILTROS
    public function getLookupColaboradorQuery()
    {
        return "SELECT 
                    COLABORADORES.ID,
                    COLABORADORES.DSC
                FROM (
                    SELECT DISTINCT
                        R034FUN.NUMEMP,
                        R034FUN.NUMCAD AS ID,
                        R034FUN.NOMFUN,
                        SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 1, 3) || '.' ||
                        SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 4, 3) || '.' ||
                        SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 7, 3) || '-' ||
                        SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 10, 2) || ' - ' || 
                        UPPER(R034FUN.NOMFUN) || ' - ' || 
                        R034FUN.NUMCAD AS DSC
                    FROM VETORH.R034FUN
                    LEFT JOIN VETORH.R034USU ON R034USU.NUMEMP = R034FUN.NUMEMP AND R034USU.NUMCAD = R034FUN.NUMCAD
                    LEFT JOIN VETORH.R910USU ON R910USU.CODENT = R034USU.CODUSU   
                    WHERE R034FUN.NUMEMP = 5
                    AND R910USU.CONHAB = 1
                ) COLABORADORES
                ORDER BY COLABORADORES.NOMFUN ASC"; 
    }
    public function getLookupSupervisorQuery()
    {
        return "SELECT 
                    COLABORADORES.ID
                    ,COLABORADORES.DSC
                FROM (
                SELECT DISTINCT
                    R034FUN.NUMEMP
                    ,R034FUN.NUMCAD AS ID
                    ,R034FUN.NOMFUN
                    ,SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 1, 3) || '.' ||
                    SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 4, 3) || '.' ||
                    SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 7, 3) || '-' ||
                    SUBSTR(LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0'), 10, 2) || ' - ' || 
                    UPPER(R034FUN.NOMFUN) || ' - ' || 
                    R034FUN.NUMCAD AS DSC
                FROM VETORH.R034CPL 
                LEFT JOIN VETORH.R034FUN ON R034FUN.NUMEMP = R034CPL.USU_NUMEMP AND R034FUN.TIPCOL = R034CPL.USU_TIPCOL AND R034FUN.NUMCAD = R034CPL.USU_NUMCAD
                LEFT JOIN VETORH.R034USU ON R034USU.NUMEMP = R034FUN.NUMEMP AND R034USU.NUMCAD = R034FUN.NUMCAD                                                                                                                                                      
                LEFT JOIN VETORH.R910USU ON R910USU.CODENT = R034USU.CODUSU   
                WHERE R034CPL.USU_NUMCAD IS NOT NULL
                AND R910USU.CONHAB = 1
                AND R034FUN.NUMEMP = 5
                ) COLABORADORES
                ORDER BY COLABORADORES.NOMFUN ASC"; 
    }
    public function getLookupCentroCustoQuery()
    {
        return "SELECT 
                    CCU.ID
                    ,CCU.DSC
                FROM (
                    SELECT DISTINCT
                        R018CCU.CODCCU AS ID
                        ,R018CCU.NOMCCU
                        ,R018CCU.CODCCU || ' - ' || UPPER(R018CCU.NOMCCU) AS DSC
                        FROM VETORH.R018CCU
                        WHERE R018CCU.CODCCU NOT IN (111,156,157,159,158,152,133,118,154)
                ) CCU
                ORDER BY CCU.NOMCCU ASC"; 
    }
    public function getLookupEscalaQuery()
    {
        return "SELECT 
                     R006ESC.CODESC AS ID
                    ,R006ESC.CODESC || ' - ' || R006ESC.NOMESC AS DSC
                FROM VETORH.R006ESC
                ORDER BY CODESC"; 
    }
    public function getLookupFilialQuery()
    {
        return "SELECT 
                    CODFIL as ID,
                    CODFIL || ' - ' || UPPER(NOMFIL) || ' - ' || REGEXP_REPLACE(SUBSTR(NUMCGC, 1, 2) || '.' || SUBSTR(NUMCGC, 3, 3) || '.' || SUBSTR(NUMCGC, 6, 3) || '/' || SUBSTR(NUMCGC, 9, 4) || '-' || SUBSTR(NUMCGC, 13, 2), '[^0-9./-]', '') || ' - ' || UPPER(R074CID.NOMCID) AS DSC
                FROM VETORH.R030FIL
                INNER JOIN VETORH.R074CID ON R074CID.CODCID = R030FIL.CODCID 
                WHERE NUMEMP = 5
                ORDER BY CODFIL"; 
    }
}
