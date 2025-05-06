<?php
namespace Application\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Crypt\Password\Bcrypt;

class DepartamentoRepository
{
    private $adapter;
    private $bcrypt;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->bcrypt = new Bcrypt(); // Instância do Bcrypt para criptografar a senha
    }

    public function listarDepartamentos($skip, $take, $sort = null)
    {
        // Query base
        $sql = 'SELECT id, nome_departamento, id_gestor_departamento, ativo FROM departamento';

        // Adiciona ordenação, se fornecida
        if ($sort) {
            $sort = json_decode($sort, true);
            $orderBy = array_map(function ($item) {
                return $item['selector'] . ' ' . $item['desc'] ? 'DESC' : 'ASC';
            }, $sort);
            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
        }

        // Adiciona paginação
        $sql .= ' LIMIT :take OFFSET :skip';

        // Executa a query
        $statement = $this->adapter->createStatement($sql);
        $result = $statement->execute([
            ':take' => $take,
            ':skip' => $skip,
        ]);

        // Obtém os dados
        $data = [];
        foreach ($result as $row) {
            $data[] = $row;
        }

        // Conta o total de registros
        $totalCount = $this->adapter->query('SELECT COUNT(*) FROM departamento')->execute()->current()['count'];

        return [
            'data' => $data,
            'totalCount' => $totalCount,
        ];
    }
    public function inserirDepartamento(array $data)
    {
        // Valida os dados obrigatórios
        if (empty($data['nome_departamento'])) {
            throw new \Exception('Nome Departamento é obrigatório.');
        }

        // Query de inserção
        $sql = 'INSERT INTO departamento (nome_departamento, id_gestor_departamento, ativo) VALUES (:nome_departamento, :id_gestor_departamento, :ativo)';
        $statement = $this->adapter->createStatement($sql);
        $statement->execute([
            ':nome_departamento' => $data['nome_departamento'],
            ':id_gestor_departamento' => $data['id_gestor_departamento'] ?? null,
            ':ativo' => $data['ativo'] ?? false,
        ]);
    }
    public function atualizarDepartamento(array $data)
    {
        // Query de atualização
        $sql = 'UPDATE departamento SET nome_departamento = :nome_departamento, id_gestor_departamento = :id_gestor_departamento, ativo = :ativo WHERE id = :id';
        $statement = $this->adapter->createStatement($sql);
        $statement->execute([
            ':nome_departamento' => $data['nome_departamento'],
            ':id_gestor_departamento' => $data['id_gestor_departamento'],
            ':ativo' => $data['ativo'] ?? false,
            ':id' => $data['id'],
        ]);
    }
    public function excluirDepartamento($id)
    {
        // Verifica se o ID foi fornecido
        if (empty($id)) {
            throw new \Exception('ID do departamento não fornecido.');
        }

        // Query de exclusão
        $sql = 'DELETE FROM departamento WHERE id = :id';
        $statement = $this->adapter->createStatement($sql);
        $statement->execute([
            ':id' => $id,
        ]);
    }


    
    public function getLookupGestorDepartamento($filtro, $id_gestor_departamento)
    {

        $wheres = "";
        if (isset($id_gestor_departamento) && !empty($id_gestor_departamento)) {
            $wheres .= " AND id = $id_gestor_departamento";
        }

        // Query base
        $sql = "SELECT id as id_gestor_departamento, nome 
                FROM usuario where ativo = true
                {$wheres}";

        // Executa a query
        $statement = $this->adapter->createStatement($sql);
        $result = $statement->execute();

        // Obtém os dados
        $data = [];
        foreach ($result as $row) {
            $data[] = $row;
        }

        return $data;
    }
    public function getLookupDepartamento($filtro, $id_departamento)
    {

        $wheres = "";
        if (isset($id_departamento) && !empty($id_departamento)) {
            $wheres .= " AND id = $id_departamento";
        }

        // Query base
        $sql = "SELECT id as id_departamento, nome_departamento
                FROM departamento where ativo = true
                {$wheres}
                ORDER BY nome_departamento ASC";

        // Executa a query
        $statement = $this->adapter->createStatement($sql);
        $result = $statement->execute();

        // Obtém os dados
        $data = [];
        foreach ($result as $row) {
            $data[] = $row;
        }

        return $data;
    }
}