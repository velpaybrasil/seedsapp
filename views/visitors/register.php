<?php
// Configuração do banco de dados
$db_host = 'localhost';
$db_name = 'u315624178_gcmanager';
$db_user = 'u315624178_gcmanager';
$db_pass = 'gugaLima8*';

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gera um novo token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Classe Visitor simplificada
class Visitor {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO visitors (
            name, gender, marital_status, phone, whatsapp, email, 
            neighborhood, city, prayer_requests, wants_group,
            birth_date, first_visit_date, status, created_at
        ) VALUES (
            :name, :gender, :marital_status, :phone, :whatsapp, :email,
            :neighborhood, :city, :prayer_requests, :wants_group,
            :birth_date, :first_visit_date, :status, NOW()
        )";

        $stmt = $this->pdo->prepare($sql);
        
        // Prepara os dados com valores padrão para campos opcionais
        $params = [
            'name' => $data['name'] ?? null,
            'gender' => $data['gender'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'email' => $data['email'] ?? null,
            'neighborhood' => $data['neighborhood'] ?? null,
            'city' => $data['city'] ?? null,
            'prayer_requests' => $data['prayer_requests'] ?? null,
            'wants_group' => $data['wants_group'] ?? 'no',
            'birth_date' => $data['birth_date'] ?? null,
            'first_visit_date' => date('Y-m-d'),
            'status' => $data['status'] ?? 'pending'
        ];
        
        return $stmt->execute($params);
    }
}

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $visitor = new Visitor($pdo);
        $data = [
            'name' => $_POST['name'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'marital_status' => $_POST['marital_status'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'whatsapp' => $_POST['whatsapp'] ?? '',
            'email' => $_POST['email'] ?? '',
            'neighborhood' => $_POST['neighborhood'] ?? '',
            'city' => $_POST['city'] ?? '',
            'wants_group' => isset($_POST['wants_group']) ? 'yes' : 'no',
            'prayer_requests' => $_POST['prayer_requests'] ?? '',
            'birth_date' => $_POST['birth_date'] ?? null,
            'status' => 'pending'
        ];

        $visitor->create($data);
        $success = true;
    } catch (Exception $e) {
        $error = "Erro ao salvar visitante: " . $e->getMessage();
    }
}

// Define o título da página
$title = 'Cadastro de Visitante';

// Inclui o cabeçalho
require_once __DIR__ . '/../layouts/default.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="logo-container text-center mb-4">
                <img src="https://ccvideira.com.br/wp-content/uploads/elementor/thumbs/logo-site-160-qn47g9jb95m0r45q5jddwrbmv06b9tafl4bl9sir4g.webp" 
                     alt="CC Videira Logo" 
                     class="img-fluid">
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-center mb-4">Cadastro de Visitante</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Cadastro realizado com sucesso! Entraremos em contato em breve.
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gênero</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Selecione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Estado Civil</label>
                                <select class="form-select" id="marital_status" name="marital_status">
                                    <option value="">Selecione...</option>
                                    <option value="single">Solteiro(a)</option>
                                    <option value="married">Casado(a)</option>
                                    <option value="divorced">Divorciado(a)</option>
                                    <option value="widowed">Viúvo(a)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="neighborhood" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="neighborhood" name="neighborhood">
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="wants_group" name="wants_group">
                                <label class="form-check-label" for="wants_group">
                                    Deseja participar de um grupo?
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="prayer_requests" class="form-label">Pedidos de Oração</label>
                            <textarea class="form-control" id="prayer_requests" name="prayer_requests" rows="3"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
