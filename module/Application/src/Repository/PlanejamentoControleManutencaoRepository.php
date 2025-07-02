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
            $sql = 'SELECT id, nome, descricao FROM tipos_manutencao where flg_ativo = true ORDER BY nome';
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
            $sql = 'SELECT id, nome, cpf, cargo_funcao, contato, area_tecnica_id, flg_ativo FROM tecnicos ORDER BY id';
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
                            cpf = :cpf, 
                            cargo_funcao = :cargo_funcao, 
                            contato = :contato, 
                            area_tecnica_id = :area_tecnica_id,
                            flg_ativo = :flg_ativo
                        WHERE id = :id';
                $params = [
                    ':nome' => $data['nome'],
                    ':cpf' => $data['cpf'],
                    ':cargo_funcao' => $data['cargo_funcao'],
                    ':contato' => $data['contato'] ?? null,
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':flg_ativo' => $flgAtivo,
                    ':id' => $data['id']
                ];
            } else {
                $sql = 'INSERT INTO tecnicos (nome, cpf, cargo_funcao, contato, area_tecnica_id, flg_ativo) 
                        VALUES (:nome, :cpf, :cargo_funcao, :contato, :area_tecnica_id, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
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
            $sql = "SELECT id, codigo, nome, setor_id, status, observacoes, centro_custo FROM equipamentos ORDER BY codigo";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarEquipamento(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do equipamento é obrigatório.');
            }

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
        public function getLookupEquipamentos()
        {
            $sql = "SELECT id, codigo, nome, setor_id, status, observacoes, centro_custo FROM equipamentos where status NOT LIKE 'Inativo' ORDER BY codigo";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function atualizarStatusEquipamentos() // Essa função atualiza o status dos equipamentos com base nas manutenções ativas
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // Atualiza para "Em Manutenção" se a data_inicio for menor ou igual a hoje e não tiver data_fim ou data_fim maior que hoje
                $sqlUpdateManutencao = "
                    UPDATE equipamentos
                    SET status = 'Em Manutenção'
                    WHERE id IN (
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
                    WHERE id NOT IN (
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
            $sql = "SELECT pmp.*, eq.nome as nome_equipamento, at.nome as nome_area, st.nome as nome_setor
                    FROM programacao_manutencao_preventiva pmp
                    JOIN equipamentos eq ON eq.id = pmp.equipamento_id
                    JOIN areas_tecnicas at ON at.id = pmp.area_tecnica_id
                    JOIN setores st ON st.id = pmp.setor_id
                    where eq.status = 'Ativo'
                    and pmp.status = 'Programada'
                    ORDER BY pmp.data_programada DESC";

            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarProgramacaoPreventiva(array $data)
        {
            if (!empty($data['id'])) {
                $sql = 'UPDATE programacao_manutencao_preventiva SET 
                            equipamento_id = :equipamento_id,
                            area_tecnica_id = :area_tecnica_id,
                            descricao_servico = :descricao_servico,
                            data_programada = :data_programada,
                            setor_id = :setor_id,
                            status = :status
                        WHERE id = :id';

                $params = [
                    ':equipamento_id' => $data['equipamento_id'],
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':descricao_servico' => $data['descricao_servico'],
                    ':data_programada' => $data['data_programada'],
                    ':setor_id' => $data['setor_id'],
                    ':status' => $data['status'],
                    ':id' => $data['id']
                ];
            } else {
                $sql = 'INSERT INTO programacao_manutencao_preventiva (
                            equipamento_id, area_tecnica_id, descricao_servico, data_programada, setor_id, status
                        ) VALUES (
                            :equipamento_id, :area_tecnica_id, :descricao_servico, :data_programada, :setor_id, :status
                        )';

                $params = [
                    ':equipamento_id' => $data['equipamento_id'],
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':descricao_servico' => $data['descricao_servico'],
                    ':data_programada' => $data['data_programada'],
                    ':setor_id' => $data['setor_id'],
                    ':status' => $data['status'] ?? 'Programada'
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirProgramacaoPreventiva($id)
        {
            $sql = 'DELETE FROM programacao_manutencao_preventiva WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function aprovarProgramacaoPreventiva(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // Inserção em controle_manutencao
                $sqlInsert = "INSERT INTO controle_manutencao (
                                equipamento_id,
                                area_tecnica_id,
                                setor_id,
                                data_programada,
                                descricao_defeito,
                                status,
                                data_solicitacao,
                                data_inicio,
                                nome_solicitante,
                                prioridade,
                                tipo_manutencao_id,
                                tecnico_id
                            ) VALUES (
                                :equipamento_id,
                                :area_tecnica_id,
                                :setor_id,
                                :data_programada,
                                :descricao_defeito,
                                'Pendente',
                                :data_solicitacao,
                                :data_inicio,
                                :nome_solicitante,
                                :prioridade,
                                :tipo_manutencao_id,
                                :tecnico_id
                            )";
                $paramsInsert = [
                    ':equipamento_id' => $data['equipamento_id'],
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':setor_id' => $data['setor_id'],
                    ':data_programada' => $data['data_programada'],
                    ':descricao_defeito' => $data['descricao_defeito'],
                    ':data_solicitacao' => $data['data_solicitacao'],
                    ':data_inicio' => $data['data_inicio'],
                    ':nome_solicitante' => $data['nome_solicitante'],
                    ':prioridade' => $data['prioridade'],
                    ':tipo_manutencao_id' => $data['tipo_manutencao_id'],
                    ':tecnico_id' => $data['tecnico_id']
                ];
                $this->adapter->createStatement($sqlInsert)->execute($paramsInsert);

                // Atualiza programacao_manutencao_preventiva para status 'Pendente'
                $sqlUpdate = "UPDATE programacao_manutencao_preventiva SET status = 'Pendente' WHERE
                            equipamento_id = :equipamento_id
                            AND area_tecnica_id = :area_tecnica_id
                            AND setor_id = :setor_id
                            AND data_programada = :data_programada";
                $paramsUpdate = [
                    ':equipamento_id' => $data['equipamento_id'],
                    ':area_tecnica_id' => $data['area_tecnica_id'],
                    ':setor_id' => $data['setor_id'],
                    ':data_programada' => $data['data_programada'],
                ];
                $this->adapter->createStatement($sqlUpdate)->execute($paramsUpdate);
                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
        public function reprovarProgramacaoPreventiva(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // Atualiza programacao_manutencao_preventiva para status 'Reprovada' com base no ID
                $sqlUpdate = "UPDATE programacao_manutencao_preventiva 
                            SET status = 'Reprovada' 
                            WHERE id = :id";
                $paramsUpdate = [
                    ':id' => $data['id']
                ];
                $this->adapter->createStatement($sqlUpdate)->execute($paramsUpdate);

                $this->adapter->getDriver()->getConnection()->commit();
            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
    #endRegion

    #region Controle de Manutenção
        public function listarControlesManutencao()
        {
            $sql = "SELECT * FROM controle_manutencao ORDER BY data_programada DESC";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarControleManutencao(array $data)
        {
            if (empty($data['data_programada']) || empty($data['equipamento_id']) || empty($data['tipo_manutencao_id']) || empty($data['area_tecnica_id'])) {
                throw new \Exception('Campos obrigatórios não preenchidos.');
            }

            $params = [
                ':data_programada' => $data['data_programada'],
                ':data_solicitacao' => $data['data_solicitacao'] ?? null,
                ':nome_solicitante' => $data['nome_solicitante'] ?? null,
                ':setor_id' => $data['setor_id'],
                ':prioridade' => $data['prioridade'] ?? null,
                ':equipamento_id' => $data['equipamento_id'],
                ':tipo_manutencao_id' => $data['tipo_manutencao_id'],
                ':area_tecnica_id' => $data['area_tecnica_id'],
                ':descricao_defeito' => $data['descricao_defeito'] ?? null,
                ':tecnico_id' => $data['tecnico_id'],
                ':data_inicio' => $data['data_inicio'] ?? null,
                ':data_final' => $data['data_final'] ?? null,
                ':status' => $data['status'] ?? 'Pendente',
                ':info_servico' => $data['info_servico'] ?? null,
                ':observacoes' => $data['observacoes'] ?? null,
                ':custo_total' => $data['custo_total'] ?? null,
            ];

            if (!empty($data['id'])) {
                $sql = "UPDATE controle_manutencao SET 
                            data_programada = :data_programada,
                            data_solicitacao = :data_solicitacao,
                            nome_solicitante = :nome_solicitante,
                            setor_id = :setor_id,
                            prioridade = :prioridade,
                            equipamento_id = :equipamento_id,
                            tipo_manutencao_id = :tipo_manutencao_id,
                            area_tecnica_id = :area_tecnica_id,
                            descricao_defeito = :descricao_defeito,
                            tecnico_id = :tecnico_id,
                            data_inicio = :data_inicio,
                            data_final = :data_final,
                            status = :status,
                            info_servico = :info_servico,
                            observacoes = :observacoes,
                            custo_total = :custo_total
                        WHERE id = :id";
                $params[':id'] = $data['id'];
            } else {
                $sql = "INSERT INTO controle_manutencao (
                            data_programada, data_solicitacao, nome_solicitante, setor_id, prioridade,
                            equipamento_id, tipo_manutencao_id, area_tecnica_id, descricao_defeito, tecnico_id,
                            data_inicio, data_final, status, info_servico, observacoes, custo_total
                        ) VALUES (
                            :data_programada, :data_solicitacao, :nome_solicitante, :setor_id, :prioridade,
                            :equipamento_id, :tipo_manutencao_id, :area_tecnica_id, :descricao_defeito, :tecnico_id,
                            :data_inicio, :data_final, :status, :info_servico, :observacoes, :custo_total
                        )";
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirControleManutencao($id)
        {
            $sql = "DELETE FROM controle_manutencao WHERE id = :id";
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function finalizarManutencao(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                $sqlUpdate = "
                    UPDATE controle_manutencao
                    SET 
                        data_final = :data_final,
                        info_servico = :info_servico,
                        observacoes = :observacoes,
                        custo_total = :custo_total,
                        status = 'Finalizada'
                    WHERE id = :id
                ";

                $paramsUpdate = [
                    ':data_final' => $data['data_final'] ?? null,
                    ':info_servico' => $data['info_servico'] ?? null,
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':custo_total' => $data['custo_total'] ?? null,
                    ':id' => $data['id'],
                ];

                $this->adapter->createStatement($sqlUpdate)->execute($paramsUpdate);

                 // Chama a atualização dos status dos equipamentos
                $this->atualizarStatusEquipamentos();

                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
    #endRegion

    #region Ordem de Serviço
        public function getInfoOrdemServico($id)
        {
            $sql = "SELECT
                        cm.id, 
                        LPAD(cm.id::text, 3, '0') AS nr_ordem_servico,
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
                        t.nome AS nome_tecnico,
                        cm.status,
                        cm.info_servico,
                        cm.observacoes,
                        cm.custo_total
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
    #endRegion
}
