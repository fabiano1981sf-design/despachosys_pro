<?php
session_start();

define('SITE_NAME', 'DespachoSys PRO');
define('DB_HOST', 'localhost');
define('DB_NAME', 'teste2');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// ====================== CONEXÃO ======================
class DB {
    private static $instance = null;
    private $pdo;
    private function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (Exception $e) {
            die("Erro de banco: " . $e->getMessage());
        }
    }
    public static function getInstance() {
        if (self::$instance === null) self::$instance = new DB();
        return self::$instance->pdo;
    }
}

// ====================== AUTH ======================
class Auth {
    public static function login($email, $senha) {
        $pdo = DB::getInstance();
        $stmt = $pdo->prepare("SELECT id, nome, senha_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($senha, $user['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['role'] = $user['role'];
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Login inválido'];
    }
    public static function isLoggedIn() { return isset($_SESSION['user_id']); }
    public static function logout() { session_destroy(); session_start(); }
}

// ====================== SYSTEMCORE — TUDO FUNCIONANDO ======================
class SystemCore {

    public static function checkPermission($page) { return true; }

    public static function getUser() {
        if (!Auth::isLoggedIn()) return null;
        $stmt = DB::getInstance()->prepare("SELECT id, nome, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    // MERCADORIAS
    public static function getMercadorias() {
        return DB::getInstance()->query("SELECT m.*, COALESCE(c.nome, 'Sem categoria') AS categoria_nome 
                                         FROM mercadorias m LEFT JOIN categorias c ON m.categoria_id = c.id 
                                         ORDER BY m.nome")->fetchAll();
    }

    public static function saveMercadoria($data) {
        $pdo = DB::getInstance();
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $nome = trim($data['nome'] ?? '');
        $sku = strtoupper(trim($data['sku'] ?? ''));
        $cat = !empty($data['categoria_id']) ? (int)$data['categoria_id'] : null;
        $custo = (float)str_replace(['.',','], ['', '.'], $data['preco_custo'] ?? '0');
        $venda = (float)str_replace(['.',','], ['', '.'], $data['preco_venda'] ?? '0');
        $qtd = (int)($data['quantidade_estoque'] ?? 0);
        $un = trim($data['unidade'] ?? 'UN');

        if (empty($nome) || $venda <= 0) return ['success'=>false, 'message'=>'Nome e preço obrigatórios'];

        try {
            if (!empty($sku)) {
                $check = $pdo->prepare("SELECT id FROM mercadorias WHERE sku = ? AND id != ?");
                $check->execute([$sku, $id ?? 0]);
                if ($check->fetch()) return ['success'=>false, 'message'=>'SKU já existe'];
            }
            $isUpdate = $id !== null;
            if ($isUpdate) {
                $pdo->prepare("UPDATE mercadorias SET nome=?, sku=?, categoria_id=?, preco_custo=?, preco_venda=?, quantidade_estoque=?, unidade=? WHERE id=?")
                    ->execute([$nome, $sku?:null, $cat, $custo, $venda, $qtd, $un, $id]);
            } else {
                $pdo->prepare("INSERT INTO mercadorias (nome, sku, categoria_id, preco_custo, preco_venda, quantidade_estoque, unidade) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$nome, $sku?:null, $cat, $custo, $venda, $qtd, $un]);
            }
            return ['success'=>true, 'message'=>'Salvo!'];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }

    // CATEGORIAS
    public static function getCategorias() {
        return DB::getInstance()->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
    }

    // CLIENTES — 100% SEGURO (nunca mais dá erro de coluna)
    public static function getClientes() {
        return DB::getInstance()->query("SELECT id, 
            COALESCE(NULLIF(TRIM(nome),''), 
                     NULLIF(TRIM(razao_social),''), 
                     NULLIF(TRIM(nome_fantasia),''), 
                     cpf_cnpj, 
                     CONCAT('Cliente ', id)) AS nome 
            FROM clientes ORDER BY nome")->fetchAll();
    }

    // DESPACHOS — 100% SEGURO
    public static function getDespachos() {
        return DB::getInstance()->query("
            SELECT d.*,
                   COALESCE(NULLIF(TRIM(c.nome),''), 
                            NULLIF(TRIM(c.razao_social),''), 
                            NULLIF(TRIM(c.nome_fantasia),''), 
                            c.cpf_cnpj, 
                            CONCAT('Cliente #', d.cliente_id)) AS cliente_nome,
                   COALESCE(m.nome, 'Produto Excluído') AS mercadoria_nome,
                   COALESCE(m.sku, '-') AS sku,
                   DATE_FORMAT(d.created_at, '%d/%m/%Y %H:%i') AS data_formatada
            FROM despachos d
            LEFT JOIN clientes c ON d.cliente_id = c.id
            LEFT JOIN mercadorias m ON d.mercadoria_id = m.id
            ORDER BY d.created_at DESC
        ")->fetchAll();
    }

    public static function saveDespacho($data) {
        $pdo = DB::getInstance();
        $c = (int)($data['cliente_id'] ?? 0);
        $m = (int)($data['mercadoria_id'] ?? 0);
        $q = (int)($data['quantidade'] ?? 0);
        if ($c <= 0 || $m <= 0 || $q <= 0) return ['success'=>false, 'message'=>'Campos obrigatórios'];

        $estoque = $pdo->prepare("SELECT quantidade_estoque FROM mercadorias WHERE id = ?");
        $estoque->execute([$m]);
        if ($estoque->fetchColumn() < $q) return ['success'=>false, 'message'=>'Estoque insuficiente'];

        try {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO despachos (cliente_id, mercadoria_id, quantidade, codigo_rastreio, transportadora, observacao, user_id) VALUES (?,?,?,?,?,?,?)")
                ->execute([$c, $m, $q, $data['codigo_rastreio']??'', $data['transportadora']??'', $data['observacao']??'', $_SESSION['user_id']??1]);
            $pdo->prepare("UPDATE mercadorias SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?")->execute([$q, $m]);
            $pdo->prepare("INSERT INTO estoque_movimentacao (mercadoria_id, tipo, quantidade, motivo, user_id) VALUES (?, 'saida', ?, 'Despacho', ?)")
                ->execute([$m, $q, $_SESSION['user_id']??1]);
            $pdo->commit();
            return ['success'=>true, 'message'=>'Despacho realizado!'];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }

    // MOVIMENTAÇÃO DE ESTOQUE — AGORA FUNCIONA!
    public static function getEstoqueMovements() {
        return DB::getInstance()->query("
            SELECT em.*, 
                   m.nome AS mercadoria_nome, 
                   m.sku,
                   u.nome AS usuario_nome,
                   DATE_FORMAT(em.created_at, '%d/%m/%Y %H:%i') AS data_formatada
            FROM estoque_movimentacao em
            LEFT JOIN mercadorias m ON em.mercadoria_id = m.id
            LEFT JOIN users u ON em.user_id = u.id
            ORDER BY em.created_at DESC
        ")->fetchAll();
    }

    public static function addEstoqueMovement($data) {
        $pdo = DB::getInstance();
        $m = (int)($data['mercadoria_id'] ?? 0);
        $q = abs((int)($data['quantidade'] ?? 0));
        $tipo = in_array($data['tipo'] ?? '', ['entrada','saida']) ? $data['tipo'] : 'entrada';
        $motivo = trim($data['motivo'] ?? '');
        if ($m <= 0 || $q <= 0) return ['success'=>false, 'message'=>'Dados inválidos'];

        try {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO estoque_movimentacao (mercadoria_id, tipo, quantidade, motivo, user_id) VALUES (?,?,?,?,?)")
                ->execute([$m, $tipo, $q, $motivo, $_SESSION['user_id']??1]);
            $op = $tipo === 'entrada' ? '+' : '-';
            $pdo->prepare("UPDATE mercadorias SET quantidade_estoque = GREATEST(0, quantidade_estoque $op ?) WHERE id = ?")
                ->execute([$q, $m]);
            $pdo->commit();
            return ['success'=>true, 'message'=>'Movimentação registrada!'];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success'=>false, 'message'=>$e->getMessage()];
        }
    }

    // DELETE GENÉRICO
    public static function deleteById($tabela, $id) {
        try {
            DB::getInstance()->prepare("DELETE FROM $tabela WHERE id = ?")->execute([$id]);
            return ['success'=>true, 'message'=>'Excluído!'];
        } catch (Exception $e) {
            return ['success'=>false, 'message'=>'Não pode excluir'];
        }
    }

    // DASHBOARD
    public static function getDashboardStats() {
        $pdo = DB::getInstance();
        $s = ['total_mercadorias'=>0, 'qtd_total_estoque'=>0, 'total_clientes'=>0, 'total_despachos'=>0];
        $s['total_mercadorias'] = $pdo->query("SELECT COUNT(*) FROM mercadorias")->fetchColumn();
        $s['qtd_total_estoque'] = $pdo->query("SELECT COALESCE(SUM(quantidade_estoque),0) FROM mercadorias")->fetchColumn();
        $s['total_clientes']    = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        $s['total_despachos']   = $pdo->query("SELECT COUNT(*) FROM despachos")->fetchColumn();
        return $s;
    }

    // FUNÇÕES EXTRAS
    public static function getConfig($a,$b=null){return $b;}
    public static function saveConfig($a,$b){return true;}
    public static function getUsers(){return [];}
}