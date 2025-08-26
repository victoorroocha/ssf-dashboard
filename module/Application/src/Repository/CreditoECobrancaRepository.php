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
        public function getDadosSoftsulPedidoQuery($codigoSafra, $emissao_inicio = null, $emissao_fim = null, $key = null, $search = null, $skip, $take, $isLoadingAll, $filterArray = null)
        {
            $ands = "";
            $andsFilter = "";
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            if (!empty($emissao_inicio)) {
                $emissao_fim = !empty($emissao_fim) ? $emissao_fim : date('Y-m-d');
                $ands .= " AND P.CREATED_AT BETWEEN TO_DATE('{$emissao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$emissao_fim}', 'YYYY-MM-DD')";
            }

            if (!empty($search)) {
                $ands .= " AND (P.ID LIKE '%$search%' OR P.CODIGO LIKE '%$search%')";
            }
            if (!empty($key)) {
                $ands .= " AND P.ID = $key";
            }

            if ($filterArray) {
                $parsedFilter = $this->parseDxFilter($filterArray);
                if (!empty($parsedFilter)) {
                    $andsFilter .= " AND ($parsedFilter)";
                }
            }

            if ($isLoadingAll == 'true') {
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
                        ,max(CLISENIOR.CGCCPF) CGC_CPF_CLIENTE
                        ,max(CLISENIOR.ENDCLI) ENDERECO_CLIENTE
                        ,max(CLISENIOR.CPLEND) CPL_ENDERECO_CLIENTE
                        ,max(CLISENIOR.CEPCLI) CEP_CLIENTE
                        ,max(CLISENIOR.BAICLI) BAIRRO_CLIENTE
                        ,max(CLISENIOR.CIDCLI) CIDADE_CLIENTE
                        ,max(CLISENIOR.SIGUFS) ESTADO_CLIENTE
                        ,max(CLISENIOR.FONCLI) FONE1_CLIENTE
                        ,max(CLISENIOR.FONCL2) FONE2_CLIENTE
                        ,max(CLISENIOR.INSEST) INSEST_CLIENTE
                        ,max(CLISENIOR.EMANFE) EMANFE_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
		                ,vend.NAME AS NOME_VENDEDOR
                        ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                        ,nvl(MAX(E069GRE.NOMGRE), 'SEM GRUPO CLIENTE') AS NOME_GRUPO_CLIENTE
                        ,SUM(IP.QUANT) AS QUANTIDADE
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'G'),0) AS PRECO_TOTAL_GERMOPLASMA_SALDO_REAL
                        ,TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA
                        ,MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES  
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'R'),0) AS PRECO_TOTAL_ROYALTIES_SALDO_REAL  
                        ,TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES
                        ,MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'T'),0) AS PRECO_TOTAL_TSI_SALDO_REAL 
                        ,TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI
                        ,MAX(TM.DESCRICAO) AS PAGAMENTO_TSI
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'F'),0) AS PRECO_TOTAL_FRETE_SALDO_REAL  
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
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.CODIGOCLIFOR
                    LEFT JOIN SAPIENS.E069GRE E069GRE ON E069GRE.CODGRE = CLISENIOR.CODGRE
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                    ORDER BY P.CREATED_AT desc
                    ) A
                    WHERE A.TIPO_PRAZO = 'Prazo Safra'
                    ORDER BY A.NOME_CLIENTE ASC";  
            } else {
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
                        ,max(CLISENIOR.CGCCPF) CGC_CPF_CLIENTE
                        ,max(CLISENIOR.ENDCLI) ENDERECO_CLIENTE
                        ,max(CLISENIOR.CPLEND) CPL_ENDERECO_CLIENTE
                        ,max(CLISENIOR.CEPCLI) CEP_CLIENTE
                        ,max(CLISENIOR.BAICLI) BAIRRO_CLIENTE
                        ,max(CLISENIOR.CIDCLI) CIDADE_CLIENTE
                        ,max(CLISENIOR.SIGUFS) ESTADO_CLIENTE
                        ,max(CLISENIOR.FONCLI) FONE1_CLIENTE
                        ,max(CLISENIOR.FONCL2) FONE2_CLIENTE
                        ,max(CLISENIOR.INSEST) INSEST_CLIENTE
                        ,max(CLISENIOR.EMANFE) EMANFE_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
		                ,vend.NAME AS NOME_VENDEDOR
                        ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                        ,nvl(MAX(E069GRE.NOMGRE), 'SEM GRUPO CLIENTE') AS NOME_GRUPO_CLIENTE
                        ,SUM(IP.QUANT) AS QUANTIDADE
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'G'),0) AS PRECO_TOTAL_GERMOPLASMA_SALDO_REAL
                        ,TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA
                        ,MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES  
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'R'),0) AS PRECO_TOTAL_ROYALTIES_SALDO_REAL  
                        ,TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES
                        ,MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'T'),0) AS PRECO_TOTAL_TSI_SALDO_REAL 
                        ,TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI
                        ,MAX(TM.DESCRICAO) AS PAGAMENTO_TSI
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'F'),0) AS PRECO_TOTAL_FRETE_SALDO_REAL  
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
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.CODIGOCLIFOR
                    LEFT JOIN SAPIENS.E069GRE E069GRE ON E069GRE.CODGRE = CLISENIOR.CODGRE
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                    ORDER BY P.CREATED_AT desc
                    ) A
                    WHERE A.TIPO_PRAZO = 'Prazo Safra'
                    {$andsFilter}
                    ORDER BY A.NOME_CLIENTE ASC
                    OFFSET {$skip} ROWS FETCH NEXT {$take} ROWS ONLY";  
            }
        }
        public function getDadosSoftsulPedidoCountQuery($codigoSafra, $emissao_inicio = null, $emissao_fim = null, $key = null, $search = null, $skip, $take, $isLoadingAll, $filterArray = null)
        {
            $ands = "";
            $andsFilter = "";
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            if (!empty($emissao_inicio)) {
                $emissao_fim = !empty($emissao_fim) ? $emissao_fim : date('Y-m-d');
                $ands .= " AND P.CREATED_AT BETWEEN TO_DATE('{$emissao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$emissao_fim}', 'YYYY-MM-DD')";
            }

            if (!empty($search)) {
                $ands .= " AND (P.ID LIKE '%$search%' OR P.CODIGO LIKE '%$search%')";
            }
            if (!empty($key)) {
                $ands .= " AND P.ID = $key";
            }
            if ($filterArray) {
                $parsedFilter = $this->parseDxFilter($filterArray);
                if (!empty($parsedFilter)) {
                    $andsFilter .= " AND ($parsedFilter)";
                }
            }

            return "SELECT COUNT(*) AS TOTAL FROM (
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
                        ,max(CLISENIOR.CGCCPF) CGC_CPF_CLIENTE
                        ,max(CLISENIOR.ENDCLI) ENDERECO_CLIENTE
                        ,max(CLISENIOR.CPLEND) CPL_ENDERECO_CLIENTE
                        ,max(CLISENIOR.CEPCLI) CEP_CLIENTE
                        ,max(CLISENIOR.BAICLI) BAIRRO_CLIENTE
                        ,max(CLISENIOR.CIDCLI) CIDADE_CLIENTE
                        ,max(CLISENIOR.SIGUFS) ESTADO_CLIENTE
                        ,max(CLISENIOR.FONCLI) FONE1_CLIENTE
                        ,max(CLISENIOR.FONCL2) FONE2_CLIENTE
                        ,max(CLISENIOR.INSEST) INSEST_CLIENTE
                        ,max(CLISENIOR.EMANFE) EMANFE_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
		                ,vend.NAME AS NOME_VENDEDOR
                        ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                        ,nvl(MAX(E069GRE.NOMGRE), 'SEM GRUPO CLIENTE') AS NOME_GRUPO_CLIENTE
                        ,SUM(IP.QUANT) AS QUANTIDADE
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'G'),0) AS PRECO_TOTAL_GERMOPLASMA_SALDO_REAL
                        ,TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA
                        ,MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES  
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'R'),0) AS PRECO_TOTAL_ROYALTIES_SALDO_REAL  
                        ,TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES
                        ,MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'T'),0) AS PRECO_TOTAL_TSI_SALDO_REAL 
                        ,TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI
                        ,MAX(TM.DESCRICAO) AS PAGAMENTO_TSI
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'F'),0) AS PRECO_TOTAL_FRETE_SALDO_REAL  
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
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.CODIGOCLIFOR
                    LEFT JOIN SAPIENS.E069GRE E069GRE ON E069GRE.CODGRE = CLISENIOR.CODGRE
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                    ORDER BY P.CREATED_AT desc
                    ) A
                    WHERE A.TIPO_PRAZO = 'Prazo Safra'
                    {$andsFilter}
                    ORDER BY A.NOME_CLIENTE ASC";  
        }
        public function getDadosSoftsulPedidoDashboardQuery($codigoSafra, $emissao_inicio = null, $emissao_fim = null)
        {
            $ands = "";
            $andsFilter = "";
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
                        ,max(CLISENIOR.CGCCPF) CGC_CPF_CLIENTE
                        ,max(CLISENIOR.ENDCLI) ENDERECO_CLIENTE
                        ,max(CLISENIOR.CPLEND) CPL_ENDERECO_CLIENTE
                        ,max(CLISENIOR.CEPCLI) CEP_CLIENTE
                        ,max(CLISENIOR.BAICLI) BAIRRO_CLIENTE
                        ,max(CLISENIOR.CIDCLI) CIDADE_CLIENTE
                        ,max(CLISENIOR.SIGUFS) ESTADO_CLIENTE
                        ,max(CLISENIOR.FONCLI) FONE1_CLIENTE
                        ,max(CLISENIOR.FONCL2) FONE2_CLIENTE
                        ,max(CLISENIOR.INSEST) INSEST_CLIENTE
                        ,max(CLISENIOR.EMANFE) EMANFE_CLIENTE
                        ,p.RTV_USER_ID AS VENDEDOR_ID
		                ,vend.NAME AS NOME_VENDEDOR
                        ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                        ,nvl(MAX(E069GRE.NOMGRE), 'SEM GRUPO CLIENTE') AS NOME_GRUPO_CLIENTE
                        ,SUM(IP.QUANT) AS QUANTIDADE
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'G'),0) AS PRECO_TOTAL_GERMOPLASMA_SALDO_REAL
                        ,TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA
                        ,MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES  
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'R'),0) AS PRECO_TOTAL_ROYALTIES_SALDO_REAL  
                        ,TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES
                        ,MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'T'),0) AS PRECO_TOTAL_TSI_SALDO_REAL 
                        ,TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI
                        ,MAX(TM.DESCRICAO) AS PAGAMENTO_TSI
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) - NVL((SELECT SUM(VALOR) FROM web.RECEBIMENTOS r WHERE PEDIDO_ID = P.ID AND TIPO = 'F'),0) AS PRECO_TOTAL_FRETE_SALDO_REAL  
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
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.CODIGOCLIFOR
                    LEFT JOIN SAPIENS.E069GRE E069GRE ON E069GRE.CODGRE = CLISENIOR.CODGRE
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                    ORDER BY P.CREATED_AT desc
                    ) A
                    WHERE A.TIPO_PRAZO = 'Prazo Safra'
                    ORDER BY A.NOME_CLIENTE ASC";  
           
        }
        private function parseDxFilter($filter) {
            if (!is_array($filter)) {
                return '';
            }

            // Caso seja simples: [campo, operador, valor]
            if (count($filter) === 3 && is_string($filter[0])) {
                list($field, $operator, $value) = $filter;

                switch (strtolower($operator)) {
                    case '=':
                        return "$field = " . (is_numeric($value) ? $value : "'$value'");
                    case '<>':
                        return "$field <> " . (is_numeric($value) ? $value : "'$value'");
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                        return "$field $operator " . (is_numeric($value) ? $value : "'$value'");
                    case 'contains':
                        return "UPPER($field) LIKE UPPER('%" . addslashes($value) . "%')";
                    case 'startswith':
                        return "UPPER($field) LIKE UPPER('" . addslashes($value) . "%')";
                    case 'endswith':
                        return "UPPER($field) LIKE UPPER('%" . addslashes($value) . "')";
                    default:
                        return '';
                }
            }

            // Caso tenha vários AND/OR em sequência
            $sqlParts = [];
            $logic = null;

            foreach ($filter as $item) {
                if (is_string($item)) {
                    $logic = strtoupper($item); // AND ou OR
                } else {
                    $sqlParts[] = $this->parseDxFilter($item);
                }
            }

            if (!empty($sqlParts)) {
                return '(' . implode(" $logic ", $sqlParts) . ')';
            }

            return '';
        }

        // Recebido STATUS
        public function getStatusDocumentoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                            count(distinct dp.id_documento) qtd
                            ,(select count(distinct id) from documentos where documentos.flg_documento_obrigatorio = true and documentos.ativo = true AND documentos.tipo_pessoa = '{$tipoPessoa}') qtd_total
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
        public function getStatusNotaPromissoriaQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido gp
                        left join garantias g on g.id = gp.id_garantia 
                        where g.ativo = true 
                        and gp.ativo = true
                        and g.flg_nota_promissoria = true
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
                            ) qtd_total
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

        // Enviado STATUS
        public function getStatusDocumentoEnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                            count(distinct dp.id_documento) qtd
                            ,(select count(distinct id) from documentos where documentos.flg_documento_obrigatorio = true and documentos.ativo = true AND documentos.tipo_pessoa = '{$tipoPessoa}') qtd_total
                        FROM documentos_pedido_enviado dp
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
        public function getStatusCPREnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido_enviado gp
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
        public function getStatusNotaPromissoriaEnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido_enviado gp
                        left join garantias g on g.id = gp.id_garantia 
                        where g.ativo = true 
                        and gp.ativo = true
                        and g.flg_nota_promissoria = true
                        and id_pedido = {$idPedido}
                        AND g.tipo_pessoa = '{$tipoPessoa}'";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function getStatusInstrumentoFiancaEnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido_enviado gp
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
        public function getStatusConfissaoDividaEnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT 
                             count(*) as qtd
                        from garantias_pedido_enviado gp
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
        public function getStatusGarantiasEnviadoQuery($idPedido, $tipoPessoa)
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
                            ) qtd_total
                        FROM garantias_pedido_enviado gp
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


        public function getDadosClientesSeniorAvalistasQuery($search, $key = null)
        {
            // Acrescenta filtro de pesquisa se houver
            $ands = "";
            if ($search) {
                $ands .= " AND UPPER((CLISENIOR.CODCLI || '-' || CLISENIOR.NOMCLI)) LIKE '%{$search}%' ";
            }

            if (!empty($key)) {
                $ands .= " AND CLISENIOR.CODCLI = $key ";
            }

            return "SELECT
                         CLISENIOR.CODCLI AS ID_CLIENTE
                        ,(CLISENIOR.CODCLI || '-' || CLISENIOR.NOMCLI) AS NOME_CLIENTE
                        ,CLISENIOR.NOMCLI AS NOME_CLIENTE_S_ID
                        ,CLISENIOR.CGCCPF AS CGC_CPF_CLIENTE
                        ,CASE WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' ELSE NULL END  AS TIPO_PESSOA
                        -- Endereço concatenado sem hífen extra
                        ,RTRIM(
                            NVL(CLISENIOR.ENDCLI, '') ||
                            CASE WHEN CLISENIOR.BAICLI IS NOT NULL THEN ' - ' || CLISENIOR.BAICLI ELSE '' END ||
                            CASE WHEN CLISENIOR.CEPCLI IS NOT NULL THEN ' - ' || CLISENIOR.CEPCLI ELSE '' END ||
                            CASE WHEN CLISENIOR.CIDCLI IS NOT NULL THEN ' - ' || CLISENIOR.CIDCLI ELSE '' END ||
                            CASE WHEN CLISENIOR.SIGUFS IS NOT NULL THEN ' - ' || CLISENIOR.SIGUFS ELSE '' END
                        ) AS ENDERECO_CLIENTE
                    FROM SAPIENS.E085CLI CLISENIOR
                    WHERE 1 = 1
                    $ands
                    FETCH FIRST 30 ROWS ONLY";  
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
                            ,max(CLISENIOR.CGCCPF) CGC_CPF_CLIENTE
                            ,max(CLISENIOR.ENDCLI) ENDERECO_CLIENTE
                            ,max(CLISENIOR.CPLEND) CPL_ENDERECO_CLIENTE
                            ,max(CLISENIOR.CEPCLI) CEP_CLIENTE
                            ,max(CLISENIOR.BAICLI) BAIRRO_CLIENTE
                            ,max(CLISENIOR.CIDCLI) CIDADE_CLIENTE
                            ,max(CLISENIOR.SIGUFS) ESTADO_CLIENTE
                            ,max(CLISENIOR.FONCLI) FONE1_CLIENTE
                            ,max(CLISENIOR.FONCL2) FONE2_CLIENTE
                            ,p.RTV_USER_ID AS VENDEDOR_ID
                            ,vend.NAME AS NOME_VENDEDOR
                            ,MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE
                            ,nvl(MAX(E069GRE.NOMGRE), 'SEM GRUPO CLIENTE') AS NOME_GRUPO_CLIENTE
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
                        LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.CODIGOCLIFOR
                        LEFT JOIN SAPIENS.E069GRE E069GRE ON E069GRE.CODGRE = CLISENIOR.CODGRE
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
        public function getDocumentosPedidoEnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql = "SELECT DISTINCT
                            d.id AS id_documento,
                            dp.id_pedido,
                            d.dsc_documento,
                            COALESCE(dp.ativo, false) AS ativo
                        FROM documentos d
                        LEFT JOIN documentos_pedido_enviado dp ON dp.id_documento = d.id AND dp.id_pedido = {$idPedido}
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
        public function getGarantiasPedidoEnviadoQuery($idPedido, $tipoPessoa)
        {
            if (!empty($idPedido) && !empty($tipoPessoa)) {
                $sql =" SELECT DISTINCT
                            garantias.id as id_garantia,
                            garantias_pedido_enviado.id_pedido,
                            garantias.dsc_garantia,
                            COALESCE(garantias_pedido_enviado.ativo, false) as ativo
                        FROM garantias
                        LEFT JOIN garantias_pedido_enviado ON garantias_pedido_enviado.id_garantia = garantias.id AND garantias_pedido_enviado.id_pedido = {$idPedido}
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
        public function getControleDocumentosQuery($idPedido)
        {
            if (!empty($idPedido)) {
                $sql = "SELECT * FROM controle_documentos where id_pedido = {$idPedido}";
            } else {
                $sql = "";
            }

            return $sql;
        }
        public function marcarDocumentosEnviados(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // Verifica se o registro já existe
                $sqlSelect = "SELECT COUNT(*) AS total FROM controle_documentos WHERE id_pedido = :id_pedido";
                $result = $this->adapter->query($sqlSelect, [':id_pedido' => $data['id_pedido']])->current();

                if ($result && $result['total'] > 0) {
                    // Atualiza o registro existente
                    $sqlUpdate = "UPDATE controle_documentos
                                SET flg_documento_enviado = TRUE
                                WHERE id_pedido = :id_pedido";

                    $paramsUpdate = [
                        ':id_pedido' => $data['ID_PEDIDO']
                    ];

                    $this->adapter->createStatement($sqlUpdate)->execute($paramsUpdate);

                } else {
                    // Insere um novo registro
                    $sqlInsert = "INSERT INTO controle_documentos (id_pedido, codigo_pedido, id_cliente, flg_documento_enviado)
                                VALUES (:id_pedido, :codigo_pedido, :id_cliente, TRUE)";

                    $paramsInsert = [
                        ':id_pedido' => $data['ID_PEDIDO'],
                        ':codigo_pedido' => $data['CODIGO_PEDIDO'],
                        ':id_cliente' => isset($data['ID_CLIENTE']) ? $data['ID_CLIENTE'] : null
                    ];

                    $this->adapter->createStatement($sqlInsert)->execute($paramsInsert);
                }

                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
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
                $sql = 'SELECT id, dsc_garantia, ativo, flg_instrumento_fianca, flg_confissao_divida, flg_cpr, flg_garantia_obrigatorio, flg_nota_promissoria, tipo_pessoa FROM garantias order by id';

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
                        (dsc_garantia, ativo, flg_instrumento_fianca, flg_confissao_divida, flg_cpr, flg_garantia_obrigatorio, flg_nota_promissoria, tipo_pessoa) 
                        VALUES 
                        (:dsc_garantia, :ativo, :flg_instrumento_fianca, :flg_confissao_divida, :flg_cpr, :flg_garantia_obrigatorio, :flg_nota_promissoria, :tipo_pessoa)';
                        
                $statement = $this->adapter->createStatement($sql);
                $statement->execute([
                    ':dsc_garantia' => $data['dsc_garantia'],
                    ':ativo' => $data['ativo'] ?? true,
                    ':flg_instrumento_fianca' => $data['flg_instrumento_fianca'] ?? false,
                    ':flg_confissao_divida' => $data['flg_confissao_divida'] ?? false,
                    ':flg_cpr' => $data['flg_cpr'] ?? false,
                    ':flg_garantia_obrigatorio' => $data['flg_garantia_obrigatorio'] ?? false,
                    ':flg_nota_promissoria' => $data['flg_nota_promissoria'] ?? false,
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
                        flg_nota_promissoria = :flg_nota_promissoria,
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
                    ':flg_nota_promissoria' => $data['flg_nota_promissoria'] ?? false,
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

    #region Dashboard Monitoramento Pedidos
        public function getInfoCardsMonitoramentoPedidos($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT
                        (   SELECT 
                                SUM(NVL(PEDIDOS_EMITIDOS.PRECO_TOTAL, 0)) AS TOTAL_EMITIDO
                            FROM (
                                SELECT  
                                    P.ID AS ID_PEDIDO,
                                    P.CODIGO AS CODIGO_PEDIDO,
                                    pedidoMae.CODIGO AS MAE_PEDIDO_ID,
                                    pedidoOrigem.CODIGO AS ORIGEM_PEDIDO_ID,
                                    TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                                    P.CODIGOSAFRA,
                                    EXTRACT(YEAR FROM S.INICIO) AS ANO_SAFRA,
                                    CASE 
                                        WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                                        WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                                        ELSE NULL 
                                    END AS TIPO_PESSOA,
                                    CLI.CODIGOCLIFOR AS ID_CLIENTE,
                                    CLI.NOME AS NOME_CLIENTE,
                                    p.RTV_USER_ID AS VENDEDOR_ID,
                                    vend.NAME AS NOME_VENDEDOR,
                                    MAX(CLISENIOR.CODGRE) AS GRUPO_CLIENTE,
                                    SUM(IP.QUANT) AS QUANTIDADE,
                                    SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA,
                                    TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA,
                                    MAX(GM.DESCRICAO) AS PAGAMENTO_GERMOPLASTMA,
                                    SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES,
                                    TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES,
                                    MAX(RM.DESCRICAO) AS PAGAMENTO_ROYALTIES,
                                    SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI,
                                    TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI,
                                    MAX(TM.DESCRICAO) AS PAGAMENTO_TSI,
                                    MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE,
                                    TO_CHAR(MAX(P.VENCIMENTO_FRETE), 'YYYY-MM-DD') AS VENCIMENTO_FRETE,
                                    MAX(FM.DESCRICAO) AS PAGAMENTO_FRETE,
                                    SUM(NVL(IP.PRECO_TOTAL, 0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL,
                                    (
                                        CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA, 0)) ELSE 0 END +
                                        CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_ROYALTIES, 0)) ELSE 0 END +
                                        CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO) THEN SUM(NVL(IP.PRECO_TOTAL_TSI, 0)) ELSE 0 END +
                                        CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO) THEN MAX(NVL(P.PRECO_TOTAL_FRETE, 0)) ELSE 0 END
                                    ) AS PRECO_TOTAL_PRAZO_SAFRA,
                                    CASE 
                                        WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO)
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
                                LEFT JOIN EMPRESA.MODALIDADES GM ON GM.CODIGOMODALIDADE = P.GERMOPLASMA_CODIGOMODALIDADE 
                                LEFT JOIN EMPRESA.MODALIDADES RM ON RM.CODIGOMODALIDADE = P.ROYALTIES_CODIGOMODALIDADE 
                                LEFT JOIN EMPRESA.MODALIDADES TM ON TM.CODIGOMODALIDADE = P.TSI_CODIGOMODALIDADE 
                                LEFT JOIN EMPRESA.MODALIDADES FM ON FM.CODIGOMODALIDADE = P.FRETE_CODIGOMODALIDADE 
                                LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                                LEFT JOIN web.pedidos_v2 pedidoOrigem ON pedidoOrigem.id = p.ORIGEM_PEDIDO_ID
                                LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                                LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                                WHERE IP.CODIGOCULTIVAR IS NOT NULL
                                AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                                {$ands}
                                AND P.CREATED_AT BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                                GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME, p.RTV_USER_ID, vend.NAME
                            ) PEDIDOS_EMITIDOS
                        ) AS TOTAL_EMITIDO,
                        (   SELECT 
                                SUM(NVL(R.VALOR, 0)) + SUM(NVL(R.JUROS, 0)) - SUM(NVL(R.DESCONTO, 0)) 
                            FROM web.pedidos_v2 p
                            LEFT JOIN WEB.RECEBIMENTOS R ON R.PEDIDO_ID = P.ID
                            WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                            {$ands}
                            AND R.RECEBIDO_EM BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                        ) AS TOTAL_PAGO,
                        (   SELECT  
                                SUM(parc.PRECO_PARCELA)
                            FROM web.view_vencimentos_por_data parc
                            LEFT JOIN web.pedidos_v2 p ON P.ID = PARC.PEDIDO_ID 
                            WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                            {$ands}
                            AND (PARC.VALOR_RECEBIDO IS NULL OR PARC.VALOR_RECEBIDO = 0)
                            AND parc.VENCIMENTO_PARCELA <= cast(SYSDATE AS DATE)-1 -- DATA ATUAL PRA TRAZ
                        ) AS TOTAL_VENCIDO,
                        (   SELECT 
                                SUM(parc.PRECO_PARCELA)
                            FROM web.view_vencimentos_por_data parc
                            LEFT JOIN web.pedidos_v2 p ON P.ID = PARC.PEDIDO_ID 
                            WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                            {$ands}
                            AND (PARC.VALOR_RECEBIDO IS NULL OR PARC.VALOR_RECEBIDO = 0)
                            AND parc.VENCIMENTO_PARCELA BETWEEN cast(SYSDATE AS DATE)-1 AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                        ) AS TOTAL_A_VENCER,
                        (   SELECT 
                                SUM(A.PRECO_TOTAL_GERMOPLASMA)  
                            FROM (
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
                                AND GM.CODIGOMODALIDADE = 2 -- PERMUTA SOJA
                                AND P.CREATED_AT BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                                GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME
                                ORDER BY P.CREATED_AT DESC
                            ) A
                        ) AS TOTAL_PERMUTA,
                        (   SELECT sum(a.PRECO_TOTAL) 
                            FROM (
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
                                ORDER BY P.CREATED_AT DESC
                            ) A
                        ) AS TOTAL_SAFRA
                    FROM DUAL";  
        }
        public function getInfoGermoplasmaTipoPrazo($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Ano' THEN PRECO_TOTAL_GERMOPLASMA ELSE 0 END) AS PRAZO_ANO_GERMOPLASMA,
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Safra' THEN PRECO_TOTAL_GERMOPLASMA ELSE 0 END) AS PRAZO_SAFRA_GERMOPLASMA
                    FROM (
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
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA))
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO
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
                ) A";  
        }
        public function getInfoRoyaltiesTipoPrazo($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Ano' THEN PRECO_TOTAL_ROYALTIES ELSE 0 END) AS PRAZO_ANO_ROYALTIES,
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Safra' THEN PRECO_TOTAL_ROYALTIES ELSE 0 END) AS PRAZO_SAFRA_ROYALTIES
                    FROM (
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
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES))
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO
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
                ) A";  
        }
        public function getInfoTSITipoPrazo($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Ano' THEN PRECO_TOTAL_TSI ELSE 0 END) AS PRAZO_ANO_TSI,
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Safra' THEN PRECO_TOTAL_TSI ELSE 0 END) AS PRAZO_SAFRA_TSI
                    FROM (
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
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI))
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO
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
                ) A";  
        }
        public function getInfoFreteTipoPrazo($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Ano' THEN PRECO_TOTAL_FRETE ELSE 0 END) AS PRAZO_ANO_FRETE,
                        SUM(CASE WHEN TIPO_PRAZO = 'Prazo Safra' THEN PRECO_TOTAL_FRETE ELSE 0 END) AS PRAZO_SAFRA_FRETE
                    FROM (
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
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,TO_CHAR(MAX(P.VENCIMENTO_FRETE), 'YYYY-MM-DD') AS VENCIMENTO_FRETE
                        ,MAX(FM.DESCRICAO) AS PAGAMENTO_FRETE
                        ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,CASE WHEN 
                                EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO)
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
                    ORDER BY P.CREATED_AT DESC
                    ) A";  
        }
        public function getInfoRecebimentoPorDataPagamento($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        TO_CHAR(R.RECEBIDO_EM, 'YYYY-MM-DD') AS DATA_PAGAMENTO,
                        SUM(NVL(R.VALOR, 0)) + SUM(NVL(R.JUROS, 0)) - SUM(NVL(R.DESCONTO, 0)) AS VALOR_RECEBIDO
                    FROM web.pedidos_v2 p
                    LEFT JOIN WEB.RECEBIMENTOS R ON R.PEDIDO_ID = P.ID
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    AND R.RECEBIDO_EM BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    {$ands}
                    GROUP BY TO_CHAR(R.RECEBIDO_EM, 'YYYY-MM-DD')
                    ORDER BY MIN(R.RECEBIDO_EM)";  
        }
        public function getInfoAReceberRecebido($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        SUM(VALOR_A_RECEBER) - SUM(VALOR_RECEBIDO) as VALOR_A_RECEBER, 
                        SUM(VALOR_RECEBIDO) VALOR_RECEBIDO 
                    FROM (
                    SELECT
                        PEDIDO_ID
                        ,parc.PRECO_PARCELA AS VALOR_A_RECEBER
                        ,parc.valor_recebido
                    FROM web.view_vencimentos_por_data parc
                    LEFT JOIN web.pedidos_v2 p ON P.ID = PARC.PEDIDO_ID 
                    LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    AND parc.VENCIMENTO_PARCELA BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    )  A";  
        }
        public function getInfoTopClientes($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                         CLI.CODIGOCLIFOR AS ID_CLIENTE
                        ,CLI.NOME AS NOME_CLIENTE
                        ,SUM(NVL(R.VALOR, 0)) + SUM(NVL(R.JUROS, 0)) - SUM(NVL(R.DESCONTO, 0)) AS VALOR
                    FROM web.pedidos_v2 p
                    LEFT JOIN WEB.RECEBIMENTOS R ON R.PEDIDO_ID = P.ID
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    AND R.RECEBIDO_EM BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    {$ands}
                    GROUP BY CLI.CODIGOCLIFOR, CLI.NOME
                    ORDER BY 3 desc
                    FETCH FIRST 10 ROWS ONLY";  
        }
        public function getInfoTopVendedores($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        p.RTV_USER_ID AS VENDEDOR_ID
                        ,vend.NAME AS NOME_VENDEDOR
                        ,SUM(NVL(R.VALOR, 0)) + SUM(NVL(R.JUROS, 0)) - SUM(NVL(R.DESCONTO, 0)) AS VALOR
                    FROM web.pedidos_v2 p
                    LEFT JOIN WEB.RECEBIMENTOS R ON R.PEDIDO_ID = P.ID
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    AND R.RECEBIDO_EM BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    {$ands}
                    GROUP BY p.RTV_USER_ID, vend.NAME
                    ORDER BY 3 desc
                    FETCH FIRST 10 ROWS ONLY";  
        }
        public function getDetalhesPedidosEmitidosPedidos($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT  
                        P.ID AS ID_PEDIDO,
                        P.CODIGO AS CODIGO_PEDIDO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END AS TIPO_PESSOA,
                        CLI.CODIGOCLIFOR AS ID_CLIENTE,
                        CLI.NOME AS NOME_CLIENTE,
                        p.RTV_USER_ID AS VENDEDOR_ID,
                        vend.NAME AS NOME_VENDEDOR,
                        SUM(IP.QUANT) AS QUANTIDADE,
                        SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA,
                        TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA,
                        SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES,
                        TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES,
                        SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI,
                        TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI,
                        MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE,
                        TO_CHAR(MAX(P.VENCIMENTO_FRETE), 'YYYY-MM-DD') AS VENCIMENTO_FRETE,
                        SUM(NVL(IP.PRECO_TOTAL, 0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || P.ID || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
                    FROM web.pedidos_v2 p
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN web.itens_pedido_v2 ip ON ip.PEDIDO_ID = p.ID 
                    LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                    LEFT JOIN EMPRESA.MODALIDADES GM ON GM.CODIGOMODALIDADE = P.GERMOPLASMA_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES RM ON RM.CODIGOMODALIDADE = P.ROYALTIES_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES TM ON TM.CODIGOMODALIDADE = P.TSI_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES FM ON FM.CODIGOMODALIDADE = P.FRETE_CODIGOMODALIDADE 
                    LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                    LEFT JOIN web.pedidos_v2 pedidoOrigem ON pedidoOrigem.id = p.ORIGEM_PEDIDO_ID
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    AND P.CREATED_AT BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME, p.RTV_USER_ID, vend.NAME";  
        }
        public function getDetalhesPedidosPagosPedidos($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        P.ID AS ID_PEDIDO,
                        P.CODIGO AS CODIGO_PEDIDO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END AS TIPO_PESSOA,
                        CLI.CODIGOCLIFOR AS ID_CLIENTE,
                        CLI.NOME AS NOME_CLIENTE,
                        P.RTV_USER_ID AS VENDEDOR_ID,
                        VEND.NAME AS NOME_VENDEDOR,
                        TO_CHAR(R.RECEBIDO_EM, 'YYYY-MM-DD') AS RECEBIDO_EM,
                        SUM(R.DESCONTO) AS DESCONTO,
                        SUM(R.JUROS) AS JUROS,
                        SUM(R.VALOR) AS VALOR_RECEBIDO
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || P.ID || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
                    FROM web.pedidos_v2 p
                    LEFT JOIN WEB.RECEBIMENTOS R ON R.PEDIDO_ID = P.ID
                    LEFT JOIN EMPRESA.CLIFOR CLI ON CLI.CODIGOCLIFOR = P.CODIGOLOCAL
                    LEFT JOIN WEB.USERS VEND ON VEND.ID = P.RTV_USER_ID
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    AND R.RECEBIDO_EM BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    GROUP BY
                        P.ID,
                        P.CODIGO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD'),
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END,
                        CLI.CODIGOCLIFOR,
                        CLI.NOME,
                        P.RTV_USER_ID,
                        VEND.NAME,
                        TO_CHAR(R.RECEBIDO_EM, 'YYYY-MM-DD')";
        }
        public function getDetalhesPedidosVencidosPedidos($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT  
                        P.ID AS ID_PEDIDO,
                        P.CODIGO AS CODIGO_PEDIDO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END AS TIPO_PESSOA,
                        CLI.CODIGOCLIFOR AS ID_CLIENTE,
                        CLI.NOME AS NOME_CLIENTE,
                        P.RTV_USER_ID AS VENDEDOR_ID,
                        VEND.NAME AS NOME_VENDEDOR,
                        TO_CHAR(PARC.VENCIMENTO_PARCELA, 'YYYY-MM-DD') as VENCIMENTO_PARCELA,
                        SUM(PARC.PRECO_PARCELA) AS PRECO_PARCELA
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || P.ID || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
                    FROM web.view_vencimentos_por_data parc
                    LEFT JOIN web.pedidos_v2 p ON P.ID = PARC.PEDIDO_ID 
                    LEFT JOIN EMPRESA.CLIFOR CLI ON CLI.CODIGOCLIFOR = P.CODIGOLOCAL
                    LEFT JOIN WEB.USERS VEND ON VEND.ID = P.RTV_USER_ID
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    AND (PARC.VALOR_RECEBIDO IS NULL OR PARC.VALOR_RECEBIDO = 0)
                    AND parc.VENCIMENTO_PARCELA <= cast(SYSDATE AS DATE)-1
                    GROUP BY
                        P.ID,
                        P.CODIGO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD'),
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END,
                        CLI.CODIGOCLIFOR,
                        CLI.NOME,
                        P.RTV_USER_ID,
                        VEND.NAME,
                        PARC.VENCIMENTO_PARCELA
                    HAVING SUM(PARC.PRECO_PARCELA) > 0";
        }
        public function getDetalhesPedidosAVencerPedidos($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT 
                        P.ID AS ID_PEDIDO,
                        P.CODIGO AS CODIGO_PEDIDO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END AS TIPO_PESSOA,
                        CLI.CODIGOCLIFOR AS ID_CLIENTE,
                        CLI.NOME AS NOME_CLIENTE,
                        P.RTV_USER_ID AS VENDEDOR_ID,
                        VEND.NAME AS NOME_VENDEDOR,
                        TO_CHAR(PARC.VENCIMENTO_PARCELA, 'YYYY-MM-DD') AS VENCIMENTO_PARCELA,
                        SUM(parc.PRECO_PARCELA) AS PRECO_PARCELA
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || P.ID || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
                    FROM web.view_vencimentos_por_data parc
                    LEFT JOIN web.pedidos_v2 p ON P.ID = PARC.PEDIDO_ID 
                    LEFT JOIN EMPRESA.CLIFOR CLI ON CLI.CODIGOCLIFOR = P.CODIGOLOCAL
                    LEFT JOIN WEB.USERS VEND ON VEND.ID = P.RTV_USER_ID
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                    WHERE P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    AND P.CODIGOSAFRA = 163
                    AND (PARC.VALOR_RECEBIDO IS NULL OR PARC.VALOR_RECEBIDO = 0)
                    AND parc.VENCIMENTO_PARCELA BETWEEN cast(SYSDATE AS DATE)-1 AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    GROUP BY
                        P.ID,
                        P.CODIGO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD'),
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END,
                        CLI.CODIGOCLIFOR,
                        CLI.NOME,
                        P.RTV_USER_ID,
                        VEND.NAME,
                        PARC.VENCIMENTO_PARCELA
                    HAVING SUM(PARC.PRECO_PARCELA) > 0";
        }
        public function getDetalhesPedidosPermuta($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT  
                        P.ID AS ID_PEDIDO,
                        P.CODIGO AS CODIGO_PEDIDO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END AS TIPO_PESSOA,
                        CLI.CODIGOCLIFOR AS ID_CLIENTE,
                        CLI.NOME AS NOME_CLIENTE,
                        p.RTV_USER_ID AS VENDEDOR_ID,
                        vend.NAME AS NOME_VENDEDOR,
                        SUM(IP.QUANT) AS QUANTIDADE,
                        SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA,
                        TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA,
                        SUM(NVL(IP.PRECO_TOTAL, 0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || P.ID || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
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
                    AND GM.CODIGOMODALIDADE = 2 -- PERMUTA SOJA
                    AND P.CREATED_AT BETWEEN TO_DATE('{$apuracao_inicio}', 'YYYY-MM-DD') AND TO_DATE('{$apuracao_fim}', 'YYYY-MM-DD')
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME, p.RTV_USER_ID, vend.NAME";  
        }
        public function getDetalhesTodosPedidosSafra($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT  
                        P.ID AS ID_PEDIDO,
                        P.CODIGO AS CODIGO_PEDIDO,
                        TO_CHAR(P.CREATED_AT, 'YYYY-MM-DD') AS DATA_PEDIDO,
                        P.CODIGOSAFRA,
                        CASE 
                            WHEN CLISENIOR.TIPCLI = 'F' THEN 'PF' 
                            WHEN CLISENIOR.TIPCLI = 'J' THEN 'PJ' 
                            ELSE NULL 
                        END AS TIPO_PESSOA,
                        CLI.CODIGOCLIFOR AS ID_CLIENTE,
                        CLI.NOME AS NOME_CLIENTE,
                        p.RTV_USER_ID AS VENDEDOR_ID,
                        vend.NAME AS NOME_VENDEDOR,
                        SUM(IP.QUANT) AS QUANTIDADE,
                        SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA,
                        TO_CHAR(MAX(P.VENCIMENTO_GERMOPLASMA), 'YYYY-MM-DD') AS VENCIMENTO_GERMOPLASMA,
                        SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES,
                        TO_CHAR(MAX(P.VENCIMENTO_ROYALTIES), 'YYYY-MM-DD') AS VENCIMENTO_ROYALTIES,
                        SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI,
                        TO_CHAR(MAX(P.VENCIMENTO_TSI), 'YYYY-MM-DD') AS VENCIMENTO_TSI,
                        MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE,
                        TO_CHAR(MAX(P.VENCIMENTO_FRETE), 'YYYY-MM-DD') AS VENCIMENTO_FRETE,
                        SUM(NVL(IP.PRECO_TOTAL, 0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,'https://saofrancisco.softsul.agr.br/pedidos-v2/' || P.ID || '?tab=sobre' AS LINK_REDIRECT_SOFTSUL
                    FROM web.pedidos_v2 p
                    LEFT JOIN EMPRESA.CLIFOR cli ON cli.CODIGOCLIFOR = p.CODIGOLOCAL
                    LEFT JOIN web.itens_pedido_v2 ip ON ip.PEDIDO_ID = p.ID 
                    LEFT JOIN ALMOX.SAFRAS s ON s.codigosafra = p.codigosafra
                    LEFT JOIN EMPRESA.MODALIDADES GM ON GM.CODIGOMODALIDADE = P.GERMOPLASMA_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES RM ON RM.CODIGOMODALIDADE = P.ROYALTIES_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES TM ON TM.CODIGOMODALIDADE = P.TSI_CODIGOMODALIDADE 
                    LEFT JOIN EMPRESA.MODALIDADES FM ON FM.CODIGOMODALIDADE = P.FRETE_CODIGOMODALIDADE 
                    LEFT JOIN web.pedidos_v2 pedidoMae ON pedidoMae.id = p.MAE_PEDIDO_ID
                    LEFT JOIN web.pedidos_v2 pedidoOrigem ON pedidoOrigem.id = p.ORIGEM_PEDIDO_ID
                    LEFT JOIN SAPIENS.E085CLI CLISENIOR ON CLISENIOR.CODCLI = CLI.SENIOR_CLIFOR
                    LEFT JOIN WEB.USERS vend ON vend.ID = p.RTV_USER_ID
                    WHERE IP.CODIGOCULTIVAR IS NOT NULL
                    AND P.TIPO_VENDA_ID NOT IN (4,161,164,162,163,164,201,202)
                    {$ands}
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME, p.RTV_USER_ID, vend.NAME";  
        }
    #endregion

    #region Dashboard Propostas Crédito x Documentos
        public function getInfoComponentesTipoPrazo($apuracao_inicio = null, $apuracao_fim = null, $codigoSafra = null)
        {
            $ands = "";
           
            if (!empty($codigoSafra)) {
                $ands .= " AND p.CODIGOSAFRA = {$codigoSafra}";
            }
            
            return "SELECT  
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
                        ,SUM(NVL(IP.PRECO_TOTAL_GERMOPLASMA ,0)) AS PRECO_TOTAL_GERMOPLASMA
                        ,SUM(NVL(IP.PRECO_TOTAL_ROYALTIES ,0)) AS PRECO_TOTAL_ROYALTIES
                        ,SUM(NVL(IP.PRECO_TOTAL_TSI ,0)) AS PRECO_TOTAL_TSI
                        ,MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL_FRETE
                        ,SUM(NVL(IP.PRECO_TOTAL,0)) + MAX(NVL(P.PRECO_TOTAL_FRETE ,0)) AS PRECO_TOTAL
                        ,EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA))
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_GERMOPLASMA)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO_GERMOPLASMA
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_ROYALTIES)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO_ROYALTIES
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_TSI)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO_TSI
                        ,CASE WHEN EXTRACT(YEAR FROM MAX(P.VENCIMENTO_FRETE)) > EXTRACT(YEAR FROM S.INICIO) THEN 'Prazo Safra' ELSE 'Prazo Ano' END AS TIPO_PRAZO_FRETE
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
                    AND P.CODIGOSAFRA = 163
                    GROUP BY P.ID, P.CODIGO, pedidoMae.CODIGO, pedidoOrigem.CODIGO, P.CREATED_AT, P.CODIGOSAFRA, EXTRACT(YEAR FROM S.INICIO), CLISENIOR.TIPCLI, CLI.CODIGOCLIFOR, CLI.NOME,p.RTV_USER_ID,vend.NAME";  
        }
        public function getInfoDocumentosGarantiasAtivosPedido()
        {
            return "SELECT DISTINCT id_pedido FROM (
                        SELECT id_pedido FROM documentos_pedido dp WHERE ativo = true
                        UNION ALL
                        SELECT id_pedido FROM garantias_pedido gp WHERE ativo = true
                    ) A";  
        }
    #endregion
}
