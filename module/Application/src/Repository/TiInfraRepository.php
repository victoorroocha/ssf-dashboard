<?php

namespace Application\Repository;

use Laminas\Db\Adapter\AdapterInterface;

class TiInfraRepository
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
                    FROM tif_departamento 
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
                $sql = 'UPDATE tif_departamento SET 
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
                $sql = 'INSERT INTO tif_departamento (nome, descricao, flg_ativo) 
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

            $sql = 'UPDATE tif_departamento SET flg_ativo = false WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupDepartamentos()
        {
            $sql = 'SELECT id, nome, descricao 
                    FROM tif_departamento 
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

    #region Cadastro Tipo Equipamento
        public function listarTipoEquipamento()
        {
            $sql = 'SELECT id, nome, descricao, flg_ativo 
                    FROM tif_tipo_equipamento 
                    ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function salvarTipoEquipamento(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Descrição do tipo equipamento é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : false;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE tif_tipo_equipamento SET 
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
                $sql = 'INSERT INTO tif_tipo_equipamento (nome, descricao, flg_ativo) 
                        VALUES (:nome, :descricao, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':descricao' => $data['descricao'] ?? null,
                    ':flg_ativo' => $flgAtivo,
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirTipoEquipamento($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do tipo equipamento não fornecido.');
            }

            $sql = 'UPDATE tif_tipo_equipamento SET flg_ativo = false WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupTipoEquipamento()
        {
            $sql = 'SELECT id, nome, descricao 
                    FROM tif_tipo_equipamento 
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

    #region Cadastro Acessorios
        public function listarAcessorios()
        {
            $sql = 'SELECT id, nome, flg_ativo 
                    FROM tif_acessorios 
                    ORDER BY nome'; 
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute();

            $data = [];
            foreach ($result as $row) {
                $data[] = $row;
            }

            return $data;
        }
        public function salvarAcessorio(array $data)
        {
            if (empty($data['nome'])) {
                throw new \Exception('Nome do acessório é obrigatório.');
            }

            $flgAtivo = isset($data['flg_ativo']) ? (bool)$data['flg_ativo'] : true;

            if (!empty($data['id'])) {
                // Atualizar
                $sql = 'UPDATE tif_acessorios SET 
                            nome = :nome,
                            flg_ativo = :flg_ativo
                        WHERE id = :id';
                $params = [
                    ':nome' => $data['nome'],
                    ':flg_ativo' => $flgAtivo,
                    ':id' => $data['id'],
                ];
            } else {
                // Inserir
                $sql = 'INSERT INTO tif_acessorios (nome, flg_ativo) 
                        VALUES (:nome, :flg_ativo)';
                $params = [
                    ':nome' => $data['nome'],
                    ':flg_ativo' => $flgAtivo,
                ];
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirAcessorio($id)
        {
            if (empty($id)) {
                throw new \Exception('ID do acessório não fornecido.');
            }

            // Apenas inativa o acessório
            $sql = 'UPDATE tif_acessorios SET flg_ativo = false WHERE id = :id';
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getLookupAcessorios()
        {
            $sql = 'SELECT id, nome 
                    FROM tif_acessorios 
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
        private function buscarAcessoriosPorEquipamento($equipamentoId)
        {
            if (!$equipamentoId) {
                return ['nomes' => '', 'ids' => []];
            }

            $sql = "
                SELECT a.id, a.nome
                FROM tif_acessorios a
                INNER JOIN tif_equipamento_acessorios ea ON ea.acessorio_id = a.id
                WHERE ea.equipamento_id = ?
            ";
            
            $result = $this->adapter->createStatement($sql, [$equipamentoId])->execute();
            
            $ids = [];
            $nomes = [];
            
            foreach ($result as $row) {
                $ids[] = (int)$row['id'];
                $nomes[] = $row['nome'];
            }
            
            return [
                'ids' => $ids,
                'nomes' => implode(', ', $nomes)
            ];
        }
    #endRegion

    #region Cadastro Equipamentos
        public function listarEquipamentos()
        {
            $sql = "
                SELECT 
                    e.id, 
                    e.nome, 
                    e.tipo_equipamento_id,
                    e.hostname,
                    e.num_solicitacao,
                    e.num_ordem_compra,
                    e.num_nota_fiscal,
                    e.numemp,
                    e.numfilial,
                    e.serie,
                    e.patrimonio,
                    e.partnumber,
                    e.data_fabricacao,
                    e.link_fabricante,
                    e.observacoes,
                    e.status,
                    e.flg_equipamento_novo,
                    te.nome AS tipo_equipamento,
                    (SELECT COUNT(*) FROM tif_equipamentos_imagens WHERE equipamento_id = e.id) AS quantidade_imagens,
                    (
                        SELECT json_agg(json_build_object('id', a.id, 'nome', a.nome))
                        FROM tif_equipamento_acessorios ea
                        JOIN tif_acessorios a ON a.id = ea.acessorio_id
                        WHERE ea.equipamento_id = e.id
                    ) AS acessorios,
                     (
                        SELECT distinct observacao
                        FROM tif_equipamento_acessorios ea
                        JOIN tif_acessorios a ON a.id = ea.acessorio_id
                        WHERE ea.equipamento_id = e.id
                    ) AS observacoes_acessorios,
                    c.processador,
                    c.memoria,
                    c.armazenamento,
                    c.sistema_operacional,
                    c.outros_softwares
                FROM tif_equipamentos e
                LEFT JOIN tif_tipo_equipamento te ON te.id = e.tipo_equipamento_id
                LEFT JOIN tif_caracteristicas c ON c.equipamento_id = e.id
                ORDER BY e.nome;
            ";
            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                // Processa acessórios - versão simplificada
                if (!empty($row['acessorios'])) {
                    $acessoriosArray = json_decode($row['acessorios'], true);
                    $row['acessorios'] = array_column($acessoriosArray, 'id');
                } else {
                    $row['acessorios'] = [];
                }

                // Processa características - garante valores padrão
                $row['processador'] = $row['processador'] ?? '';
                $row['memoria'] = $row['memoria'] ?? '';
                $row['armazenamento'] = $row['armazenamento'] ?? '';
                $row['sistema_operacional'] = $row['sistema_operacional'] ?? '';
                $row['outros_softwares'] = $row['outros_softwares'] ?? '';

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
            if (empty($data['serie'])) {
                throw new \Exception('Número de série é obrigatório.');
            }

            // Verifica duplicidade
            $sqlCheck = "SELECT COUNT(*) AS total FROM tif_equipamentos WHERE serie = :serie";
            $paramsCheck = [':serie' => $data['serie']];
            if (!empty($data['id'])) {
                $sqlCheck .= " AND id <> :id";
                $paramsCheck[':id'] = $data['id'];
            }
            $result = $this->adapter->query($sqlCheck, $paramsCheck)->current();
            if ($result && $result['total'] > 0) {
                throw new \Exception('Já existe um equipamento cadastrado com esse número de série.');
            }
            #endregion

            $connection = $this->adapter->getDriver()->getConnection();
            $connection->beginTransaction();

            try {
                if (!empty($data['id'])) {
                    // UPDATE
                    $sql = 'UPDATE tif_equipamentos
                            SET nome = :nome,
                                tipo_equipamento_id = :tipo_equipamento_id,
                                hostname = :hostname,
                                num_solicitacao = :num_solicitacao,
                                num_ordem_compra = :num_ordem_compra,
                                num_nota_fiscal = :num_nota_fiscal,
                                numemp = :numemp,
                                numfilial = :numfilial,
                                serie = :serie,
                                patrimonio = :patrimonio,
                                partnumber = :partnumber,
                                data_fabricacao = :data_fabricacao,
                                link_fabricante = :link_fabricante,
                                observacoes = :observacoes,
                                flg_equipamento_novo = :flg_equipamento_novo,
                                status = :status
                            WHERE id = :id';
                    $params = [
                        ':id' => $data['id'],
                        ':nome' => $data['nome'],
                        ':tipo_equipamento_id' => $data['tipo_equipamento_id'],
                        ':hostname' => $data['hostname'],
                        ':num_solicitacao' => $data['num_solicitacao'] ?? null,
                        ':num_ordem_compra' => $data['num_ordem_compra'] ?? null,
                        ':num_nota_fiscal' => $data['num_nota_fiscal'] ?? null,
                        ':numemp' => $data['numemp'] ?? null,
                        ':numfilial' => $data['numfilial'] ?? null,
                        ':serie' => $data['serie'],
                        ':patrimonio' => $data['patrimonio'] ?? null,
                        ':partnumber' => $data['partnumber'] ?? null,
                        ':data_fabricacao' => $data['data_fabricacao'] ?? null,
                        ':link_fabricante' => $data['link_fabricante'] ?? null,
                        ':observacoes' => $data['observacoes'] ?? null,
                        ':flg_equipamento_novo' => $data['flg_equipamento_novo'] ?? true,
                        ':status' => $data['status'] ?? null
                    ];
                    $this->adapter->createStatement($sql)->execute($params);
                    $equipamentoId = $data['id'];

                } else {
                    // INSERT
                    $sql = 'INSERT INTO tif_equipamentos
                            (nome, tipo_equipamento_id, hostname, num_solicitacao, num_ordem_compra, num_nota_fiscal,
                            numemp, numfilial, serie, patrimonio, partnumber, data_fabricacao, link_fabricante,
                            observacoes, flg_equipamento_novo, status)
                            VALUES
                            (:nome, :tipo_equipamento_id, :hostname, :num_solicitacao, :num_ordem_compra, :num_nota_fiscal,
                            :numemp, :numfilial, :serie, :patrimonio, :partnumber, :data_fabricacao, :link_fabricante,
                            :observacoes, :flg_equipamento_novo, :status)
                            RETURNING id';
                    $params = [
                        ':nome' => $data['nome'],
                        ':tipo_equipamento_id' => $data['tipo_equipamento_id'],
                        ':hostname' => $data['hostname'] ?? null,
                        ':num_solicitacao' => $data['num_solicitacao'] ?? null,
                        ':num_ordem_compra' => $data['num_ordem_compra'] ?? null,
                        ':num_nota_fiscal' => $data['num_nota_fiscal'] ?? null,
                        ':numemp' => $data['numemp'] ?? null,
                        ':numfilial' => $data['numfilial'] ?? null,
                        ':serie' => $data['serie'],
                        ':patrimonio' => $data['patrimonio'] ?? null,
                        ':partnumber' => $data['partnumber'] ?? null,
                        ':data_fabricacao' => $data['data_fabricacao'] ?? null,
                        ':link_fabricante' => $data['link_fabricante'] ?? null,
                        ':observacoes' => $data['observacoes'] ?? null,
                        ':flg_equipamento_novo' => $data['flg_equipamento_novo'] ?? true,
                        ':status' => $data['status'] ?? null
                    ];
                    $result = $this->adapter->createStatement($sql)->execute($params)->current();
                    $equipamentoId = $result['id'];
                }

                // Processa imagens
                if (!empty($data['imagens']) && is_array($data['imagens'])) {
                    $this->processarImagensEquipamento($equipamentoId, $data['imagens']);
                }

                // Processa acessórios
                if (isset($data['acessorios']) && is_array($data['acessorios'])) {
                    // Remove acessórios antigos
                    $sqlDeleteAcc = "DELETE FROM tif_equipamento_acessorios WHERE equipamento_id = :equipamento_id";
                    $this->adapter->createStatement($sqlDeleteAcc)->execute([':equipamento_id' => $equipamentoId]);

                    // Insere novos acessórios
                    $sqlInsertAcc = "INSERT INTO tif_equipamento_acessorios (equipamento_id, acessorio_id, observacao) VALUES (:equipamento_id, :acessorio_id, :observacao)";
                    foreach ($data['acessorios'] as $acessorioId) {
                        $this->adapter->createStatement($sqlInsertAcc)->execute([
                            ':equipamento_id' => $equipamentoId,
                            ':acessorio_id' => $acessorioId,
                            ':observacao' => $data['observacoes_acessorios']
                        ]);
                    }
                }

                // Processa características
                $this->processarCaracteristicas($equipamentoId, $data);

                $connection->commit();
                return $equipamentoId;

            } catch (\Exception $e) {
                $connection->rollback();
                throw $e;
            }
        }
        private function processarCaracteristicas($equipamentoId, array $data)
        {
            // Verifica se existem características para processar
            $temCaracteristicas = isset($data['processador']) || isset($data['memoria']) || 
                                isset($data['armazenamento']) || isset($data['sistema_operacional']) || 
                                isset($data['outros_softwares']);

            if (!$temCaracteristicas) {
                return;
            }

            // Verifica se já existe registro de características
            $sqlCheck = "SELECT COUNT(*) AS total FROM tif_caracteristicas WHERE equipamento_id = :equipamento_id";
            $result = $this->adapter->query($sqlCheck, [':equipamento_id' => $equipamentoId])->current();
            $existe = $result && $result['total'] > 0;

            if ($existe) {
                // UPDATE
                $sql = 'UPDATE tif_caracteristicas 
                        SET processador = :processador,
                            memoria = :memoria,
                            armazenamento = :armazenamento,
                            sistema_operacional = :sistema_operacional,
                            outros_softwares = :outros_softwares
                        WHERE equipamento_id = :equipamento_id';
            } else {
                // INSERT
                $sql = 'INSERT INTO tif_caracteristicas 
                        (equipamento_id, processador, memoria, armazenamento, sistema_operacional, outros_softwares)
                        VALUES 
                        (:equipamento_id, :processador, :memoria, :armazenamento, :sistema_operacional, :outros_softwares)';
            }

            $params = [
                ':equipamento_id' => $equipamentoId,
                ':processador' => $data['processador'] ?? null,
                ':memoria' => $data['memoria'] ?? null,
                ':armazenamento' => $data['armazenamento'] ?? null,
                ':sistema_operacional' => $data['sistema_operacional'] ?? null,
                ':outros_softwares' => $data['outros_softwares'] ?? null
            ];

            $this->adapter->createStatement($sql)->execute($params);
        }
        private function processarImagensEquipamento($equipamentoId, $imagens)
        {
            $pastaDestino = '/var/www/html/ssf-dashboard/data/ti-infra/equipamento';

            if (!is_dir($pastaDestino) && !mkdir($pastaDestino, 0755, true)) {
                throw new \Exception('Não foi possível criar o diretório: ' . $pastaDestino);
            }

            if (!is_writable($pastaDestino)) {
                throw new \Exception('Diretório sem permissão de escrita: ' . $pastaDestino);
            }

            // Remove imagens antigas
            $this->removerImagensAntigas($equipamentoId);

            foreach ($imagens as $imagem) {
                if (empty($imagem['binario']) || empty($imagem['nome_arquivo'])) continue;

                $extensao = pathinfo($imagem['nome_arquivo'], PATHINFO_EXTENSION);
                $nomeUnico = uniqid() . '_' . $equipamentoId . '.' . $extensao;
                $caminhoCompleto = $pastaDestino . '/' . $nomeUnico;

                $dadosBinarios = base64_decode($imagem['binario']);
                if ($dadosBinarios === false) {
                    throw new \Exception('Dados binários da imagem inválidos: ' . $imagem['nome_arquivo']);
                }

                if ($imagem['tamanho'] > 5 * 1024 * 1024) {
                    throw new \Exception('Imagem muito grande. Tamanho máximo permitido: 5 MB.');
                }

                $tmpFile = sys_get_temp_dir() . '/' . uniqid('equip_') . '.' . $extensao;
                file_put_contents($tmpFile, $dadosBinarios);
                $this->otimizarImagem($tmpFile);

                if (!rename($tmpFile, $caminhoCompleto)) {
                    throw new \Exception('Falha ao mover imagem otimizada: ' . $imagem['nome_arquivo']);
                }

                if (!file_exists($caminhoCompleto) || filesize($caminhoCompleto) === 0) {
                    throw new \Exception('Arquivo de imagem não foi criado corretamente: ' . $imagem['nome_arquivo']);
                }

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
            if ($info === false) return;

            list($larguraOriginal, $alturaOriginal) = $info;
            $tipo = $info['mime'];

            $larguraMax = 800;
            $alturaMax = 800;
            $ratio = min($larguraMax / $larguraOriginal, $alturaMax / $alturaOriginal, 1);
            $novaLargura = intval($larguraOriginal * $ratio);
            $novaAltura = intval($alturaOriginal * $ratio);

            switch ($tipo) {
                case 'image/jpeg': $src = imagecreatefromjpeg($caminho); break;
                case 'image/png': $src = imagecreatefrompng($caminho); break;
                case 'image/webp': $src = imagecreatefromwebp($caminho); break;
                default: return;
            }

            $dst = imagecreatetruecolor($novaLargura, $novaAltura);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $novaLargura, $novaAltura, $larguraOriginal, $alturaOriginal);

            if ($tipo === 'image/jpeg') imagejpeg($dst, $caminho, 80);
            elseif ($tipo === 'image/png') imagepng($dst, $caminho, 6);
            elseif ($tipo === 'image/webp') imagewebp($dst, $caminho, 80);

            imagedestroy($src);
            imagedestroy($dst);
        }
        private function removerImagensAntigas($equipamentoId)
        {
            $sqlSelect = "SELECT caminho FROM tif_equipamentos_imagens WHERE equipamento_id = :equipamento_id";
            $result = $this->adapter->createStatement($sqlSelect)->execute([':equipamento_id' => $equipamentoId]);

            foreach ($result as $row) {
                if (file_exists($row['caminho'])) unlink($row['caminho']);
            }

            $sqlDelete = "DELETE FROM tif_equipamentos_imagens WHERE equipamento_id = :equipamento_id";
            $this->adapter->createStatement($sqlDelete)->execute([':equipamento_id' => $equipamentoId]);
        }
        private function salvarImagemBanco($equipamentoId, $dadosImagem)
        {
            $sql = "INSERT INTO tif_equipamentos_imagens 
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
        public function carregarImagensEquipamento($equipamentoId) 
        {
            $sql = "SELECT id, nome_arquivo, caminho, tipo_mime, tamanho
                    FROM tif_equipamentos_imagens 
                    WHERE equipamento_id = :equipamento_id
                    ORDER BY nome_arquivo";
            $result = $this->adapter->createStatement($sql)->execute([':equipamento_id' => $equipamentoId]);

            $imagens = [];
            foreach ($result as $row) {
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
            return $mimeTypes[$extension] ?? 'application/octet-stream';
        }
        public function removerImagemEquipamento($idImagem)
        {
            $sqlSelect = "SELECT caminho FROM tif_equipamentos_imagens WHERE id = :id";
            $imagem = $this->adapter->createStatement($sqlSelect)->execute([':id' => $idImagem])->current();

            if (!$imagem) throw new \Exception('Imagem não encontrada.');
            if (file_exists($imagem['caminho']) && !unlink($imagem['caminho'])) {
                throw new \Exception('Não foi possível remover o arquivo físico da imagem.');
            }

            $sqlDelete = "DELETE FROM tif_equipamentos_imagens WHERE id = :id";
            $this->adapter->createStatement($sqlDelete)->execute([':id' => $idImagem]);
        }
        public function excluirEquipamento($id)
        {
            $connection = $this->adapter->getDriver()->getConnection();
            $connection->beginTransaction();

            try {
                $this->removerImagensAntigas($id);
                $sql = 'DELETE FROM tif_equipamentos WHERE id = :id';
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
                $ands .= " AND (e.nome ILIKE '%{$searchTerm}%' OR e.serie ILIKE '%{$searchTerm}%' OR cast(e.num_ordem_compra as varchar) ILIKE '%{$searchTerm}%' OR e.hostname ILIKE '%{$searchTerm}%')";
            }

            if (!empty($key)) $ands .= " AND e.id = $key";

            $sql = "SELECT 
                        e.id, e.nome, e.serie, e.hostname, e.observacoes, e.num_ordem_compra, te.nome as tipo_equipamento,
                        (SELECT COUNT(*) FROM tif_equipamentos_imagens WHERE equipamento_id = e.id) as quantidade_imagens
                    FROM tif_equipamentos e
                    left join tif_tipo_equipamento te on te.id = e.tipo_equipamento_id
                    WHERE 1=1 
                    {$ands}
                    ORDER BY e.nome
                    LIMIT $limit OFFSET $offset";
            $result = $this->adapter->createStatement($sql)->execute();

            $countSql = "SELECT COUNT(*) as total FROM tif_equipamentos e WHERE 1=1 {$ands}";
            $countResult = $this->adapter->createStatement($countSql)->execute()->current();
            $totalCount = $countResult['total'] ?? 0;

            $data = [];
            foreach ($result as $row) $data[] = $row;

            return ['data' => $data, 'totalCount' => $totalCount];
        }
    #endRegion

    #region Controle de Empréstimo
        public function listarControlesEmprestimo()
        {
            $sql = "
                SELECT 
                    cm.*,
                    cm.numcad AS matricula,
                    CASE 
                        WHEN cm.data_devolucao IS NOT NULL THEN 'devolvido'
                        ELSE 'emprestado'
                    END AS status,

                    e.id AS equipamento_id,
                    e.nome AS equipamento_nome,
                    e.hostname,
                    e.serie,
                    e.patrimonio,
                    e.partnumber,
                    e.observacoes AS equipamento_observacoes,
                    e.tipo_equipamento_id,
                    e.flg_equipamento_novo,
                    te.nome AS tipo_equipamento_nome,

                    d.nome AS departamento_nome,

                    c.processador,
                    c.memoria,
                    c.armazenamento,
                    c.sistema_operacional,
                    c.outros_softwares

                FROM tif_controle_emprestimo cm
                LEFT JOIN tif_equipamentos e ON e.id = cm.equipamento_id
                LEFT JOIN tif_tipo_equipamento te ON te.id = e.tipo_equipamento_id
                LEFT JOIN tif_departamento d ON d.id = cm.departamento_id
                LEFT JOIN tif_caracteristicas c ON c.equipamento_id = e.id
                ORDER BY cm.data_entrega DESC
            ";

            $result = $this->adapter->createStatement($sql)->execute();

            $data = [];
            foreach ($result as $row) {
                // Busca os acessórios do equipamento usando o repository
                $acessorios = $this->buscarAcessoriosPorEquipamento($row['equipamento_id']);
                
                $row['acessorios_nomes'] = $acessorios['nomes'];
                $row['acessorios_ids'] = $acessorios['ids'];
                
                $data[] = $row;
            }

            return $data;
        }
        public function salvarControleEmprestimo(array $data)
        {
            $params = [
                ':data_entrega'       => $data['data_entrega'] ?? null,
                ':numcad'             => $data['numcad'] ?? null,
                ':nome'               => $data['nome'] ?? null,
                ':cpf'                => $data['cpf'] ?? null,
                ':cargo_funcao'       => $data['cargo_funcao'] ?? null,
                ':contato'            => $data['contato'] ?? null,
                ':departamento_id'    => $data['departamento_id'] ?? null,
                ':equipamento_id'     => $data['equipamento_id'] ?? null,
                ':centro_custo'       => $data['centro_custo'] ?? null,
                ':data_devolucao'     => $data['data_devolucao'] ?? null,
                ':observacoes'        => $data['observacoes'] ?? null,
                ':status'             => $data['status'] ?? 'emprestado',
            ];

            if (!empty($data['id'])) {
                $sql = "UPDATE tif_controle_emprestimo SET 
                            data_entrega = :data_entrega,
                            numcad = :numcad,
                            nome = :nome,
                            cpf = :cpf,
                            cargo_funcao = :cargo_funcao,
                            contato = :contato,
                            departamento_id = :departamento_id,
                            equipamento_id = :equipamento_id,
                            centro_custo = :centro_custo,
                            data_devolucao = :data_devolucao,
                            observacoes = :observacoes,
                            status = :status
                        WHERE id = :id";
                $params[':id'] = $data['id'];
            } else {
                $sql = "INSERT INTO tif_controle_emprestimo (
                            data_entrega, numcad, nome, cpf, cargo_funcao, contato, 
                            departamento_id, equipamento_id, centro_custo, 
                            data_devolucao, observacoes, status
                        ) VALUES (
                            :data_entrega, :numcad, :nome, :cpf, :cargo_funcao, :contato, 
                            :departamento_id, :equipamento_id, :centro_custo, 
                            :data_devolucao, :observacoes, :status
                        )";
            }

            $this->adapter->createStatement($sql)->execute($params);
        }
        public function excluirControleEmprestimo($id)
        {
            $sql = "DELETE FROM tif_controle_emprestimo WHERE id = :id";
            $this->adapter->createStatement($sql)->execute([':id' => $id]);
        }
        public function getInfoTermoEmprestimo($id)
        {
            $sql = "SELECT
                        cm.*, 
                        LPAD(cm.id::text, 5, '0') AS nr_termo,
                        TO_CHAR(cm.data_entrega, 'DD/MM/YYYY') AS data_entrega,
                        TO_CHAR(cm.data_devolucao, 'DD/MM/YYYY') AS data_devolucao,
                        e.codigo || '-' || e.nome AS nome_equipamento,
                        d.nome AS departamento_nome,
                        cm.observacoes
                    FROM tif_controle_emprestimo cm
                    LEFT JOIN tif_equipamentos e ON e.id = cm.equipamento_id
                    LEFT JOIN tif_departamento d ON d.id = cm.departamento_id
                    WHERE cm.id = :id";
            $statement = $this->adapter->createStatement($sql);
            $result = $statement->execute([':id' => $id]);
            return $result->current();
        }
        public function marcarDevolucaoEquipamento(array $data)
        {
            $this->adapter->getDriver()->getConnection()->beginTransaction();

            try {
                $sqlValida = "SELECT cm.* FROM tif_controle_emprestimo cm WHERE cm.id = :id";
                $resultValida = $this->adapter->query($sqlValida, [':id' => $data['id']])->current();
                if (!$resultValida) {
                    throw new \InvalidArgumentException('Empréstimo não encontrado.');
                }

                $sqlUpdate = "UPDATE tif_controle_emprestimo SET 
                                status = 'devolvido', 
                                data_devolucao = NOW() 
                            WHERE id = :id";
                
                $this->adapter->createStatement($sqlUpdate)->execute([':id' => $data['id']]);

                $this->adapter->getDriver()->getConnection()->commit();

            } catch (\Exception $e) {
                $this->adapter->getDriver()->getConnection()->rollback();
                throw $e;
            }
        }
    #endRegion

}
