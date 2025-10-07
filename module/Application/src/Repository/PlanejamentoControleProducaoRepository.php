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

    // #region Cadastro Funcionários
    //     public function listarFuncionarios()
    //     {
    //         $sql = 'SELECT id, numcad, nome, cpf, cargo_funcao, contato, departamento_id, flg_ativo 
    //                 FROM pcp_funcionario 
    //                 ORDER BY id';
    //         $result = $this->adapter->createStatement($sql)->execute();

    //         $data = [];
    //         foreach ($result as $row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     public function salvarFuncionario(array $data)
    //     {
    //         if (empty($data['nome']) || empty($data['cpf']) || empty($data['cargo_funcao']) || empty($data['departamento_id'])) {
    //             throw new \Exception('Nome, CPF, cargo/função e departamento são obrigatórios.');
    //         }

    //         $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : true;

    //         // 🔎 Verifica se CPF já existe
    //         $sqlCheck = 'SELECT id FROM pcp_funcionario WHERE cpf = :cpf';
    //         $paramsCheck = [':cpf' => $data['cpf']];
    //         $result = $this->adapter->createStatement($sqlCheck)->execute($paramsCheck)->current();

    //         if ($result) {
    //             if (empty($data['id']) || $result['id'] != $data['id']) {
    //                 throw new \Exception('Já existe um funcionário cadastrado com este CPF.');
    //             }
    //         }

    //         if (!empty($data['id'])) {
    //             $sql = 'UPDATE pcp_funcionario SET 
    //                         nome = :nome, 
    //                         numcad = :numcad, 
    //                         cpf = :cpf, 
    //                         cargo_funcao = :cargo_funcao, 
    //                         contato = :contato, 
    //                         departamento_id = :departamento_id,
    //                         flg_ativo = :flg_ativo
    //                     WHERE id = :id';
    //             $params = [
    //                 ':nome' => $data['nome'],
    //                 ':numcad' => $data['numcad'],
    //                 ':cpf' => $data['cpf'],
    //                 ':cargo_funcao' => $data['cargo_funcao'],
    //                 ':contato' => $data['contato'] ?? null,
    //                 ':departamento_id' => $data['departamento_id'],
    //                 ':flg_ativo' => $flgAtivo,
    //                 ':id' => $data['id']
    //             ];
    //         } else {
    //             $sql = 'INSERT INTO pcp_funcionario (nome, numcad, cpf, cargo_funcao, contato, departamento_id, flg_ativo) 
    //                     VALUES (:nome, :numcad, :cpf, :cargo_funcao, :contato, :departamento_id, :flg_ativo)';
    //             $params = [
    //                 ':nome' => $data['nome'],
    //                 ':numcad' => $data['numcad'],
    //                 ':cpf' => $data['cpf'],
    //                 ':cargo_funcao' => $data['cargo_funcao'],
    //                 ':contato' => $data['contato'] ?? null,
    //                 ':departamento_id' => $data['departamento_id'],
    //                 ':flg_ativo' => $flgAtivo
    //             ];
    //         }

    //         $this->adapter->createStatement($sql)->execute($params);
    //     }
    //     public function excluirFuncionario($id)
    //     {
    //         if (empty($id)) {
    //             throw new \Exception('ID do funcionário não fornecido.');
    //         }

    //         $sql = 'UPDATE pcp_funcionario SET flg_ativo = false WHERE id = :id';
    //         $this->adapter->createStatement($sql)->execute([':id' => $id]);
    //     }
    //     public function getLookupFuncionarios()
    //     {
    //         $sql = 'SELECT 
    //                     f.id, 
    //                     f.nome, 
    //                     f.cpf, 
    //                     f.cargo_funcao, 
    //                     f.contato, 
    //                     f.departamento_id,
    //                     d.nome as nome_departamento
    //                 FROM pcp_funcionario f
    //                 LEFT JOIN pcp_departamento d ON d.id = f.departamento_id
    //                 WHERE f.flg_ativo = true 
    //                 ORDER BY d.nome, f.nome';
    //         $result = $this->adapter->createStatement($sql)->execute();

    //         $data = [];
    //         foreach ($result as $row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    // #endRegion

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
                        e.status,
                        (SELECT COUNT(*) FROM pcp_equipamentos_imagens WHERE equipamento_id = e.id) as quantidade_imagens
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

            // Inicia transação
            $connection = $this->adapter->getDriver()->getConnection();
            $connection->beginTransaction();

            try {
                if (!empty($data['id'])) {
                    // UPDATE
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
                    
                    $this->adapter->createStatement($sql)->execute($params);
                    $equipamentoId = $data['id'];
                    
                    // Processa imagens para UPDATE
                    if (!empty($data['imagens']) && is_array($data['imagens'])) {
                        $this->processarImagensEquipamento($equipamentoId, $data['imagens']);
                    }
                    
                } else {
                    // INSERT
                    $sql = 'INSERT INTO pcp_equipamentos (codigo, nome, quantidade, quantidade_disponivel, valor, observacoes, status) 
                            VALUES (:codigo, :nome, :quantidade, :quantidade_disponivel, :valor, :observacoes, :status) 
                            RETURNING id';
                    $params = [
                        ':codigo' => $data['codigo'],
                        ':nome' => $data['nome'],
                        ':quantidade' => $data['quantidade'] ?? null,
                        ':quantidade_disponivel' => $data['quantidade'] ?? null,
                        ':valor' => $data['valor'] ?? null,
                        ':observacoes' => $data['observacoes'] ?? null,
                        ':status' => $data['status'] ?? null
                    ];
                    
                    $result = $this->adapter->createStatement($sql)->execute($params)->current();
                    $equipamentoId = $result['id'];
                    
                    // Processa imagens para INSERT
                    if (!empty($data['imagens']) && is_array($data['imagens'])) {
                        $this->processarImagensEquipamento($equipamentoId, $data['imagens']);
                    }
                }

                $connection->commit();
                return $equipamentoId;

            } catch (\Exception $e) {
                $connection->rollback();
                throw $e;
            }
        }
        private function processarImagensEquipamento($equipamentoId, $imagens)
        {
            // // VERIFICAÇÃO ADICIONAL - DEBUG
            // $this->verificarConfiguracaoDiretorio();

            $pastaDestino = '/var/www/html/ssf-dashboard/data/planejamento-controle-producao/equipamento';
            
            // Verifica se a pasta existe, se não, cria com permissões adequadas
            if (!is_dir($pastaDestino)) {
                if (!mkdir($pastaDestino, 0755, true)) {
                    throw new \Exception('Não foi possível criar o diretório: ' . $pastaDestino);
                }
            }

            // Verifica permissões de escrita
            if (!is_writable($pastaDestino)) {
                throw new \Exception('Diretório sem permissão de escrita: ' . $pastaDestino);
            }

            // Remove imagens antigas se for uma atualização
            $this->removerImagensAntigas($equipamentoId);

            foreach ($imagens as $imagem) {
                // Verifica se todos os campos necessários estão presentes
                if (empty($imagem['binario']) || empty($imagem['nome_arquivo'])) {
                    continue;
                }

                // Gera um nome único para o arquivo
                $extensao = pathinfo($imagem['nome_arquivo'], PATHINFO_EXTENSION);
                $nomeUnico = uniqid() . '_' . $equipamentoId . '.' . $extensao;
                $caminhoCompleto = $pastaDestino . '/' . $nomeUnico;

                // Decodifica o base64
                $dadosBinarios = base64_decode($imagem['binario']);
                if ($dadosBinarios === false) {
                    throw new \Exception('Dados binários da imagem inválidos: ' . $imagem['nome_arquivo']);
                }

                // Verifica o tamanho
                if ($imagem['tamanho'] > 5 * 1024 * 1024) { // 5 MB
                    throw new \Exception('Imagem muito grande. Tamanho máximo permitido: 5 MB.');
                }

                // Caminho temporário
                $tmpFile = sys_get_temp_dir() . '/' . uniqid('equip_') . '.' . $extensao;

                // Salva temporariamente
                file_put_contents($tmpFile, $dadosBinarios);

                // Otimiza a imagem *antes* de salvar no destino final
                $this->otimizarImagem($tmpFile);

                // Move a otimizada para o caminho definitivo
                if (!rename($tmpFile, $caminhoCompleto)) {
                    throw new \Exception('Falha ao mover imagem otimizada: ' . $imagem['nome_arquivo']);
                }

                // Confere se o arquivo existe e tem conteúdo
                if (!file_exists($caminhoCompleto) || filesize($caminhoCompleto) === 0) {
                    throw new \Exception('Arquivo de imagem não foi criado corretamente: ' . $imagem['nome_arquivo']);
                }

                // Salva no banco de dados
                $this->salvarImagemBanco($equipamentoId, [
                    'nome_arquivo' => $nomeUnico,
                    'caminho' => $caminhoCompleto,
                    'tipo_mime' => $imagem['tipo_mime'] ?? 'application/octet-stream',
                    'tamanho' => $imagem['tamanho'] ?? filesize($caminhoCompleto)
                ]);
            }
        }
        private function otimizarImagem($caminho)
        {
            $info = getimagesize($caminho);
            if ($info === false) {
                return; // Não é uma imagem válida
            }

            list($larguraOriginal, $alturaOriginal) = $info;
            $tipo = $info['mime'];

            // Define tamanho máximo
            $larguraMax = 800;
            $alturaMax = 800;

            // Calcula nova proporção
            $ratio = min($larguraMax / $larguraOriginal, $alturaMax / $alturaOriginal, 1);
            $novaLargura = intval($larguraOriginal * $ratio);
            $novaAltura = intval($alturaOriginal * $ratio);

            // Cria imagem a partir do tipo
            switch ($tipo) {
                case 'image/jpeg':
                    $src = imagecreatefromjpeg($caminho);
                    break;
                case 'image/png':
                    $src = imagecreatefrompng($caminho);
                    break;
                case 'image/webp':
                    $src = imagecreatefromwebp($caminho);
                    break;
                default:
                    return; // Tipo não suportado
            }

            // Cria imagem redimensionada
            $dst = imagecreatetruecolor($novaLargura, $novaAltura);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $novaLargura, $novaAltura, $larguraOriginal, $alturaOriginal);

            // Sobrescreve o arquivo original com compressão
            if ($tipo === 'image/jpeg') {
                imagejpeg($dst, $caminho, 80); // Qualidade 80%
            } elseif ($tipo === 'image/png') {
                imagepng($dst, $caminho, 6); // Compressão 0–9 (6 é um bom meio termo)
            } elseif ($tipo === 'image/webp') {
                imagewebp($dst, $caminho, 80);
            }

            // Libera memória
            imagedestroy($src);
            imagedestroy($dst);
        }
        private function verificarConfiguracaoDiretorio()
        {
            $pastaDestino = '/var/www/html/ssf-dashboard/data/planejamento-controle-producao/equipamento';
            
            echo "Diagnóstico do diretório:\n";
            echo "Caminho: " . $pastaDestino . "\n";
            echo "Existe: " . (is_dir($pastaDestino) ? 'SIM' : 'NÃO') . "\n";
            echo "É gravável: " . (is_writable($pastaDestino) ? 'SIM' : 'NÃO') . "\n";
            echo "Permissões: " . substr(sprintf('%o', fileperms($pastaDestino)), -4) . "\n";
            
            // Teste de escrita
            $arquivoTeste = $pastaDestino . '/teste.txt';
            if (file_put_contents($arquivoTeste, 'teste') !== false) {
                echo "Teste de escrita: OK\n";
                unlink($arquivoTeste);
            } else {
                echo "Teste de escrita: FALHOU\n";
            }
            
            // Verificar espaço em disco
            echo "Espaço livre: " . round(disk_free_space($pastaDestino) / 1024 / 1024, 2) . " MB\n";
        }
        private function removerImagensAntigas($equipamentoId)
        {
            // Busca imagens existentes no banco
            $sqlSelect = "SELECT caminho FROM pcp_equipamentos_imagens WHERE equipamento_id = :equipamento_id";
            $result = $this->adapter->createStatement($sqlSelect)
                ->execute([':equipamento_id' => $equipamentoId]);

            // Remove arquivos físicos
            foreach ($result as $row) {
                if (file_exists($row['caminho'])) {
                    unlink($row['caminho']);
                }
            }

            // Remove registros do banco
            $sqlDelete = "DELETE FROM pcp_equipamentos_imagens WHERE equipamento_id = :equipamento_id";
            $this->adapter->createStatement($sqlDelete)
                ->execute([':equipamento_id' => $equipamentoId]);
        }
        private function salvarImagemBanco($equipamentoId, $dadosImagem)
        {
            $sql = "INSERT INTO pcp_equipamentos_imagens 
                    (equipamento_id, nome_arquivo, caminho, tipo_mime, tamanho) 
                    VALUES (:equipamento_id, :nome_arquivo, :caminho, :tipo_mime, :tamanho)";

            $params = [
                ':equipamento_id' => $equipamentoId,
                ':nome_arquivo' => $dadosImagem['nome_arquivo'],
                ':caminho' => $dadosImagem['caminho'],
                ':tipo_mime' => $dadosImagem['tipo_mime'],
                ':tamanho' => $dadosImagem['tamanho']
            ];

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirEquipamento($id)
        {
            // Inicia transação para excluir equipamento e imagens
            $connection = $this->adapter->getDriver()->getConnection();
            $connection->beginTransaction();

            try {
                // Primeiro remove as imagens
                $this->removerImagensAntigas($id);
                
                // Depois exclui o equipamento
                $sql = 'DELETE FROM pcp_equipamentos WHERE id = :id';
                $this->adapter->createStatement($sql)->execute([':id' => $id]);
                
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollback();
                throw $e;
            }
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
                        e.status,
                        (SELECT COUNT(*) FROM pcp_equipamentos_imagens WHERE equipamento_id = e.id) as quantidade_imagens
                    FROM pcp_equipamentos e
                    WHERE 1=1
                    AND e.status = true
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
        public function carregarImagensEquipamento($equipamentoId)
        {
            $sql = "SELECT 
                        id,
                        nome_arquivo,
                        caminho,
                        tipo_mime,
                        tamanho
                    FROM pcp_equipamentos_imagens 
                    WHERE equipamento_id = :equipamento_id
                    ORDER BY nome_arquivo";
            
            $result = $this->adapter->createStatement($sql)
                ->execute([':equipamento_id' => $equipamentoId]);

            $imagens = [];
            foreach ($result as $row) {
                // Lê o arquivo físico e converte para base64
                if (file_exists($row['caminho'])) {
                    $binario = base64_encode(file_get_contents($row['caminho']));
                    
                    $imagens[] = [
                        'id' => $row['id'], 
                        'nome_arquivo' => $row['nome_arquivo'],
                        'caminho' => $row['caminho'],
                        'tipo_mime' => $row['tipo_mime'] ?? $this->getMimeTypeFromExtension($row['nome_arquivo']),
                        'tamanho' => $row['tamanho'],
                        'binario' => $binario
                    ];
                }
            }

            return $imagens;
        }
        // Método auxiliar para determinar o tipo MIME baseado na extensão
        private function getMimeTypeFromExtension($filename)
        {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'webp' => 'image/webp'
            ];
            
            return $mimeTypes[$extension] ?? 'image/jpeg';
        }
        public function removerImagemEquipamento($idImagem)
        {
            // Busca imagem no banco para apagar o arquivo físico
            $sqlSelect = "SELECT caminho FROM pcp_equipamentos_imagens WHERE id = :id";
            $imagem = $this->adapter->createStatement($sqlSelect)->execute([':id' => $idImagem])->current();

            if (!$imagem) {
                throw new \Exception('Imagem não encontrada.');
            }

            $caminho = $imagem['caminho'];

            // Remove o arquivo físico, se existir
            if (file_exists($caminho)) {
                if (!unlink($caminho)) {
                    throw new \Exception('Não foi possível remover o arquivo físico da imagem.');
                }
            }

            // Remove o registro do banco
            $sqlDelete = "DELETE FROM pcp_equipamentos_imagens WHERE id = :id";
            $this->adapter->createStatement($sqlDelete)->execute([':id' => $idImagem]);
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
