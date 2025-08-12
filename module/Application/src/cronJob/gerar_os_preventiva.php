<?php
require 'vendor/autoload.php';

use Laminas\Db\Adapter\Adapter;

// Configuração do banco
$db = new Adapter([
    'driver'   => 'Pdo_Pgsql',
    'hostname' => 'localhost',
    'database' => 'seu_banco',
    'username' => 'seu_usuario',
    'password' => 'sua_senha',
]);

// Busca programações ativas que precisam gerar OS hoje
$sql = "
    SELECT * 
    FROM programacao_manutencao_preventiva
    WHERE status = 'Ativa'
      AND proxima_execucao <= CURRENT_DATE
";
$programacoes = $db->query($sql, Adapter::QUERY_MODE_EXECUTE);

foreach ($programacoes as $prog) {
    // Evita duplicar OS para mesma data
    $check = $db->query("
        SELECT COUNT(*) AS total
        FROM controle_manutencao
        WHERE programacao_id = {$prog['id']}
          AND data_programada = '{$prog['proxima_execucao']}'
    ", Adapter::QUERY_MODE_EXECUTE)->current();

    if ($check['total'] == 0) {
        // Cria OS
        $insert = "
            INSERT INTO controle_manutencao
                (data_programada, setor_id, tipo_ordem_servico, equipamento_id, 
                 centro_custo_id, tipo_manutencao_id, area_tecnica_id, status, programacao_id)
            VALUES
                (:data_programada, :setor_id, :tipo_ordem_servico, :equipamento_id, 
                 :centro_custo_id, :tipo_manutencao_id, :area_tecnica_id, 'Pendente', :programacao_id)
        ";
        $db->query($insert, [
            'data_programada'    => $prog['proxima_execucao'],
            'setor_id'           => $prog['setor_id'],
            'tipo_ordem_servico' => $prog['tipo_ordem_servico'],
            'equipamento_id'     => $prog['equipamento_id'],
            'centro_custo_id'    => $prog['centro_custo_id'],
            'tipo_manutencao_id' => 2, // ID da preventiva (fixo ou buscado)
            'area_tecnica_id'    => $prog['area_tecnica_id'],
            'programacao_id'     => $prog['id'],
        ]);

        // Atualiza próxima execução
        $db->query("
            UPDATE programacao_manutencao_preventiva
            SET data_ultima_execucao = proxima_execucao,
                proxima_execucao = proxima_execucao + INTERVAL '{$prog['periodicidade_dias']} days'
            WHERE id = {$prog['id']}
        ", Adapter::QUERY_MODE_EXECUTE);
    }
}
