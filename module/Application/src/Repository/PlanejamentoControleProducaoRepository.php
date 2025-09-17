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
                // Remove caracteres especiais para evitar problemas na busca
                $searchTerm = str_replace(['%', '_'], ['\%', '\_'], $search);
                
                $ands .= " AND (e.codigo::text ILIKE '%{$searchTerm}%' 
                            OR e.nome::text ILIKE '%{$searchTerm}%' 
                            OR s.nome::text ILIKE '%{$searchTerm}%' 
                            OR e.observacoes::text ILIKE '%{$searchTerm}%' 
                            OR e.centro_custo::text ILIKE '%{$searchTerm}%') ";
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
}
