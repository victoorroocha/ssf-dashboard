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
                        WHEN SDE.CNPJ_TOMADOR = '9022330000667' AND NFC.CODEMP = 1000 AND NFC.CODFIL = 6 THEN 'Certo'
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
                            WHEN SDE.CNPJ_TOMADOR = '9022330000667' AND NFC.CODEMP = 1000 AND NFC.CODFIL = 6 THEN 'Certo'
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
                        WHEN SDE.CNPJ_TOMADOR = '9022330000667' AND NFC.CODEMP = 1000 AND NFC.CODFIL = 6 THEN 'Certo'
                        WHEN NFC.CODEMP = SDE.EMPRESA_TOMADOR AND NFC.CODFIL = SDE.FILIAL_TOMADOR THEN 'Certo'
                        ELSE 'Divergente'
                    END DESC
        ";
    }
    public function getConferenciaSaidasProduto($empresa = null, $filial = null, $dataInicio = null, $dataFim = null, $chaveNfe = null)
    {
        $ands = "";

        if (!empty($empresa)) {
            $ands .= " AND NFV.CODEMP = {$empresa}";
        }
        if (!empty($filial)) {
            $ands .= " AND NFV.CODFIL = {$filial}";
        }
        if (!empty($dataInicio) && !empty($dataFim)) {
            $ands .= " AND NFV.DATEMI BETWEEN TO_DATE('{$dataInicio}', 'YYYY-MM-DD') AND TO_DATE('{$dataFim}', 'YYYY-MM-DD')";
        }
        if (!empty($chaveNfe)) {
            $ands .= " AND IDE.CHVDOE = '{$chaveNfe}'";
        }
        return "SELECT 
                    NFV.CODEMP As Empresa,
                    NFV.CodFil As Filial,
                    IDE.CHVDOE As ChaveNFE,
                    NFV.NUMNFV AS NumNF,
                    NFV.CODSNF,
                    NFV.DatEmi As DtEmissao,
                    IPV.TNSPRO As Trans_Prod,
                    (NFV.CODCLI || ' - ' || CLI.APECLI) As Cliente,
                    IPV.CODPRO,
                    RTRIM(IPV.CPLIPV) As DescProd,
                    TO_CHAR(CLF.CLAFIS) As NCM,
                    IPV.CODTRD As CodRed_Z,
                    IPV.CTARED As CtaRed,
                    IPV.CODCCU As CodCCU,
                    CODBNF As cBenef,
                    IPV.SEQIPV,
                    MAX(TO_NUMBER(TO_CHAR(IPV.QTDFAT))) As QtdFat,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBRU))) As VlrBru,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRDSC))) As VlrDsc,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRLIQ))) As VlrLiq,
                    (MAX(TO_NUMBER(TO_CHAR(IPV.VLRLIQ))) -
                    (MAX(TO_NUMBER(TO_CHAR(IPV.VLRBRU))) -
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRDSC))))) As VlrLiq_Dif,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBIC))) As VlrBase_ICM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERICM))) As PERICM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRICM))) As VLRICM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRIIC))) As VlrIsen_ICM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLROIC))) As VlrOutr_ICM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRICD))) As VlrDeson_ICM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERFUN))) As PERFUN,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBFU))) As VlrBase_FUNR,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRFUN))) As VlrFunrural,
                    COALESCE(MAX(to_number(to_char(IPV.USU_VLRFICS+IPV.USU_VLRFDI))), 0) As FundeINFRA,
                    COALESCE(MAX(to_number(to_char(FIC.USU_PERFICS))), 0) As PerFInfPARAM,
                    MAX(TO_NUMBER(TO_CHAR(IPV.USU_VLRFAC))) As VlrFACS,
                    MAX(TO_NUMBER(TO_CHAR(IPV.USU_VLRFETHAB))) As VlrFETHab,
                    IPV.CODSTR As Sit_Trib,
                    IPV.CSTPIS,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERPIF))) As PerPIS_Fat,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBPF))) As VlrBase_PIS,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRPIF))) As VlrImp_PISFat,
                    IPV.CSTCOF,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERCFF))) As PerCOF_Fat,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBCF))) As VlrImp_COFFat,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRCFF))) As VlrRet_COF,
                    0 As PerISS,
                    0 As VlrBase_ISS,
                    0 As VlrIMP_ISS,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERIRF))) As PerIRRF,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBIR))) As VlrBase_IRF,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRIRF))) As VlrIMP_IRF,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERCRT))) As PerFat_COF,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBCT))) AS VlrBase_COFRet,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRCRT))) AS VlrIMP_COFRet,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERPIT))) As PerFat_PIS,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBPT))) AS VlrBase_PISRet,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRPIT))) AS VlrIMP_PISRet,
                    MAX(TO_NUMBER(TO_CHAR(IPV.PERCSL))) As PerFat_CSLL,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBCL))) AS VlrBase_CSLLRet,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRCSL))) AS VlrIMP_CSLLRet,
                    IPV.CSTIPI,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRBIP))) As VlrBase_IPI,
                    MAX(TO_NUMBER(TO_CHAR(IPV.VLRIPI))) As VlrIMP_IPI,
                    IPV.OBSIPV As Obs_Prod,
                    ClaPro,
                    (CASE WHEN EPRO.ClaPRO = '1' THEN 'De Estoque'
                        WHEN EPRO.ClaPRO = '2' THEN 'De Passagem Direta'
                        WHEN EPRO.ClaPRO = '3' THEN 'Imobilizado'
                        ELSE 'Outros'
                    END) As ClaPro_Desc,
                    NFV.SitNFV As SitNFSaida,
                    (CASE When SitNFV = '1' then
                        'Digitada'
                        When SitNFV = '2' then
                        'Fechada'
                        When SitNFV = '3' then
                        'Cancelada'
                        When SitNFV = '4' then
                        'DocFisEmitida-Saida'
                        When SitNFV = '5' then
                        'AguardandoFechamento_pós-Saida'
                        When SitNFV = '6' then
                        'AguardandoIntWMS'
                        Else
                        'DigitadaInt'
                    End) As SitNFSaida_Descr,
                    MIN(A.VENC01_CR) As Venc01_CR,
                    MAX(A.TOTAL_CR) As Total_CR,
                    MAX(A.QNTPARC_CR) As QntParc_CR,
                    NFV.DATGER,
                    NFV.USUGER,
                    NFV.HORGER,
                    MAX(MNV.MSGNFV) As Mensagem_1,
                    MAX(NFV.OBSMOT) As Obs_Motivo,
                    MAX(MENS.MENS1) As Mens1,
                    MAX(MENS.MENS2) As Mens2,
                    MAX(MENS.MENS3) As Mens3,
                    MAX(MENS.MENS4) As Mens4,
                    MAX(MENS.MENS5) As Mens5,
                    MAX(MENS.MENS6) As Mens6,
                    MAX(MENS.MENS7) As Mens7,
                    MAX(MENS.MENS8) As Mens8,
                    MAX(MENS.MENS9) As Mens9
                FROM SAPIENS.E140NFV NFV
                INNER JOIN SAPIENS.E140IPV IPV ON NFV.CODEMP = IPV.CODEMP AND NFV.CODFIL = IPV.CODFIL AND NFV.CODSNF = IPV.CODSNF AND NFV.NUMNFV = IPV.NUMNFV AND NFV.CODSNF = IPV.CODSNF
                INNER JOIN SAPIENS.E140IDE IDE ON NFV.CODEMP = IDE.CODEMP AND NFV.CODFIL = IDE.CODFIL AND NFV.CODSNF = IDE.CODSNF AND NFV.NUMNFV = IDE.NUMNFV 
                LEFT JOIN SAPIENS.E022CLF CLF ON IPV.CODCLF = CLF.CODCLF 
                INNER JOIN SAPIENS.E085CLI CLI	ON CLI.CODCLI = NFV.CODCLI
                INNER JOIN SAPIENS.E075PRO EPRO ON EPRO.CODPRO = IPV.CODPRO
                LEFT JOIN SAPIENS.E140MNV MNV ON MNV.CODEMP = NFV.CODEMP AND MNV.CODFIL = NFV.CODFIL AND MNV.NUMNFV = NFV.NUMNFV AND MNV.CODSNF = NFV.CODSNF
                LEFT JOIN (
                    SELECT CODEMP,
                            CODFIL,
                            NUMNFV,
                            CODSNF,
                            CODTPT,
                            MIN(VCTPAR) Venc01_CR,
                            SUM(VLRPAR) Total_CR,
                            COUNT(CODPAR) QntParc_CR
                    FROM SAPIENS.E140PAR X
                    WHERE X.VCTPAR >= SYSDATE - 420 
                    group BY CODEMP, CODFIL, NUMNFV, CODSNF, CODTPT
                ) A ON A.CODEMP = NFV.CODEMP AND A.CODFIL = NFV.CODFIL AND A.NUMNFV = NFV.NUMNFV AND A.CODSNF = NFV.CODSNF
                LEFT JOIN SAPIENS.USU_T049FICS FIC ON (FIC.USU_CODEMP = IPV.CODEMP AND FIC.USU_CODFIL = IPV.CODFIL AND FIC.USU_CODPRO = IPV.CODPRO) OR (FIC.USU_CODEMP = IPV.CODEMP AND FIC.USU_CODFIL = IPV.CODFIL AND 	FIC.USU_CODFAM = SUBSTR(IPV.CODPRO, 1, 6) AND FIC.USU_CODPRO = ' ')
                LEFT JOIN (
                    SELECT *
                    FROM (
                    SELECT CODEMP,
                            CODFIL,
                            CODSNF,
                            NUMNFV,
                            ('MENS' ||
                            TO_CHAR(ROW_NUMBER() OVER(PARTITION BY CODEMP || CODFIL || CODSNF || NUMNFV ORDER BY SEQOBS))) HANK,
                            A.USU_DESMSG
                    FROM ( 
                            SELECT *
                            FROM SAPIENS.E140OBS A
                            WHERE DATGER >= SYSDATE - 420
                            ORDER BY CODEMP, CODFIL, CODSNF, numnfv, seqobs
                    ) A) B
                    PIVOT(MAX(USU_DESMSG)
                    FOR HANK IN('MENS1' Mens1,
                            'MENS2' Mens2,
                            'MENS3' Mens3,
                            'MENS4' Mens4,
                            'MENS5' Mens5,
                            'MENS6' Mens6,
                            'MENS7' Mens7,
                            'MENS8' Mens8,
                            'MENS9' Mens9))
                ) MENS ON MENS.CODEMP = NFV.CODEMP AND MENS.CODFIL = NFV.CODFIL AND MENS.NUMNFV = NFV.NUMNFV AND MENS.CODSNF = NFV.CODSNF
                WHERE 0=0 
                {$ands}
                GROUP BY NFV.CODEMP,
                        NFV.CodFil,
                        NFV.DatEmi,
                        IPV.TNSPRO,
                        NFV.NUMNFV,
                        IDE.CHVDOE,
                        NFV.CODSNF,
                        NFV.CODCLI,
                        CLI.APECLI,
                        IPV.CODPRO,
                        IPV.CPLIPV,
                        IPV.SEQIPV,
                        IPV.CODSTR,
                        IPV.CSTPIS,
                        IPV.CSTCOF,
                        CLF.CLAFIS,
                        IPV.CODTRD,
                        IPV.CTARED,
                        IPV.CODCCU,
                        NFV.DATGER,
                        NFV.USUGER,
                        NFV.HORGER,
                        IPV.CSTIPI,
                        IPV.OBSIPV,
                        EPRO.ClaPRO,
                        NFV.SitNFV,
                        CODBNF
                ORDER BY EMPRESA, FILIAL, DTEMISSAO, NUMNF, CODSNF, SEQIPV
        ";
    }
    public function getConferenciaSaidasServico($empresa = null, $filial = null, $dataInicio = null, $dataFim = null, $chaveNfe = null)
    {
        $ands = "";

        if (!empty($empresa)) {
            $ands .= " AND NFV.CODEMP = {$empresa}";
        }
        if (!empty($filial)) {
            $ands .= " AND NFV.CODFIL = {$filial}";
        }
        if (!empty($dataInicio) && !empty($dataFim)) {
            $ands .= " AND NFV.DATEMI BETWEEN TO_DATE('{$dataInicio}', 'YYYY-MM-DD') AND TO_DATE('{$dataFim}', 'YYYY-MM-DD')";
        }
        if (!empty($chaveNfe)) {
            $ands .= " AND IDE.CHVDOE = '{$chaveNfe}'";
        }
        return "SELECT 
                    NFV.CODEMP As Empresa,
                    NFV.CodFil As Filial,
                    NFV.DatEmi As DtEmissao,
                    ISV.TNSSER As Trans_Serv,
                    NFV.NUMNFV AS NumNF,
                    IDE.CHVDOE As ChaveNFE,
                    NFV.CODSNF,
                    (NFV.CODCLI || ' - ' || CLI.APECLI) As Cliente,
                    ISV.CODSER,
                    RTRIM(ISV.CPLISV) As DescProd,
                    ISV.SEQISV,
                    MAX(TO_NUMBER(TO_CHAR(ISV.QTDFAT))) As QtdFat,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBRU))) As VlrBru,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRDSC))) As VlrDsc,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRLIQ))) As VlrLiq,
                    (MAX(TO_NUMBER(TO_CHAR(ISV.VLRLIQ))) -
                    (MAX(TO_NUMBER(TO_CHAR(ISV.VLRBRU))) -
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRDSC))))) As VlrLiq_Dif,
                    ISV.CODSTR As Sit_Trib,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBIC))) As VlrBase_ICM,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERICM))) As PERICM,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRICM))) As VLRICM,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRIIC))) As VlrIsen_ICM,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLROIC))) As VlrOutr_ICM,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRICD))) As VlrDeson_ICM,
                    0 As PERFUN,
                    0 As VlrBase_FUNR,
                    0 As VlrFunrural,
                    ISV.CSTPIS,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERPIF))) As PerPIS_Fat,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBPF))) As VlrBase_PIS,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRPIF))) As VlrImp_PISFat,
                    ISV.CSTCOF,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERCFF))) As PerCOF_Fat,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBCF))) As VlrImp_COFFat,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRCFF))) As VlrRet_COF,
                    TO_CHAR(CLF.CLAFIS) As NCM,
                    ISV.CODTRD As CodRed_Z,
                    ISV.CTARED As CtaRed,
                    ISV.CODCCU As CodCCU,
                    0 As PerISS,
                    0 As VlrBase_ISS,
                    0 As VlrIMP_ISS,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERIRF))) As PerIRRF,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBIR))) As VlrBase_IRF,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRIRF))) As VlrIMP_IRF,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERCRT))) As PerFat_COF,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBCT))) AS VlrBase_COFRet,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRCRT))) AS VlrIMP_COFRet,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERPIT))) As PerFat_PIS,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBPT))) AS VlrBase_PISRet,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRPIT))) AS VlrIMP_PISRet,
                    MAX(TO_NUMBER(TO_CHAR(ISV.PERCSL))) As PerFat_CSLL,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBCL))) AS VlrBase_CSLLRet,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRCSL))) AS VlrIMP_CSLLRet,
                    NFV.DATGER,
                    NFV.USUGER,
                    NFV.HORGER,
                    ISV.CSTIPI,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRBIP))) As VlrBase_IPI,
                    MAX(TO_NUMBER(TO_CHAR(ISV.VLRIPI))) As VlrIMP_IPI,
                    ISV.OBSISV As Obs_Prod,
                    NFV.SitNFV As SitNFSaida,
                    (
                        Case When SitNFV = '1' then 'Digitada' When SitNFV = '2' then 'Fechada' When SitNFV = '3' then 'Cancelada' When SitNFV = '4' then 'DocFisEmitida-Saida' 
                             When SitNFV = '5' then 'AguardandoFechamento_pós-Saida' When SitNFV = '6' then 'AguardandoIntWMS'
                        Else 'DigitadaInt' End
                    ) As SitNFSaida_Descr,
                    MIN(A.VENC01_CR) As Venc01_CR,
                    MAX(A.TOTAL_CR) As Total_CR,
                    MAX(A.QNTPARC_CR) As QntParc_CR,
                    CODBNF As cBenef,
                    MAX(MNV.MSGNFV) As Mensagem_1,
                    MAX(NFV.OBSMOT) As Obs_Motivo,
                    MAX(MENS.MENS1) As Mens1,
                    MAX(MENS.MENS2) As Mens2,
                    MAX(MENS.MENS3) As Mens3,
                    MAX(MENS.MENS4) As Mens4,
                    MAX(MENS.MENS5) As Mens5,
                    MAX(MENS.MENS6) As Mens6,
                    MAX(MENS.MENS7) As Mens7,
                    MAX(MENS.MENS8) As Mens8,
                    MAX(MENS.MENS9) As Mens9
                FROM SAPIENS.E140NFV NFV
                INNER JOIN SAPIENS.E140ISV ISV ON NFV.CODEMP = ISV.CODEMP AND NFV.CODFIL = ISV.CODFIL AND NFV.CODSNF = ISV.CODSNF AND NFV.NUMNFV = ISV.NUMNFV AND NFV.CODSNF = ISV.CODSNF
                LEFT JOIN SAPIENS.E140IDE IDE ON NFV.CODEMP = IDE.CODEMP AND NFV.CODFIL = IDE.CODFIL AND NFV.CODSNF = IDE.CODSNF AND NFV.NUMNFV = IDE.NUMNFV
                LEFT JOIN SAPIENS.E022CLF CLF ON ISV.CODCLF = CLF.CODCLF
                INNER JOIN SAPIENS.E085CLI CLI ON CLI.CODCLI = NFV.CODCLI
                LEFT JOIN SAPIENS.E140MNV MNV ON MNV.CODEMP = NFV.CODEMP AND MNV.CODFIL = NFV.CODFIL AND MNV.NUMNFV = NFV.NUMNFV AND MNV.CODSNF = NFV.CODSNF
                LEFT JOIN (
                    SELECT CODEMP,
                            CODFIL,
                            NUMNFV,
                            CODSNF,
                            CODTPT,
                            MIN(VCTPAR) Venc01_CR,
                            SUM(VLRPAR) Total_CR,
                            COUNT(CODPAR) QntParc_CR
                    FROM SAPIENS.E140PAR X
                    WHERE X.VCTPAR >= SYSDATE - 420 --Vencimentos a partir de...
                    group BY CODEMP, CODFIL, NUMNFV, CODSNF, CODTPT
                ) A ON A.CODEMP = NFV.CODEMP AND A.CODFIL = NFV.CODFIL AND A.NUMNFV = NFV.NUMNFV AND A.CODSNF = NFV.CODSNF
                LEFT JOIN (
                        SELECT *
                        FROM (
                            SELECT 
                                CODEMP,
                                CODFIL,
                                CODSNF,
                                NUMNFV,
                                ('MENS' || TO_CHAR(ROW_NUMBER() OVER(PARTITION BY CODEMP || CODFIL || CODSNF || NUMNFV ORDER BY SEQOBS))) HANK,
                                A.USU_DESMSG
                            FROM (
                                SELECT *
                                FROM SAPIENS.E140OBS A
                                WHERE DATGER >= SYSDATE - 420
                                ORDER BY CODEMP, CODFIL, CODSNF, numnfv, seqobs) A
                            ) B
                            PIVOT(MAX(USU_DESMSG)
                            FOR HANK IN('MENS1' Mens1,
                                        'MENS2' Mens2,
                                        'MENS3' Mens3,
                                        'MENS4' Mens4,
                                        'MENS5' Mens5,
                                        'MENS6' Mens6,
                                        'MENS7' Mens7,
                                        'MENS8' Mens8,
                                        'MENS9' Mens9)
                        )
                ) MENS ON MENS.CODEMP = NFV.CODEMP AND MENS.CODFIL = NFV.CODFIL AND MENS.NUMNFV = NFV.NUMNFV AND MENS.CODSNF = NFV.CODSNF
                WHERE 0 = 0
                {$ands}
                GROUP BY NFV.CODEMP,
                        NFV.CodFil,
                        NFV.DatEmi,
                        ISV.TNSSER,
                        NFV.NUMNFV,
                        IDE.CHVDOE,
                        NFV.CODSNF,
                        NFV.CODCLI,
                        CLI.APECLI,
                        ISV.CODSER,
                        ISV.CPLISV,
                        ISV.SEQISV,
                        ISV.CODSTR,
                        ISV.CSTPIS,
                        ISV.CSTCOF,
                        CLF.CLAFIS,
                        ISV.CODTRD,
                        ISV.CTARED,
                        ISV.CODCCU,
                        NFV.DATGER,
                        NFV.USUGER,
                        NFV.HORGER,
                        ISV.CSTIPI,
                        ISV.OBSISV,
                        NFV.SitNFV,
                        ISV.CODBNF
                ORDER BY EMPRESA, FILIAL, DTEMISSAO, NUMNF, CODSNF
        ";
    }



}
