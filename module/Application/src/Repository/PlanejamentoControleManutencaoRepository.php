<?php

namespace Application\Repository;

use Laminas\Db\Adapter\AdapterInterface;

class PlanejamentoControleManutencaoRepository
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    #region Cadastro Areas Técnicas
        public function listarAreas()
        {
            $sql = 'SELECT id, nome, descricao, flg_ativo FROM areas_tecnicas ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function salvarArea(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome da área é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : false;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE areas_tecnicas SET 
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
                $sql = 'INSERT INTO areas_tecnicas (nome, descricao, flg_ativo) 
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
        public function excluirArea($id)
        {
            if (empty($id)) {
                throw new \Exception('ID da área não fornecido.');
            }

            $sql = 'UPDATE areas_tecnicas SET flg_ativo = false WHERE id = :id';
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([':id' => $id]);
        }
        public function getLookupAreas()
        {
            $sql = 'SELECT id, nome, descricao FROM areas_tecnicas where flg_ativo = true ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
    #endRegion

    #region Cadastro Setores
        public function listarSetores()
        {
            $sql = 'SELECT 
                        s.id, 
                        s.nome, 
                        s.descricao,
                        s.flg_ativo
                    FROM setores s
                    ORDER BY s.nome';

            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarSetor(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do setor é obrigatório.');
            }

            // Define valor padrão para flg_ativo se não enviado
            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : true;

            if (!empty($data['id'])) {
                // Update
                $sql = 'UPDATE setores SET 
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
                // Insert
                $sql = 'INSERT INTO setores (nome, descricao, flg_ativo) 
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
        public function excluirSetor($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do setor não fornecido.');
            }

            $sql = 'UPDATE setores SET flg_ativo = false WHERE id = :id';
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([':id' => $id]);
        }
        public function getLookupSetores()
        {
            $sql = 'SELECT 
                        s.id, 
                        s.nome, 
                        s.descricao,
                        s.flg_ativo
                    FROM setores s
                    where s.flg_ativo = true
                    ORDER BY s.nome';

            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
    #endRegion

    #region Cadastro Tipos de Manutenção
        public function listarTiposManutencao()
        {
            $sql = 'SELECT id, nome, descricao, flg_ativo FROM tipos_manutencao ORDER BY id';
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarTipoManutencao(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do tipo de manutenção é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : true;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE tipos_manutencao SET 
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
                $sql = 'INSERT INTO tipos_manutencao (nome, descricao, flg_ativo) 
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
        public function excluirTipoManutencao($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do tipo de manutenção não fornecido.');
            }

            $sql = 'UPDATE tipos_manutencao SET flg_ativo = false WHERE id = :id';
            $statement = $this->adapter->createStatement($sql);
            $statement->execute([':id' => $id]);
        }
        public function getLookupTiposManutencao()
        {
            $sql = "SELECT id, nome, descricao FROM tipos_manutencao where flg_ativo = true and nome not like 'Preventiva' ORDER BY nome";
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
    #endRegion

    #region Cadastro Técnicos
        public function listarTecnicos()
        {
            $sql = 'SELECT id, numcad, nome, cpf, cargo_funcao, contato, area_tecnica_id, flg_ativo FROM tecnicos ORDER BY id';
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarTecnico(array $data)
        {
            if (empty($data['nome']) || empty($data['cpf']) || empty($data['cargo_funcao']) || empty($data['area_tecnica_id'])) {
                throw new \Exception('Nome, CPF, cargo/função e área técnica são obrigatórios.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : true;

            if (!empty($data['id'])) {
                $sql = 'UPDATE tecnicos SET 
                            nome = :nome, 
                            numcad = :numcad, 
                            cpf = :cpf, 
                            cargo_funcao = :cargo_funcao, 
                            contato = :contato, 
                            area_tecnica_id = :area_tecnica_id,
                            flg_ativo = :flg_ativo
                        WHERE id = :id';
                $params = [
                    ':nome' => $data['nome'],
                    ':numcad' => $data['numcad'],
                    ':cpf' => $data['cpf'],
                    ':cargo_funcao' => $data['cargo_funcao'],
                    ':contato' => $data['contato'] ?? null,
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':flg_ativo' => $flgAtivo,
                    ':id' => $data['id']
                ];
            } else {
                $sql = 'INSERT INTO tecnicos (nome, numcad, cpf, cargo_funcao, contato, area_tecnica_id, flg_ativo) 
                        VALUES (:nome, :numcad, :cpf, :cargo_funcao, :contato, :area_tecnica_id, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':numcad' => $data['numcad'],
                    ':cpf' => $data['cpf'],
                    ':cargo_funcao' => $data['cargo_funcao'],
                    ':contato' => $data['contato'] ?? null,
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':flg_ativo' => $flgAtivo
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirTecnico($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do técnico não fornecido.');
            }

            $sql = 'UPDATE tecnicos SET flg_ativo = false WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupTecnicos()
        {
            $sql = 'SELECT 
                        t.id, 
                        t.nome, 
                        t.cpf, 
                        t.cargo_funcao, 
                        t.contato, 
                        t.area_tecnica_id,
                        at.nome as nome_area_tecnica
                    FROM tecnicos t
                    left join areas_tecnicas at on at.id = t.area_tecnica_id
                    where t.flg_ativo = true 
                    ORDER BY at.nome, t.nome';
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
    #endRegion    

    #region Cadastro Equipamentos
        public function listarEquipamentos()
        {
            $sql = "SELECT 
                        e.id, 
                        e.codigo, 
                        e.nome, 
                        e.codigo || '-' || e.nome ||
                        COALESCE(
                            CASE 
                                WHEN e.observacoes IS NOT NULL AND e.observacoes <> '' THEN ' - Observação: ' || e.observacoes
                            END, 
                            ''
                        ) AS dsc_equipamento,
                        e.setor_id, 
                        s.nome as setor_nome,
                        e.status, 
                        e.observacoes, 
                        e.centro_custo 
                    FROM equipamentos e
                    left join setores s on s.id = e.setor_id 
                    ORDER BY codigo";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarEquipamento(array $data)
        {
            #region Validações
                if (empty($data['nome'])) {
                    throw new \Exception('Nome do equipamento é obrigatório.');
                }
                if (empty($data['codigo'])) {
                    throw new \Exception('Código do equipamento é obrigatório.');
                }
                // Verifica se já existe equipamento com o mesmo código (exceto o atual, se for update)
                $sqlCheck = "SELECT COUNT(*) AS total FROM equipamentos WHERE codigo = :codigo";
                $paramsCheck = [':codigo' => $data['codigo']];
                if (!empty($data['id'])) {
                    $sqlCheck .= " AND id <> :id";
                    $paramsCheck[':id'] = $data['id'];
                }
                $result = $this->adapter->query($sqlCheck, $paramsCheck)->current();
                if ($result && $result['total'] > 0) {
                    throw new \Exception('Já existe um equipamento cadastrado com esse código.');
                }
            #endregion

            if (!empty($data['id'])) {
                $sql = 'UPDATE equipamentos SET codigo = :codigo, nome = :nome, setor_id = :setor_id, status = :status, observacoes = :observacoes, centro_custo = :centro_custo WHERE id = :id';
                $params = [
                    ':codigo' => $data['codigo'],
                    ':nome' => $data['nome'],
                    ':setor_id' => $data['setor_id'],
                    ':status' => $data['status'],
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':centro_custo' => $data['centro_custo'],
                    ':id' => $data['id']
                ];
            } else {
                $sql = 'INSERT INTO equipamentos (codigo, nome, setor_id, status, observacoes, centro_custo) VALUES (:codigo, :nome, :setor_id, :status, :observacoes, :centro_custo)';
                $params = [
                    ':codigo' => $data['codigo'],
                    ':nome' => $data['nome'],
                    ':setor_id' => $data['setor_id'],
                    ':status' => $data['status'],
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':centro_custo' => $data['centro_custo']
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirEquipamento($id)
        {
            $sql = 'DELETE FROM equipamentos WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupEquipamentos($search = null, $key = null, $offset = 0, $limit = 30)
        {
            // Acrescenta filtro de pesquisa se houver
            $ands = "";
            if (!empty($search)) {
                $ands .= " AND (e.codigo || '-' || e.nome || COALESCE(
                            CASE 
                                WHEN e.observacoes IS NOT NULL AND e.observacoes <> '' THEN ' - Observação: ' || e.observacoes
                            END, 
                            ''
                        )) LIKE '%{$search}%' ";
            }

            if (!empty($key)) {
                $ands .= " AND e.id = $key ";
            }
            
            // Query principal com paginação
            $sql = "SELECT 
                        e.id, 
                        e.codigo, 
                        e.nome, 
                        e.codigo || '-' || e.nome || COALESCE(
                            CASE 
                                WHEN e.observacoes IS NOT NULL AND e.observacoes <> '' THEN ' - Observação: ' || e.observacoes
                            END, 
                            ''
                        ) AS dsc_equipamento,
                        e.setor_id, 
                        s.nome as setor_nome,
                        e.status, 
                        e.observacoes, 
                        e.centro_custo 
                    FROM equipamentos e
                    LEFT JOIN setores s ON s.id = e.setor_id 
                    WHERE status NOT LIKE 'Inativo'
                    {$ands}
                    ORDER BY codigo
                    LIMIT $limit OFFSET $offset";  // Paginação incluída aqui
                    
            $result = $this->adapter->createStatement($sql)->execute();

            // Query para contar o total de registros (para paginação)
            $countSql = "SELECT COUNT(*) as total 
                        FROM equipamentos e
                        LEFT JOIN setores s ON s.id = e.setor_id 
                        WHERE status NOT LIKE 'Inativo'
                        {$ands}";
                        
            $countResult = $this->adapter->createStatement($countSql)->execute()->current();
            $totalCount = $countResult['total'] ?? 0;

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            
            return [
                'data' => $data,
                'totalCount' => $totalCount
            ];
        }
        public function atualizarStatusEquipamentos() // Essa função atualiza o status dos equipamentos com base nas manutenções ativas
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // Atualiza para "Em Manutenção" se a data_inicio for menor ou igual a hoje e não tiver data_fim ou data_fim maior que hoje
                $sqlUpdateManutencao = "
                    UPDATE equipamentos
                    SET status = 'Em Manutenção'
                    WHERE status <> 'Inativo' 
                    AND id IN (
                        SELECT DISTINCT equipamento_id
                        FROM controle_manutencao
                        WHERE data_inicio <= CURRENT_DATE
                        AND (data_final IS NULL OR data_final > CURRENT_DATE)
                    )
                ";
                $this->adapter->createStatement($sqlUpdateManutencao)->execute();

                // Atualiza para "Ativo" se a última manutenção tiver data_fim menor ou igual a hoje ou não houver mais manutenção ativa
                $sqlUpdateAtivo = "
                    UPDATE equipamentos
                    SET status = 'Ativo'
                    WHERE status <> 'Inativo'
                    AND id NOT IN (
                        SELECT DISTINCT equipamento_id
                        FROM controle_manutencao
                        WHERE data_inicio <= CURRENT_DATE
                        AND (data_final IS NULL OR data_final > CURRENT_DATE)
                    )
                ";
                $this->adapter->createStatement($sqlUpdateAtivo)->execute();
                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
    #endRegion

    #region Programação Manutenção Preventiva
        public function listarProgramacoesPreventivas()
        {
            $sql = "SELECT 
                        pmp.*, 
                        eq.nome as nome_equipamento, 
                        at.nome as nome_area, 
                        st.nome as nome_setor
                    FROM programacao_manutencao_preventiva pmp
                    LEFT JOIN equipamentos eq ON eq.id = pmp.equipamento_id
                    LEFT JOIN areas_tecnicas at ON at.id = pmp.area_tecnica_id
                    LEFT JOIN setores st ON st.id = pmp.setor_id
                    where 1=1
                    and pmp.status_programacao <> 'Cancelada'
                    ORDER BY pmp.proxima_execucao ASC";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarProgramacaoPreventiva(array $data)
        {
            $status_programacao = $data['status_programacao'] ?? 'Ativa';
            $periodicidade_dias = isset($data['periodicidade_dias']) ? (int)$data['periodicidade_dias'] : null;

            // Definir tipo_ordem_servico e setor_id conforme regras
            if (isset($data['equipamento_id']) && !empty($data['equipamento_id'])) {
                $data['tipo_ordem_servico'] = 'Equipamento';

                // Busca o setor_id do equipamento
                $sqlSetor = "SELECT setor_id FROM equipamentos WHERE id = :id";
                $resultSetor = $this->adapter->createStatement($sqlSetor)->execute([':id' => $data['equipamento_id']])->current();
                if ($resultSetor && isset($resultSetor['setor_id'])) {
                    $data['setor_id'] = $resultSetor['setor_id']; // Sobrescreve o setor vindo do form
                }
            } elseif (isset($data['centro_custo_id']) && !empty($data['centro_custo_id'])) {
                $data['tipo_ordem_servico'] = 'Centro de Custo';
            } else {
                // Caso não tenha nem equipamento nem centro de custo, lance exceção ou defina padrão
                throw new \InvalidArgumentException("Informe equipamento ou centro de custo.");
            }

            if (!empty($data['id'])) {
                // Busca dados atuais para comparar
                $sqlSelect = "SELECT data_programada, periodicidade_dias FROM programacao_manutencao_preventiva WHERE id = :id";
                $stmtSelect = $this->adapter->createStatement($sqlSelect);
                $result = $stmtSelect->execute([':id' => $data['id']])->current();

                $recalcularProxima = false;
                if ($result) {
                    if ($result['data_programada'] != $data['data_programada'] || $result['periodicidade_dias'] != $periodicidade_dias) {
                        $recalcularProxima = true;
                        $data['proxima_execucao'] = $data['data_programada'];
                    } else {
                        $data['proxima_execucao'] = $data['proxima_execucao'];
                    }
                }

                $sql = 'UPDATE programacao_manutencao_preventiva SET 
                            equipamento_id = :equipamento_id,
                            centro_custo_id = :centro_custo_id,
                            nome_solicitante = :nome_solicitante,
                            area_tecnica_id = :area_tecnica_id,
                            observacoes = :observacoes,
                            info_servico = :info_servico,
                            data_programada = :data_programada,
                            proxima_execucao = :proxima_execucao,
                            periodicidade_dias = :periodicidade_dias,
                            dias_aviso = :dias_aviso,
                            setor_id = :setor_id,
                            tipo_ordem_servico = :tipo_ordem_servico,
                            status_programacao = :status_programacao,
                            motivo_cancelamento = :motivo_cancelamento
                        WHERE id = :id';

                $params = [
                    ':equipamento_id' => $data['equipamento_id'] ?? null,
                    ':centro_custo_id' => $data['centro_custo_id'] ?? null,
                    ':nome_solicitante' => $data['nome_solicitante'],
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':info_servico' => $data['info_servico'] ?? null,
                    ':data_programada' => $data['data_programada'],
                    ':proxima_execucao' => $data['proxima_execucao'],
                    ':periodicidade_dias' => $periodicidade_dias,
                    ':dias_aviso' => $data['dias_aviso'] ?? null,
                    ':setor_id' => $data['setor_id'],
                    ':tipo_ordem_servico' => $data['tipo_ordem_servico'],
                    ':status_programacao' => $status_programacao,
                    ':motivo_cancelamento' => $data['motivo_cancelamento'] ?? null,
                    ':id' => $data['id']
                ];

            } else {
                // Inserção nova
                $sql = 'INSERT INTO programacao_manutencao_preventiva (
                    equipamento_id,
                    centro_custo_id,
                    nome_solicitante,
                    area_tecnica_id,
                    info_servico,
                    observacoes,
                    data_programada,
                    periodicidade_dias,
                    dias_aviso,
                    setor_id,
                    tipo_ordem_servico,
                    status_programacao,
                    proxima_execucao
                ) VALUES (
                    :equipamento_id,
                    :centro_custo_id,
                    :nome_solicitante,
                    :area_tecnica_id,
                    :info_servico,
                    :observacoes,
                    :data_programada,
                    :periodicidade_dias,
                    :dias_aviso,
                    :setor_id,
                    :tipo_ordem_servico,
                    :status_programacao,
                    :proxima_execucao
                )';

                $params = [
                    ':equipamento_id' => $data['equipamento_id'] ?? null,
                    ':centro_custo_id' => $data['centro_custo_id'] ?? null,
                    ':nome_solicitante' => $data['nome_solicitante'],
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':info_servico' => $data['info_servico'] ?? null,
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':data_programada' => $data['data_programada'],
                    ':periodicidade_dias' => $periodicidade_dias,
                    ':dias_aviso' => $data['dias_aviso'] ?? null,
                    ':setor_id' => $data['setor_id'],
                    ':tipo_ordem_servico' => $data['tipo_ordem_servico'],
                    ':status_programacao' => $status_programacao,
                    ':proxima_execucao' => $data['data_programada'], // inicializa com data_programada
                ];

            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function atualizarStatusProgramacao($id, $status)
        {
            $sql = 'UPDATE programacao_manutencao_preventiva SET status_programacao = :status WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([
                ':status' => $status,
                ':id' => $id
            ]);
        }
        public function cancelarProgramacao(array $data)
        {
            $sql = "UPDATE programacao_manutencao_preventiva SET status_programacao = 'Cancelada', motivo_cancelamento = :motivo WHERE id = :id";

            $this->adapter->createStatement($sql)->execute([
                ':motivo' => $data['motivo_cancelamento'],
                ':id' => $data['id']
            ]);
        }
        public function gerarOsPreventiva()
        {
            $sql = "SELECT * FROM programacao_manutencao_preventiva WHERE status_programacao = 'Ativa' AND proxima_execucao <= CURRENT_DATE";
            $programacoes = $this->adapter->query($sql, $this->adapter::QUERY_MODE_EXECUTE);

            foreach ($programacoes as $prog) {
                $checkSql = "SELECT COUNT(*) AS total FROM controle_manutencao WHERE programacao_id = ? AND data_programada = ?";
                $check = $this->adapter->query($checkSql, [$prog['id'], $prog['proxima_execucao']])->current();

                if ($check['total'] == 0) {
                    $insertSql = "INSERT INTO controle_manutencao 
                        (data_programada, setor_id, tipo_ordem_servico, equipamento_id, centro_custo_id, nome_solicitante, prioridade,
                        tipo_manutencao_id, area_tecnica_id, status, info_servico, observacoes, programacao_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $this->adapter->query($insertSql, [
                        $prog['proxima_execucao'],  // data_programada
                        $prog['setor_id'],          // setor_id
                        $prog['tipo_ordem_servico'],// tipo_ordem_servico
                        $prog['equipamento_id'],    // equipamento_id
                        $prog['centro_custo_id'],   // centro_custo_id
                        $prog['nome_solicitante'],  // nome_solicitante
                        'Alta',                     // prioridade
                        1,                          // tipo_manutencao_id (fixo)
                        $prog['area_tecnica_id'],   // area_tecnica_id
                        'Pendente',                 // status
                        $prog['info_servico'],      // info_servico
                        $prog['observacoes'],       // observacoes
                        $prog['id']                 // programacao_id
                    ]);

                    $updateSql = "UPDATE programacao_manutencao_preventiva SET 
                                    data_ultima_execucao = proxima_execucao, 
                                    proxima_execucao = proxima_execucao + INTERVAL '{$prog['periodicidade_dias']} days' 
                                WHERE id = ?";
                    $this->adapter->query($updateSql, [$prog['id']]);
                }
            }
        }
    #endRegion

    #region Controle de Manutenção
        public function listarControlesManutencao()
        {
            $sql = "SELECT 
                        *,
                        (
                            select sum(qtd) from (
                                select count(*) qtd from apontamentos_manutencao am where am.id_manutencao = cm.id
                                union
                                select count(*) qtd from itens_manutencao im where im.id_manutencao = cm.id
                            ) A
                        ) as qtd_apontamentos
                    FROM controle_manutencao cm
                    ORDER BY data_programada DESC";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $row['qtd_apontamentos'] = (int)$row['qtd_apontamentos'];
                $data[] = $row;
            }
            return $data;
        }
        public function salvarControleManutencao(array $data)
        {
            // Alimenta tipo_ordem_servico
            if (isset($data['equipamento_id']) && !empty($data['equipamento_id'])) {
                $data['tipo_ordem_servico'] = 'Equipamento';

                 // Busca o setor_id do equipamento
                $sqlSetor = "SELECT setor_id FROM equipamentos WHERE id = :id";
                $result = $this->adapter->createStatement($sqlSetor)->execute([':id' => $data['equipamento_id']])->current();
                if ($result && isset($result['setor_id'])) {
                    $data['setor_id'] = $result['setor_id']; // Sobrescreve o setor vindo do form
                }
            } 
            if (isset($data['centro_custo_id']) && !empty($data['centro_custo_id'])) {
                $data['tipo_ordem_servico'] = 'Centro de Custo';
            } 

            if (isset($data['data_inicio']) && $data['data_inicio'] !== null) {
                $data['status'] = 'Em Execução';
            }

            $params = [
                ':data_solicitacao' => $data['data_solicitacao'] ?? null,
                ':nome_solicitante' => $data['nome_solicitante'] ?? null,
                ':setor_id' => $data['setor_id'],
                ':prioridade' => $data['prioridade'] ?? null,
                ':tipo_ordem_servico' => $data['tipo_ordem_servico'],
                ':equipamento_id' => $data['equipamento_id'] ?? null,
                ':centro_custo_id' => $data['centro_custo_id'] ?? null,
                ':tipo_manutencao_id' => $data['tipo_manutencao_id'],
                ':area_tecnica_id' => $data['area_tecnica_id'],
                ':tecnico_id' => $data['tecnico_id'],
                ':descricao_defeito' => $data['descricao_defeito'] ?? null,
                ':data_inicio' => $data['data_inicio'] ?? null,
                ':tempo_previsto' => $data['tempo_previsto'] ?? null,
                ':data_final' => $data['data_final'] ?? null,
                ':status' => $data['status'] ?? 'Pendente',
                ':info_servico' => $data['info_servico'] ?? null,
                ':observacoes' => $data['observacoes'] ?? null,
            ];

            if (!empty($data['id'])) {
                $sql = "UPDATE controle_manutencao SET 
                            data_solicitacao = :data_solicitacao,
                            nome_solicitante = :nome_solicitante,
                            setor_id = :setor_id,
                            prioridade = :prioridade,
                            tipo_ordem_servico = :tipo_ordem_servico,
                            equipamento_id = :equipamento_id,
                            centro_custo_id = :centro_custo_id,
                            tipo_manutencao_id = :tipo_manutencao_id,
                            area_tecnica_id = :area_tecnica_id,
                            tecnico_id = :tecnico_id,
                            descricao_defeito = :descricao_defeito,
                            data_inicio = :data_inicio,
                            tempo_previsto = :tempo_previsto,
                            data_final = :data_final,
                            status = :status,
                            info_servico = :info_servico,
                            observacoes = :observacoes
                        WHERE id = :id";
                $params[':id'] = $data['id'];
            } else {
                $sql = "INSERT INTO controle_manutencao (
                            data_solicitacao, nome_solicitante, setor_id, prioridade, tipo_ordem_servico,
                            equipamento_id, centro_custo_id, tecnico_id, tipo_manutencao_id, area_tecnica_id, descricao_defeito, 
                            data_inicio, tempo_previsto, data_final, status, info_servico, observacoes
                        ) VALUES (
                            :data_solicitacao, :nome_solicitante, :setor_id, :prioridade, :tipo_ordem_servico,
                            :equipamento_id, :centro_custo_id, :tecnico_id, :tipo_manutencao_id, :area_tecnica_id, :descricao_defeito,
                            :data_inicio, :tempo_previsto, :data_final, :status, :info_servico, :observacoes
                        )";

            }
            // Chama a atualização dos status dos equipamentos
            $this->atualizarStatusEquipamentos();
            
            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirControleManutencao($id)
        {
            $sql = "DELETE FROM controle_manutencao WHERE id = :id";
            $this->adapter->createStatement($sql)->execute([':id' => $id]);

            // Chama a atualização dos status dos equipamentos
            $this->atualizarStatusEquipamentos();
        }
        public function validarOsApontamentos($id)
        {
            $sql = "SELECT COUNT(*) AS total FROM controle_manutencao WHERE status <> 'Finalizada' AND id = :id";
            $statement = $this->adapter->createStatement($sql);
            $statement->prepare();
            $statement->execute(['id' => $id]);

            $result = $statement->getResource()->fetch();

            if ($result && $result['total'] > 0) {
                return [
                    'success' => true,
                    'message' => 'OS válida para apontamentos.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Ordem de Serviço não encontrada ou já finalizada.'
                ];
            }
        }
        public function getApontamentosPorOS($osId)
        {
            $sql = "SELECT * FROM apontamentos_manutencao WHERE id_manutencao = :id";
            $result = $this->adapter->createStatement($sql)->execute(['id' => $osId]);
            
            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function getItensUtilizadosPorOS($osId)
        {
            $sql = "SELECT * FROM itens_manutencao WHERE id_manutencao = :id";
            $result = $this->adapter->createStatement($sql)->execute(['id' => $osId]);
            
            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function apontamentosManutencaoOs(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            $id_manutencao = intval($data['id_os']);

            try {
                // Remove itens anteriores, se houver
                $this->adapter->createStatement('DELETE FROM apontamentos_manutencao WHERE id_manutencao = :id')->execute([':id' => $id_manutencao]);

                // Insere os novos Apontamentos
                if (!empty($data['apontamentos']) && is_array($data['apontamentos'])) {
                    $sqlInsertApontamentos = "
                        INSERT INTO apontamentos_manutencao (
                            id_manutencao,
                            tecnico_id,
                            data_inicio,
                            data_fim,
                            observacao
                        ) VALUES (
                            :id_manutencao,
                            :tecnico_id,
                            :data_inicio,
                            :data_fim,
                            :observacao
                        )
                    ";

                    foreach ($data['apontamentos'] as $apontamentos) {
                        $paramsInsertApontamentos = [
                            ':id_manutencao' => $id_manutencao,
                            ':tecnico_id' => $apontamentos['tecnico_id'] ?? null,
                            ':data_inicio' => $apontamentos['data_inicio'] ?? null,
                            ':data_fim' => $apontamentos['data_fim'] ?? null,
                            ':observacao' => $apontamentos['observacao'] ?? null,
                        ];

                        $this->adapter->createStatement($sqlInsertApontamentos)->execute($paramsInsertApontamentos);
                    }
                }


                // Remove itens anteriores, se houver
                $this->adapter->createStatement('DELETE FROM itens_manutencao WHERE id_manutencao = :id')->execute([':id' => $id_manutencao]);

                // Insere os novos itens
                if (!empty($data['itens']) && is_array($data['itens'])) {
                    $sqlInsertItens = "
                        INSERT INTO itens_manutencao (
                            id_manutencao,
                            cod_produto,
                            descricao_produto,
                            cod_deposito,
                            descricao_deposito,
                            unidade_medida,
                            qtd_utilizada,
                            preco_medio_unitario,
                            custo_unitario,
                            observacao,
                            data_utilizacao
                        ) VALUES (
                            :id_manutencao,
                            :cod_produto,
                            :descricao_produto,
                            :cod_deposito,
                            :descricao_deposito,
                            :unidade_medida,
                            :qtd_utilizada,
                            :preco_medio_unitario,
                            :custo_unitario,
                            :observacao,
                            :data_utilizacao
                        )
                    ";

                    foreach ($data['itens'] as $item) {
                        $paramsInsertItens = [
                            ':id_manutencao' => $id_manutencao,
                            ':cod_produto' => $item['cod_produto'] ?? null,
                            ':descricao_produto' => $item['descricao_produto'] ?? null,
                            ':cod_deposito' => $item['cod_deposito'] ?? null,
                            ':descricao_deposito' => $item['descricao_deposito'] ?? null,
                            ':unidade_medida' => $item['unidade_medida'] ?? null,
                            ':qtd_utilizada' => $item['qtd_utilizada'] ?? 0,
                            ':preco_medio_unitario' => $item['preco_medio'] ?? null,
                            ':custo_unitario' => $item['custo_unitario'] ?? null,
                            ':observacao' => $item['observacao'] ?? null,
                            ':data_utilizacao' => $item['data_utilizacao'] ?? null
                        ];

                        $this->adapter->createStatement($sqlInsertItens)->execute($paramsInsertItens);
                    }
                }

                // Atualiza status de equipamentos
                $this->atualizarStatusEquipamentos();

                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
        public function finalizarOs(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // Primeiro verifica se existem apontamentos para essa OS
                $sqlCount = "SELECT COUNT(*) AS total FROM apontamentos_manutencao WHERE id_manutencao = :id";
                $result = $this->adapter->query($sqlCount, [':id' => $data['id']])->current();

                if ($result && $result['total'] > 0) {
                    // Tem apontamentos: usa data final e tempo execução calculados
                    $sqlUpdate = "UPDATE controle_manutencao 
                                    SET status = 'Finalizada', 
                                        data_final = (
                                            SELECT MAX(data_fim) 
                                            FROM apontamentos_manutencao am 
                                            WHERE am.id_manutencao = :id
                                        ), 
                                        tempo_execucao = (
                                            SELECT (make_interval(secs => SUM(total_horas) * 3600))::time
                                            FROM apontamentos_manutencao am
                                            WHERE am.id_manutencao = :id
                                        )
                                    WHERE id = :id";
                    $paramsUpdate = [
                        ':id' => $data['id'],
                    ];
                } else {
                    // Não tem apontamentos: data_final = NOW(), tempo_execucao = NULL
                    $sqlUpdate = "UPDATE controle_manutencao
                                    SET status = 'Finalizada',
                                        data_final = NOW(),
                                        tempo_execucao = NULL
                                    WHERE id = :id";
                    $paramsUpdate = [
                        ':id' => $data['id'],
                    ];
                }

                $this->adapter->createStatement($sqlUpdate)->execute($paramsUpdate);

                // Atualiza status de equipamentos
                $this->atualizarStatusEquipamentos();

                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
    #endRegion

    #region Controle Retirada Itens
        public function listarItensPendentes()
        {
            $sql = "SELECT 
                        im.id 
                        ,LPAD(cm.id::text, 5, '0') AS nr_ordem_servico
                        ,im.cod_produto
                        ,im.descricao_produto 
                        ,im.cod_deposito 
                        ,im.descricao_deposito 
                        ,im.qtd_utilizada 
                        ,im.custo_unitario 
                        ,im.custo_total 
                    FROM itens_manutencao im 
                    LEFT JOIN controle_manutencao cm on cm.id = im.id_manutencao 
                    WHERE (im.flg_retirado = false or im.flg_retirado = null)";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function marcarRetirada(array $data)
        {
            if (empty($data['ids']) || !is_array($data['ids'])) {
                throw new \InvalidArgumentException('Nenhum item informado para retirada.');
            }

            $sql = "UPDATE itens_manutencao 
                    SET flg_retirado = TRUE
                    WHERE id = :id";

            $stmt = $this->adapter->createStatement($sql);

            foreach ($data['ids'] as $id) {
                $stmt->execute([':id' => $id]);
            }
        }
    #endRegion
    
    #region Dashboard Controle de Manutenção
        public function buscarResumoCards($dataInicio, $dataFim)
        {
            $sql = "
                SELECT
                    COUNT(DISTINCT cm.id) FILTER (WHERE status = 'Finalizada') AS finalizadas,
                    COUNT(DISTINCT cm.id) FILTER (WHERE status = 'Programada') AS programadas,
                    COUNT(DISTINCT cm.id) FILTER (WHERE status = 'Pendente') AS pendentes,
                    COUNT(DISTINCT cm.id) AS total,
                    COALESCE(SUM(im.custo_total), 0) AS custo_total
                FROM controle_manutencao cm
                LEFT JOIN itens_manutencao im ON cm.id = im.id_manutencao
                WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
            ";

            $stmt = $this->adapter->createStatement($sql, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);
            $result = $stmt->execute()->current(); 
            return $result;
        }
        public function buscarPorTipoManutencao($dataInicio, $dataFim)
        {
            $sql = "
                SELECT tm.nome AS tipo, COUNT(distinct cm.id) AS quantidade
                FROM controle_manutencao cm
                JOIN tipos_manutencao tm ON tm.id = cm.tipo_manutencao_id
                WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
                GROUP BY tm.nome
            ";

            $stmt = $this->adapter->createStatement($sql, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);
            $result = $stmt->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function buscarPorAreaTecnica($dataInicio, $dataFim)
        {
            $sql = "
                SELECT at.nome AS area, COUNT(distinct cm.id) AS quantidade
                FROM controle_manutencao cm
                JOIN areas_tecnicas at ON at.id = cm.area_tecnica_id
                WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
                GROUP BY at.nome
            ";

            $stmt = $this->adapter->createStatement($sql, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);
            $result = $stmt->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function buscarPorEquipamento($dataInicio, $dataFim)
        {
            $sql = "
                    SELECT 
                        (e.codigo || '-' || e.nome) AS equipamento,
                        tm.nome AS tipo,
                        COUNT(distinct cm.id) AS quantidade
                    FROM controle_manutencao cm
                    JOIN equipamentos e ON e.id = cm.equipamento_id
                    JOIN tipos_manutencao tm ON tm.id = cm.tipo_manutencao_id
                    WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
                    GROUP BY (e.codigo || '-' || e.nome), tm.nome
            ";

            $stmt = $this->adapter->createStatement($sql, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);
            $result = $stmt->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function buscarPorSetor($dataInicio, $dataFim)
        {
            $sql = "
                SELECT s.nome AS setor, COUNT(distinct cm.id) AS quantidade
                FROM controle_manutencao cm
                JOIN setores s ON s.id = cm.setor_id
                WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
                GROUP BY s.nome
            ";

            $stmt = $this->adapter->createStatement($sql, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);
            $result = $stmt->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function buscarPorTecnico($dataInicio, $dataFim)
        {
            // Consulta para buscar
            $sql = "SELECT 
                        t.nome AS tecnico,
                        COUNT(distinct cm.id) AS quantidade
                    from apontamentos_manutencao am 
                    left join tecnicos t on t.id = am.tecnico_id 
                    left join controle_manutencao cm on cm.id = am.id_manutencao 
                    WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
                    GROUP BY t.nome";

            $stmt = $this->adapter->createStatement($sql, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);
            $result = $stmt->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function getDetalhamentoCard($tipo = null, $dataInicio = null, $dataFim = null)
        {
            $sqlBase = "
                SELECT 
                    LPAD(cm.id::text, 3, '0') AS nr_ordem_servico,
                    eq.nome AS equipamento,
                    s.nome AS setor,
                    at.nome AS area_tecnica,
                    tm.nome AS tipo_manutencao,
                    t.nome AS nome_tecnico,
                    cm.data_solicitacao,
                    cm.data_inicio,
                    cm.data_final,
                    cm.status,
                    COALESCE(SUM(im.custo_total), 0) AS custo_total
                FROM controle_manutencao cm
                INNER JOIN equipamentos eq ON eq.id = cm.equipamento_id
                INNER JOIN setores s ON s.id = cm.setor_id
                INNER JOIN areas_tecnicas at ON at.id = cm.area_tecnica_id
                INNER JOIN tipos_manutencao tm ON tm.id = cm.tipo_manutencao_id
                INNER JOIN tecnicos t ON t.id = cm.tecnico_id
                LEFT JOIN itens_manutencao im ON im.id_manutencao = cm.id
                WHERE cm.data_solicitacao BETWEEN :inicio AND :fim
            ";

            // Adiciona filtro conforme o tipo
            switch ($tipo) {
                case 'FINALIZADAS':
                    $sqlBase .= " AND cm.status = 'Finalizada'";
                    break;
                case 'PROGRAMADAS':
                    $sqlBase .= " AND cm.status = 'Programada'";
                    break;
                case 'PENDENTES':
                    $sqlBase .= " AND cm.status = 'Pendente'";
                    break;
                case 'CUSTO_TOTAL':
                    $sqlBase .= " AND im.custo_total > 0";
                    break;
                case 'TOTAL':
                default:
                    // Sem filtros adicionais
                    break;
            }

            $sqlBase .= " GROUP BY cm.id, eq.nome, s.nome, at.nome, tm.nome, t.nome, cm.data_solicitacao, cm.status
                          ORDER BY cm.data_solicitacao DESC";

            $stmt = $this->adapter->createStatement($sqlBase, [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]);

            $result = $stmt->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }



    #endRegion

    #region Ordem de Serviço
        public function getInfoOrdemServico($id)
        {
            $sql = "SELECT
                        cm.id, 
                        LPAD(cm.id::text, 5, '0') AS nr_ordem_servico,
                        TO_CHAR(cm.data_programada, 'DD/MM/YYYY') AS data_programada,
                        TO_CHAR(cm.data_solicitacao, 'DD/MM/YYYY') AS data_solicitacao,
                        TO_CHAR(cm.data_inicio, 'DD/MM/YYYY') AS data_inicio,
                        TO_CHAR(cm.data_final, 'DD/MM/YYYY') AS data_final,
                        cm.nome_solicitante,
                        s.nome AS nome_setor,
                        cm.prioridade,
                        e.nome AS nome_equipamento,
                        tm.nome AS tipo_manutencao,
                        at.nome AS area_tecnica,
                        cm.descricao_defeito,
                        cm.status,
                        cm.info_servico,
                        cm.observacoes,
                        (
                            SELECT string_agg(DISTINCT t.nome, ', ')
                            FROM apontamentos_manutencao am
                            LEFT JOIN tecnicos t ON t.id = am.tecnico_id
                            WHERE am.id_manutencao = cm.id
                        ) AS nome_tecnico_exec,
                        t.nome as nome_tecnico,
                        cm.centro_custo_id
                    FROM controle_manutencao cm
                    LEFT JOIN setores s ON s.id = cm.setor_id
                    LEFT JOIN equipamentos e ON e.id = cm.equipamento_id
                    LEFT JOIN tipos_manutencao tm ON tm.id = cm.tipo_manutencao_id
                    LEFT JOIN areas_tecnicas at ON at.id = cm.area_tecnica_id
                    LEFT JOIN tecnicos t ON t.id = cm.tecnico_id
                    WHERE cm.id = :id";
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute([':id' => $id]);
            return $result->current();
        }
        public function getInfoItensOrdemServico($id)
        {
            $sql = "select * from itens_manutencao im
                    WHERE im.id_manutencao = :id";
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute([':id' => $id]);
            
            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
    #endRegion
}
