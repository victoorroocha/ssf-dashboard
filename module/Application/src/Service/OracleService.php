<?php
namespace Application\Service;

class OracleService
{
    private $username;
    private $password;
    private $connection_string;
    private $connection;

    public function __construct($username, $password, $connection_string)
    {
        $this->username = $username;
        $this->password = $password;
        $this->connection_string = $connection_string;
        $this->connect();
    }

    private function connect()
    {
        $this->connection = oci_connect($this->username, $this->password, $this->connection_string);

        if (!$this->connection) {
            throw new \Exception("Erro de conexão Oracle: " . oci_error()['message']);
        }
    }

    /**
     * Executa uma consulta no banco Oracle e retorna os resultados como um array.
     * @param string $sql A consulta SQL a ser executada.
     * @return array Retorna um array de resultados.
     */
    public function executeQuery($sql)
    {
        // Prepara a consulta
        $stid = oci_parse($this->connection, $sql);

        // Executa a consulta
        if (!oci_execute($stid)) {
            $error = oci_error($stid);
            throw new \Exception("Erro ao executar a consulta: " . $error['message']);
        }

        // Armazena os resultados
        $results = [];
        while ($row = oci_fetch_assoc($stid)) {
            $results[] = $row;
        }

        // Libera a memória da consulta
        oci_free_statement($stid);

        // Retorna os resultados como um array
        return $results;
    }

    public function __destruct()
    {
        // Fecha a conexão quando o objeto for destruído
        oci_close($this->connection);
    }


    public function getCargoUsuario($username)
    {
        $sql = "SELECT upper(R024CAR.TITCAR) as TITCAR
                FROM VETORH.R034FUN
                INNER JOIN VETORH.R034USU ON R034USU.NUMEMP = R034FUN.NUMEMP AND R034USU.NUMCAD = R034FUN.NUMCAD
                INNER JOIN VETORH.R910USU ON R910USU.CODENT = R034USU.CODUSU     
                INNER JOIN VETORH.R999USU ON R999USU.CODUSU = R910USU.CODENT 
                INNER JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR
                WHERE R999USU.NOMUSU LIKE :username";
        
        $stid = oci_parse($this->connection, $sql);
        oci_bind_by_name($stid, ':username', $username);
        
        if (!oci_execute($stid)) {
            $error = oci_error($stid);
            oci_free_statement($stid);
            throw new \Exception("Erro ao buscar cargo: " . $error['message']);
        }
        
        $result = oci_fetch_assoc($stid);
        oci_free_statement($stid);
        
        return $result['TITCAR'] ?? null;
    }
}
