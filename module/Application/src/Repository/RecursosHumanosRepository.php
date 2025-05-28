<?php

namespace Application\Repository;

class RecursosHumanosRepository
{

    public function getLancamentosApuracoesColaboradores($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codCentroCusto = null, $codEscala = null, $codFilial = null, $tipoApuracao = null, $datReferencia = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $apuracao_fim = !empty($apuracao_fim) ? $apuracao_fim : date('Y-m-d');
            $wheresInternos .= " AND R066APU.DATAPU BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')";
            $wheresInternos .= " AND CASE WHEN SITUACAO_COLABORADOR.TIPSIT = 7 AND R034FUN.DATAFA < R066APU.DATAPU THEN 1 ELSE 0 END = 0"; // não listar usuários demitidos DE ACORDO COM A DATA APURAÇÃO
        }
        if (!empty($datReferencia)) {
            $wheresInternos .= " AND R066APU.PERREF = TO_DATE('{$datReferencia}', 'YYYY-MM-DD')";
            $wheresInternos .= " AND CASE WHEN SITUACAO_COLABORADOR.TIPSIT = 7 AND R034FUN.DATAFA < R066APU.DATAPU THEN 1 ELSE 0 END = 0"; // não listar usuários demitidos DE ACORDO COM A DATA APURAÇÃO
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R066APU.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codCentroCusto)) {
            $wheresInternos .= " AND R034FUN.CODCCU = {$codCentroCusto}";
        }
        if (!empty($codFilial)) {
            $wheresInternos .= " AND R034FUN.CODFIL = {$codFilial}";
        }
        if (!empty($codEscala)) {
            $wheresInternos .= " AND R066APU.CODESC = {$codEscala}";
        }

        if (!empty($tipoApuracao)) {
            switch($tipoApuracao) {
                case 1: // Faltas
                    $wheresExternos .= " AND dadosApuracao.FALTA = 1 AND dadosApuracao.FLG_MARCACAO = 0";
                    $columnTipoApuracao .= "Faltas";
                break;
                case 2: // Intrajornada
                    $wheresExternos .= " AND dadosApuracao.INTRAJORNADA = 1";
                    $columnTipoApuracao .= "Intrajornada";
                break;
                case 3: // Interjornada
                    $wheresExternos .= " AND dadosApuracao.INTERJORNADA = 1";
                    $columnTipoApuracao .= "Interjornada";
                break;
                case 4: // Horas Extras (Acima de 2h)
                    $wheresExternos .= " AND dadosApuracao.HORAS_EXTRAS_2HRS = 1";
                    $columnTipoApuracao .= "Horas Extras (Acima de 2h)";
                break;
                case 5: // Adicional Noturno
                    $wheresExternos .= " AND dadosApuracao.ADICIONAL_NOTURNO = 1";
                    $columnTipoApuracao .= "Adicional Noturno";
                break;
                case 6: // Trabalho em Folga
                    $wheresExternos .= " AND dadosApuracao.FLG_TRABALHOU_FOLGA = 1";
                    $columnTipoApuracao .= "Trabalho em Folga";
                break;
                case 7: // Trabalho em Feriado
                    $wheresExternos .= " AND dadosApuracao.FLG_TRABALHOU_FERIADO = 1";
                    $columnTipoApuracao .= "Trabalho em Feriado";
                break;
                case 8: // Justificativa Marcação - Treinamento
                    $wheresExternos .= " AND NVL(dadosApuracao.TREINAMENTO,0) > 0";
                    $columnTipoApuracao .= "Justificativa Marcação - Treinamento";
                break;
                case 9: // Justificativa Marcação - Serviço Externo
                    $wheresExternos .= " AND NVL(dadosApuracao.SERVICO_EXTERNO,0) > 0";
                    $columnTipoApuracao .= "Justificativa Marcação - Serviço Externo";
                break;
                case 10: // Justificativa Marcação - Home Office
                    $wheresExternos .= " AND NVL(dadosApuracao.HOME_OFFICE,0) > 0";
                    $columnTipoApuracao .= "Justificativa Marcação - Home Office";
                break;
                case 11: // Justificativa Marcação - Registro Duplicado
                    $wheresExternos .= " AND NVL(dadosApuracao.REGISTRO_DUPLICADO,0) > 0";
                    $columnTipoApuracao .= "Justificativa Marcação - Registro Duplicado";
                break;
                case 12: // Justificativa Marcação - Falha Equipamento
                    $wheresExternos .= " AND NVL(dadosApuracao.FALHA_EQUIPAMENTO,0) > 0";
                    $columnTipoApuracao .= "Justificativa Marcação - Falha Equipamento";
                break;
                case 13: // Justificativa Marcação - Esquecimento
                    $wheresExternos .= " AND NVL(dadosApuracao.ESQUECIMENTO,0) > 0";
                    $columnTipoApuracao .= "Justificativa Marcação - Esquecimento";
                break;
                case 14: // Compensação
                    $wheresExternos .= " AND dadosApuracao.TIPO_COMPENSACAO IS NOT NULL";
                    $columnTipoApuracao .= "Compensação";
                break;
                case 15: // Horas Extras 50
                    $wheresExternos .= " AND dadosApuracao.HORAS_EXTRAS_50 = 1";
                    $columnTipoApuracao .= "Horas Extras 50%";
                break;
                case 16: // Horas Extras 100
                    $wheresExternos .= " AND dadosApuracao.HORAS_EXTRAS_100 = 1";
                    $columnTipoApuracao .= "Horas Extras 100%";
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
                        ,R034FUN.SITAFA
                        ,CASE WHEN TO_CHAR(R034FUN.DATAFA, 'YYYY-MM-DD') = '1900-12-31' THEN NULL ELSE TO_CHAR(R034FUN.DATAFA, 'YYYY-MM-DD') END AS DATAFA
                        ,SITUACAO_COLABORADOR.DESSIT AS DSC_SITUACAO_COLABORADOR
                        ,R034FUN.CODCCU 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC
                        ,upper(R016ORN.NOMLOC) as NOMLOC
                        ,R034CPL.USU_NUMCAD
                        ,CASE WHEN SUPERV.NOMFUN IS NULL THEN 'SEM SUPERVISOR IMEDIATO' ELSE SUPERV.NOMFUN END AS NOME_SUPERVISOR
                        ,R066APU.HORDAT 
                        ,R004HOR.DESHOR
                        ,CASE WHEN R066APU.HORDAT IN (9999,9996) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FOLGA
                        ,CASE WHEN R066APU.HORDAT IN (9997) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FERIADO
                        ,R034FUN.CODESC AS CODIGO_ESCALA_CADASTRO
                        ,R006ESC.NOMESC AS ESCALA_CADASTRO
                        ,R066APU.CODESC AS COD_ESCALA_APURACAO
                        ,ESCALA_TROCA.NOMESC AS ESCALA_TROCA
                        ,(ESCALA_TROCA.HORSEM/5) AS JORNADA_DIA
                        ,CASE WHEN R034FUN.CODESC <> R066APU.CODESC THEN 1 ELSE 0 END FLG_TROCA_ESCALA
                        ,R066APU.DATAPU
                        ,TO_CHAR(R066APU.DATAPU, 'YYYY-MM-DD') AS DATAPU_CONVERT
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
                        ,NVL(SITUACAO.HORAS_EXTRAS_50,0) AS HORAS_EXTRAS_50
                        ,CASE WHEN {$tipoApuracao} = 15 THEN SITUACAO.QTD_HORAS_EXTRAS_50 ELSE NULL END AS QTD_HORAS_EXTRAS_50
                        ,NVL(SITUACAO.HORAS_EXTRAS_100,0) AS HORAS_EXTRAS_100
                        ,CASE WHEN {$tipoApuracao} = 16 THEN SITUACAO.QTD_HORAS_EXTRAS_100 ELSE NULL END AS QTD_HORAS_EXTRAS_100
                        ,CASE WHEN {$tipoApuracao} = 5 THEN SITUACAO.QTD_HORAS_ADICIONAL_NOTURNO ELSE NULL END AS QTD_HORAS_ADICIONAL_NOTURNO
                        ,COMPENSACAO.DESSIT AS TIPO_COMPENSACAO
                        ,'{$columnTipoApuracao}' AS TIPO_APURACAO
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
                    LEFT JOIN VETORH.R010SIT SITUACAO_COLABORADOR ON SITUACAO_COLABORADOR.CODSIT = R034FUN.SITAFA
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
                            ,SUM(CASE WHEN R066SIT.CODSIT IN (50) THEN R066SIT.QTDHOR ELSE 0 END) QTD_HORAS_ADICIONAL_NOTURNO
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (301,302) THEN 1 ELSE 0 END) HORAS_EXTRAS_50
                            ,SUM(CASE WHEN R066SIT.CODSIT IN (301,302) THEN R066SIT.QTDHOR ELSE 0 END) QTD_HORAS_EXTRAS_50
                            ,MAX(CASE WHEN R066SIT.CODSIT IN (303,304) THEN 1 ELSE 0 END) HORAS_EXTRAS_100
                            ,SUM(CASE WHEN R066SIT.CODSIT IN (303,304) THEN R066SIT.QTDHOR ELSE 0 END) QTD_HORAS_EXTRAS_100
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
                    LEFT JOIN (
                        SELECT 
                            R064CMP.NUMEMP
                            ,R064CMP.TIPCOL
                            ,R064CMP.NUMCAD
                            ,R064CMP.DATINI 
                            ,R064CMP.DATFIM
                            ,R010SIT.DESSIT
                        FROM VETORH.R064CMP
                        INNER JOIN VETORH.R010SIT ON  R010SIT.CODSIT = R064CMP.CODSIT
                    ) COMPENSACAO ON COMPENSACAO.NUMEMP = R066APU.NUMEMP AND COMPENSACAO.TIPCOL = R066APU.TIPCOL AND COMPENSACAO.NUMCAD = R066APU.NUMCAD AND COMPENSACAO.DATINI >= R066APU.DATAPU AND COMPENSACAO.DATFIM <= R066APU.DATAPU
                WHERE R066APU.NUMEMP = 5
                {$wheresInternos}
                ) dadosApuracao
                WHERE 1 = 1
                {$wheresExternos}";  
    }

    public function getBancoHorasColaboradores($dataInicial = null, $dataFinal = null, $codColaborador = null, $codSupervisor = null, $codCentroCusto = null, $codFilial = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($dataInicial)) {
            $dataFinal = !empty($dataFinal) ? $dataFinal : date('Y-m-d');
            $wheresExternos .= " AND DADOS.CMPLAN BETWEEN TO_DATE('{$dataInicial}', 'YYYY-MM-DD') AND TO_DATE('{$dataFinal}', 'YYYY-MM-DD')";
            $wheresInternos .= " AND CASE WHEN R010SIT.TIPSIT = 7 AND R034FUN.DATAFA < R011LAN.DATLAN THEN 1 ELSE 0 END = 0"; // não considerar saldos de colaboradores demitidos até a data de lançamento.
        }
        
        return "SELECT 
                     DADOS.NUMEMP
                    ,DADOS.NOMEMP
                    ,DADOS.CODFIL
                    ,DADOS.NOMFIL
                    ,DADOS.CODCCU
                    ,DADOS.TIPCOL
                    ,DADOS.NUMCAD
                    ,DADOS.NUMCRA 
                    ,DADOS.NOMFUN 
                    ,DADOS.DATADM 
                    ,DADOS.CODCAR
                    ,DADOS.TITCAR
                    ,DADOS.TABORG 
                    ,DADOS.NUMLOC 
                    ,DADOS.NOMLOC 
                    ,DADOS.USU_NUMCAD
                    ,DADOS.NOME_SUPERVISOR
                    ,(
                        SELECT COUNT(*) 
                        FROM (
                        SELECT SYSDATE + LEVEL - 1 AS DATA
                        FROM DUAL
                        CONNECT BY SYSDATE + LEVEL - 1 <= DADOS.DATCMP
                        )
                        WHERE TO_CHAR(DATA, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                    ) AS DIAS_UTEIS_FECHAMENTO_PONTO
                    ,DADOS.MES_REFERENCIA
                    ,DADOS.ANO_REFERENCIA
                    ,DADOS.CMPLAN
                    ,SUM(DADOS.SALDO) AS SALDO
                    ,CASE WHEN SUM(DADOS.SALDO) < 0 THEN '-' || TO_CHAR(FLOOR(ABS(SUM(DADOS.SALDO)) / 60), 'FM99999990') || ':' || TO_CHAR(MOD(ABS(SUM(DADOS.SALDO)), 60), 'FM00') || ':00'
                    ELSE TO_CHAR(FLOOR(SUM(DADOS.SALDO) / 60), 'FM99999990') || ':' || TO_CHAR(MOD(SUM(DADOS.SALDO), 60), 'FM00') || ':00' END AS SALDO_FORMAT
                FROM (
                    SELECT 
                         R011LAN.NUMEMP
                        ,R030EMP.NOMEMP
                        ,R011LAN.TIPCOL
                        ,R011LAN.NUMCAD
                        ,R034FUN.CODFIL
                        ,R030FIL.NOMFIL
                        ,R034FUN.CODCCU
                        ,R034FUN.NUMCRA 
                        ,R034FUN.NOMFUN 
                        ,R034FUN.DATADM 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC 
                        ,R034FUN.DATAFA
                        ,R016ORN.NOMLOC 
                        ,R034CPL.USU_NUMCAD
                        ,SUPERV.NOMFUN AS NOME_SUPERVISOR
                        ,R011LAN.DATCMP
                        ,R011LAN.CODBHR
                        ,R011BHR.DESBHR
                        ,R011LAN.CODSIT
                        ,R011LAN.SINLAN
                        ,R011LAN.DATLAN
                        ,R011LAN.CMPLAN
                        ,TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'MM')) AS MES_REFERENCIA
                        ,TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'YYYY')) AS ANO_REFERENCIA
                        ,SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END) 
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS SALDO
                        ,ROW_NUMBER() OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, TO_CHAR(R011LAN.CMPLAN, 'MM'), TO_CHAR(R011LAN.CMPLAN, 'YYYY') ORDER BY R011LAN.DATLAN DESC) AS RN
                    FROM VETORH.R011LAN
                    LEFT JOIN VETORH.R034FUN ON R034FUN.NUMEMP = R011LAN.NUMEMP AND R034FUN.TIPCOL = R011LAN.TIPCOL AND R034FUN.NUMCAD = R011LAN.NUMCAD
                    LEFT JOIN VETORH.R034CPL ON R034CPL.NUMEMP = R034FUN.NUMEMP AND R034CPL.TIPCOL = R034FUN.TIPCOL AND R034CPL.NUMCAD = R034FUN.NUMCAD 
                    LEFT JOIN VETORH.R030EMP ON R030EMP.NUMEMP = R011LAN.NUMEMP 
                    LEFT JOIN VETORH.R034FUN SUPERV ON SUPERV.NUMEMP = R034CPL.USU_NUMEMP AND SUPERV.TIPCOL = R034CPL.USU_TIPCOL AND SUPERV.NUMCAD = R034CPL.USU_NUMCAD
                    LEFT JOIN VETORH.R011BHR ON R011BHR.CODBHR = R011LAN.CODBHR 
                    LEFT JOIN VETORH.R010SIT ON R010SIT.CODSIT = R011LAN.CODSIT 
                    LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR   
                    LEFT JOIN VETORH.R016ORN ON R016ORN.TABORG = R034FUN.TABORG AND R016ORN.NUMLOC = R034FUN.NUMLOC
                    LEFT JOIN VETORH.R030FIL ON R030FIL.CODFIL = R034FUN.CODFIL AND R030FIL.NUMEMP = R011LAN.NUMEMP
                    WHERE (R011LAN.PERREF NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')) OR R011LAN.CMPLAN NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')))
                    AND TO_NUMBER(TO_CHAR(DATLAN, 'YYYY')) >= 2023
                    AND R011LAN.ORILAN in ('A', 'D', 'B')
                    {$wheresInternos}
                    GROUP BY R011LAN.NUMEMP,R030EMP.NOMEMP,R011LAN.TIPCOL,R011LAN.NUMCAD,R034FUN.CODFIL,R030FIL.NOMFIL,R034FUN.CODCCU,R034FUN.NUMCRA,R034FUN.NOMFUN,R034FUN.DATADM,R034FUN.CODCAR,R024CAR.TITCAR,R034FUN.TABORG,R034FUN.NUMLOC, R034FUN.DATAFA,R016ORN.NOMLOC,R034CPL.USU_NUMCAD,SUPERV.NOMFUN,R011LAN.DATCMP
                            ,R011LAN.CODBHR,R011BHR.DESBHR,R011LAN.CODSIT,R011LAN.SINLAN,R011LAN.DATLAN,R011LAN.CMPLAN,R011LAN.QTDHOR
                    ORDER BY R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, R011LAN.DATLAN
                ) DADOS
                WHERE DADOS.RN = 1
                {$wheresExternos}
                GROUP BY NUMEMP,NOMEMP,CODFIL, NOMFIL, CODCCU, TIPCOL,NUMCAD,NUMCRA,NOMFUN,DATADM,CODCAR,TITCAR,TABORG,NUMLOC,NOMLOC,USU_NUMCAD,NOME_SUPERVISOR,DATCMP,MES_REFERENCIA,ANO_REFERENCIA,DADOS.CMPLAN
                ORDER BY DADOS.NUMCAD, DADOS.ANO_REFERENCIA, DADOS.MES_REFERENCIA ASC ";  
    }



    #region Informações do DashboardRecursosHumanos1
    public function getInformacoesCards($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codLocal = null, $codCargo = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $wheresInternos .= " AND R066APU.PERREF = TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD')";
            $wheresInternos .= " AND CASE WHEN SITUACAO_COLABORADOR.TIPSIT = 7 AND R034FUN.DATAFA < R066APU.DATAPU THEN 1 ELSE 0 END = 0"; // não listar usuários demitidos DE ACORDO COM A DATA APURAÇÃO
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R066APU.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codLocal)) {
            $codLocalStr = implode(',', $codLocal);
            $wheresInternos .= " AND R034FUN.NUMLOC IN ({$codLocalStr})";
        }
        if (!empty($codCargo)) {
            $codCargoStr = implode(',', $codCargo);
            $wheresInternos .= " AND R034FUN.CODCAR IN ({$codCargoStr})";
        }

        
        return "SELECT
                     MIN(DATAPU) AS MIN_DATAPU
                    ,MAX(DATAPU) AS MAX_DATAPU
                    ,COUNT(DISTINCT dadosApuracao.NUMCAD) AS QTD_FUNCIONARIO
                    ,COUNT(DISTINCT dadosApuracao.NUMCAD_AFASTADO) AS QTD_AFASTADOS
                    ,COUNT(DISTINCT dadosApuracao.NUMCAD_ADMITIDOS) AS QTD_ADMITIDO
                    ,COUNT(DISTINCT dadosApuracao.NUMCAD_DEMITIDO) AS QTD_DEMITIDO
                    ,SUM(dadosApuracao.FALTA) AS QTD_FALTAS
                    ,SUM(dadosApuracao.INTRAJORNADA) AS QTD_INTRAJORNADA
                    ,SUM(dadosApuracao.INTERJORNADA) AS QTD_INTERJORNADA
                    ,SUM(dadosApuracao.HORAS_EXTRAS_2HRS) AS QTD_HORAS_EXTRAS_2HRS
                    ,SUM(dadosApuracao.ADICIONAL_NOTURNO) AS QTD_ADICIONAL_NOTURNO
                    ,ROUND(AVG(dadosApuracao.VALSAL), 2) AS MEDIA_SALARIAL
                    ,case when COUNT(DISTINCT dadosApuracao.NUMCAD)*100 > 0 
                            then ROUND(((COUNT(DISTINCT dadosApuracao.NUMCAD_ADMITIDOS) + COUNT(DISTINCT dadosApuracao.NUMCAD_DEMITIDO))/2)/COUNT(DISTINCT dadosApuracao.NUMCAD)*100, 2) 
                            else 0 end as TURNOVER_PERC
                FROM (
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
                        ,CASE WHEN R034FUN.VALSAL > 0 THEN R034FUN.VALSAL ELSE NULL END AS VALSAL
                        ,R034FUN.SITAFA
                        ,CASE WHEN R034FUN.DATAFA = TO_DATE('31/12/1900', 'DD/MM/YYYY') THEN NULL ELSE R034FUN.DATAFA END DATAFA
                        ,SITUACAO_COLABORADOR.DESSIT AS DSC_SITUACAO_COLABORADOR
                        ,R034FUN.CODCCU 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC
                        ,upper(R016ORN.NOMLOC) as NOMLOC
                        ,R034CPL.USU_NUMCAD
                        ,CASE WHEN SUPERV.NOMFUN IS NULL THEN 'SEM SUPERVISOR IMEDIATO' ELSE SUPERV.NOMFUN END AS NOME_SUPERVISOR
                        ,R066APU.HORDAT 
                        ,R004HOR.DESHOR
                        ,CASE WHEN R066APU.HORDAT IN (9999,9996) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FOLGA
                        ,CASE WHEN R066APU.HORDAT IN (9997) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FERIADO
                        ,R034FUN.CODESC AS CODIGO_ESCALA_CADASTRO
                        ,R006ESC.NOMESC AS ESCALA_CADASTRO
                        ,R066APU.CODESC AS COD_ESCALA_APURACAO
                        ,ESCALA_TROCA.NOMESC AS ESCALA_TROCA
                        ,(ESCALA_TROCA.HORSEM/5) AS JORNADA_DIA
                        ,CASE WHEN R034FUN.CODESC <> R066APU.CODESC THEN 1 ELSE 0 END FLG_TROCA_ESCALA
                        ,R066APU.DATAPU
                        ,TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AS MES_REFERENCIA_APU
                        ,TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY')) AS ANO_REFERENCIA_APU
                        ,TO_CHAR(R066APU.DATAPU, 'YYYY-MM-DD') AS DATAPU_CONVERT
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 'Não Houve Marcações' ELSE MARCACAO.MARCACOES END MARCACOES
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 0 ELSE 1 END FLG_MARCACAO
                        ,CASE WHEN NVL(SITUACAO.FALTA,0) = 1 AND MARCACAO.MARCACOES IS NULL THEN 1 ELSE 0 END AS FALTA
                        ,CASE WHEN NVL(SITUACAO.INTRAJORNADA,0) = 1 THEN 1 ELSE 0 END AS INTRAJORNADA
                        ,CASE WHEN NVL(SITUACAO.INTERJORNADA,0) = 1 THEN 1 ELSE 0 END AS INTERJORNADA
                        ,CASE WHEN NVL(SITUACAO.HORAS_EXTRAS_2HRS,0) = 1 THEN 1 ELSE 0 END AS HORAS_EXTRAS_2HRS
                        ,CASE WHEN NVL(SITUACAO.ADICIONAL_NOTURNO,0) = 1 THEN 1 ELSE 0 END AS ADICIONAL_NOTURNO
                        ,NVL(JUSTIFICATIVAS.TREINAMENTO,0) AS TREINAMENTO
                        ,NVL(JUSTIFICATIVAS.SERVICO_EXTERNO,0) AS SERVICO_EXTERNO
                        ,NVL(JUSTIFICATIVAS.HOME_OFFICE,0) AS HOME_OFFICE
                        ,NVL(JUSTIFICATIVAS.REGISTRO_DUPLICADO,0) AS REGISTRO_DUPLICADO
                        ,NVL(JUSTIFICATIVAS.TESTE,0) AS TESTE
                        ,NVL(JUSTIFICATIVAS.FALHA_EQUIPAMENTO,0) AS FALHA_EQUIPAMENTO
                        ,NVL(JUSTIFICATIVAS.ESQUECIMENTO,0) AS ESQUECIMENTO
                        ,COMPENSACAO.DESSIT AS TIPO_COMPENSACAO
                        ,CASE WHEN R034FUN.SITAFA <> 7 AND R034FUN.DATAFA <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_AFASTADO
                        ,CASE WHEN R034FUN.DATADM <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATADM, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATADM, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_ADMITIDOS
                        ,CASE WHEN R034FUN.SITAFA = 7 AND R034FUN.DATAFA <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_DEMITIDO
                        ,CASE WHEN SITUACAO_COLABORADOR.TIPSIT = 7 AND R034FUN.DATAFA <= R066APU.DATAPU THEN 1 ELSE 0 END as FLG_DEMITIDOS
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
                    LEFT JOIN VETORH.R010SIT SITUACAO_COLABORADOR ON SITUACAO_COLABORADOR.CODSIT = R034FUN.SITAFA
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
                    LEFT JOIN (
                        SELECT 
                            R064CMP.NUMEMP
                            ,R064CMP.TIPCOL
                            ,R064CMP.NUMCAD
                            ,R064CMP.DATINI 
                            ,R064CMP.DATFIM
                            ,R010SIT.DESSIT
                        FROM VETORH.R064CMP
                        INNER JOIN VETORH.R010SIT ON  R010SIT.CODSIT = R064CMP.CODSIT
                    ) COMPENSACAO ON COMPENSACAO.NUMEMP = R066APU.NUMEMP AND COMPENSACAO.TIPCOL = R066APU.TIPCOL AND COMPENSACAO.NUMCAD = R066APU.NUMCAD AND COMPENSACAO.DATINI >= R066APU.DATAPU AND COMPENSACAO.DATFIM <= R066APU.DATAPU 
                    WHERE R066APU.NUMEMP = 5
                    {$wheresInternos}
                ) dadosApuracao
                WHERE 1 = 1";  
    }
    public function getInfoCardSaldoBancoHoras($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codLocal = null, $codCargo = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $apuracao_fim = !empty($apuracao_fim) ? $apuracao_fim : date('Y-m-d');
            $wheresExternos .= " AND DADOS.CMPLAN BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')";
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R011LAN.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codLocal)) {
            $codLocalStr = implode(',', $codLocal);
            $wheresInternos .= " AND R034FUN.NUMLOC IN ({$codLocalStr})";
        }
        if (!empty($codCargo)) {
            $codCargoStr = implode(',', $codCargo);
            $wheresInternos .= " AND R034FUN.CODCAR IN ({$codCargoStr})";
        }

        return "SELECT 
                    A.SALDO 
                    ,CASE WHEN A.SALDO < 0 THEN '-' || TO_CHAR(FLOOR(ABS(A.SALDO) / 60), 'FM99999990') || ':' || TO_CHAR(MOD(ABS(A.SALDO), 60), 'FM00') 
                    ELSE TO_CHAR(FLOOR(A.SALDO / 60), 'FM99999990') || ':' || TO_CHAR(MOD(A.SALDO, 60), 'FM00')  END AS SALDO_FORMAT
                FROM (
                    SELECT DADOS.CMPLAN ,SUM(DADOS.SALDO) AS SALDO FROM (
                        SELECT 
                            R011LAN.NUMEMP
                            ,R011LAN.TIPCOL
                            ,R011LAN.NUMCAD
                            ,R011LAN.DATCMP
                            ,R011LAN.CODBHR
                            ,R011BHR.DESBHR
                            ,R011LAN.CODSIT
                            ,R011LAN.SINLAN
                            ,R011LAN.DATLAN
                            ,R011LAN.CMPLAN
                            ,R011LAN.QTDHOR
                            ,TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'MM')) AS MES_REFERENCIA
                            ,TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'YYYY')) AS ANO_REFERENCIA
                            ,SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END) 
                            OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS SALDO
                            ,ROW_NUMBER() OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, TO_CHAR(R011LAN.CMPLAN, 'MM'), TO_CHAR(R011LAN.CMPLAN, 'YYYY') ORDER BY R011LAN.DATLAN DESC) AS RN
                        FROM VETORH.R011LAN
                        LEFT JOIN VETORH.R034FUN ON R034FUN.NUMEMP = R011LAN.NUMEMP AND R034FUN.TIPCOL = R011LAN.TIPCOL AND R034FUN.NUMCAD = R011LAN.NUMCAD
                        LEFT JOIN VETORH.R034CPL ON R034CPL.NUMEMP = R034FUN.NUMEMP AND R034CPL.TIPCOL = R034FUN.TIPCOL AND R034CPL.NUMCAD = R034FUN.NUMCAD 
                        LEFT JOIN VETORH.R010SIT ON R010SIT.CODSIT = R011LAN.CODSIT
                        LEFT JOIN VETORH.R011BHR ON R011BHR.CODBHR = R011LAN.CODBHR 
                        WHERE (R011LAN.PERREF NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')) OR 	R011LAN.CMPLAN NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')))
                        AND R011LAN.NUMEMP = 5
                        AND TO_NUMBER(TO_CHAR(R011LAN.DATLAN, 'YYYY')) >= 2023
                        AND R011LAN.ORILAN in ('A', 'D', 'B')
                        AND CASE WHEN R010SIT.TIPSIT = 7 AND R034FUN.DATAFA < R011LAN.DATLAN THEN 1 ELSE 0 END = 0
                        {$wheresInternos}
                        GROUP BY R011LAN.NUMEMP,R011LAN.TIPCOL,R011LAN.NUMCAD,R011LAN.DATCMP,R011LAN.CODBHR,R011BHR.DESBHR,R011LAN.CODSIT,R011LAN.SINLAN,R011LAN.DATLAN,R011LAN.CMPLAN,R011LAN.QTDHOR
                        ORDER BY R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, R011LAN.DATLAN
                    ) DADOS
                    WHERE DADOS.RN = 1
                    {$wheresExternos}
                    GROUP BY DADOS.CMPLAN
                    ORDER BY DADOS.CMPLAN DESC
                ) A
                WHERE ROWNUM = 1";  
    }


    public function getGraficoOcorrenciasDiaSemana($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codLocal = null, $codCargo = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $wheresInternos .= " AND R066APU.PERREF = TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD')";
            $wheresInternos .= " AND CASE WHEN SITUACAO_COLABORADOR.TIPSIT = 7 AND R034FUN.DATAFA < R066APU.DATAPU THEN 1 ELSE 0 END = 0"; // não listar usuários demitidos DE ACORDO COM A DATA APURAÇÃO
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R066APU.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codLocal)) {
            $codLocalStr = implode(',', $codLocal);
            $wheresInternos .= " AND R034FUN.NUMLOC IN ({$codLocalStr})";
        }
        if (!empty($codCargo)) {
            $codCargoStr = implode(',', $codCargo);
            $wheresInternos .= " AND R034FUN.CODCAR IN ({$codCargoStr})";
        }

        
        return "SELECT * FROM (
                SELECT 
                     trim(DIA_SEMANA_APU) DIA_SEMANA_APU
                    ,SUM(dadosApuracao.FALTA) AS QTD_FALTAS
                    ,SUM(dadosApuracao.INTRAJORNADA) AS QTD_INTRAJORNADA
                    ,SUM(dadosApuracao.INTERJORNADA) AS QTD_INTERJORNADA
                    ,SUM(dadosApuracao.HORAS_EXTRAS_2HRS) AS QTD_HORAS_EXTRAS_2HRS
                    ,SUM(dadosApuracao.ADICIONAL_NOTURNO) AS QTD_ADICIONAL_NOTURNO
                    ,(SUM(dadosApuracao.FALTA) + SUM(dadosApuracao.INTRAJORNADA) + SUM(dadosApuracao.INTERJORNADA) + SUM(dadosApuracao.HORAS_EXTRAS_2HRS) + SUM(dadosApuracao.ADICIONAL_NOTURNO)) as TOTAL
                FROM (
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
                        ,CASE WHEN R034FUN.VALSAL > 0 THEN R034FUN.VALSAL ELSE NULL END AS VALSAL
                        ,R034FUN.SITAFA
                        ,CASE WHEN R034FUN.DATAFA = TO_DATE('31/12/1900', 'DD/MM/YYYY') THEN NULL ELSE R034FUN.DATAFA END DATAFA
                        ,SITUACAO_COLABORADOR.DESSIT AS DSC_SITUACAO_COLABORADOR
                        ,R034FUN.CODCCU 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC
                        ,upper(R016ORN.NOMLOC) as NOMLOC
                        ,R034CPL.USU_NUMCAD
                        ,CASE 
                            WHEN SUPERV.NOMFUN IS NULL THEN 'SEM SUPERVISOR IMEDIATO' 
                            ELSE 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+') || ' ' || 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+\s+(\S+)', 1, 1, NULL, 1)
                        END AS NOME_SUPERVISOR
                        ,R066APU.HORDAT 
                        ,R004HOR.DESHOR
                        ,CASE WHEN R066APU.HORDAT IN (9999,9996) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FOLGA
                        ,CASE WHEN R066APU.HORDAT IN (9997) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FERIADO
                        ,R034FUN.CODESC AS CODIGO_ESCALA_CADASTRO
                        ,R006ESC.NOMESC AS ESCALA_CADASTRO
                        ,R066APU.CODESC AS COD_ESCALA_APURACAO
                        ,ESCALA_TROCA.NOMESC AS ESCALA_TROCA
                        ,(ESCALA_TROCA.HORSEM/5) AS JORNADA_DIA
                        ,CASE WHEN R034FUN.CODESC <> R066APU.CODESC THEN 1 ELSE 0 END FLG_TROCA_ESCALA
                        ,R066APU.DATAPU
                        ,TO_CHAR(R066APU.DATAPU, 'DAY') DIA_SEMANA_APU
                        ,TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AS MES_REFERENCIA_APU
                        ,TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY')) AS ANO_REFERENCIA_APU
                        ,TO_CHAR(R066APU.DATAPU, 'YYYY-MM-DD') AS DATAPU_CONVERT
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 'Não Houve Marcações' ELSE MARCACAO.MARCACOES END MARCACOES
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 0 ELSE 1 END FLG_MARCACAO
                        ,CASE WHEN NVL(SITUACAO.FALTA,0) = 1 AND MARCACAO.MARCACOES IS NULL THEN 1 ELSE 0 END AS FALTA
                        ,CASE WHEN NVL(SITUACAO.INTRAJORNADA,0) = 1 THEN 1 ELSE 0 END AS INTRAJORNADA
                        ,CASE WHEN NVL(SITUACAO.INTERJORNADA,0) = 1 THEN 1 ELSE 0 END AS INTERJORNADA
                        ,CASE WHEN NVL(SITUACAO.HORAS_EXTRAS_2HRS,0) = 1 THEN 1 ELSE 0 END AS HORAS_EXTRAS_2HRS
                        ,CASE WHEN NVL(SITUACAO.ADICIONAL_NOTURNO,0) = 1 THEN 1 ELSE 0 END AS ADICIONAL_NOTURNO
                        ,NVL(JUSTIFICATIVAS.TREINAMENTO,0) AS TREINAMENTO
                        ,NVL(JUSTIFICATIVAS.SERVICO_EXTERNO,0) AS SERVICO_EXTERNO
                        ,NVL(JUSTIFICATIVAS.HOME_OFFICE,0) AS HOME_OFFICE
                        ,NVL(JUSTIFICATIVAS.REGISTRO_DUPLICADO,0) AS REGISTRO_DUPLICADO
                        ,NVL(JUSTIFICATIVAS.TESTE,0) AS TESTE
                        ,NVL(JUSTIFICATIVAS.FALHA_EQUIPAMENTO,0) AS FALHA_EQUIPAMENTO
                        ,NVL(JUSTIFICATIVAS.ESQUECIMENTO,0) AS ESQUECIMENTO
                        ,COMPENSACAO.DESSIT AS TIPO_COMPENSACAO
                        ,CASE WHEN R034FUN.SITAFA <> 7 AND R034FUN.DATAFA <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_AFASTADO
                        ,CASE WHEN R034FUN.DATADM <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATADM, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATADM, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_ADMITIDOS
                        ,CASE WHEN R034FUN.SITAFA = 7 AND R034FUN.DATAFA <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_DEMITIDO
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
                    LEFT JOIN VETORH.R010SIT SITUACAO_COLABORADOR ON SITUACAO_COLABORADOR.CODSIT = R034FUN.SITAFA
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
                    LEFT JOIN (
                        SELECT 
                            R064CMP.NUMEMP
                            ,R064CMP.TIPCOL
                            ,R064CMP.NUMCAD
                            ,R064CMP.DATINI 
                            ,R064CMP.DATFIM
                            ,R010SIT.DESSIT
                        FROM VETORH.R064CMP
                        INNER JOIN VETORH.R010SIT ON  R010SIT.CODSIT = R064CMP.CODSIT
                    ) COMPENSACAO ON COMPENSACAO.NUMEMP = R066APU.NUMEMP AND COMPENSACAO.TIPCOL = R066APU.TIPCOL AND COMPENSACAO.NUMCAD = R066APU.NUMCAD AND COMPENSACAO.DATINI >= R066APU.DATAPU AND COMPENSACAO.DATFIM <= R066APU.DATAPU 
                    WHERE R066APU.NUMEMP = 5
                    {$wheresInternos}   
                ) dadosApuracao
                WHERE 1 = 1
                GROUP BY DIA_SEMANA_APU
                ) A 
                WHERE A.TOTAL > 0";  
    }
    public function getGraficoOcorrenciasSupervisor($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codLocal = null, $codCargo = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $wheresInternos .= " AND R066APU.PERREF = TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD')";
            $wheresInternos .= " AND CASE WHEN SITUACAO_COLABORADOR.TIPSIT = 7 AND R034FUN.DATAFA < R066APU.DATAPU THEN 1 ELSE 0 END = 0"; // não listar usuários demitidos DE ACORDO COM A DATA APURAÇÃO
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R066APU.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codLocal)) {
            $codLocalStr = implode(',', $codLocal);
            $wheresInternos .= " AND R034FUN.NUMLOC IN ({$codLocalStr})";
        }
        if (!empty($codCargo)) {
            $codCargoStr = implode(',', $codCargo);
            $wheresInternos .= " AND R034FUN.CODCAR IN ({$codCargoStr})";
        }

        
        return "SELECT * FROM (
                SELECT 
                     trim(NOME_SUPERVISOR) NOME_SUPERVISOR
                    ,COUNT(DISTINCT dadosApuracao.NUMCAD) AS QTD_FUNCIONARIO
                    ,SUM(dadosApuracao.FALTA) AS QTD_FALTAS
                    ,SUM(dadosApuracao.INTRAJORNADA) AS QTD_INTRAJORNADA
                    ,SUM(dadosApuracao.INTERJORNADA) AS QTD_INTERJORNADA
                    ,SUM(dadosApuracao.HORAS_EXTRAS_2HRS) AS QTD_HORAS_EXTRAS_2HRS
                    ,SUM(dadosApuracao.ADICIONAL_NOTURNO) AS QTD_ADICIONAL_NOTURNO
                    ,(SUM(dadosApuracao.FALTA) + SUM(dadosApuracao.INTRAJORNADA) + SUM(dadosApuracao.INTERJORNADA) + SUM(dadosApuracao.HORAS_EXTRAS_2HRS) + SUM(dadosApuracao.ADICIONAL_NOTURNO)) as TOTAL
                FROM (
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
                        ,CASE WHEN R034FUN.VALSAL > 0 THEN R034FUN.VALSAL ELSE NULL END AS VALSAL
                        ,R034FUN.SITAFA
                        ,CASE WHEN R034FUN.DATAFA = TO_DATE('31/12/1900', 'DD/MM/YYYY') THEN NULL ELSE R034FUN.DATAFA END DATAFA
                        ,SITUACAO_COLABORADOR.DESSIT AS DSC_SITUACAO_COLABORADOR
                        ,R034FUN.CODCCU 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC
                        ,upper(R016ORN.NOMLOC) as NOMLOC
                        ,R034CPL.USU_NUMCAD
                        ,CASE 
                            WHEN SUPERV.NOMFUN IS NULL THEN 'SEM SUPERVISOR IMEDIATO' 
                            ELSE 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+') || ' ' || 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+\s+(\S+)', 1, 1, NULL, 1)
                        END AS NOME_SUPERVISOR
                        ,R066APU.HORDAT 
                        ,R004HOR.DESHOR
                        ,CASE WHEN R066APU.HORDAT IN (9999,9996) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FOLGA
                        ,CASE WHEN R066APU.HORDAT IN (9997) AND MARCACAO.MARCACOES IS NOT NULL THEN 1 ELSE 0 END FLG_TRABALHOU_FERIADO
                        ,R034FUN.CODESC AS CODIGO_ESCALA_CADASTRO
                        ,R006ESC.NOMESC AS ESCALA_CADASTRO
                        ,R066APU.CODESC AS COD_ESCALA_APURACAO
                        ,ESCALA_TROCA.NOMESC AS ESCALA_TROCA
                        ,(ESCALA_TROCA.HORSEM/5) AS JORNADA_DIA
                        ,CASE WHEN R034FUN.CODESC <> R066APU.CODESC THEN 1 ELSE 0 END FLG_TROCA_ESCALA
                        ,R066APU.DATAPU
                        ,TO_CHAR(R066APU.DATAPU, 'DAY') DIA_SEMANA_APU
                        ,TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AS MES_REFERENCIA_APU
                        ,TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY')) AS ANO_REFERENCIA_APU
                        ,TO_CHAR(R066APU.DATAPU, 'YYYY-MM-DD') AS DATAPU_CONVERT
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 'Não Houve Marcações' ELSE MARCACAO.MARCACOES END MARCACOES
                        ,CASE WHEN MARCACAO.MARCACOES IS NULL THEN 0 ELSE 1 END FLG_MARCACAO
                        ,CASE WHEN NVL(SITUACAO.FALTA,0) = 1 AND MARCACAO.MARCACOES IS NULL THEN 1 ELSE 0 END AS FALTA
                        ,CASE WHEN NVL(SITUACAO.INTRAJORNADA,0) = 1 THEN 1 ELSE 0 END AS INTRAJORNADA
                        ,CASE WHEN NVL(SITUACAO.INTERJORNADA,0) = 1 THEN 1 ELSE 0 END AS INTERJORNADA
                        ,CASE WHEN NVL(SITUACAO.HORAS_EXTRAS_2HRS,0) = 1 THEN 1 ELSE 0 END AS HORAS_EXTRAS_2HRS
                        ,CASE WHEN NVL(SITUACAO.ADICIONAL_NOTURNO,0) = 1 THEN 1 ELSE 0 END AS ADICIONAL_NOTURNO
                        ,NVL(JUSTIFICATIVAS.TREINAMENTO,0) AS TREINAMENTO
                        ,NVL(JUSTIFICATIVAS.SERVICO_EXTERNO,0) AS SERVICO_EXTERNO
                        ,NVL(JUSTIFICATIVAS.HOME_OFFICE,0) AS HOME_OFFICE
                        ,NVL(JUSTIFICATIVAS.REGISTRO_DUPLICADO,0) AS REGISTRO_DUPLICADO
                        ,NVL(JUSTIFICATIVAS.TESTE,0) AS TESTE
                        ,NVL(JUSTIFICATIVAS.FALHA_EQUIPAMENTO,0) AS FALHA_EQUIPAMENTO
                        ,NVL(JUSTIFICATIVAS.ESQUECIMENTO,0) AS ESQUECIMENTO
                        ,COMPENSACAO.DESSIT AS TIPO_COMPENSACAO
                        ,CASE WHEN R034FUN.SITAFA <> 7 AND R034FUN.DATAFA <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_AFASTADO
                        ,CASE WHEN R034FUN.DATADM <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATADM, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATADM, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_ADMITIDOS
                        ,CASE WHEN R034FUN.SITAFA = 7 AND R034FUN.DATAFA <> TO_DATE('31/12/1900', 'DD/MM/YYYY') AND (TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'MM')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'MM')) AND TO_NUMBER(TO_CHAR(R034FUN.DATAFA, 'YYYY')) = TO_NUMBER(TO_CHAR(R066APU.DATAPU, 'YYYY'))) THEN R066APU.NUMCAD ELSE NULL END AS NUMCAD_DEMITIDO
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
                    LEFT JOIN VETORH.R010SIT SITUACAO_COLABORADOR ON SITUACAO_COLABORADOR.CODSIT = R034FUN.SITAFA
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
                    LEFT JOIN (
                        SELECT 
                            R064CMP.NUMEMP
                            ,R064CMP.TIPCOL
                            ,R064CMP.NUMCAD
                            ,R064CMP.DATINI 
                            ,R064CMP.DATFIM
                            ,R010SIT.DESSIT
                        FROM VETORH.R064CMP
                        INNER JOIN VETORH.R010SIT ON  R010SIT.CODSIT = R064CMP.CODSIT
                    ) COMPENSACAO ON COMPENSACAO.NUMEMP = R066APU.NUMEMP AND COMPENSACAO.TIPCOL = R066APU.TIPCOL AND COMPENSACAO.NUMCAD = R066APU.NUMCAD AND COMPENSACAO.DATINI >= R066APU.DATAPU AND COMPENSACAO.DATFIM <= R066APU.DATAPU 
                    WHERE R066APU.NUMEMP = 5
                    {$wheresInternos}   
                ) dadosApuracao
                WHERE 1 = 1
                GROUP BY NOME_SUPERVISOR
                ) A ";  
    }
    public function getInfoSaldoBancoHorasSupervisor($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codLocal = null, $codCargo = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $apuracao_fim = !empty($apuracao_fim) ? $apuracao_fim : date('Y-m-d');
            $wheresExternos .= " AND DADOS.CMPLAN BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')";
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R011LAN.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codLocal)) {
            $codLocalStr = implode(',', $codLocal);
            $wheresInternos .= " AND R034FUN.NUMLOC IN ({$codLocalStr})";
        }
        if (!empty($codCargo)) {
            $codCargoStr = implode(',', $codCargo);
            $wheresInternos .= " AND R034FUN.CODCAR IN ({$codCargoStr})";
        }

        return "SELECT * FROM (
                SELECT 
                    DADOS.NOME_SUPERVISOR
                    ,DADOS.CMPLAN
                    ,SUM(DADOS.SALDO) AS SALDO
                    ,COUNT(DISTINCT(DADOS.NUMCAD)) QTD_FUNCIONARIO
                    ,COUNT(DISTINCT(DADOS.QTD_NEGATIVO)) QTD_NEGATIVO
                    ,COUNT(DISTINCT(DADOS.QTD_POSITIVO)) QTD_POSITIVO
                    ,ROW_NUMBER() OVER (PARTITION BY DADOS.NOME_SUPERVISOR ORDER BY DADOS.CMPLAN DESC) RN2
                    ,CASE WHEN SUM(DADOS.SALDO) < 0 THEN '-' || TO_CHAR(FLOOR(ABS(SUM(DADOS.SALDO)) / 60), 'FM99999990') || ':' || TO_CHAR(MOD(ABS(SUM(DADOS.SALDO)), 60), 'FM00')
                    ELSE TO_CHAR(FLOOR(SUM(DADOS.SALDO) / 60), 'FM99999990') || ':' || TO_CHAR(MOD(SUM(DADOS.SALDO), 60), 'FM00') END AS SALDO_FORMAT
                FROM (
                    SELECT 
                        R011LAN.NUMEMP
                        ,R030EMP.NOMEMP
                        ,R011LAN.TIPCOL
                        ,R011LAN.NUMCAD
                        ,R034FUN.CODFIL
                        ,R030FIL.NOMFIL
                        ,R034FUN.CODCCU
                        ,R034FUN.NUMCRA 
                        ,R034FUN.NOMFUN 
                        ,R034FUN.DATADM 
                        ,R034FUN.CODCAR
                        ,R024CAR.TITCAR
                        ,R034FUN.TABORG 
                        ,R034FUN.NUMLOC 
                        ,R034FUN.DATAFA
                        ,R016ORN.NOMLOC 
                        ,R034CPL.USU_NUMCAD
                        ,CASE 
                            WHEN SUPERV.NOMFUN IS NULL THEN 'SEM SUPERVISOR IMEDIATO' 
                            ELSE 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+') || ' ' || 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+\s+(\S+)', 1, 1, NULL, 1)
                        END AS NOME_SUPERVISOR
                        ,R011LAN.DATCMP
                        ,R011LAN.CODBHR
                        ,R011BHR.DESBHR
                        ,R011LAN.CODSIT
                        ,R011LAN.SINLAN
                        ,R011LAN.DATLAN
                        ,R011LAN.CMPLAN
                        ,TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'MM')) AS MES_REFERENCIA
                        ,TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'YYYY')) AS ANO_REFERENCIA
                        ,SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END) 
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS SALDO
                        ,ROW_NUMBER() OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, TO_CHAR(R011LAN.CMPLAN, 'MM'), TO_CHAR(R011LAN.CMPLAN, 'YYYY') ORDER BY R011LAN.DATLAN DESC) AS RN
                        ,CASE WHEN SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END) 
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) < 0 THEN R011LAN.NUMCAD ELSE NULL END QTD_NEGATIVO
                        ,CASE WHEN SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END) 
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) > 0 THEN R011LAN.NUMCAD ELSE NULL END QTD_POSITIVO
                    FROM VETORH.R011LAN
                    LEFT JOIN VETORH.R034FUN ON R034FUN.NUMEMP = R011LAN.NUMEMP AND R034FUN.TIPCOL = R011LAN.TIPCOL AND R034FUN.NUMCAD = R011LAN.NUMCAD
                    LEFT JOIN VETORH.R034CPL ON R034CPL.NUMEMP = R034FUN.NUMEMP AND R034CPL.TIPCOL = R034FUN.TIPCOL AND R034CPL.NUMCAD = R034FUN.NUMCAD 
                    LEFT JOIN VETORH.R030EMP ON R030EMP.NUMEMP = R011LAN.NUMEMP 
                    LEFT JOIN VETORH.R034FUN SUPERV ON SUPERV.NUMEMP = R034CPL.USU_NUMEMP AND SUPERV.TIPCOL = R034CPL.USU_TIPCOL AND SUPERV.NUMCAD = R034CPL.USU_NUMCAD
                    LEFT JOIN VETORH.R011BHR ON R011BHR.CODBHR = R011LAN.CODBHR 
                    LEFT JOIN VETORH.R010SIT ON R010SIT.CODSIT = R011LAN.CODSIT 
                    LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR   
                    LEFT JOIN VETORH.R016ORN ON R016ORN.TABORG = R034FUN.TABORG AND R016ORN.NUMLOC = R034FUN.NUMLOC
                    LEFT JOIN VETORH.R030FIL ON R030FIL.CODFIL = R034FUN.CODFIL AND R030FIL.NUMEMP = R011LAN.NUMEMP
                    WHERE (R011LAN.PERREF NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')) OR R011LAN.CMPLAN NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')))
                    AND R011LAN.NUMEMP = 5
                    AND SUPERV.NUMEMP = 5
                    AND TO_NUMBER(TO_CHAR(DATLAN, 'YYYY')) >= 2023
                    AND R011LAN.ORILAN in ('A', 'D', 'B')
                    AND CASE WHEN R010SIT.TIPSIT = 7 AND R034FUN.DATAFA < R011LAN.DATLAN THEN 1 ELSE 0 END = 0
                    {$wheresInternos}
                    GROUP BY R011LAN.NUMEMP,R030EMP.NOMEMP,R011LAN.TIPCOL,R011LAN.NUMCAD,R034FUN.CODFIL,R030FIL.NOMFIL,R034FUN.CODCCU,R034FUN.NUMCRA,R034FUN.NOMFUN,R034FUN.DATADM,R034FUN.CODCAR,R024CAR.TITCAR,R034FUN.TABORG,R034FUN.NUMLOC, R034FUN.DATAFA,R016ORN.NOMLOC,R034CPL.USU_NUMCAD,SUPERV.NOMFUN,R011LAN.DATCMP
                            ,R011LAN.CODBHR,R011BHR.DESBHR,R011LAN.CODSIT,R011LAN.SINLAN,R011LAN.DATLAN,R011LAN.CMPLAN,R011LAN.QTDHOR
                    ORDER BY R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, R011LAN.DATLAN
                ) DADOS
                WHERE DADOS.RN = 1
                {$wheresExternos}
                GROUP BY DADOS.NOME_SUPERVISOR,DADOS.CMPLAN
                ORDER BY DADOS.NOME_SUPERVISOR, DADOS.CMPLAN DESC 
                ) A
                WHERE A.RN2 = 1";  
    }

    public function getInfoBalancoDeHorasSupervisor($apuracao_inicio = null, $apuracao_fim = null, $codColaborador = null, $codSupervisor = null, $codLocal = null, $codCargo = null)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        if (!empty($apuracao_inicio)) {
            $apuracao_fim = !empty($apuracao_fim) ? $apuracao_fim : date('Y-m-d');
            $wheresExternos .= " AND DADOS.CMPLAN BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')";
        }
        if (!empty($codColaborador)) {
            $wheresInternos .= " AND R011LAN.NUMCAD = {$codColaborador}";
        }
        if (!empty($codSupervisor)) {
            $wheresInternos .= " AND R034CPL.USU_NUMCAD = {$codSupervisor}";
        }
        if (!empty($codLocal)) {
            $codLocalStr = implode(',', $codLocal);
            $wheresInternos .= " AND R034FUN.NUMLOC IN ({$codLocalStr})";
        }
        if (!empty($codCargo)) {
            $codCargoStr = implode(',', $codCargo);
            $wheresInternos .= " AND R034FUN.CODCAR IN ({$codCargoStr})";
        }

        return "SELECT * FROM (
                SELECT 
                    DADOS.NOME_SUPERVISOR,
                    DADOS.CMPLAN,
                    
                    -- Somatório de saldo POSITIVO
                    SUM(CASE 
                        WHEN DADOS.SALDO > 0 THEN DADOS.SALDO 
                        ELSE null 
                    END) AS SALDO_POSITIVO,

                    -- Somatório de saldo NEGATIVO
                    SUM(CASE 
                        WHEN DADOS.SALDO < 0 THEN DADOS.SALDO 
                        ELSE null 
                    END) AS SALDO_NEGATIVO,
                    
                    COUNT(DISTINCT(DADOS.NUMCAD)) QTD_FUNCIONARIO,
                    
                    -- Para contar quantos funcionários estão negativos
                    COUNT(DISTINCT(DADOS.QTD_NEGATIVO)) QTD_FUNCIONARIO_NEGATIVO,

                    -- Para contar quantos funcionários estão positivos
                    COUNT(DISTINCT(DADOS.QTD_POSITIVO)) QTD_FUNCIONARIO_POSITIVO,

                    ROW_NUMBER() OVER (PARTITION BY DADOS.NOME_SUPERVISOR ORDER BY DADOS.CMPLAN DESC) RN2,

                    -- Formatação das horas POSITIVAS
                    CASE 
                        WHEN SUM(CASE WHEN DADOS.SALDO > 0 THEN DADOS.SALDO ELSE 0 END) = 0 THEN '00:00'
                        ELSE TO_CHAR(FLOOR(SUM(CASE WHEN DADOS.SALDO > 0 THEN DADOS.SALDO ELSE 0 END) / 60), 'FM99999990') || ':' ||
                            TO_CHAR(MOD(SUM(CASE WHEN DADOS.SALDO > 0 THEN DADOS.SALDO ELSE 0 END), 60), 'FM00')
                    END AS SALDO_POSITIVO_FORMAT,

                    -- Formatação das horas NEGATIVAS
                    CASE 
                        WHEN SUM(CASE WHEN DADOS.SALDO < 0 THEN ABS(DADOS.SALDO) ELSE 0 END) = 0 THEN '00:00'
                        ELSE '-' || TO_CHAR(FLOOR(SUM(CASE WHEN DADOS.SALDO < 0 THEN ABS(DADOS.SALDO) ELSE 0 END) / 60), 'FM99999990') || ':' ||
                            TO_CHAR(MOD(SUM(CASE WHEN DADOS.SALDO < 0 THEN ABS(DADOS.SALDO) ELSE 0 END), 60), 'FM00')
                    END AS SALDO_NEGATIVO_FORMAT

                FROM (
                    SELECT 
                        R011LAN.NUMEMP,
                        R030EMP.NOMEMP,
                        R011LAN.TIPCOL,
                        R011LAN.NUMCAD,
                        R034FUN.CODFIL,
                        R030FIL.NOMFIL,
                        R034FUN.CODCCU,
                        R034FUN.NUMCRA,
                        R034FUN.NOMFUN,
                        R034FUN.DATADM,
                        R034FUN.CODCAR,
                        R024CAR.TITCAR,
                        R034FUN.TABORG,
                        R034FUN.NUMLOC,
                        R034FUN.DATAFA,
                        R016ORN.NOMLOC,
                        R034CPL.USU_NUMCAD,
                        CASE 
                            WHEN SUPERV.NOMFUN IS NULL THEN 'SEM SUPERVISOR IMEDIATO'
                            ELSE 
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+') || ' ' ||
                                REGEXP_SUBSTR(SUPERV.NOMFUN, '^\S+\s+(\S+)', 1, 1, NULL, 1)
                        END AS NOME_SUPERVISOR,
                        R011LAN.DATCMP,
                        R011LAN.CODBHR,
                        R011BHR.DESBHR,
                        R011LAN.CODSIT,
                        R011LAN.SINLAN,
                        R011LAN.DATLAN,
                        R011LAN.CMPLAN,
                        TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'MM')) AS MES_REFERENCIA,
                        TO_NUMBER(TO_CHAR(R011LAN.CMPLAN, 'YYYY')) AS ANO_REFERENCIA,
                        SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END)
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD 
                            ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS SALDO,
                        ROW_NUMBER() OVER (
                            PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, 
                                        TO_CHAR(R011LAN.CMPLAN, 'MM'), TO_CHAR(R011LAN.CMPLAN, 'YYYY') 
                            ORDER BY R011LAN.DATLAN DESC
                        ) AS RN,
                        CASE WHEN SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END)
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD 
                            ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) < 0 THEN R011LAN.NUMCAD ELSE NULL END QTD_NEGATIVO,
                        CASE WHEN SUM(CASE WHEN R011LAN.SINLAN = '+' THEN R011LAN.QTDHOR WHEN R011LAN.SINLAN = '-' THEN -R011LAN.QTDHOR ELSE 0 END)
                        OVER (PARTITION BY R011LAN.DATCMP, R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD 
                            ORDER BY R011LAN.DATLAN ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) > 0 THEN R011LAN.NUMCAD ELSE NULL END QTD_POSITIVO
                    FROM VETORH.R011LAN
                    LEFT JOIN VETORH.R034FUN ON R034FUN.NUMEMP = R011LAN.NUMEMP AND R034FUN.TIPCOL = R011LAN.TIPCOL AND R034FUN.NUMCAD = R011LAN.NUMCAD
                    LEFT JOIN VETORH.R034CPL ON R034CPL.NUMEMP = R034FUN.NUMEMP AND R034CPL.TIPCOL = R034FUN.TIPCOL AND R034CPL.NUMCAD = R034FUN.NUMCAD
                    LEFT JOIN VETORH.R030EMP ON R030EMP.NUMEMP = R011LAN.NUMEMP
                    LEFT JOIN VETORH.R034FUN SUPERV ON SUPERV.NUMEMP = R034CPL.USU_NUMEMP AND SUPERV.TIPCOL = R034CPL.USU_TIPCOL AND SUPERV.NUMCAD = R034CPL.USU_NUMCAD
                    LEFT JOIN VETORH.R011BHR ON R011BHR.CODBHR = R011LAN.CODBHR
                    LEFT JOIN VETORH.R010SIT ON R010SIT.CODSIT = R011LAN.CODSIT
                    LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR
                    LEFT JOIN VETORH.R016ORN ON R016ORN.TABORG = R034FUN.TABORG AND R016ORN.NUMLOC = R034FUN.NUMLOC
                    LEFT JOIN VETORH.R030FIL ON R030FIL.CODFIL = R034FUN.CODFIL AND R030FIL.NUMEMP = R011LAN.NUMEMP
                    WHERE (R011LAN.PERREF NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')) OR R011LAN.CMPLAN NOT IN (TO_DATE('31/12/1900', 'DD/MM/YYYY')))
                    AND R011LAN.NUMEMP = 5
                    AND SUPERV.NUMEMP = 5
                    AND TO_NUMBER(TO_CHAR(DATLAN, 'YYYY')) >= 2023
                    AND R011LAN.ORILAN in ('A', 'D', 'B')
                    AND CASE WHEN R010SIT.TIPSIT = 7 AND R034FUN.DATAFA < R011LAN.DATLAN THEN 1 ELSE 0 END = 0
                    {$wheresInternos}
                    GROUP BY R011LAN.NUMEMP, R030EMP.NOMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, R034FUN.CODFIL, R030FIL.NOMFIL, 
                            R034FUN.CODCCU, R034FUN.NUMCRA, R034FUN.NOMFUN, R034FUN.DATADM, R034FUN.CODCAR, R024CAR.TITCAR,
                            R034FUN.TABORG, R034FUN.NUMLOC, R034FUN.DATAFA, R016ORN.NOMLOC, R034CPL.USU_NUMCAD, SUPERV.NOMFUN,
                            R011LAN.DATCMP, R011LAN.CODBHR, R011BHR.DESBHR, R011LAN.CODSIT, R011LAN.SINLAN, R011LAN.DATLAN,
                            R011LAN.CMPLAN, R011LAN.QTDHOR
                    ORDER BY R011LAN.CODBHR, R011LAN.NUMEMP, R011LAN.TIPCOL, R011LAN.NUMCAD, R011LAN.DATLAN
                ) DADOS
                WHERE DADOS.RN = 1
                {$wheresExternos}
                GROUP BY DADOS.NOME_SUPERVISOR, DADOS.CMPLAN
                ORDER BY DADOS.NOME_SUPERVISOR, DADOS.CMPLAN DESC
            ) A
            WHERE A.RN2 = 1";  
    }
    
    #endregion

    #region LOOKUPS FILTROS
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
                    -- LEFT JOIN VETORH.R034USU ON R034USU.NUMEMP = R034FUN.NUMEMP AND R034USU.NUMCAD = R034FUN.NUMCAD
                    -- LEFT JOIN VETORH.R910USU ON R910USU.CODENT = R034USU.CODUSU   
                    WHERE R034FUN.NUMEMP = 5
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
    public function getLookupLocalQuery()
    {
        return "SELECT  
                    A.ID
                    ,A.DSC 
                FROM (
                SELECT DISTINCT
                    R016ORN.NUMLOC AS ID
                    ,r016orn.TABORG 
                    ,R016ORN.NOMLOC
                    ,R016ORN.NUMLOC || ' - ' || UPPER(R016ORN.NOMLOC) AS DSC
                FROM VETORH.R034FUN
                LEFT JOIN VETORH.R016ORN ON R016ORN.TABORG = R034FUN.TABORG AND R016ORN.NUMLOC = R034FUN.NUMLOC
                WHERE R016ORN.TABORG = 3
                AND R034FUN.NUMEMP = 5
                ORDER BY R016ORN.NOMLOC
                ) A"; 
    }
    public function getLookupCargoQuery()
    {
        return "SELECT  
                    A.ID
                    ,A.DSC 
                FROM (
                SELECT DISTINCT
                    R034FUN.CODCAR AS id
                    ,R024CAR.TITCAR
                    ,R034FUN.CODCAR || ' - ' || R024CAR.TITCAR AS dsc
                FROM VETORH.R034FUN
                LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR
                WHERE R034FUN.NUMEMP = 5
                ORDER BY R024CAR.TITCAR
                ) A"; 
    }
    #endregion
}
