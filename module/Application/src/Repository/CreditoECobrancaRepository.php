<?php

namespace Application\Repository;

use Laminas\Db\Adapter\AdapterInterface;

class CreditoECobrancaRepository
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getDadosSoftsulQuery($codigoSafra, $emissao_inicio = null, $emissao_fim = null)
    {
        $wheres = "";
        
        if (!empty($codigoSafra)) {
            $wheres .= " AND p.CODIGOSAFRA = {$codigoSafra}";
        }
        if (!empty($emissao_inicio)) {
            $emissao_fim = !empty($emissao_fim) ? $emissao_fim : date('Y-m-d');
            $wheres .= " AND P.CREATED_AT BETWEEN TO_DATE('{$emissao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$emissao_fim}', 'YYYY-MM-DD')";
        }
       
        
        return "SELECT 
                    dadospedido.id AS \"id\"
                    ,dadospedido.codigo AS \"codigo\"
                    ,dadospedido.CODIGOSAFRA AS \"codigosafra\"
                    ,dadospedido.mae_pedido_id AS \"mae_pedido_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.status) AS \"status\"
                    ,dadospedido.tipo_frete AS \"tipo_frete\"
                    ,dadospedido.cliente_id AS \"cliente_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_cliente) AS \"nome_cliente\"
                    ,dadospedido.vendedor_id AS \"vendedor_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_vendedor) AS \"nome_vendedor\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.tipo_venda) AS \"tipo_venda\"
                    ,dadospedido.agente_id AS \"agente_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_agente) AS \"nome_agente\"
                    ,dadospedido.grupo_compras_id AS \"grupo_compras_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_grupo_compra) AS \"nome_grupo_compra\"
                    ,UTL_RAW.CAST_TO_RAW(CASE WHEN dadosPedido.tipo_parcela = 'R' THEN 'Royalties' WHEN dadosPedido.tipo_parcela = 'G' THEN 'Germoplasma' WHEN dadosPedido.tipo_parcela = 'T' THEN 'TSI' WHEN dadosPedido.tipo_parcela = 'F' THEN 'Frete' ELSE NULL END) AS \"tipo_parcela\"
                    ,TO_CHAR(dadospedido.vencimento_parcela, 'YYYY-MM-DD') AS \"vencimento_parcela\"
                    ,MAX(parc.NUMERO_PARCELA) AS \"numero_parcela\"
                    ,dadospedido.parcela_codigomodalidade AS \"parcela_codigomodalidade\"
                    ,m.DESCRICAO AS \"dsc_modalidade\"
                    ,max(dadospedido.id_tipo_desmembramento) AS \"id_tipo_desmembramento\"
                    ,UTL_RAW.CAST_TO_RAW(max(dadospedido.nome_tipo_desmembramento)) AS \"nome_tipo_desmembramento\"
                    ,TO_CHAR(dadospedido.recebido_em, 'YYYY-MM-DD') AS \"data_pagamento\"
                    ,MAX(parc.preco_parcela) AS \"valor_parcela\"
                    ,dadosPedido.ID_RECEBIMENTO as \"id_recebimento\"
                    ,nvl(SUM(dadospedido.valor), 0) as \"valor_recebido\"
                    ,MAX(dadospedido.juros) AS \"valor_recebido_juros\"
                    ,MAX(dadospedido.desconto) AS \"valor_desconto\"
                    ,(nvl(SUM(dadospedido.valor), 0) + nvl(SUM(dadospedido.juros), 0) - nvl(SUM(dadospedido.desconto), 0)) AS \"valor_liquido\"
                    ,MAX(parc.SALDO)*-1 AS \"saldo_parcela\"
                    ,(CASE WHEN MAX(parc.SALDO) = 0 THEN 'S' ELSE 'N' END) AS \"parcela_paga\"
                    -- germoplasma
                    ,MAX(nvl(dadospedido.preco_total_germoplasma, 0)) AS \"total_germoplasma\"
                    ,SUM(nvl(dadospedido.valor_germoplasma,0)) AS \"recebido_germoplasma\"
                    -- royalties
                    ,MAX(nvl(dadospedido.preco_total_royalties, 0)) AS \"total_royalties\"
                    ,SUM(nvl(dadospedido.valor_royalties,0)) AS \"recebido_royalties\"
                    -- tsi
                    ,MAX(nvl(dadospedido.preco_total_tsi, 0)) AS \"total_tsi\"
                    ,SUM(nvl(dadospedido.valor_tsi,0)) AS \"recebido_tsi\"
                    -- frete
                    ,MAX(nvl(dadospedido.preco_total_frete, 0)) AS \"total_frete\"
                    ,SUM(nvl(dadospedido.valor_frete,0)) AS \"recebido_frete\"
                    ,MAX(parc.boleto_emitido) AS \"boleto_emitido\"
                    ,MAX(parc.duplicata_emitida) AS \"duplicata_emitida\"
                FROM (   
                    SELECT 
                        p.ID
                        ,p.CODIGO
                        ,p.CODIGOSAFRA
                        ,pedidoMae.codigo AS MAE_PEDIDO_ID
                        ,td.id AS id_tipo_desmembramento
                        ,td.nome AS nome_tipo_desmembramento
                        ,(CASE WHEN (ps.status_base = 'Aguardando') THEN ps.status_base || ' ' || nvl(ps.autorizacao_setor_aguardando, '?') || ps.autorizacao_edicao
                            WHEN (ps.status_base = 'Reprovado') THEN ps.status_base || ' por ' || nvl(ps.autorizacao_setor_reprovou, '?') || ps.autorizacao_edicao
                            WHEN (ps.status_base = 'Editando' OR ps.status_base = 'Aprovado') THEN ps.status_base
                            ELSE ps.status_base || ps.autorizacao_edicao END
                        ) AS status 
                        ,CASE WHEN p.TIPO_FRETE = 'c' THEN 'CIF' WHEN p.TIPO_FRETE = 'f' THEN 'FOB' ELSE NULL END AS TIPO_FRETE
                        ,p.CODIGOLOCAL AS CLIENTE_ID
                        ,cli.NOME AS NOME_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
                        ,vend.NAME AS NOME_VENDEDOR
                        ,p.TIPO_VENDA_ID
                        ,tv.NOME AS TIPO_VENDA
                        ,p.AGENTE_CODIGOCLIFOR AS AGENTE_ID
                        ,agt.NOME AS NOME_AGENTE
                        ,p.GRUPO_COMPRA_CODIGOCLIFOR AS GRUPO_COMPRAS_ID
                        ,gc.NOME AS NOME_GRUPO_COMPRA
                        ,vp.tipo_parcela
                        ,r.id as ID_RECEBIMENTO
                        ,r.valor
                        ,r.juros
                        ,r.desconto
                        ,r.obs
                        ,trunc(r.recebido_em) AS recebido_em
                        ,vp.vencimento_parcela
                        ,vp.parcela_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN ip.preco_total_germoplasma ELSE NULL END) AS preco_total_germoplasma
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN r.valor ELSE NULL END) AS valor_germoplasma
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_germoplasma
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN vp.parcela_codigomodalidade ELSE NULL END) AS germoplasma_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN ip.preco_total_royalties ELSE NULL END) AS preco_total_royalties
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN r.valor ELSE NULL END) AS valor_royalties
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_royalties
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN vp.parcela_codigomodalidade ELSE NULL END) AS royalties_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN ip.preco_total_tsi ELSE NULL END) AS preco_total_tsi
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN r.valor ELSE NULL END) AS valor_tsi
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_tsi
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN vp.parcela_codigomodalidade ELSE NULL END) AS tsi_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN p.preco_total_frete ELSE NULL END) AS preco_total_frete
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN r.valor ELSE NULL END) AS valor_frete
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_frete
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN vp.parcela_codigomodalidade ELSE NULL END) AS frete_codigomodalidade
                    FROM (
                        SELECT 
                            p1.id,
                            trunc(p1.vencimento_germoplasma) AS vencimento_parcela,
                            p1.germoplasma_codigomodalidade AS parcela_codigomodalidade,
                            'G' AS tipo_parcela
                        FROM web.pedidos_v2 p1
                        UNION
                        SELECT 
                            p2.id,
                            trunc(p2.vencimento_royalties) AS vencimento_parcela,
                            p2.royalties_codigomodalidade AS parcela_codigomodalidade,
                            'R' AS tipo_parcela
                        FROM web.pedidos_v2 p2
                        UNION
                        SELECT 
                            p3.id,
                            trunc(p3.vencimento_tsi) AS vencimento_parcela,
                            p3.tsi_codigomodalidade AS parcela_codigomodalidade,
                            'T' AS tipo_parcela
                        FROM web.pedidos_v2 p3
                        UNION
                        SELECT 
                            p4.id,
                            trunc(p4.vencimento_frete) AS vencimento_parcela,
                            p4.frete_codigomodalidade AS parcela_codigomodalidade,
                            'F' AS tipo_parcela
                        FROM web.pedidos_v2 p4
                    ) vp --subquery pega todos os vencimentos do pedido.
                    INNER JOIN web.pedidos_v2 p ON vp.id = p.id --JOIN PARA PEGAR TODAS INFORMAÇÕES PEDIDO
                    LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                    INNER JOIN (
                        SELECT i.pedido_id,
                        nvl(SUM(i.preco_total_germoplasma), 0) AS preco_total_germoplasma,
                        nvl(SUM(i.preco_total_royalties), 0) AS preco_total_royalties,
                        nvl(SUM(i.preco_total_tsi), 0) AS preco_total_tsi
                        FROM web.itens_pedido_v2 i
                        GROUP BY i.pedido_id
                    ) ip ON p.id = ip.pedido_id -- JOIN PARA PEGAR INFORMAÇÕES DE ITENS DO PEDIDO
                    LEFT JOIN web.recebimentos r ON (vp.id = r.pedido_id AND vp.tipo_parcela = r.tipo) -- JOIN PARA PEGAR INFORMAÇÕES DE RECEBIMENTO
                    LEFT JOIN web.view_vencimentos_por_data vvpd ON (r.pedido_id = vvpd.pedido_id AND vp.vencimento_parcela = vvpd.vencimento_parcela AND vp.parcela_codigomodalidade = vvpd.parcela_codigomodalidade)
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN EMPRESA.CLIFOR agt ON agt.CODIGOCLIFOR = p.AGENTE_CODIGOCLIFOR 
                    LEFT JOIN EMPRESA.CLIFOR gc ON gc.CODIGOCLIFOR = p.GRUPO_COMPRA_CODIGOCLIFOR 
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID 
                    LEFT JOIN WEB.TIPOS_VENDA tv ON tv.ID  = p.TIPO_VENDA_ID
                    INNER JOIN (
                        SELECT
                            p3.id
                            ,p4.status_base
                            ,(CASE WHEN (status_base = 'Aguardando' AND venda_autorizou IS NULL ) THEN 'Venda'
                                WHEN (status_base = 'Aguardando' AND nvl(t2.slug, '_') != 'mae' AND tipo_frete = 'c' AND logistica_autorizou IS NULL) THEN 'Logística'
                                WHEN (status_base = 'Aguardando' AND (nvl(t2.slug, '_') = 'mae' OR (tipo_frete = 'c' AND logistica_autorizou = '1') OR tipo_frete = 'f') AND diretoria_autorizou IS NULL) THEN 'Gerente Comercial'
                                WHEN (status_base = 'Aguardando' AND (nvl(t2.slug, '_') = 'mae' OR (tipo_frete = 'c' AND logistica_autorizou = '1') OR tipo_frete = 'f') AND diretoria_autorizou = '1' AND comercial_autorizou IS NULL) THEN 'Adm. Comercial'
                            ELSE NULL END
                            ) AS autorizacao_setor_aguardando
                            ,(CASE WHEN (status_base = 'Reprovado' AND nvl(t2.slug, '_') != 'mae' AND tipo_frete = 'c' AND logistica_autorizou = '0') THEN 'Logística'
                                WHEN (status_base = 'Reprovado' AND diretoria_autorizou = '0') THEN 'Gerente Comercial'
                                WHEN (status_base = 'Reprovado' AND comercial_autorizou = '0') THEN 'Adm. Comercial'
                            ELSE NULL END
                            ) AS autorizacao_setor_reprovou,
                            (CASE WHEN (aprovou = '1') THEN ' (Edit.)' ELSE NULL END) AS autorizacao_edicao
                        FROM web.pedidos_v2 p3 
                        INNER JOIN web.view_status_pedidos_v2 p4 ON p3.id = p4.id
                        LEFT JOIN web.tipos_desmembramento t2 ON p3.tipo_desmembramento_id = t2.id
                    ) ps ON ps.id = p.id
                    LEFT JOIN WEB.TIPOS_DESMEMBRAMENTO TD ON TD.ID = P.TIPO_DESMEMBRAMENTO_ID
                    WHERE (
                        (vp.tipo_parcela = 'G' 
                        AND nvl(ip.preco_total_germoplasma, 0) > 0) OR (vp.tipo_parcela = 'R' 
                        AND nvl(ip.preco_total_royalties, 0) > 0) OR (vp.tipo_parcela = 'T' 
                        AND nvl(ip.preco_total_tsi, 0) > 0) OR (vp.tipo_parcela = 'F' 
                        AND nvl(p.preco_total_frete, 0) > 0)
                    )
                    --AND P.CREATED_AT BETWEEN '01/12/2024' AND '30/03/2025'
                    {$wheres}
                    --AND p.id IN (28089,21688)
                    --AND P.CODIGO in (24410074, 24430316)
                    --AND vp.vencimento_parcela >= '25/02/2025'
                ) dadosPedido
                INNER JOIN web.view_vencimentos_por_data parc ON (dadosPedido.id = parc.pedido_id AND dadosPedido.vencimento_parcela = parc.vencimento_parcela AND dadosPedido.parcela_codigomodalidade = parc.parcela_codigomodalidade)
                left join EMPRESA.MODALIDADES m on m.CODIGOMODALIDADE = dadosPedido.parcela_codigomodalidade
                GROUP BY dadosPedido.ID
                    ,dadosPedido.CODIGO
                    ,dadosPedido.CODIGOSAFRA
                    ,dadosPedido.MAE_PEDIDO_ID
                    ,dadosPedido.STATUS
                    ,dadosPedido.TIPO_FRETE
                    ,dadosPedido.CLIENTE_ID	
                    ,dadosPedido.NOME_CLIENTE
                    ,dadosPedido.VENDEDOR_ID
                    ,dadosPedido.NOME_VENDEDOR
                    ,dadosPedido.TIPO_VENDA
                    ,dadosPedido.AGENTE_ID
                    ,dadosPedido.NOME_AGENTE
                    ,dadosPedido.GRUPO_COMPRAS_ID
                    ,dadosPedido.NOME_GRUPO_COMPRA
                    ,dadosPedido.tipo_parcela 
                    ,dadosPedido.VENCIMENTO_PARCELA
                    ,dadospedido.parcela_codigomodalidade
                    ,m.descricao
                    ,dadospedido.recebido_em
                    ,dadosPedido.ID_RECEBIMENTO
                ORDER BY dadosPedido.CLIENTE_ID, dadosPedido.VENCIMENTO_PARCELA, dadospedido.recebido_em ASC";  
    }
    public function getDadosSoftsulDataPagamentoQuery($codigoSafra, $pagamento_inicio = null, $pagamento_fim = null)
    {
        $wheres = "";
        
        if (!empty($codigoSafra)) {
            $wheres .= " AND dadospedido.CODIGOSAFRA = {$codigoSafra}";
        }
        if (!empty($pagamento_inicio)) {
            $pagamento_fim = !empty($pagamento_fim) ? $pagamento_fim : date('Y-m-d');
            $wheres .= " AND dadospedido.recebido_em BETWEEN TO_DATE('{$pagamento_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$pagamento_fim}', 'YYYY-MM-DD')";
        }

       
        
        return "SELECT 
                    dadospedido.id AS \"id\"
                    ,dadospedido.codigo AS \"codigo\"
                    ,dadospedido.CODIGOSAFRA AS \"codigosafra\"
                    ,dadospedido.mae_pedido_id AS \"mae_pedido_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.status) AS \"status\"
                    ,dadospedido.tipo_frete AS \"tipo_frete\"
                    ,dadospedido.cliente_id AS \"cliente_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_cliente) AS \"nome_cliente\"
                    ,dadospedido.vendedor_id AS \"vendedor_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_vendedor) AS \"nome_vendedor\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.tipo_venda) AS \"tipo_venda\"
                    ,dadospedido.agente_id AS \"agente_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_agente) AS \"nome_agente\"
                    ,dadospedido.grupo_compras_id AS \"grupo_compras_id\"
                    ,UTL_RAW.CAST_TO_RAW(dadospedido.nome_grupo_compra) AS \"nome_grupo_compra\"
                    ,UTL_RAW.CAST_TO_RAW(CASE WHEN dadosPedido.tipo_parcela = 'R' THEN 'Royalties' WHEN dadosPedido.tipo_parcela = 'G' THEN 'Germoplasma' WHEN dadosPedido.tipo_parcela = 'T' THEN 'TSI' WHEN dadosPedido.tipo_parcela = 'F' THEN 'Frete' ELSE NULL END) AS \"tipo_parcela\"
                    ,TO_CHAR(dadospedido.vencimento_parcela, 'YYYY-MM-DD') AS \"vencimento_parcela\"
                    ,MAX(parc.NUMERO_PARCELA) AS \"numero_parcela\"
                    ,dadospedido.parcela_codigomodalidade AS \"parcela_codigomodalidade\"
                    ,max(dadospedido.id_tipo_desmembramento) AS \"id_tipo_desmembramento\"
                    ,UTL_RAW.CAST_TO_RAW(max(dadospedido.nome_tipo_desmembramento)) AS \"nome_tipo_desmembramento\"
                    ,TO_CHAR(dadospedido.recebido_em, 'YYYY-MM-DD') AS \"data_pagamento\"
                    ,MAX(parc.preco_parcela) AS \"valor_parcela\"
                    ,dadosPedido.ID_RECEBIMENTO as \"id_recebimento\"
                    ,nvl(SUM(dadospedido.valor), 0) as \"valor_recebido\"
                    ,MAX(dadospedido.juros) AS \"valor_recebido_juros\"
                    ,MAX(dadospedido.desconto) AS \"valor_desconto\"
                    ,(nvl(SUM(dadospedido.valor), 0) + nvl(SUM(dadospedido.juros), 0) - nvl(SUM(dadospedido.desconto), 0)) AS \"valor_liquido\"
                    ,MAX(parc.SALDO)*-1 AS \"saldo_parcela\"
                    ,(CASE WHEN MAX(parc.SALDO) = 0 THEN 'S' ELSE 'N' END) AS \"parcela_paga\"
                    -- germoplasma
                    ,MAX(nvl(dadospedido.preco_total_germoplasma, 0)) AS \"total_germoplasma\"
                    ,SUM(nvl(dadospedido.valor_germoplasma,0)) AS \"recebido_germoplasma\"
                    -- royalties
                    ,MAX(nvl(dadospedido.preco_total_royalties, 0)) AS \"total_royalties\"
                    ,SUM(nvl(dadospedido.valor_royalties,0)) AS \"recebido_royalties\"
                    -- tsi
                    ,MAX(nvl(dadospedido.preco_total_tsi, 0)) AS \"total_tsi\"
                    ,SUM(nvl(dadospedido.valor_tsi,0)) AS \"recebido_tsi\"
                    -- frete
                    ,MAX(nvl(dadospedido.preco_total_frete, 0)) AS \"total_frete\"
                    ,SUM(nvl(dadospedido.valor_frete,0)) AS \"recebido_frete\"
                    ,MAX(parc.boleto_emitido) AS \"boleto_emitido\"
                    ,MAX(parc.duplicata_emitida) AS \"duplicata_emitida\"
                FROM (   
                    SELECT 
                        p.ID
                        ,p.CODIGO
                        ,p.CODIGOSAFRA
                        ,pedidoMae.codigo AS MAE_PEDIDO_ID
                        ,td.id AS id_tipo_desmembramento
                        ,td.nome AS nome_tipo_desmembramento
                        ,(CASE WHEN (ps.status_base = 'Aguardando') THEN ps.status_base || ' ' || nvl(ps.autorizacao_setor_aguardando, '?') || ps.autorizacao_edicao
                            WHEN (ps.status_base = 'Reprovado') THEN ps.status_base || ' por ' || nvl(ps.autorizacao_setor_reprovou, '?') || ps.autorizacao_edicao
                            WHEN (ps.status_base = 'Editando' OR ps.status_base = 'Aprovado') THEN ps.status_base
                            ELSE ps.status_base || ps.autorizacao_edicao END
                        ) AS status 
                        ,CASE WHEN p.TIPO_FRETE = 'c' THEN 'CIF' WHEN p.TIPO_FRETE = 'f' THEN 'FOB' ELSE NULL END AS TIPO_FRETE
                        ,p.CODIGOLOCAL AS CLIENTE_ID
                        ,cli.NOME AS NOME_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
                        ,vend.NAME AS NOME_VENDEDOR
                        ,p.TIPO_VENDA_ID
                        ,tv.NOME AS TIPO_VENDA
                        ,p.AGENTE_CODIGOCLIFOR AS AGENTE_ID
                        ,agt.NOME AS NOME_AGENTE
                        ,p.GRUPO_COMPRA_CODIGOCLIFOR AS GRUPO_COMPRAS_ID
                        ,gc.NOME AS NOME_GRUPO_COMPRA
                        ,vp.tipo_parcela
                        ,r.id as ID_RECEBIMENTO
                        ,r.valor
                        ,r.juros
                        ,r.desconto
                        ,r.obs
                        ,trunc(r.recebido_em) AS recebido_em
                        ,vp.vencimento_parcela
                        ,vp.parcela_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN ip.preco_total_germoplasma ELSE NULL END) AS preco_total_germoplasma
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN r.valor ELSE NULL END) AS valor_germoplasma
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_germoplasma
                        ,(CASE WHEN vp.tipo_parcela = 'G' THEN vp.parcela_codigomodalidade ELSE NULL END) AS germoplasma_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN ip.preco_total_royalties ELSE NULL END) AS preco_total_royalties
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN r.valor ELSE NULL END) AS valor_royalties
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_royalties
                        ,(CASE WHEN vp.tipo_parcela = 'R' THEN vp.parcela_codigomodalidade ELSE NULL END) AS royalties_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN ip.preco_total_tsi ELSE NULL END) AS preco_total_tsi
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN r.valor ELSE NULL END) AS valor_tsi
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_tsi
                        ,(CASE WHEN vp.tipo_parcela = 'T' THEN vp.parcela_codigomodalidade ELSE NULL END) AS tsi_codigomodalidade
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN p.preco_total_frete ELSE NULL END) AS preco_total_frete
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN r.valor ELSE NULL END) AS valor_frete
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN vp.vencimento_parcela ELSE NULL END) AS vencimento_frete
                        ,(CASE WHEN vp.tipo_parcela = 'F' THEN vp.parcela_codigomodalidade ELSE NULL END) AS frete_codigomodalidade
                    FROM (
                        SELECT 
                            p1.id,
                            trunc(p1.vencimento_germoplasma) AS vencimento_parcela,
                            p1.germoplasma_codigomodalidade AS parcela_codigomodalidade,
                            'G' AS tipo_parcela
                        FROM web.pedidos_v2 p1
                        UNION
                        SELECT 
                            p2.id,
                            trunc(p2.vencimento_royalties) AS vencimento_parcela,
                            p2.royalties_codigomodalidade AS parcela_codigomodalidade,
                            'R' AS tipo_parcela
                        FROM web.pedidos_v2 p2
                        UNION
                        SELECT 
                            p3.id,
                            trunc(p3.vencimento_tsi) AS vencimento_parcela,
                            p3.tsi_codigomodalidade AS parcela_codigomodalidade,
                            'T' AS tipo_parcela
                        FROM web.pedidos_v2 p3
                        UNION
                        SELECT 
                            p4.id,
                            trunc(p4.vencimento_frete) AS vencimento_parcela,
                            p4.frete_codigomodalidade AS parcela_codigomodalidade,
                            'F' AS tipo_parcela
                        FROM web.pedidos_v2 p4
                    ) vp --subquery pega todos os vencimentos do pedido.
                    INNER JOIN web.pedidos_v2 p ON vp.id = p.id --JOIN PARA PEGAR TODAS INFORMAÇÕES PEDIDO
                    LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                    INNER JOIN (
                        SELECT i.pedido_id,
                        nvl(SUM(i.preco_total_germoplasma), 0) AS preco_total_germoplasma,
                        nvl(SUM(i.preco_total_royalties), 0) AS preco_total_royalties,
                        nvl(SUM(i.preco_total_tsi), 0) AS preco_total_tsi
                        FROM web.itens_pedido_v2 i
                        GROUP BY i.pedido_id
                    ) ip ON p.id = ip.pedido_id -- JOIN PARA PEGAR INFORMAÇÕES DE ITENS DO PEDIDO
                    LEFT JOIN web.recebimentos r ON (vp.id = r.pedido_id AND vp.tipo_parcela = r.tipo) -- JOIN PARA PEGAR INFORMAÇÕES DE RECEBIMENTO
                    LEFT JOIN web.view_vencimentos_por_data vvpd ON (r.pedido_id = vvpd.pedido_id AND vp.vencimento_parcela = vvpd.vencimento_parcela AND vp.parcela_codigomodalidade = vvpd.parcela_codigomodalidade)
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN EMPRESA.CLIFOR agt ON agt.CODIGOCLIFOR = p.AGENTE_CODIGOCLIFOR 
                    LEFT JOIN EMPRESA.CLIFOR gc ON gc.CODIGOCLIFOR = p.GRUPO_COMPRA_CODIGOCLIFOR 
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID 
                    LEFT JOIN WEB.TIPOS_VENDA tv ON tv.ID  = p.TIPO_VENDA_ID
                    INNER JOIN (
                        SELECT
                            p3.id
                            ,p4.status_base
                            ,(CASE WHEN (status_base = 'Aguardando' AND venda_autorizou IS NULL ) THEN 'Venda'
                                WHEN (status_base = 'Aguardando' AND nvl(t2.slug, '_') != 'mae' AND tipo_frete = 'c' AND logistica_autorizou IS NULL) THEN 'Logística'
                                WHEN (status_base = 'Aguardando' AND (nvl(t2.slug, '_') = 'mae' OR (tipo_frete = 'c' AND logistica_autorizou = '1') OR tipo_frete = 'f') AND diretoria_autorizou IS NULL) THEN 'Gerente Comercial'
                                WHEN (status_base = 'Aguardando' AND (nvl(t2.slug, '_') = 'mae' OR (tipo_frete = 'c' AND logistica_autorizou = '1') OR tipo_frete = 'f') AND diretoria_autorizou = '1' AND comercial_autorizou IS NULL) THEN 'Adm. Comercial'
                            ELSE NULL END
                            ) AS autorizacao_setor_aguardando
                            ,(CASE WHEN (status_base = 'Reprovado' AND nvl(t2.slug, '_') != 'mae' AND tipo_frete = 'c' AND logistica_autorizou = '0') THEN 'Logística'
                                WHEN (status_base = 'Reprovado' AND diretoria_autorizou = '0') THEN 'Gerente Comercial'
                                WHEN (status_base = 'Reprovado' AND comercial_autorizou = '0') THEN 'Adm. Comercial'
                            ELSE NULL END
                            ) AS autorizacao_setor_reprovou,
                            (CASE WHEN (aprovou = '1') THEN ' (Edit.)' ELSE NULL END) AS autorizacao_edicao
                        FROM web.pedidos_v2 p3 
                        INNER JOIN web.view_status_pedidos_v2 p4 ON p3.id = p4.id
                        LEFT JOIN web.tipos_desmembramento t2 ON p3.tipo_desmembramento_id = t2.id
                    ) ps ON ps.id = p.id
                    LEFT JOIN WEB.TIPOS_DESMEMBRAMENTO TD ON TD.ID = P.TIPO_DESMEMBRAMENTO_ID
                    WHERE (
                        (vp.tipo_parcela = 'G' 
                        AND nvl(ip.preco_total_germoplasma, 0) > 0) OR (vp.tipo_parcela = 'R' 
                        AND nvl(ip.preco_total_royalties, 0) > 0) OR (vp.tipo_parcela = 'T' 
                        AND nvl(ip.preco_total_tsi, 0) > 0) OR (vp.tipo_parcela = 'F' 
                        AND nvl(p.preco_total_frete, 0) > 0)
                    )
                ) dadosPedido
                INNER JOIN web.view_vencimentos_por_data parc ON (dadosPedido.id = parc.pedido_id AND dadosPedido.vencimento_parcela = parc.vencimento_parcela AND dadosPedido.parcela_codigomodalidade = parc.parcela_codigomodalidade)
                where 1 = 1
                {$wheres}
                GROUP BY dadosPedido.ID
                    ,dadosPedido.CODIGO
                    ,dadosPedido.CODIGOSAFRA
                    ,dadosPedido.MAE_PEDIDO_ID
                    ,dadosPedido.STATUS
                    ,dadosPedido.TIPO_FRETE
                    ,dadosPedido.CLIENTE_ID	
                    ,dadosPedido.NOME_CLIENTE
                    ,dadosPedido.VENDEDOR_ID
                    ,dadosPedido.NOME_VENDEDOR
                    ,dadosPedido.TIPO_VENDA
                    ,dadosPedido.AGENTE_ID
                    ,dadosPedido.NOME_AGENTE
                    ,dadosPedido.GRUPO_COMPRAS_ID
                    ,dadosPedido.NOME_GRUPO_COMPRA
                    ,dadosPedido.tipo_parcela 
                    ,dadosPedido.VENCIMENTO_PARCELA
                    ,dadospedido.parcela_codigomodalidade
                    ,dadospedido.recebido_em
                    ,dadosPedido.ID_RECEBIMENTO";  
    }
    public function getDadosControlRecebimentoDataPagamentoQuery($codigoSafra, $pagamento_inicio, $pagamento_fim)
    {
        $wheres = "";
        
        if (!empty($codigoSafra)) {
            $wheres .= " AND cr.codigosafra = {$codigoSafra}";
        }
        if (!empty($pagamento_inicio)) {
            $pagamento_fim = !empty($pagamento_fim) ? $pagamento_fim : date('Y-m-d');
            $wheres .= " AND cr.data_pagamento BETWEEN TO_DATE('{$pagamento_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$pagamento_fim}', 'YYYY-MM-DD')";
        }

        return "SELECT * 
                FROM controle_recebimento cr
                where 1 = 1
                {$wheres}"; 
    }
    public function getLookupSafraQuery()
    {
        return "SELECT 
                    S.CODIGOSAFRA AS \"codigosafra\", 
                    UTL_RAW.CAST_TO_RAW(CAST(S.CODIGOSAFRA AS VARCHAR(255)) || ' - ' || S.ANO || ' - ' || REGEXP_REPLACE(C.DESCRICAO, '\s+', ' ') || ' - ' || REGEXP_REPLACE(CLIFOR.NOME, '\s+', ' '))  AS \"dsc\"
                FROM ALMOX.SAFRAS S
                INNER JOIN ALMOX.CULTURAS C ON C.CODIGOCULTURA = S.CODIGOCULTURA
                INNER JOIN EMPRESA.CLIFOR CLIFOR ON CLIFOR.CODIGOCLIFOR  = S.CODIGOCLIFOR 
                ORDER BY CODIGOSAFRA"; 
    }



    #region Controle Documentos
        public function getDadosSoftsulPedidoQuery($codigoSafra, $emissao_inicio = null, $emissao_fim = null)
        {
            $ands = "";
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            if (!empty($emissao_inicio)) {
                $emissao_fim = !empty($emissao_fim) ? $emissao_fim : date('Y-m-d');
                $ands .= " AND P.CREATED_AT BETWEEN TO_DATE('{$emissao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$emissao_fim}', 'YYYY-MM-DD')";
            }
        
            return "SELECT * FROM (
                    SELECT  
                         P.ID AS ID_PEDIDO
                        ,P.CODIGO AS CODIGO_PEDIDO
                        ,pedidoMae.CODIGO AS MAE_PEDIDO_ID 
                        ,pedidoOrigem.CODIGO AS ORIGEM_PEDIDO_ID 
                        ,TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO
                        ,P.CODIGOSAFRA 
                        ,EXTRACT(YEAR FROM S.INICIO) ANO_SAFRA
                        ,CASE WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' ELSE NULL END AS TIPO_PESSOA
                        ,CLI.CODIGOCLIFOR AS ID_CLIENTE
                        ,CLI.NOME AS NOME_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
		                ,vend.NAME AS NOME_VENDEDOR
                        ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                        ,SUM(IP.QUANT) AS QUANTIDADE
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA
                        ,MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES  
                        ,TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES
                        ,MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI
                        ,MAX(TM.DESCRICAO) AS PAGAMENTO_TSI
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,TO_CHAR(MAX(P.VENCIMENTO_FRETE), 'YYYY-MM-DD') AS VENCIMENTO_FRETE
                        ,MAX(FM.DESCRICAO) AS PAGAMENTO_FRETE
                        ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,(  CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA, 0)) ELSE 0 END +
                            CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_ROYALTIES, 0)) ELSE 0 END +
                            CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_TSI, 0)) ELSE 0 END +
                            CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO) THEN MAX(NVL(P.PRECO_TOTAL_FRETE, 0)) ELSE 0 END
                        ) AS PRECO_TOTAL_PRAZO_SAFRA
                        ,CASE WHEN 
                                EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO)
                            OR EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO)
                            OR EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO)
                            OR EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO)
                            THEN 'Prazo Safra'
                            ELSE 'Prazo Ano'
                        END AS TIPO_PRAZO
                    FROM web.pedidos_v2 p
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN web.itens_pedido_v2 ip ON ip.PEDIDO_ID = p.ID 
                    LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                    LEFT JOIN EMPRESA.MODALIDADES GM ON GM.CODIGOMODALIDADE  = P.GERMOPLASMA_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES RM ON RM.CODIGOMODALIDADE  = P.ROYALTIES_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES TM ON TM.CODIGOMODALIDADE  = P.TSI_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES FM ON FM.CODIGOMODALIDADE  = P.FRETE_CODIGOMODALIDADE 
                    LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                    LEFT JOIN web.pedidos_v2 pedidoOrigem ON pedidoOrigem.id = p.ORIGEM_PEDIDO_ID
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                    ORDER BY P.CREATED_AT desc
                    ) A
                    WHERE A.TIPO_PRAZO = 'Prazo Safra'";  
        }
        public function getStatusDocumentoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                            count(distinct dp.id_documento) qtd
                            ,(select count(distinct id) from documentos where documentos.flg_documento_obrigatorio = true and documentos.ativo = true AND documentos.tipo_pessoa = '{$tipoPessoa}') qtd_documentos_obrigatorios
                        FROM documentos_pedido dp
                        LEFT JOIN documentos d ON d.id = dp.id_documento 
                        WHERE d.ativo = true 
                        AND d.flg_documento_obrigatorio = true
                        AND dp.ativo = true
                        AND id_pedido = {$idPedido}
                        AND d.tipo_pessoa = '{$tipoPessoa}'
                ";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getStatusDuplicatasQuery($idPedido)
        {
            if (!empty($idPedido)) {
                $sql = "SELECT
                            count(distinct dbp.id_parcela_pedido) qtd
                        FROM duplicata_boleto_pedido dbp
                        WHERE dbp.duplicata_recebido  = true
                        and dbp.id_pedido = {$idPedido}
                        GROUP by dbp.id_pedido;";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getStatusCPRQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido gp
                        left join garantias g on g.id = gp.id_garantia 
                        where g.ativo = true 
                        and gp.ativo = true
                        and g.flg_cpr = true
                        and id_pedido = {$idPedido}
                        AND g.tipo_pessoa = '{$tipoPessoa}'";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getStatusInstrumentoFiancaQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido gp
                        left join garantias g on g.id = gp.id_garantia 
                        where g.ativo = true 
                        and gp.ativo = true
                        and g.flg_instrumento_fianca = true
                        and id_pedido = {$idPedido}
                        AND g.tipo_pessoa = '{$tipoPessoa}'";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getStatusConfissaoDividaQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido gp
                        left join garantias g on g.id = gp.id_garantia 
                        where g.ativo = true 
                        and gp.ativo = true
                        and g.flg_confissao_divida = true
                        and id_pedido = {$idPedido}
                        AND g.tipo_pessoa = '{$tipoPessoa}'";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getStatusGarantiasQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                            count(distinct gp.id_garantia) qtd
                            ,(
                                --Considera apenas 1 flg_instrumento_fianca
                                CASE WHEN EXISTS (
                                    SELECT 1
                                    from garantias 
                                    where garantias.ativo = true
                                    and garantias.flg_instrumento_fianca = true
                                    and garantias.tipo_pessoa = '{$tipoPessoa}'
                                ) THEN 1 ELSE 0 end + 
                                --Considera apenas 1 flg_cpr
                                CASE WHEN EXISTS (
                                    SELECT 1
                                    from garantias 
                                    where garantias.ativo = true
                                    and garantias.flg_cpr = true
                                    and garantias.tipo_pessoa = '{$tipoPessoa}'
                                ) THEN 1 ELSE 0 end +
                                --Considera apenas 1 flg_confissao_divida
                                CASE WHEN EXISTS (
                                    SELECT 1
                                    from garantias 
                                    where garantias.ativo = true
                                    and garantias.flg_confissao_divida = true
                                    and garantias.tipo_pessoa = '{$tipoPessoa}'
                                ) THEN 1 ELSE 0 end +
                                (select count(distinct id) from garantias where garantias.ativo = true and flg_instrumento_fianca = false and flg_cpr = false and flg_confissao_divida = false AND garantias.tipo_pessoa = '{$tipoPessoa}')
                            ) qtd_garantias
                        FROM garantias_pedido gp
                        LEFT JOIN garantias g ON g.id = gp.id_garantia 
                        WHERE g.ativo = true 
                        AND gp.ativo = true
                        AND gp.id_pedido = {$idPedido}
                        AND g.tipo_pessoa = '{$tipoPessoa}'
                ";
            } else {
                $sql = "";
            }

            return $sql;
        }

        
        public function getPedidosGrupoClienteSafra($grupoClienteID, $codigoSafra)
        {
            if (!empty($grupoClienteID) && !empty($codigoSafra)) {
                $sql = "SELECT ID_PEDIDO FROM (
                            SELECT  
                                P.ID AS ID_PEDIDO
                                ,P.CODIGO AS CODIGO_PEDIDO
                                ,pedidoMae.CODIGO AS MAE_PEDIDO_ID 
                                ,pedidoOrigem.CODIGO AS ORIGEM_PEDIDO_ID 
                                ,TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO
                                ,P.CODIGOSAFRA 
                                ,EXTRACT(YEAR FROM S.INICIO) ANO_SAFRA
                                ,CASE WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' ELSE NULL END AS TIPO_PESSOA
                                ,CLI.CODIGOCLIFOR AS ID_CLIENTE
                                ,CLI.NOME AS NOME_CLIENTE
                                ,p.RTV_USER_ID AS VENDEDOR_ID
                                ,vend.NAME AS NOME_VENDEDOR
                                ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                                ,SUM(IP.QUANT) AS QUANTIDADE
                                ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                                ,TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA
                                ,MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA
                                ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES  
                                ,TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES
                                ,MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES
                                ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                                ,TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI
                                ,MAX(TM.DESCRICAO) AS PAGAMENTO_TSI
                                ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                                ,TO_CHAR(MAX(P.VENCIMENTO_FRETE), 'YYYY-MM-DD') AS VENCIMENTO_FRETE
                                ,MAX(FM.DESCRICAO) AS PAGAMENTO_FRETE
                                ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                                ,(  CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA, 0)) ELSE 0 END +
                                    CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_ROYALTIES, 0)) ELSE 0 END +
                                    CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_TSI, 0)) ELSE 0 END +
                                    CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO) THEN MAX(NVL(P.PRECO_TOTAL_FRETE, 0)) ELSE 0 END
                                ) AS PRECO_TOTAL_PRAZO_SAFRA
                                ,CASE WHEN 
                                        EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO)
                                    OR EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO)
                                    OR EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO)
                                    OR EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO)
                                    THEN 'Prazo Safra'
                                    ELSE 'Prazo Ano'
                                END AS TIPO_PRAZO
                            FROM web.pedidos_v2 p
                            LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                            LEFT JOIN web.itens_pedido_v2 ip ON ip.PEDIDO_ID = p.ID 
                            LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                            LEFT JOIN EMPRESA.MODALIDADES GM ON GM.CODIGOMODALIDADE  = P.GERMOPLASMA_CODIGOMODALIDADE 
                            LEFT JOIN EMPRESA.MODALIDADES RM ON RM.CODIGOMODALIDADE  = P.ROYALTIES_CODIGOMODALIDADE 
                            LEFT JOIN EMPRESA.MODALIDADES TM ON TM.CODIGOMODALIDADE  = P.TSI_CODIGOMODALIDADE 
                            LEFT JOIN EMPRESA.MODALIDADES FM ON FM.CODIGOMODALIDADE  = P.FRETE_CODIGOMODALIDADE 
                            LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                            LEFT JOIN web.pedidos_v2 pedidoOrigem ON pedidoOrigem.id = p.ORIGEM_PEDIDO_ID
                            LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                            LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                            WHERE IP.CODIGOCULTIVAR IS NOT NULL
                            AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                            AND CLISENIOR.CODGRE = {$grupoClienteID}
                            AND P.CODIGOSAFRA = {$codigoSafra}
                            GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                            ORDER BY P.CREATED_AT desc
                        ) A
                        WHERE A.TIPO_PRAZO = 'Prazo Safra'
                ";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getDocumentosPedidoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT DISTINCT
                            d.id AS id_documento,
                            dp.id_pedido,
                            d.dsc_documento,
                            COALESCE(dp.ativo, false) AS ativo
                        FROM documentos d
                        LEFT JOIN documentos_pedido dp ON dp.id_documento = d.id AND dp.id_pedido = {$idPedido}
                        WHERE d.ativo = true
                        and d.tipo_pessoa = '{$tipoPessoa}'
                        ORDER BY d.id
                ";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getObservacoesPedidoQuery($idPedido)
        {
            if (!empty($idPedido)) {
                $sql = "SELECT observacao FROM observacoes_pedido WHERE id_pedido = {$idPedido}";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getGarantiasPedidoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql =" SELECT DISTINCT
                            garantias.id as id_garantia,
                            garantias_pedido.id_pedido,
                            garantias.dsc_garantia,
                            COALESCE(garantias_pedido.ativo, false) as ativo
                        FROM garantias
                        LEFT JOIN garantias_pedido ON garantias_pedido.id_garantia = garantias.id AND garantias_pedido.id_pedido = {$idPedido}
                        WHERE garantias.ativo = true
                        and garantias.tipo_pessoa = '{$tipoPessoa}'
                        ORDER BY id_garantia";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getDuplicatasBoletosPedidoOracleQuery($idPedido)
        {
            if (!empty($idPedido)) {
                $sql ="  SELECT
                            ID,
                            PEDIDO_ID,
                            TO_CHAR(VENCIMENTO, 'YYYY-MM-DD') AS VENCIMENTO_PARCELA,
                            CASE WHEN BOLETO_EMITIDO = 1 THEN 1 ELSE 0 END AS BOLETO_EMITIDO,
                            CASE WHEN DUPLICATA_EMITIDA = 1 THEN 1 ELSE 0 END AS DUPLICATA_EMITIDA
                        FROM WEB.PARCELAS_PEDIDO_V2
                        WHERE PEDIDO_ID = {$idPedido}";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getDuplicatasBoletosPedidoPostgresQuery($idPedido, $id_parcela_pedido)
        {
            if (!empty($idPedido) && !empty($id_parcela_pedido)) {
                $sql =" SELECT 
                            id_duplicata_boleto_pedido, 
                            id_pedido, 
                            id_parcela_pedido, 
                            data_insercao, boleto_recebido, 
                            duplicata_recebido 
                        FROM duplicata_boleto_pedido
                        WHERE id_pedido = {$idPedido}
                        AND id_parcela_pedido = {$id_parcela_pedido}";
            } else {
                $sql = "";
            }

            return $sql;
        }


        #region Cadastro Documentos
            public function listarDocumentos($skip, $take, $sort = null)
            {
                $sql = 'SELECT id, dsc_documento, ativo, flg_documento_obrigatorio, tipo_pessoa FROM documentos order by id';

                if ($sort) {
                    $sort = json_decode($sort, true);
                    $orderBy = array_map(function ($item) {
                        return $item['selector'] . ' ' . ($item['desc'] ? 'DESC' : 'ASC');
                    }, $sort);
                    $sql .= ' ORDER BY ' . implode(', ', $orderBy);
                }

                $sql .= ' LIMIT :take OFFSET :skip';

                $statement = $this->adapter->createStatement($sql);
                $result = $statement->execute([
                    ':take' => $take,
                    ':skip' => $skip,
                ]);

                $data = [];
                foreach ($result as $row) {
                    $data[] = $row;
                }

                $totalCount = $this->adapter->query('SELECT COUNT(*) FROM documentos')->execute()->current()['count'];

                return [
                    'data' => $data,
                    'totalCount' => $totalCount,
                ];
            }
            public function inserirDocumento(array $data)
            {
                if (empty($data['dsc_documento'])) {
                    throw new \Exception('Descrição do documento é obrigatória.');
                }

                $sql = 'INSERT INTO documentos (dsc_documento, ativo, flg_documento_obrigatorio, tipo_pessoa) 
                        VALUES (:dsc_documento, :ativo, :flg_documento_obrigatorio, :tipo_pessoa)';
                        
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([
                    ':dsc_documento' => $data['dsc_documento'],
                    ':ativo' => $data['ativo'] ?? true,
                    ':flg_documento_obrigatorio' => $data['flg_documento_obrigatorio'] ?? false,
                    ':tipo_pessoa' => !empty($data['tipo_pessoa']) ? $data['tipo_pessoa'] : null,
                ]);
            }
            public function atualizarDocumento(array $data)
            {
                $sql = 'UPDATE documentos SET 
                        dsc_documento = :dsc_documento, 
                        ativo = :ativo, 
                        flg_documento_obrigatorio = :flg_documento_obrigatorio,
                        tipo_pessoa = :tipo_pessoa
                        WHERE id = :id';
                        
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([
                    ':dsc_documento' => $data['dsc_documento'],
                    ':ativo' => $data['ativo'] ?? true,
                    ':flg_documento_obrigatorio' => $data['flg_documento_obrigatorio'] ?? false,
                    ':tipo_pessoa' => !empty($data['tipo_pessoa']) ? $data['tipo_pessoa'] : null,
                    ':id' => $data['id'],
                ]);
            }
            public function excluirDocumento($id)
            {
                if (empty($id)) {
                    throw new \Exception('ID do documento não fornecido.');
                }

                $sql = 'DELETE FROM documentos WHERE id = :id';
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([':id' => $id]);
            }
        #endregion
        
        #region Cadastro Garantias
            public function listarGarantias($skip, $take, $sort = null)
            {
                $sql = 'SELECT id, dsc_garantia, ativo, flg_instrumento_fianca, flg_confissao_divida, flg_cpr, flg_garantia_obrigatorio, tipo_pessoa FROM garantias order by id';

                if ($sort) {
                    $sort = json_decode($sort, true);
                    $orderBy = array_map(function ($item) {
                        return $item['selector'] . ' ' . ($item['desc'] ? 'DESC' : 'ASC');
                    }, $sort);
                    $sql .= ' ORDER BY ' . implode(', ', $orderBy);
                }

                $sql .= ' LIMIT :take OFFSET :skip';

                $statement = $this->adapter->createStatement($sql);
                $result = $statement->execute([
                    ':take' => $take,
                    ':skip' => $skip,
                ]);

                $data = [];
                foreach ($result as $row) {
                    $data[] = $row;
                }

                $totalCount = $this->adapter->query('SELECT COUNT(*) FROM garantias')->execute()->current()['count'];

                return [
                    'data' => $data,
                    'totalCount' => $totalCount,
                ];
            }
            public function inserirGarantia(array $data)
            {
                if (empty($data['dsc_garantia'])) {
                    throw new \Exception('Descrição da garantia é obrigatória.');
                }

                $sql = 'INSERT INTO garantias 
                        (dsc_garantia, ativo, flg_instrumento_fianca, flg_confissao_divida, flg_cpr, flg_garantia_obrigatorio, tipo_pessoa) 
                        VALUES 
                        (:dsc_garantia, :ativo, :flg_instrumento_fianca, :flg_confissao_divida, :flg_cpr, :flg_garantia_obrigatorio, :tipo_pessoa)';
                        
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([
                    ':dsc_garantia' => $data['dsc_garantia'],
                    ':ativo' => $data['ativo'] ?? true,
                    ':flg_instrumento_fianca' => $data['flg_instrumento_fianca'] ?? false,
                    ':flg_confissao_divida' => $data['flg_confissao_divida'] ?? false,
                    ':flg_cpr' => $data['flg_cpr'] ?? false,
                    ':flg_garantia_obrigatorio' => $data['flg_garantia_obrigatorio'] ?? false,
                    ':tipo_pessoa' => !empty($data['tipo_pessoa']) ? $data['tipo_pessoa'] : null,
                ]);
            }
            public function atualizarGarantia(array $data)
            {
                $sql = 'UPDATE garantias SET 
                        dsc_garantia = :dsc_garantia, 
                        ativo = :ativo,
                        flg_instrumento_fianca = :flg_instrumento_fianca,
                        flg_confissao_divida = :flg_confissao_divida,
                        flg_cpr = :flg_cpr,
                        flg_garantia_obrigatorio = :flg_garantia_obrigatorio,
                        tipo_pessoa = :tipo_pessoa
                        WHERE id = :id';
                        
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([
                    ':dsc_garantia' => $data['dsc_garantia'],
                    ':ativo' => $data['ativo'] ?? true,
                    ':flg_instrumento_fianca' => $data['flg_instrumento_fianca'] ?? false,
                    ':flg_confissao_divida' => $data['flg_confissao_divida'] ?? false,
                    ':flg_cpr' => $data['flg_cpr'] ?? false,
                    ':flg_garantia_obrigatorio' => $data['flg_garantia_obrigatorio'] ?? false,
                    ':tipo_pessoa' => !empty($data['tipo_pessoa']) ? $data['tipo_pessoa'] : null,
                    ':id' => $data['id'],
                ]);
            }
            public function excluirGarantia($id)
            {
                if (empty($id)) {
                    throw new \Exception('ID da garantia não fornecido.');
                }

                $sql = 'DELETE FROM garantias WHERE id = :id';
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([':id' => $id]);
            }
        #endregion

    #endregion

}
