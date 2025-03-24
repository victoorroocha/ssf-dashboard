<?php

namespace Application\Service;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;
use Laminas\Crypt\Password\Bcrypt;

use Laminas\Http\Client;
use Laminas\Http\Request;

class AuthService
{
    private $dbAdapter;
    private $oracleService;

    public function __construct(Adapter $dbAdapter, OracleService $oracleService)
    {
        $this->dbAdapter = $dbAdapter;
        $this->oracleService = $oracleService;
    }
    
    public function authenticate($email, $senha)
    {
        // Primeiro tenta autenticar localmente
        $localUser = $this->authenticateLocally($email, $senha);
        if ($localUser) {
            return $localUser;
        }

        // Se não encontrou localmente, tenta autenticar via API
        $apiUser = $this->authenticateViaApi($email, $senha);
        if ($apiUser) {
            // Cria o usuário localmente
            $createdUser = $this->createLocalUser($email, $senha);
            return $createdUser;
        }

        return null; // Autenticação falhou
    }

    private function authenticateLocally($email, $senha)
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('usuario');
        $select->where(['email' => $email]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
    
        if ($result->count() == 1) {
            $user = $result->current();
    
            // Verifica se o usuário está ativo
            if (!$user['ativo']) {
                return null;
            }
    
            $bcrypt = new Bcrypt();
    
            if ($bcrypt->verify($senha, $user['senha'])) {
                return $user;
            }
        }
        
        return null;
    }

    private function authenticateViaApi($email, $senha)
    {
        // Remove o domínio do email para usar na API
        $username = str_replace('@sementessaofrancisco.com.br', '', $email);
    
        try {
            $client = new Client();
            $client->setUri('http://192.168.0.54:8882/api/LoginAD');
            $client->setMethod(Request::METHOD_GET);
            $client->setParameterGet([
                'psDominio' => 'SAOFRANCISCO',
                'psUsuario' => $username,
                'psSenha' => $senha
            ]);

            // Configurações adicionais recomendadas
            $client->setOptions([
                'timeout' => 30,
                'sslverifypeer' => false // Apenas para desenvolvimento!
            ]);
    
            $response = $client->send();
            
            if ($response->isSuccess()) {
                $body = $response->getBody();
                $result = json_decode($body, true);

                // Adapte esta verificação conforme a resposta real da sua API
                if ($result && isset($result['status']) && $result['status'] == 202) {
                    return [
                        'email' => $email,
                        'username' => $username
                    ];
                }
            } else {
                error_log('Falha na API: ' . $response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            }
        } catch (\Exception $e) {
            error_log('Erro na conexão com a API: ' . $e->getMessage());
        }
        
        return null;
    }

    private function createLocalUser($email, $senha)
    {
        $bcrypt = new Bcrypt();
        $hashedPassword = $bcrypt->create($senha);

        // Extrai o username do email (parte antes do @)
        $username = strstr($email, '@', true);
        
        // Formata o nome
        $nome = $this->formatarNomeUsuario($username);
        
        // Define um role padrão
        $role = $this->determinarRole($username); // verifica o cargo do usuário para definir a role automaticamente, caso nao tenha cadastra como convidado.
        
        $sql = new Sql($this->dbAdapter);
        $insert = $sql->insert('usuario');
        $insert->values([
            'email' => $email,
            'senha' => $hashedPassword,
            'nome' => $nome,
            'ativo' => true,
            'role' => $role
        ]);
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        
        if ($result->getAffectedRows() === 1) {
            // Obtém o ID gerado
            $id = $result->getGeneratedValue();
            
            // Retorna o usuário criado com todos os campos necessários
            return [
                'id' => $id,
                'email' => $email,
                'nome' => $nome,
                'role' => $role,
                'ativo' => true
                // Adicione outros campos que sua aplicação espera
            ];
        }
        
        return null;
    }

    private function determinarRole($username)
    {
        try {
            $cargo = $this->oracleService->getCargoUsuario($username);

            // Mapeamento de cargos para roles
            $mapeamentoRoles = [
                'DIRETOR' => 'Diretor',
                'GERENTE' => 'Gerente',
                'COORDENADOR' => 'Coordenador',
                'ANALISTA' => 'Analista',
                'ASSISTENTE' => 'Assistente',
                'AUXILIAR' => 'Auxiliar'
            ];

            // Normaliza o cargo para facilitar a comparação (remove acentos e coloca em maiúsculo)
            $cargoNormalizado = mb_strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT', $cargo));

            $roleEncontrado = 'Convidado'; 

            foreach ($mapeamentoRoles as $palavraChave => $role) {
                // Normaliza a palavra-chave para comparação
                $palavraNormalizada = mb_strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT', $palavraChave));
                
                if (strpos($cargoNormalizado, $palavraNormalizada) !== false) {
                    $roleEncontrado = $role;
                    break; // Sai do loop quando encontrar a primeira correspondência
                } else {
                    $roleEncontrado = 'Convidado';
                }
            }
            
            return $roleEncontrado;
            
        } catch (\Exception $e) {
            error_log("Erro ao consultar cargo no Oracle: " . $e->getMessage());
            return 'Convidado'; // Retorna o valor padrão em caso de erro
        }
    }

    private function formatarNomeUsuario($username)
    {
        // Remove o domínio se ainda estiver presente
        $username = str_replace('@sementessaofrancisco.com.br', '', $username);
        
        // Remove pontos e substitui underlines por espaços
        $nome = str_replace(['.', '_'], ' ', $username);
        
        // Capitaliza cada palavra
        $nome = ucwords(strtolower($nome));
        
        return $nome;
    }
}
