<?php

namespace Application\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;

class ContabilidadeRepository
{

    private $tableGateway;
    private $userMenuTableGateway;

    public function __construct(Adapter $adapter)
    {
        $this->tableGateway = new TableGateway('menu', $adapter);
        $this->userMenuTableGateway = new TableGateway('usuario_menu', $adapter);
        $this->adapter = $adapter;
    }

    public function getConferenciaEntradasCte($empresa = null, $filial = null, $dataInicio = null, $dataFim = null, $chaveNfe = null)
    {
        $ands = "";

        if (!empty($empresa)) {
            $ands .= " AND NFC.CODEMP = {$empresa}";
        }
        if (!empty($filial)) {
            $ands .= " AND NFC.CODFIL = {$filial}";
        }
        if (!empty($dataInicio) && !empty($dataFim)) {
            $ands .= " AND NFC.DATENT BETWEEN TO_DATE('{$dataInicio}', 'YYYY-MM-DD') AND TO_DATE('{$dataFim}', 'YYYY-MM-DD')";
        }
        if (!empty($chaveNfe)) {
            $ands .= " AND NFC.CHVNEL = '{$chaveNfe}'";
        }
        return "SELECT 
                    SDE.SITUACAO,
                    NFC.CODEMP As Empresa,
                    NFC.CodFil As Filial,
                    NFC.CHVNEL As ChaveNFE,
                    NFC.NUMNFC AS NumNF,
                    SDE.NUMERO_CTE,
                    TO_CHAR(NFC.DATENT, 'YYYY-MM-DD') As DtEntrada,
                    TO_CHAR(NFC.DatEmi, 'YYYY-MM-DD') As DtEmissao,
                    ISC.TNSSER As Trans_Serv,
                    SDE.UF_EMISSOR,
                    SDE.UF_TOMADOR,
                    SDE.SERIE_CTE,
                    NFC.CODSNF,
                    (NFC.CODFOR || ' - ' || EFOR.NOMFOR) As Fornec,
                    ISC.CODSER,
                    RTRIM(ISC.CPLISC) As DescServ,
                    ISC.BEMPRI,
                    CLF.CLAFIS As NCM,
                    RAT.CTARED As CtaRed, 
                    RAT.CODCCU As CodCCU_Rat,
                    ISC.CODCCU As CodCCU_Pro,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRBRU))) As VlrBru,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRDSC))) As VlrDsc,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRLIQ))) As VlrLiq,
                    (MAX(TO_NUMBER(TO_CHAR(ISC.VLRLIQ))) - (MAX(TO_NUMBER(TO_CHAR(ISC.VLRBRU))) - MAX(TO_NUMBER(TO_CHAR(ISC.VLRDSC))))) As VlrLiq_Dif,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRBIC))) As VlrBase_ICM,
                    MAX(TO_NUMBER(TO_CHAR(ISC.PERICM))) As PERICM,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRICM))) As VLRICM,
                    MAX(TO_NUMBER(TO_CHAR(ISC.BECICM))) As VlrBase_ICMCredEfet,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VECICM))) As VlrIMP_ICMCredEfet,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRIIC))) As VlrIsen_ICM,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLROIC))) As VlrOutr_ICM,
                    0 As VlrFrete,
                    ISC.CODSTR,
                    ISC.CSTPIS,
                    MAX(TO_NUMBER(TO_CHAR(ISC.PERPIR))) As PerPIS_Rec,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRBPI))) As VlrBase_PIS,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRPIS))) As VlrImp_PISRec,
                    ISC.CSTCOF,
                    MAX(TO_NUMBER(TO_CHAR(ISC.PERCOR))) As PerCOF_Rec,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRBCR))) As VlrImp_COFRec,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRCOR))) As VlrRet_COF,
                    MAX(TO_NUMBER(TO_CHAR(ISC.PERISS))) As PerISS,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRBIS))) As VlrBase_ISS,
                    MAX(TO_NUMBER(TO_CHAR(ISC.VLRISS))) As VlrIMP_ISS,
                    MAX(TO_NUMBER(TO_CHAR(ISC.PERINS))) AS PerINS,
                    TO_CHAR(NFC.DATGER, 'YYYY-MM-DD'),
                    NFC.USUGER,
                    (
                        CASE When NFC.SitNFC = '1' THEN 'Digitada' When NFC.SitNFC = '2' THEN 'Fechada' When NFC.SitNFC = '3' THEN 'Cancelada' When NFC.SitNFC = '4' THEN 'DocFisEmitida-Saida' 
                            When NFC.SitNFC = '5' THEN 'AguardandoFec' When NFC.SitNFC = '6' THEN 'AguardandoIntWMS'
                        ELSE 'DigitadaInt' END
                    ) As SitNFEntrada_Descr,
                    TO_CHAR(MIN(Venc01), 'YYYY-MM-DD') As Venc01,
                    MAX(Total_CP) As Total_CP,
                    (CASE WHEN CTE.FORRLC = 0 THEN 'Saída' WHEN CTE.FORRLC > 0 THEN 'Entrada' ELSE '' END) As CTE_TpOri,
                    (CASE WHEN CTE.FORRLC = 0 THEN CTE.CODCLI ELSE CTE.FORRLC END) As CTE_CLIFOR,
                    CTE.NUMRLC As CTE_NFOri,
                    CTE.SNFRLC As CTE_SerNF,
                    (CASE WHEN CTE.FORRLC = 0 THEN CTE.TNSPRO ELSE '' END) As CTE_TnsNFOri,
                    (SELECT NOPPRO FROM SAPIENS.E440NFC A WHERE A.NUMNFC = CTE.NUMRLC AND A.CODSNF = CTE.SNFRLC AND CODFOR = CTE.FORRLC) As CTE_CFOPCab_Ori,
                    SDE.CHAVE_CTE,
                    SDE.TOMADOR,
                    SDE.CNPJ_TOMADOR,
                    SDE.REMETENTE,
                    SDE.EMISSOR_CTE,
                    SDE.EMPRESA_TOMADOR,
                    SDE.FILIAL_TOMADOR,
                    -- Coluna 1: Validação UF/CFOP
                    CASE 
                        WHEN SDE.UF_EMISSOR = SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '1' THEN 'Certo'
                        WHEN SDE.UF_EMISSOR = SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '2' THEN 'Divergente'
                        WHEN SDE.UF_EMISSOR <> SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '2' THEN 'Certo'
                        WHEN SDE.UF_EMISSOR <> SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '1' THEN 'Divergente'
                        ELSE NULL
                    END AS VALIDACAO_UF_CFOP,
                    --Coluna 2: Lançamento Filial
                    CASE 
                        WHEN SDE.CNPJ_TOMADOR = '9022330000667' AND NFC.CODEMP = 5 AND NFC.CODFIL = 6 THEN 'Certo'
                        WHEN NFC.CODEMP = SDE.EMPRESA_TOMADOR AND NFC.CODFIL = SDE.FILIAL_TOMADOR THEN 'Certo'
                        ELSE 'Divergente'
                    END AS LANCAMENTO_FILIAL
                FROM SAPIENS.E440NFC NFC
                INNER JOIN SAPIENS.E440ISC ISC ON NFC.CODEMP = ISC.CODEMP AND NFC.CODFIL = ISC.CODFIL AND NFC.CODFOR = ISC.CODFOR AND NFC.NUMNFC = ISC.NUMNFC AND NFC.CODSNF = ISC.CODSNF
                LEFT JOIN SAPIENS.E022CLF CLF  ON ISC.CODCLF = CLF.CODCLF 
                INNER JOIN SAPIENS.E095FOR EFOR ON EFOR.CODFOR = NFC.CODFOR
                LEFT JOIN (
                    SELECT CODEMP,
                            CODFIL,
                            NUMNFC,
                            NUMTIT,
                            SNFNFC,
                            CODTPT,
                            CODFOR,
                            MIN(VCTPRO) Venc01,
                            SUM(VLRORI) Total_CP
                    FROM SAPIENS.E501TCP X
                    WHERE X.VCTPRO >= SYSDATE - 420 
                    group BY CODEMP,CODFIL,NUMNFC,NUMTIT,SNFNFC,CODTPT,CODFOR
                ) A ON A.CODEMP = NFC.CODEMP AND A.CODFIL = NFC.CODFIL AND A.NUMNFC = NFC.NUMNFC AND A.SNFNFC = NFC.CODSNF AND A.CODFOR = NFc.Codfor 
                LEFT JOIN SAPIENS.E440RAT RAT ON RAT.CODEMP = NFC.CODEMP AND RAT.CODFIL = NFC.CODFIL AND RAT.NUMNFC = NFC.NUMNFC AND RAT.CODSNF = NFC.CODSNF AND RAT.CODFOR = NFC.CODFOR AND RAT.SEQISC = ISC.SEQISC
                LEFT JOIN (
                    SELECT B.TNSPRO, B.CODCLI, A.*
                    FROM SAPIENS.E440EXF A
                    LEFT JOIN SAPIENS.E140NFV B
                        ON B.CODEMP = A.CODEMP
                        AND B.CODFIL = A.FILRLC
                        AND B.NUMNFV = A.NUMRLC
                        AND B.CODSNF = A.SNFRLC
                    WHERE 0=0 
                    AND A.CODSNF IN ('CTR', 'CTE')
                ) CTE ON CTE.CODEMP = NFC.CODEMP AND CTE.CODFIL = NFC.CODFIL AND CTE.NUMNFC = NFC.NUMNFC AND CTE.CODSNF IN ('CTR', 'CTE') AND CTE.CODFOR = NFC.CODFOR
                LEFT JOIN (
                    SELECT  
                    nc.NUMCTE AS Numero_cte,
                    nc.SERCTE AS Serie_cte,
                    nc.DATEMI AS Data_emissao,
                    -- UF de Início (baseado no endereço do emissor)
                    (
                        SELECT st.sigest 
                        FROM SDE.N100EST st
                        LEFT JOIN SDE.N100MUN mn ON st.seqest = mn.seqest
                        WHERE mn.seqmun = (
                        SELECT MAX(e.SEQMUN) 
                        FROM SDE.N100END e
                        WHERE e.SEQEND = (
                            SELECT p.SEQEND 
                            FROM SDE.N100PES p
                            WHERE p.SEQPES = nc.SEQEMI
                        )
                        )
                    ) AS UF_Emissor,
                    -- UF de Fim (baseado no tomador)
                    (
                        SELECT st.sigest 
                        FROM SDE.N100EST st
                        LEFT JOIN SDE.N100MUN mn ON st.seqest = mn.seqest
                        WHERE mn.seqmun = (
                        SELECT MAX(e.SEQMUN)
                        FROM SDE.N100END e
                        WHERE e.SEQEND = (
                            SELECT 
                            CASE 
                                WHEN nc.TIPTOM = 0 THEN (SELECT p.SEQEND FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQREM)
                                WHEN nc.TIPTOM = 3 THEN (SELECT p.SEQEND FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQDES)
                                WHEN nc.TIPTOM = 4 THEN (SELECT p.SEQEND FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQOUT)
                            END
                            FROM dual
                        )
                        )
                    ) AS UF_Tomador,
                    nc.SITCTE AS Situacao,
                    CAST(nc.CHVCTE AS VARCHAR(255)) AS Chave_cte,
                    -- Dados do Tomador
                    CASE 
                        WHEN nc.TIPTOM = 0 THEN (SELECT p.DSCNOM FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQREM)
                        WHEN nc.TIPTOM = 3 THEN (SELECT p.DSCNOM FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQDES)
                        WHEN nc.TIPTOM = 4 THEN (SELECT p.DSCNOM FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQOUT)
                    END AS Tomador,
                    CASE 
                    WHEN nc.TIPTOM = 0 THEN (SELECT CAST (p.NUMCNP AS VARCHAR(250)) FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQREM)
                    WHEN nc.TIPTOM = 3 THEN (SELECT CAST(p.NUMCNP AS VARCHAR(250)) FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQDES)
                    WHEN nc.TIPTOM = 4 THEN (SELECT CAST(p.NUMCNP AS VARCHAR(250)) FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQOUT)
                    END AS Cnpj_tomador,
                    (SELECT p.DSCNOM FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQREM) AS Remetente,
                    (SELECT p.DSCNOM FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQEMI) AS Emissor_cte,
                    ef.CODEMP AS Empresa_tomador,
                    ef.CODFIL AS Filial_tomador
                    FROM SDE.N150CTE nc
                    LEFT JOIN SAPIENS.E070FIL ef 
                    ON REGEXP_REPLACE(ef.INSEST, '[^0-9]', '') = 
                        CASE 
                        WHEN nc.TIPTOM = 0 THEN (SELECT REGEXP_REPLACE(p.NUMICE, '[^0-9]', '') FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQREM)
                        WHEN nc.TIPTOM = 3 THEN (SELECT REGEXP_REPLACE(p.NUMICE, '[^0-9]', '') FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQDES)
                        WHEN nc.TIPTOM = 4 THEN (SELECT REGEXP_REPLACE(p.NUMICE, '[^0-9]', '') FROM SDE.N100PES p WHERE p.SEQPES = nc.SEQOUT)
                        END
                    WHERE NC.DATEMI > TO_DATE('2025-01-01', 'yyyy-mm-dd')
                ) SDE ON SDE.CHAVE_CTE = NFC.CHVNEL
                WHERE  NFC.CODSNF = 'CTE'
                {$ands}
                GROUP BY SDE.SITUACAO,NFC.CODEMP,NFC.CodFil,NFC.DATENT,NFC.DatEmi,NFC.NUMNFC,SDE.NUMERO_CTE,NFC.CHVNEL,NFC.CODSNF,NFC.CODFOR,EFOR.NOMFOR,ISC.TNSSER,SDE.UF_EMISSOR,SDE.UF_TOMADOR,
                        SDE.SERIE_CTE,ISC.CODSER,ISC.CPLISC,ISC.SEQISC,ISC.CODSTR,ISC.CSTPIS,ISC.CSTCOF,CLF.CLAFIS,ISC.CODTRD,RAT.CTARED,RAT.CODCCU,ISC.CODCCU,ISC.BEMPRI,NFC.DATGER,NFC.USUGER,NFC.HORGER,
                        ISC.CSTIPI,NFC.SitNFC,CTE.CODCLI,CTE.FORRLC,CTE.NUMRLC,CTE.SNFRLC,CTE.TNSPRO,SDE.CHAVE_CTE,SDE.TOMADOR,SDE.CNPJ_TOMADOR,SDE.REMETENTE,SDE.EMISSOR_CTE,SDE.EMPRESA_TOMADOR,SDE.FILIAL_TOMADOR,
                        CASE 
                            WHEN SDE.UF_EMISSOR = SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '1' THEN 'Certo'
                            WHEN SDE.UF_EMISSOR = SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '2' THEN 'Divergente'
                            WHEN SDE.UF_EMISSOR <> SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '2' THEN 'Certo'
                            WHEN SDE.UF_EMISSOR <> SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '1' THEN 'Divergente'
                            ELSE NULL
                        END,
                        CASE 
                            WHEN SDE.CNPJ_TOMADOR = '9022330000667' AND NFC.CODEMP = 5 AND NFC.CODFIL = 6 THEN 'Certo'
                            WHEN NFC.CODEMP = SDE.EMPRESA_TOMADOR AND NFC.CODFIL = SDE.FILIAL_TOMADOR THEN 'Certo'
                            ELSE 'Divergente'
                        END
                ORDER BY CASE 
                        WHEN SDE.UF_EMISSOR = SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '1' THEN 'Certo'
                        WHEN SDE.UF_EMISSOR = SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '2' THEN 'Divergente'
                        WHEN SDE.UF_EMISSOR <> SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '2' THEN 'Certo'
                        WHEN SDE.UF_EMISSOR <> SDE.UF_TOMADOR AND SUBSTR(ISC.TNSSER, 1, 1) = '1' THEN 'Divergente'
                        ELSE NULL
                    END DESC,
                    CASE 
                        WHEN SDE.CNPJ_TOMADOR = '9022330000667' AND NFC.CODEMP = 5 AND NFC.CODFIL = 6 THEN 'Certo'
                        WHEN NFC.CODEMP = SDE.EMPRESA_TOMADOR AND NFC.CODFIL = SDE.FILIAL_TOMADOR THEN 'Certo'
                        ELSE 'Divergente'
                    END DESC
        ";
    }



}
