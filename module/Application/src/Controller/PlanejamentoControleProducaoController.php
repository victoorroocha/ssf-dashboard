<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\PlanejamentoControleProducaoRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class PlanejamentoControleProducaoController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $PlanejamentoControleProducaoRepository;

    public function __construct(Adapter $pgAdapter, OracleService $oracleService = null, PlanejamentoControleProducaoRepository $PlanejamentoControleProducaoRepository = null, Acl $acl)
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->PlanejamentoControleProducaoRepository = $PlanejamentoControleProducaoRepository;
    }

    // Método para obter usuário da sessão (você precisa implementar conforme sua aplicação)
    private function getUsuarioSessao()
    {
        // Exemplo de como obter o usuário da sessão
        $session = new Container('auth');
        if ($session->offsetExists('user')) {
            return $session->offsetGet('user');
        }
        
        return null;
    }

    #region Cadastro Equipamentos
        public function cadastroEquipamentoAction()
        {
            $session = new Container('auth');
            if (!isset($session->user)) {
                return $this->redirect()->toRoute('login');
            }
            return new ViewModel();
        }
        public function listarEquipamentosAction()
        {
            try {
                $equipamentos = $this->PlanejamentoControleProducaoRepository->listarEquipamentos();
                return new JsonModel([
                    'success' => true,
                    'data' => $equipamentos,
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar equipamentos: ' . $e->getMessage(),
                ]);
            }
        }
        public function salvarEquipamentoAction()
        {
            if (!$this->getRequest()->isPost() && !$this->getRequest()->isPut()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleProducaoRepository->salvarEquipamento($data);
                return new JsonModel([
                    'success' => true,
                    'message' => $this->getRequest()->isPut() ? 'Equipamento atualizado com sucesso!' : 'Equipamento adicionado com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao salvar equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function excluirEquipamentoAction()
        {
            if (!$this->getRequest()->isDelete()) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Método não permitido.',
                ]);
            }

            $data = json_decode($this->getRequest()->getContent(), true);

            try {
                $this->PlanejamentoControleProducaoRepository->excluirEquipamento($data['id']);
                return new JsonModel([
                    'success' => true,
                    'message' => 'Equipamento excluído com sucesso!',
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao excluir equipamento: ' . $e->getMessage(),
                ]);
            }
        }
        public function getLookupEquipamentosAction() // Esse Lookup para os controles, não lista equipamentos inativos!
        {
            // Recebe o parâmetro de pesquisa
            $key = $this->params()->fromQuery('key', '');
            $search = $this->params()->fromQuery('search', '');
            $search = strtoupper(trim($search));
            $offset = $this->params()->fromQuery('offset', 0);
            $limit = $this->params()->fromQuery('limit', 30);

            try {
                $result = $this->PlanejamentoControleProducaoRepository->getLookupEquipamentos($search, $key, $offset, $limit);

                return new JsonModel([
                    'success' => true,
                    'data' => $result['data'],
                    'totalCount' => $result['totalCount']
                ]);
            } catch (\Exception $e) {
                return new JsonModel([
                    'success' => false,
                    'message' => 'Erro ao listar equipamentos: ' . $e->getMessage(),
                ]);
            }
        }
    #endRegion

    public function getUsuariosSeniorLookupAction()
    {
        $request = $this->getRequest();

        $sql = "SELECT 
                    R034FUN.NUMEMP 
                    ,R034FUN.TIPCOL 
                    ,R034FUN.NUMCAD AS MATRICULA
                    ,R034FUN.NOMFUN AS NOME_COLABORADOR
                    ,LPAD(TO_CHAR(R034FUN.NUMCPF), 11, '0') AS  CPF
                    ,R034FUN.DATADM
                    ,TRUNC(MONTHS_BETWEEN(SYSDATE, R034FUN.DATNAS ) / 12) AS IDADE 
                    ,CASE WHEN LENGTH(REGEXP_REPLACE(R033PES.DDDTEL, '[^0-9]', '')) = 2 AND LENGTH(REGEXP_REPLACE(R033PES.NUMTEL, '[^0-9]', '')) >= 8 THEN REGEXP_REPLACE(CONCAT(R033PES.DDDTEL, R033PES.NUMTEL), '[^0-9]', '') ELSE NULL END NUMTEL            
                    ,CASE WHEN LENGTH(REGEXP_REPLACE(R033PES.DDDCEL, '[^0-9]', '')) = 2 AND LENGTH(REGEXP_REPLACE(R033PES.NUMCEL, '[^0-9]', '')) >= 8 THEN REGEXP_REPLACE(CONCAT(R033PES.DDDTEL, R033PES.NUMCEL), '[^0-9]', '') ELSE NULL END NUMCEL            
                    ,R024CAR.TITCAR AS DSC_CARGO
                FROM VETORH.R034FUN
                INNER JOIN VETORH.R010SIT ON R010SIT.CODSIT = R034FUN.SITAFA
                LEFT JOIN VETORH.R024CAR ON R024CAR.CODCAR = R034FUN.CODCAR
                LEFT JOIN VETORH.R033PES ON R033PES.CADAUX = R034FUN.NUMCAD AND R033PES.EMPAUX = R034FUN.NUMEMP AND R033PES.NUMCPF = R034FUN.NUMCPF 
                WHERE R034FUN.TIPCOL = 1
                AND R034FUN.SITAFA <> 7
                AND R034FUN.NUMEMP IN (5,12)
                ORDER BY R034FUN.NOMFUN";

        try {
            $result = $this->oracleService->executeQuery($sql, ['search' => $search]);

            foreach ($result as $key => $row) {
                $result[$key]['MATRICULA'] = intval($row['MATRICULA']);
                $result[$key]['NOME'] = utf8_encode($row['NOME']);
                $result[$key]['DSC_CARGO'] = utf8_encode($row['DSC_CARGO']);
                $result[$key]['NOME_COLABORADOR'] = utf8_encode($row['NOME_COLABORADOR']);
            }

            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao buscar responsáveis: ' . $e->getMessage()
            ]);
        }
    }
    public function listarCentrosCustoAction()
    {
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        try {
            $sql = "SELECT
                         CODCCU as ID
                        ,CODCCU || ' - ' || DESCCU AS DSC
                    FROM E044CCU
                    WHERE CODEMP = 5
                    ORDER BY CODCCU";

            $result = $this->oracleService->executeQuery($sql);
            foreach ($result as $key => $row) {
                $result[$key]['ID'] = intval($row['ID']);
                $result[$key]['DSC'] = utf8_encode($row['DSC']);
            }

            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getCentroCustoLookupAction()
    {
        if (!$this->oracleService) {
            return new JsonModel([
                'success' => false,
                'message' => 'Serviço Oracle não disponível'
            ]);
        }

        $search = strtoupper(trim($this->params()->fromQuery('search', '')));
        $key = $this->params()->fromQuery('key', '');
        $offset = (int) $this->params()->fromQuery('offset', 0);
        $limit = (int) $this->params()->fromQuery('limit', 30);

        try {
            $where = "WHERE CODEMP = 5";
            if (!empty($search)) {
                $where .= " AND (CODCCU LIKE '%$search%' OR DESCCU LIKE '%$search%')";
            }
            if (!empty($key)) {
                $where .= " AND CODCCU = $key";
            }

            $sql = "SELECT * FROM (
                        SELECT
                            CODCCU as ID,
                            CODCCU || ' - ' || DESCCU AS DSC,
                            ROW_NUMBER() OVER (ORDER BY CODCCU) AS RN
                        FROM E044CCU
                        $where
                    ) WHERE RN BETWEEN " . ($offset + 1) . " AND " . ($offset + $limit);

            $result = $this->oracleService->executeQuery($sql);
            foreach ($result as $key => $row) {
                $result[$key]['ID'] = intval($row['ID']);
                $result[$key]['DSC'] = utf8_encode($row['DSC']);
            }

            // Contagem total para paginação
            $countSql = "SELECT COUNT(*) AS TOTAL FROM E044CCU $where";
            $countResult = $this->oracleService->executeQuery($countSql);
            $totalCount = $countResult[0]['TOTAL'] ?? 0;

            return new JsonModel([
                'success' => true,
                'data' => $result,
                'totalCount' => $totalCount
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function getProdutosEstoqueLookupAction()
    {
        $request = $this->getRequest();
        $search = strtoupper($this->params()->fromQuery('search', ''));
        $key = $this->params()->fromQuery('key', '');

        $ands = "";
        if (!empty($key)) {
            $ands .= "AND E210EST.CODPRO = '{$key}'";
        }
        if (!empty($search)) {
            $ands .= "AND E210EST.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(PRO.DESPRO), '\\s+', ' ') || ' - ' || REGEXP_REPLACE(TRIM(DEP.DESDEP), '\\s+', ' ') LIKE '%{$search}%'";
        }

        $sql = "SELECT 
                    E210EST.CODEMP,
                    E210EST.CODPRO,
                    REGEXP_REPLACE(TRIM(upper(PRO.DESPRO)), '\\s+', ' ') AS DESPRO,
                    E210EST.UNIMED,
                    E210EST.CODDEP,
                    REGEXP_REPLACE(TRIM(upper(DEP.DESDEP)), '\\s+', ' ') AS DESDEP,
                    E210EST.QTDEST,
                    nvl((SELECT AVG(PRMEST) FROM E210MVP WHERE CODPRO = E210EST.CODPRO AND CODDEP = E210EST.CODDEP ),0) AS PRMEST,
                    E210EST.CODPRO || ' - ' || REGEXP_REPLACE(TRIM(upper(PRO.DESPRO)), '\\s+', ' ') || ' - ' || REGEXP_REPLACE(TRIM(upper(DEP.DESDEP)), '\\s+', ' ') AS PRODUTO_DISPLAY,
                    CASE WHEN PRO.CLAPRO = 1 THEN 'Estoque' WHEN PRO.CLAPRO = 2 THEN 'Passagem Direta' END AS CLAPRO,
                    E210EST.CODEND
                FROM E210EST  
                LEFT JOIN E075PRO PRO ON PRO.CODEMP = E210EST.CODEMP AND PRO.CODPRO = E210EST.CODPRO
                LEFT JOIN E205DEP DEP ON DEP.CODEMP = E210EST.CODEMP AND DEP.CODDEP = E210EST.CODDEP
                WHERE E210EST.CODEMP = 5
                AND E210EST.CODDEP = 1
                AND PRO.SITPRO = 'A'
                AND PRO.CLAPRO IN (1,2)
                {$ands}
                ORDER BY PRO.DESPRO
                FETCH FIRST 30 ROWS ONLY";

        try {
            $result = $this->oracleService->executeQuery($sql);

            foreach ($result as $key => $row) {
                $result[$key]['DESPRO'] = utf8_encode($row['DESPRO']);
                $result[$key]['DESDEP'] = utf8_encode($row['DESDEP']);
                $result[$key]['PRODUTO_DISPLAY'] = utf8_encode($row['PRODUTO_DISPLAY']);
                $result[$key]['QTDEST'] = floatval(str_replace(',', '.', $row['QTDEST']));
                $result[$key]['PRMEST'] = floatval(str_replace(',', '.', $row['PRMEST']));
            }

            return new JsonModel([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'message' => 'Erro ao buscar produtos: ' . $e->getMessage()
            ]);
        }
    }


}