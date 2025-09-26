<?php

namespace Application\Repository;

use Laminas\Db\Adapter\AdapterInterface;

class PlanejamentoControleProducaoRepository
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    #region Cadastro Departamentos
        public function listarDepartamentos()
        {
            $sql = 'SELECT id, nome, descricao, flg_ativo 
                    FROM pcp_departamento 
                    ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function salvarDepartamento(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do departamento é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : false;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE pcp_departamento SET 
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
                $sql = 'INSERT INTO pcp_departamento (nome, descricao, flg_ativo) 
                        VALUES (:nome, :descricao, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':descricao' => $data['descricao'] ?? null,
                    ':flg_ativo' => $flgAtivo,
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirDepartamento($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do departamento não fornecido.');
            }

            $sql = 'UPDATE pcp_departamento SET flg_ativo = false WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupDepartamentos()
        {
            $sql = 'SELECT id, nome, descricao 
                    FROM pcp_departamento 
                    WHERE flg_ativo = true 
                    ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
    #endRegion

    #region Cadastro Funcionários
        public function listarFuncionarios()
        {
            $sql = 'SELECT id, numcad, nome, cpf, cargo_funcao, contato, departamento_id, flg_ativo 
                    FROM pcp_funcionario 
                    ORDER BY id';
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }
            return $data;
        }
        public function salvarFuncionario(array $data)
        {
            if (empty($data['nome']) || empty($data['cpf']) || empty($data['cargo_funcao']) || empty($data['departamento_id'])) {
                throw new \Exception('Nome, CPF, cargo/função e departamento são obrigatórios.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : true;

            // 🔎 Verifica se CPF já existe
            $sqlCheck = 'SELECT id FROM pcp_funcionario WHERE cpf = :cpf';
            $paramsCheck = [':cpf' => $data['cpf']];
            $result = $this->adapter->createStatement($sqlCheck)->execute($paramsCheck)->current();

            if ($result) {
                if (empty($data['id']) || $result['id'] != $data['id']) {
                    throw new \Exception('Já existe um funcionário cadastrado com este CPF.');
                }
            }

            if (!empty($data['id'])) {
                $sql = 'UPDATE pcp_funcionario SET 
                            nome = :nome, 
                            numcad = :numcad, 
                            cpf = :cpf, 
                            cargo_funcao = :cargo_funcao, 
                            contato = :contato, 
                            departamento_id = :departamento_id,
                            flg_ativo = :flg_ativo
                        WHERE id = :id';
                $params = [
                    ':nome' => $data['nome'],
                    ':numcad' => $data['numcad'],
                    ':cpf' => $data['cpf'],
                    ':cargo_funcao' => $data['cargo_funcao'],
                    ':contato' => $data['contato'] ?? null,
                    ':departamento_id' => $data['departamento_id'],
                    ':flg_ativo' => $flgAtivo,
                    ':id' => $data['id']
                ];
            } else {
                $sql = 'INSERT INTO pcp_funcionario (nome, numcad, cpf, cargo_funcao, contato, departamento_id, flg_ativo) 
                        VALUES (:nome, :numcad, :cpf, :cargo_funcao, :contato, :departamento_id, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':numcad' => $data['numcad'],
                    ':cpf' => $data['cpf'],
                    ':cargo_funcao' => $data['cargo_funcao'],
                    ':contato' => $data['contato'] ?? null,
                    ':departamento_id' => $data['departamento_id'],
                    ':flg_ativo' => $flgAtivo
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirFuncionario($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do funcionário não fornecido.');
            }

            $sql = 'UPDATE pcp_funcionario SET flg_ativo = false WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupFuncionarios()
        {
            $sql = 'SELECT 
                        f.id, 
                        f.nome, 
                        f.cpf, 
                        f.cargo_funcao, 
                        f.contato, 
                        f.departamento_id,
                        d.nome as nome_departamento
                    FROM pcp_funcionario f
                    LEFT JOIN pcp_departamento d ON d.id = f.departamento_id
                    WHERE f.flg_ativo = true 
                    ORDER BY d.nome, f.nome';
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
                        e.quantidade,
                        e.quantidade_disponivel,
                        case when quantidade_disponivel = 0 then true else false end disabled,
                        e.valor,
                        e.custo_total,
                        e.codigo || ' - ' || e.nome AS dsc_equipamento,
                        e.observacoes,
                        e.status
                    FROM pcp_equipamentos e
                    ORDER BY e.codigo";
                    
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $row['quantidade'] = floatval($row['quantidade']);
                $row['quantidade_disponivel'] = floatval($row['quantidade_disponivel']);
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
                $sqlCheck = "SELECT COUNT(*) AS total FROM pcp_equipamentos WHERE codigo = :codigo";
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
                $sql = 'UPDATE pcp_equipamentos 
                        SET codigo = :codigo, 
                            nome = :nome, 
                            quantidade = :quantidade,
                            valor = :valor,
                            observacoes = :observacoes,
                            status = :status
                        WHERE id = :id';
                $params = [
                    ':codigo' => $data['codigo'],
                    ':nome' => $data['nome'],
                    ':quantidade' => $data['quantidade'] ?? null,
                    ':valor' => $data['valor'] ?? null,
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':status' => $data['status'] ?? null,
                    ':id' => $data['id']
                ];
            } else {
                $sql = 'INSERT INTO pcp_equipamentos (codigo, nome, quantidade, quantidade_disponivel, valor, observacoes, status) 
                        VALUES (:codigo, :nome, :quantidade, :quantidade_disponivel, :valor, :observacoes, :status)';
                $params = [
                    ':codigo' => $data['codigo'],
                    ':nome' => $data['nome'],
                    ':quantidade' => $data['quantidade'] ?? null,
                    ':quantidade_disponivel' => $data['quantidade'] ?? null, //na inserção mesma quantidade total.
                    ':valor' => $data['valor'] ?? null,
                    ':observacoes' => $data['observacoes'] ?? null,
                    ':status' => $data['status'] ?? null
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirEquipamento($id)
        {
            $sql = 'DELETE FROM pcp_equipamentos WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupEquipamentos($search = null, $key = null, $offset = 0, $limit = 30)
        {
            $ands = "";
            if (!empty($search)) {
                $searchTerm = str_replace(['%', '_'], ['\%', '\_'], $search);
                $ands .= " AND (e.codigo::text ILIKE '%{$searchTerm}%' 
                            OR e.nome::text ILIKE '%{$searchTerm}%' 
                            OR e.observacoes::text ILIKE '%{$searchTerm}%') ";
            }

            if (!empty($key)) {
                $ands .= " AND e.id = $key ";
            }
            
            $sql = "SELECT 
                        e.id, 
                        e.codigo, 
                        e.nome, 
                        e.quantidade,
                        e.quantidade_disponivel,
                        case when quantidade_disponivel = 0 then true else false end disabled,
                        e.valor,
                        e.custo_total,
                        e.codigo || ' - ' || e.nome AS dsc_equipamento,
                        e.observacoes,
                        e.status
                    FROM pcp_equipamentos e
                    WHERE 1=1
                    {$ands}
                    ORDER BY e.codigo
                    LIMIT $limit OFFSET $offset";
                    
            $result = $this->adapter->createStatement($sql)->execute();

            $countSql = "SELECT COUNT(*) as total 
                        FROM pcp_equipamentos e
                        WHERE 1=1
                        {$ands}";
                        
            $countResult = $this->adapter->createStatement($countSql)->execute()->current();
            $totalCount = $countResult['total'] ?? 0;

            $data = [];
            foreach ($result as $row) {
                $row['quantidade'] = floatval($row['quantidade']);
                $row['quantidade_disponivel'] = floatval($row['quantidade_disponivel']);
                $data[] = $row;
            }
            
            return [
                'data' => $data,
                'totalCount' => $totalCount
            ];
        }
        public function recalcularQuantidadeEquipamento($equipamentoId)
        {
            // 1. Pega a quantidade total cadastrada no equipamento
            $sqlTotal = "SELECT quantidade FROM pcp_equipamentos WHERE id = :id";
            $result = $this->adapter->createStatement($sqlTotal)->execute([':id' => $equipamentoId])->current();
            $quantidadeTotal = $result ? (int)$result['quantidade'] : 0;

            // 2. Soma todos os empréstimos ativos para esse equipamento
            $sqlEmprestimos = "SELECT COALESCE(SUM(quantidade_emprestimo), 0) AS total_emprestado
                            FROM pcp_controle_emprestimo
                            WHERE equipamento_id = :id
                            AND status <> 'Devolvido'";
            $resultEmprestimos = $this->adapter->createStatement($sqlEmprestimos)->execute([':id' => $equipamentoId])->current();
            $totalEmprestado = $resultEmprestimos ? (int)$resultEmprestimos['total_emprestado'] : 0;

            // 3. Calcula o disponível
            $quantidadeDisponivel = max(0, $quantidadeTotal - $totalEmprestado);

            // 4. Atualiza a tabela de equipamentos
            $sqlUpdate = "UPDATE pcp_equipamentos 
                        SET quantidade_disponivel = :disponivel
                        WHERE id = :id";
            $this->adapter->createStatement($sqlUpdate)->execute([
                ':disponivel' => $quantidadeDisponivel,
                ':id' => $equipamentoId
            ]);
        }
    #endRegion

    #region Controle de Empréstimo
        public function listarControlesEmprestimo()
        {
            $sql = "SELECT 
                        cm.*,
                        CASE 
                            WHEN cm.data_programada_devolucao < CURRENT_DATE and cm.status not in ('Devolvido') THEN 'Atrasado'
                            ELSE cm.status
                        END AS status,
                        pe.quantidade_disponivel
                    FROM pcp_controle_emprestimo cm
                    LEFT JOIN pcp_equipamentos pe on pe.id = cm.equipamento_id";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                $row['quantidade_emprestimo'] = floatval($row['quantidade_emprestimo']);
                $row['quantidade_disponivel'] = floatval($row['quantidade_disponivel']);
                $data[] = $row;
            }
            return $data;
        }
        public function salvarControleEmprestimo(array $data)
        {
            // Validações
            if (isset($data['quantidade_emprestimo']) && $data['quantidade_emprestimo'] > 0) {
                $equipamentoId = $data['equipamento_id'];
                $idAtual = $data['id'] ?? null;

                // Buscar quantidade total do equipamento
                $sqlTotal = "SELECT quantidade FROM pcp_equipamentos WHERE id = :id";
                $equipamento = $this->adapter->createStatement($sqlTotal)->execute([':id' => $equipamentoId])->current();
                $quantidadeTotal = (float)($equipamento['quantidade'] ?? 0);

                // Somar todas as quantidades já emprestadas, exceto o registro atual se for edição
                $sqlEmprestado = "SELECT COALESCE(SUM(quantidade_emprestimo),0) AS emprestado
                                FROM pcp_controle_emprestimo
                                WHERE equipamento_id = :equipamento_id
                                and status not in ('Devolvido')
                                " . (!empty($idAtual) ? "AND id <> :id" : "");
                $params = [':equipamento_id' => $equipamentoId];
                if (!empty($idAtual)) {
                    $params[':id'] = $idAtual;
                }
                $emprestado = $this->adapter->createStatement($sqlEmprestado)->execute($params)->current();
                $quantidadeEmprestada = (float)$emprestado['emprestado'];

                // Calcula quantidade disponível
                $quantidadeDisponivel = $quantidadeTotal - $quantidadeEmprestada;

                if ($data['quantidade_emprestimo'] > $quantidadeDisponivel) {
                    throw new \InvalidArgumentException('Quantidade a ser emprestada não pode ser superior à quantidade disponível!');
                }
            }

            $params = [
                ':data_emprestimo'       => $data['data_emprestimo'] ?? null,
                ':numcad'                => $data['numcad'] ?? null,
                ':nome'                  => $data['nome'] ?? null,
                ':cpf'                   => $data['cpf'] ?? null,
                ':cargo_funcao'          => $data['cargo_funcao'] ?? null,
                ':contato'               => $data['contato'] ?? null,
                ':departamento_id'       => $data['departamento_id'] ?? null,
                ':equipamento_id'        => $data['equipamento_id'] ?? null,
                ':quantidade_emprestimo' => $data['quantidade_emprestimo'] ?? 0,
                ':data_programada_devolucao'        => $data['data_programada_devolucao'] ?? null,
                ':observacoes'           => $data['observacoes'] ?? null,
                ':status'           => $data['status'] ?? 'Retirado',
            ];

            if (!empty($data['id'])) {
                $sql = "UPDATE pcp_controle_emprestimo SET 
                            data_emprestimo = :data_emprestimo,
                            numcad = :numcad,
                            nome = :nome,
                            cpf = :cpf,
                            cargo_funcao = :cargo_funcao,
                            contato = :contato,
                            departamento_id = :departamento_id,
                            equipamento_id = :equipamento_id,
                            quantidade_emprestimo = :quantidade_emprestimo,
                            data_programada_devolucao = :data_programada_devolucao,
                            observacoes = :observacoes,
                            status = :status
                        WHERE id = :id";
                $params[':id'] = $data['id'];
            } else {
                $sql = "INSERT INTO pcp_controle_emprestimo (
                            data_emprestimo, numcad, nome, cpf, cargo_funcao, contato, 
                            departamento_id, equipamento_id, quantidade_emprestimo, 
                            data_programada_devolucao, observacoes, status
                        ) VALUES (
                            :data_emprestimo, :numcad, :nome, :cpf, :cargo_funcao, :contato, 
                            :departamento_id, :equipamento_id, :quantidade_emprestimo, 
                            :data_programada_devolucao, :observacoes, :status
                        )";
            }

            $this->adapter->createStatement($sql)->execute($params);

            // Sempre recalcula a quantidade disponível após salvar
            if (!empty($data['equipamento_id'])) {
                $this->recalcularQuantidadeEquipamento($data['equipamento_id']);
            }
        }

        public function excluirControleEmprestimo($id)
        {
            // 1. Descobrir qual equipamento estava vinculado ao empréstimo
            $sqlSelect = "SELECT equipamento_id 
                        FROM pcp_controle_emprestimo 
                        WHERE id = :id";
            $row = $this->adapter->createStatement($sqlSelect)->execute([':id' => $id])->current();
            $equipamentoId = $row['equipamento_id'] ?? null;

            // 2. Excluir o registro
            $sql = "DELETE FROM pcp_controle_emprestimo WHERE id = :id";
            $this->adapter->createStatement($sql)->execute([':id' => $id]);

            // 3. Recalcular disponível do equipamento
            if (!empty($equipamentoId)) {
                $this->recalcularQuantidadeEquipamento($equipamentoId);
            }
        }

        public function getInfoTermoEmprestimo($id)
        {
            $sql = "SELECT
                        cm.*, 
                        LPAD(cm.id::text, 5, '0') AS nr_termo,
                        TO_CHAR(cm.data_emprestimo, 'DD/MM/YYYY') AS data_emprestimo,
                        TO_CHAR(cm.data_programada_devolucao, 'DD/MM/YYYY') AS data_programada_devolucao,
                        TO_CHAR(cm.data_devolucao, 'DD/MM/YYYY') AS data_devolucao,
                        e.codigo || '-' || e.nome AS nome_equipamento,
                        at.nome AS departamento_nome,
                        cm.observacoes
                    FROM pcp_controle_emprestimo cm
                    LEFT JOIN pcp_equipamentos e ON e.id = cm.equipamento_id
                    LEFT JOIN pcp_departamento at ON at.id = cm.departamento_id
                    WHERE cm.id = :id
                  ";
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute([':id' => $id]);
            return $result->current();
        }
        public function marcarDevolucaoEquipamento(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                // 
                $sqlValida = "SELECT cm.* FROM pcp_controle_emprestimo cm WHERE cm.id = :id";
                $resultValida = $this->adapter->query($sqlValida, [':id' => $data['id']])->current();
                if (!$resultValida) {
                    throw new \InvalidArgumentException('Empréstimo não encontrada.');
                }

                // Não tem apontamentos: data_final = NOW(), tempo_execucao = NULL
                $sqlUpdate = "UPDATE pcp_controle_emprestimo SET status = 'Devolvido', data_devolucao = NOW() WHERE id = :id";
                $paramsUpdate = [
                    ':id' => $data['id'],
                ];

                $this->adapter->createStatement($sqlUpdate)->execute($paramsUpdate);

                // Sempre recalcula a quantidade disponível após salvar
                if (!empty($data['equipamento_id'])) {
                    $this->recalcularQuantidadeEquipamento($data['equipamento_id']);
                }

                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }

    #endRegion
}
