<?php

namespace Application\Repository;

class ComercialRepository
{

    public function getClassificacaoClientesSoftsul()
    {
        $wheresInternos = "";
        $wheresExternos = "";
        
        return "SELECT E.* 
                FROM (
                SELECT 
                    D.*
                    ,CASE
                        -- 🌟 1. Raiz Forte (Clientes premium: antigos, recorrentes, ticket médio alto, e TSI > 15%. São fiéis e altamente valiosos.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 3
                            AND PRIMEIRO_PEDIDO_DIAS > 180
                            AND ULTIMO_PEDIDO_DIAS <= 380
                            AND PERC_TSI > 15
                        THEN 1
                    
                        -- 🌿 2. Sementes de Ouro (Clientes consistentes, com bom histórico de compra, ticket médio saudável e uso relevante de TSI. Um nível abaixo do topo, mas bem confiáveis.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 2
                            AND PRIMEIRO_PEDIDO_DIAS > 180
                            AND ULTIMO_PEDIDO_DIAS <= 380
                            AND PERC_TSI > 10
                        THEN 2
                    
                        -- 🌱 3. Pé de Safra (Clientes ativos, ritmo mais espaçado, mas ainda assim envolvidos. Podem crescer.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 1
                            AND PRIMEIRO_PEDIDO_DIAS > 180
                            AND ULTIMO_PEDIDO_DIAS <= 380
                        THEN 3
                    
                        -- 🌾 4. Terra Promissora (Clientes relativamente novos (6 meses da primeira compra), com excelente início — bom ticket, uso de TSI e já fazendo pedidos. Grande potencial de se tornarem premium.)
                        WHEN QTD_SAFRAS_PARTICIPADAS > 0
                            AND PERC_TSI > 10
                            AND PRIMEIRO_PEDIDO_DIAS <= 180
                        THEN 4
                    
                        -- 🌿 5. Broto de Esperança (Clientes muito novos (até 6 meses da primeira compra), qualquer ticket.)
                        WHEN QTD_SAFRAS_PARTICIPADAS > 0
                            AND PRIMEIRO_PEDIDO_DIAS <= 180
                        THEN 5
                    
                        -- 🌧️ 6. Chuva na Hora Errada (Clientes que pararam de comprar, ticket acima de 100k, já foram bons, mas sumiram com queda brusca nas compras. Valem contato para entender o que houve.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 1
                            AND PERC_TSI > 10
                            AND ULTIMO_PEDIDO_DIAS > 380
                        THEN 6
                        
                        -- 😴 7. Solo Adormecido (Clientes que pararam de comprar, qualquer ticket)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 1
                            AND ULTIMO_PEDIDO_DIAS > 380
                        THEN 7
                        
                        ELSE null
                    END AS CATEGORIA_CLIENTE_ID
                    ,CASE
                        -- 🌟 1. Raiz Forte (Clientes premium: antigos, recorrentes, ticket médio alto, e TSI > 15%. São fiéis e altamente valiosos.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 3
                            AND PRIMEIRO_PEDIDO_DIAS > 180
                            AND ULTIMO_PEDIDO_DIAS <= 380
                            AND PERC_TSI > 15
                        THEN 'Raiz Forte'
                    
                        -- 🌿 2. Sementes de Ouro (Clientes consistentes, com bom histórico de compra, ticket médio saudável e uso relevante de TSI. Um nível abaixo do topo, mas bem confiáveis.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 2
                            AND PRIMEIRO_PEDIDO_DIAS > 180
                            AND ULTIMO_PEDIDO_DIAS <= 380
                            AND PERC_TSI > 10
                        THEN 'Sementes de Ouro' 
                    
                        -- 🌱 3. Pé de Safra (Clientes ativos, ritmo mais espaçado, mas ainda assim envolvidos. Podem crescer.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 1
                            AND PRIMEIRO_PEDIDO_DIAS > 180
                            AND ULTIMO_PEDIDO_DIAS <= 380
                        THEN 'Pé de Safra'
                    
                        -- 🌾 4. Terra Promissora (Clientes relativamente novos (6 meses da primeira compra), com excelente início — bom ticket, uso de TSI e já fazendo pedidos. Grande potencial de se tornarem premium.)
                        WHEN QTD_SAFRAS_PARTICIPADAS > 0
                            AND PERC_TSI > 10
                            AND PRIMEIRO_PEDIDO_DIAS <= 180
                        THEN 'Terra Promissora'
                    
                        -- 🌿 5. Broto de Esperança (Clientes muito novos (até 6 meses da primeira compra), ticket menor que o Terra Promissora.)
                        WHEN QTD_SAFRAS_PARTICIPADAS > 0
                            AND PRIMEIRO_PEDIDO_DIAS <= 180
                        THEN 'Broto de Esperança'
                    
                        -- 🌧️ 6. Chuva na Hora Errada (Clientes que pararam de comprar, ticket acima de 100k, já foram bons, mas sumiram com queda brusca nas compras. Valem contato para entender o que houve.)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 1
                            AND PERC_TSI > 10
                            AND ULTIMO_PEDIDO_DIAS > 380
                        THEN 'Chuva na Hora Errada'
                        
                        -- 😴 7. Solo Adormecido (Clientes que pararam de comprar, qualquer ticket)
                        WHEN QTD_SAFRAS_PARTICIPADAS >= 1
                            AND ULTIMO_PEDIDO_DIAS > 380
                        THEN 'Solo Adormecido'
                        
                        ELSE null
                    END AS CATEGORIA_CLIENTE
                FROM (
                SELECT  
                    C.*
                    ,ROUND(nvl(C.PRECO_TOTAL_GERMOPLASMA,0)/nvl(C.PRECO_TOTAL,0)*100,2) PERC_GERMOPLASMA
                    ,ROUND(nvl(C.PRECO_TOTAL_ROYALTIES,0)/nvl(C.PRECO_TOTAL,0)*100,2) PERC_ROYALTIES
                    ,ROUND(nvl(C.PRECO_TOTAL_FRETE,0)/nvl(C.PRECO_TOTAL,0)*100,2) PERC_FRETE
                    ,ROUND(nvl(C.PRECO_TOTAL_TSI,0)/nvl(C.PRECO_TOTAL,0)*100,2) PERC_TSI
                    ,round(CASE WHEN C.PRECO_TOTAL_ANO_ANTERIOR > 0 THEN (C.PRECO_TOTAL_ANO_ATUAL - C.PRECO_TOTAL_ANO_ANTERIOR) / C.PRECO_TOTAL_ANO_ANTERIOR *100 ELSE 0 END,2) AS PERC_CRESCIMENTO_QUEDA
                    ,round(C.PRECO_TOTAL/C.QTD_PEDIDOS,2) AS TICKET_MEDIO
                    ,round(C.PRECO_TOTAL/C.QTD_TOTAL,2) AS PRECO_MEDIO_BAG
                FROM (
                SELECT 
                    B.NOME_CLIENTE
                    ,B.CGCCPF_CLIENTE
                    ,nvl(COUNT(DISTINCT B.ID_PEDIDO),0) AS QTD_PEDIDOS
                    ,nvl(sum(B.QTD_TOTAL),0) AS QTD_TOTAL
                    ,nvl(sum(B.QTD_B50),0) AS QTD_B50
                    ,nvl(sum(B.QTD_B10),0) AS QTD_B10
                    ,nvl(sum(B.QTD_SK200),0) AS QTD_SK200
                    ,nvl(sum(B.PRECO_TOTAL),0) + nvl(SUM(B.PRECO_TOTAL_FRETE),0) AS PRECO_TOTAL
                    ,nvl(sum(B.PRECO_TOTAL_GERMOPLASMA),0) AS PRECO_TOTAL_GERMOPLASMA
                    ,nvl(sum(B.PRECO_TOTAL_ROYALTIES),0) AS PRECO_TOTAL_ROYALTIES
                    ,nvl(sum(B.PRECO_TOTAL_TSI),0) AS PRECO_TOTAL_TSI
                    ,nvl(SUM(B.PRECO_TOTAL_FRETE),0) AS PRECO_TOTAL_FRETE
                    ,TO_CHAR(MIN(B.DATA_PEDIDO), 'YYYY-MM-DD') AS DATA_PRIMEIRO_PEDIDO
                    ,TRUNC(SYSDATE - MIN(CAST(B.DATA_PEDIDO AS DATE))) AS PRIMEIRO_PEDIDO_DIAS
                    ,TO_CHAR(MAX(B.DATA_PEDIDO), 'YYYY-MM-DD') AS DATA_ULTIMO_PEDIDO
                    ,TRUNC(SYSDATE - MAX(CAST(B.DATA_PEDIDO AS DATE))) AS ULTIMO_PEDIDO_DIAS
                    ,COUNT(DISTINCT B.CODIGOSAFRA) QTD_SAFRAS_PARTICIPADAS
                    ,ROUND(AVG(B.QTD_CULTIVAR_POR_PEDIDO),2) AS MEDIA_CULTIVARES_POR_PEDIDO
                    ,ROUND(nvl(COUNT(DISTINCT B.ID_PEDIDO),0) / COUNT(DISTINCT B.CODIGOSAFRA),2) AS MEDIA_PEDIDOS_P_SAFRA
                    ,ROUND(nvl(sum(B.QTD_TOTAL),0) / COUNT(DISTINCT B.CODIGOSAFRA),2) AS MEDIA_BAGS_P_SAFRA
                    ,ROUND(AVG(B.DIAS_ENTRE_PEDIDOS),0) AS MEDIA_DIAS_ENTRE_PEDIDOS
                    ,SUM(B.PRECO_TOTAL_ANO_ATUAL) AS PRECO_TOTAL_ANO_ATUAL
                    ,SUM(B.PRECO_TOTAL_ANO_ANTERIOR) AS PRECO_TOTAL_ANO_ANTERIOR
                    ,nvl(COUNT(DISTINCT B.QTD_PEDIDO_ANO_ATUAL),0) AS QTD_PEDIDO_ANO_ATUAL
                    ,nvl(COUNT(DISTINCT B.QTD_PEDIDO_ANO_ANTERIOR),0) AS QTD_PEDIDO_ANO_ANTERIOR
                FROM ( 
                SELECT 
                    A.ID_PEDIDO
                    ,A.CODIGO_PEDIDO
                    ,A.DATA_PEDIDO
                    ,EXTRACT(YEAR FROM A.DATA_PEDIDO) ANO_PEDIDO
                    ,LAG(A.DATA_PEDIDO) OVER (PARTITION BY A.CGCCPF_CLIENTE ORDER BY A.CGCCPF_CLIENTE, A.DATA_PEDIDO) AS PEDIDO_ANTERIOR
                    ,A.CODIGOSAFRA 
                    ,COUNT(DISTINCT A.CODIGOCULTIVAR) AS QTD_CULTIVAR_POR_PEDIDO
                    ,A.ID_CLIENTE
                    ,A.NOME_CLIENTE
                    ,A.CIDADE_CLIENTE
                    ,A.ESTADO_CLIENTE
                    ,A.CLIENTE_REGIAO
                    ,A.CGCCPF_CLIENTE
                    ,A.INSCRICAO_CLIENTE
                    ,CASE WHEN ROUND(CAST(A.DATA_PEDIDO AS DATE) - CAST(LAG(A.DATA_PEDIDO) OVER (PARTITION BY A.CGCCPF_CLIENTE ORDER BY A.CGCCPF_CLIENTE, A.DATA_PEDIDO) AS DATE),0) = 0 THEN NULL ELSE ROUND(CAST(A.DATA_PEDIDO AS DATE) - CAST(LAG(A.DATA_PEDIDO) OVER (PARTITION BY A.CGCCPF_CLIENTE ORDER BY A.CGCCPF_CLIENTE, A.DATA_PEDIDO) AS DATE),0) END AS DIAS_ENTRE_PEDIDOS
                    ,SUM(A.QTD_TOTAL) AS QTD_TOTAL
                    ,SUM(A.QTD_B50) AS QTD_B50
                    ,SUM(A.QTD_B10) AS QTD_B10
                    ,SUM(A.QTD_SK200) AS QTD_SK200
                    ,MAX(A.PRECO_TOTAL_FRETE) AS PRECO_TOTAL_FRETE
                    ,SUM(A.PRECO_TOTAL_GERMOPLASMA) AS PRECO_TOTAL_GERMOPLASMA
                    ,SUM(A.PRECO_TOTAL_ROYALTIES) AS PRECO_TOTAL_ROYALTIES
                    ,SUM(A.PRECO_TOTAL_TSI) AS PRECO_TOTAL_TSI
                    ,SUM(A.PRECO_TOTAL) AS PRECO_TOTAL
                    ,CASE WHEN EXTRACT(YEAR FROM A.DATA_PEDIDO) = EXTRACT(YEAR FROM SYSDATE) THEN  A.ID_PEDIDO ELSE null END AS QTD_PEDIDO_ANO_ATUAL
                    ,CASE WHEN EXTRACT(YEAR FROM A.DATA_PEDIDO) = EXTRACT(YEAR FROM SYSDATE)-1 THEN  A.ID_PEDIDO ELSE null END AS QTD_PEDIDO_ANO_ANTERIOR
                    ,CASE WHEN EXTRACT(YEAR FROM A.DATA_PEDIDO) = EXTRACT(YEAR FROM SYSDATE) THEN  SUM(A.PRECO_TOTAL) ELSE 0 END AS PRECO_TOTAL_ANO_ATUAL
                    ,CASE WHEN EXTRACT(YEAR FROM A.DATA_PEDIDO) = EXTRACT(YEAR FROM SYSDATE)-1 THEN  SUM(A.PRECO_TOTAL) ELSE 0 END AS PRECO_TOTAL_ANO_ANTERIOR
                FROM (
                SELECT  
                    P.ID AS ID_PEDIDO
                    ,P.CODIGO AS CODIGO_PEDIDO
                    ,P.CREATED_AT AS DATA_PEDIDO
                    ,P.CODIGOSAFRA 
                    ,CLI.CODIGOCLIFOR AS ID_CLIENTE
                    ,CLI.NOME AS NOME_CLIENTE
                    ,CLI.CIDADE AS CIDADE_CLIENTE
                    ,CLI.ESTADO AS ESTADO_CLIENTE
                    ,CLI.CGC_CPF AS CGCCPF_CLIENTE
                    ,CLI.INSCRICAO AS INSCRICAO_CLIENTE
                    ,REGI.DESCRICAO AS CLIENTE_REGIAO
                    ,NVL(P.PRECO_TOTAL_FRETE ,0) AS PRECO_TOTAL_FRETE
                    ,NVL(IP.QUANT ,0) AS QTD_TOTAL
                    ,NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0) AS PRECO_TOTAL_GERMOPLASMA
                    ,NVL(IP.PRECO_TOTAL_ROYALTIES ,0) AS PRECO_TOTAL_ROYALTIES
                    ,NVL(IP.PRECO_TOTAL_TSI ,0) AS PRECO_TOTAL_TSI
                    ,NVL(IP.PRECO_TOTAL,0) AS PRECO_TOTAL
                    ,IP.CODIGOCULTIVAR
                    ,IP.CODIGO_EMBALAGEM
                    ,e.UNIMED_SENIOR 
                    ,CASE WHEN E.UNIMED_SENIOR = 'B50' THEN NVL(IP.QUANT ,0) ELSE 0 END QTD_B50
                    ,CASE WHEN E.UNIMED_SENIOR = 'B10' THEN NVL(IP.QUANT ,0) ELSE 0 END QTD_B10
                    ,CASE WHEN E.UNIMED_SENIOR = 'SK' THEN NVL(IP.QUANT ,0) ELSE 0 END QTD_SK200
                FROM web.pedidos_v2 p
                LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                LEFT JOIN EMPRESA.MUNICIPIOS MUNI ON MUNI.CODIGOMUNICIPIO = CLI.CODIGOMUNICIPIO
                LEFT JOIN SEMENTES.REGIOES REGI ON REGI.CODIGOREGIAO = MUNI.CODIGOREGIAO
                LEFT JOIN web.itens_pedido_v2 ip ON ip.PEDIDO_ID = p.ID 
                LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                LEFT JOIN SEMENTES.EMBALAGENS e ON e.EMBALAGEM = ip.CODIGO_EMBALAGEM 
                WHERE P.FINALIDADE_SEMENTE_ID IN (3,4) -- apenas Plantio, Revenda
                AND s.CODIGOCULTURA = 1 -- apenas cultura SOJA
                AND s.CODIGOINSCRICAO = 1
                AND IP.CODIGOCULTIVAR IS NOT NULL
                --AND CLI.CGC_CPF IN ('13563680004867','60498706013992','13563680002147','60498706006600')
                ORDER BY CLI.CGC_CPF, P.CREATED_AT
                ) A
                GROUP BY A.ID_PEDIDO,A.CODIGO_PEDIDO,A.DATA_PEDIDO,A.CODIGOSAFRA,A.ID_CLIENTE,A.NOME_CLIENTE,A.CIDADE_CLIENTE,A.ESTADO_CLIENTE,A.CLIENTE_REGIAO,A.CGCCPF_CLIENTE,A.INSCRICAO_CLIENTE
                ORDER BY A.CGCCPF_CLIENTE, A.DATA_PEDIDO
                ) B
                GROUP BY b.NOME_CLIENTE,b.CGCCPF_CLIENTE
                ORDER BY nvl(COUNT(DISTINCT b.ID_PEDIDO),0) DESC
                ) C
                ) D
                ) E";  
    }
    public function getPedidosCliente($clienteCgcCpf)
    {
        $wheresInternos = "";
        $wheresExternos = "";
        $columnTipoApuracao = "";
        
        if (!empty($clienteCgcCpf)) {
            $sql ="SELECT 
                        A.ID_PEDIDO
                        ,A.CODIGO_PEDIDO
                        ,A.VENDEDOR_ID
                        ,A.NOME_VENDEDOR
                        ,TO_CHAR(A.DATA_PEDIDO, 'YYYY-MM-DD') as DATA_PEDIDO
                        ,EXTRACT(YEAR FROM A.DATA_PEDIDO) ANO_PEDIDO
                        ,A.CODIGOSAFRA 
                        ,A.DSC_SAFRA
                        ,COUNT(DISTINCT A.CODIGOCULTIVAR) AS QTD_CULTIVAR_POR_PEDIDO
                        ,SUM(A.QTD_TOTAL) AS QTD_TOTAL
                        ,SUM(A.QTD_B50) AS QTD_B50
                        ,SUM(A.QTD_B10) AS QTD_B10
                        ,SUM(A.QTD_SK200) AS QTD_SK200
                        ,MAX(A.PRECO_TOTAL_FRETE) AS PRECO_TOTAL_FRETE
                        ,SUM(A.PRECO_TOTAL_GERMOPLASMA) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(A.PRECO_TOTAL_ROYALTIES) AS PRECO_TOTAL_ROYALTIES
                        ,SUM(A.PRECO_TOTAL_TSI) AS PRECO_TOTAL_TSI
                        ,(SUM(A.PRECO_TOTAL) + MAX(A.PRECO_TOTAL_FRETE)) AS PRECO_TOTAL
                        ,round((SUM(A.PRECO_TOTAL) + MAX(A.PRECO_TOTAL_FRETE))/SUM(A.QTD_TOTAL),2) AS PRECO_MEDIO_BAG
                        ,ROUND((SUM(A.PRECO_TOTAL_GERMOPLASMA) / (SUM(A.PRECO_TOTAL) + MAX(A.PRECO_TOTAL_FRETE)))*100,2) AS PERC_GERMOPLASMA
                        ,ROUND((SUM(A.PRECO_TOTAL_ROYALTIES) / (SUM(A.PRECO_TOTAL) + MAX(A.PRECO_TOTAL_FRETE)))*100,2) AS PERC_ROYALTIES
                        ,ROUND((SUM(A.PRECO_TOTAL_FRETE) / (SUM(A.PRECO_TOTAL) + MAX(A.PRECO_TOTAL_FRETE)))*100,2) AS PERC_FRETE
                        ,ROUND((SUM(A.PRECO_TOTAL_TSI) / (SUM(A.PRECO_TOTAL) + MAX(A.PRECO_TOTAL_FRETE)))*100,2) AS PERC_TSI
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || A.ID_PEDIDO || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
                    FROM (
                    SELECT  
                        P.ID AS ID_PEDIDO
                        ,P.CODIGO AS CODIGO_PEDIDO
                        ,P.CREATED_AT AS DATA_PEDIDO
                        ,P.CODIGOSAFRA 
                        ,S.ANO || ' - ' || REGEXP_REPLACE(C.DESCRICAO, '\s+', '')  as DSC_SAFRA
                        ,CLI.CODIGOCLIFOR AS ID_CLIENTE
                        ,CLI.NOME AS NOME_CLIENTE
                        ,CLI.CIDADE AS CIDADE_CLIENTE
                        ,CLI.ESTADO AS ESTADO_CLIENTE
                        ,CLI.CGC_CPF AS CGCCPF_CLIENTE
                        ,REGI.DESCRICAO AS CLIENTE_REGIAO
                        ,p.RTV_USER_ID AS VENDEDOR_ID
                        ,vend.NAME AS NOME_VENDEDOR
                        ,NVL(P.PRECO_TOTAL_FRETE ,0) AS PRECO_TOTAL_FRETE
                        ,NVL(IP.QUANT ,0) AS QTD_TOTAL
                        ,NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0) AS PRECO_TOTAL_GERMOPLASMA
                        ,NVL(IP.PRECO_TOTAL_ROYALTIES ,0) AS PRECO_TOTAL_ROYALTIES
                        ,NVL(IP.PRECO_TOTAL_TSI ,0) AS PRECO_TOTAL_TSI
                        ,NVL(IP.PRECO_TOTAL,0) AS PRECO_TOTAL
                        ,IP.CODIGOCULTIVAR
                        ,IP.CODIGO_EMBALAGEM
                        ,e.UNIMED_SENIOR 
                        ,CASE WHEN E.UNIMED_SENIOR = 'B50' THEN NVL(IP.QUANT ,0) ELSE 0 END QTD_B50
                        ,CASE WHEN E.UNIMED_SENIOR = 'B10' THEN NVL(IP.QUANT ,0) ELSE 0 END QTD_B10
                        ,CASE WHEN E.UNIMED_SENIOR = 'SK' THEN NVL(IP.QUANT ,0) ELSE 0 END QTD_SK200
                    FROM web.pedidos_v2 p
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    LEFT JOIN EMPRESA.MUNICIPIOS MUNI ON MUNI.CODIGOMUNICIPIO = CLI.CODIGOMUNICIPIO
                    LEFT JOIN SEMENTES.REGIOES REGI ON REGI.CODIGOREGIAO = MUNI.CODIGOREGIAO
                    LEFT JOIN web.itens_pedido_v2 ip ON ip.PEDIDO_ID = p.ID 
                    LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                    LEFT JOIN ALMOX.CULTURAS C ON C.CODIGOCULTURA = S.CODIGOCULTURA
                    LEFT JOIN SEMENTES.EMBALAGENS e ON e.EMBALAGEM = ip.CODIGO_EMBALAGEM 
                    WHERE P.FINALIDADE_SEMENTE_ID IN (3,4) -- apenas Plantio, Revenda
                    AND s.CODIGOCULTURA = 1 -- apenas cultura SOJA
                    AND s.CODIGOINSCRICAO = 1
                    AND IP.CODIGOCULTIVAR IS NOT NULL
                    AND CLI.CGC_CPF like '{$clienteCgcCpf}'
                    ORDER BY CLI.CGC_CPF, P.CREATED_AT
                    ) A
                    GROUP BY A.ID_PEDIDO,A.CODIGO_PEDIDO,A.DATA_PEDIDO,A.CODIGOSAFRA,A.DSC_SAFRA,A.VENDEDOR_ID,A.NOME_VENDEDOR
                    ORDER BY A.DATA_PEDIDO";  
        } else {
            $sql = "";
        }

        return $sql;

    }













    // public function getInfoPedido($cgcCpfCliente = null)
    // {
    //     $wheresInternos = "";
    //     $wheresExternos = "";
    //     if (!empty($cgcCpfCliente)) {
    //         $wheresInternos .= " AND CLI.CGC_CPF = '$cgcCpfCliente'"; // não listar usuários demitidos DE ACORDO COM A DATA APURAÇÃO
    //     }
        
    //     return "SELECT 
    //                 P.ID AS ID_PEDIDO
    //                 ,P.CODIGO AS CODIGO_PEDIDO
    //                 ,TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD hh:mm:ss') AS DATA_PEDIDO
    //                 ,CLI.CODIGOCLIFOR AS ID_CLIENTE
    //                 ,CLI.NOME AS NOME_CLIENTE
    //                 ,CLI.CIDADE AS CIDADE_CLIENTE
    //                 ,CLI.ESTADO AS ESTADO_CLIENTE
    //                 ,CLI.CGC_CPF AS CGCCPF_CLIENTE
    //                 ,VEND.ID AS ID_VENDEDOR
    //                 ,VEND.NAME AS NOME_VENDEDOR
    //                 ,(SELECT COUNT(DISTINCT P2.ID) FROM WEB.PEDIDOS_V2 P2 WHERE P2.CODIGOLOCAL = P.CODIGOLOCAL AND P2.CREATED_AT < P.CREATED_AT) QTD_PEDIDOS_CLI
    //                 ,(SELECT max(vvpd.PRECO_PARCELA) FROM WEB.VIEW_VENCIMENTOS_POR_DATA vvpd WHERE vvpd.PEDIDO_ID = P.ID) AS VLR_TOTAL_PEDIDO
    //                 ,P.PRECO_TOTAL_FRETE 
    //             FROM web.pedidos_v2 P
    //             LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
    //             LEFT JOIN EMPRESA.CLIFOR agt ON agt.CODIGOCLIFOR = p.AGENTE_CODIGOCLIFOR 
    //             LEFT JOIN EMPRESA.CLIFOR gc ON gc.CODIGOCLIFOR = p.GRUPO_COMPRA_CODIGOCLIFOR 
    //             LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID 
    //             WHERE 1 = 1
    //             {$wheresInternos}
    //             -- AND P.CREATED_AT BETWEEN '01/04/2025 00:00:0' AND '14/04/2025 23:59:59'
    //             -- WHERE P.CODIGO IN (25570022, 24410014)
    //             ORDER BY VEND.ID";  
    // }
    // public function getInfoItensPedido($pedidoId)
    // {
    //     $wheresInternos = "";
    //     $wheresExternos = "";
    //     $columnTipoApuracao = "";
        
    //     if (!empty($pedidoId)) {
    //         $sql ="SELECT 
    //                     I.PEDIDO_ID
    //                     ,I.NUMPED
    //                     ,I.CODIGOCULTIVAR
    //                     ,I.CODIGOSEMENTE
    //                     ,C.DESCRICAO AS DSC_CULTIVAR
    //                     ,I.CODIGO_EMBALAGEM
    //                     ,E.DESCRICAO AS DSC_EMBALAGEM
    //                     ,I.CODIGOCLASSE AS CATEGORIA
    //                     ,C2.DESCRICAO AS DSC_CATEGORIA
    //                     ,I.PENEIRA 
    //                     ,nvl(SUM(i.QUANT), 0) AS QTD_PEDIDO
    //                     ,nvl(SUM(i.preco_total_germoplasma), 0) AS PRECO_TOTAL_GERMOPLASMA
    //                     ,nvl(SUM(i.preco_total_royalties), 0) AS PRECO_TOTAL_ROYALTIES
    //                     ,nvl(SUM(i.preco_total_tsi), 0) AS PRECO_TOTAL_TSI
    //                     ,nvl(sum(i.PRECO_TOTAL),0) AS PRECO_TOTAL
    //                 FROM web.itens_pedido_v2 i
    //                 LEFT JOIN SEMENTES.EMBALAGENS E ON E.EMBALAGEM = I.CODIGO_EMBALAGEM 
    //                 LEFT JOIN SEMENTES.CULTIVARES C ON C.CODIGOCULTIVAR = I.CODIGOCULTIVAR 
    //                 LEFT JOIN SEMENTES.CLASSES c2 ON C2.CODIGOCLASSE = I.CODIGOCLASSE 
    //                 WHERE i.pedido_id = {$pedidoId}
    //                 GROUP BY i.pedido_id,I.NUMPED,i.codigocultivar,i.CODIGOSEMENTE,C.DESCRICAO,i.codigo_embalagem,E.DESCRICAO,i.codigoclasse,C2.DESCRICAO,i.peneira";  
    //     } else {
    //         $sql = "";
    //     }

    //     return $sql;

    // }
    // public function getInfoRecebimentosPedido($pedidoId)
    // {
    //     $wheresInternos = "";
    //     $wheresExternos = "";
    //     $columnTipoApuracao = "";
        
    //     if (!empty($pedidoId)) {
    //         $sql ="SELECT 
    //                     R.ID 
    //                     ,R.PEDIDO_ID 
    //                     ,R.TIPO 
    //                     ,R.VALOR 
    //                     ,R.JUROS 
    //                     ,R.DESCONTO 
    //                     ,R.RECEBIDO_EM 
    //                 FROM WEB.RECEBIMENTOS R
    //                 WHERE PEDIDO_ID = {$pedidoId}";  
    //     } else {
    //         $sql = "";
    //     }

    //     return $sql;

    // }
   
}
