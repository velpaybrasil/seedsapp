<?php

namespace App\Core;

use App\Core\Database\Database;

abstract class Controller 
{
    protected string $viewPath;
    protected Database $db;
    protected array $flash = [];

    protected function __construct() 
    {
        // Initialize session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize CSRF token if not set
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Set view path relative to the application root
        $this->viewPath = dirname(dirname(__DIR__)) . '/views';
        
        // Initialize database connection
        $this->db = Database::getInstance();
        
        // Initialize flash messages
        $this->flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        // Verify CSRF token for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'], $token)) {
                error_log('CSRF token validation failed');
                $this->setFlash('error', 'Token de segurança inválido. Por favor, tente novamente.');
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
                exit;
            }
        }
    }

    protected function validateCsrfToken(): bool
    {
        $token = $_POST['_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }

    protected function redirect(string $path): void 
    {
        header("Location: {$path}");
        exit;
    }

    protected function setFlash(string $type, mixed $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        if ($type === 'errors' || $type === 'old') {
            $_SESSION['flash'][$type] = $message;
        } else {
            $_SESSION['flash'] = [
                'type' => $type,
                'message' => $message
            ];
        }
    }

    protected function getFlash(): ?array 
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    protected function view(string $view, array $data = []): void 
    {
        // Add flash messages to data if they exist
        if ($flash = $this->getFlash()) {
            $data['flash'] = $flash;
        }

        try {
            View::render($view, $data);
        } catch (\Exception $e) {
            error_log("Error rendering view: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateRequest(array $rules): array
    {
        $errors = [];
        $data = $_POST;

        foreach ($rules as $field => $rule) {
            // Required field validation
            if (strpos($rule, 'required') !== false && empty($data[$field])) {
                $errors[$field][] = "O campo é obrigatório";
                continue;
            }

            // Skip other validations if field is empty and not required
            if (empty($data[$field])) {
                continue;
            }

            // Email validation
            if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field][] = "Email inválido";
            }

            // Minimum length validation
            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = (int)$matches[1];
                if (strlen($data[$field]) < $min) {
                    $errors[$field][] = "O campo deve ter no mínimo {$min} caracteres";
                }
            }

            // Maximum length validation
            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $max = (int)$matches[1];
                if (strlen($data[$field]) > $max) {
                    $errors[$field][] = "O campo deve ter no máximo {$max} caracteres";
                }
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', 'Por favor, corrija os erros no formulário.');
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            return $errors;
        }

        return [];
    }

    protected function getFormErrors(): array
    {
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        return $errors;
    }

    protected function getOldFormData(): array
    {
        $data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        return $data;
    }

    protected function isLoggedIn(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    protected function checkAuth(): void {
        if (!$this->isLoggedIn()) {
            $this->setFlash('danger', 'Você precisa estar logado para acessar esta página.');
            $this->redirect('/login');
        }
    }

    protected function addFlashMessage(string $type, string $message): void {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }

    protected function getFlashMessages(): array {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    protected function getPostData(): array
    {
        $data = $_POST;
        unset($data['_token']); // Remove CSRF token from data
        return $data;
    }

    protected function getQueryParams(): array {
        return $_GET;
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCSRF(): bool
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !isset($_SESSION['csrf_token'])) {
            return false;
        }

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }

        return true;
    }

    protected function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlash('error', 'Você precisa estar logado para acessar esta página.');
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            $this->setFlash('error', 'Você não tem permissão para acessar esta página.');
            $this->redirect('/dashboard');
        }
    }

    protected function getCurrentUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }

    protected function getCurrentUserId(): ?int 
    {
        return $_SESSION['user_id'] ?? null;
    }

    protected function validateInput(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleArray = explode('|', $rule);

            foreach ($ruleArray as $singleRule) {
                // Parse rule with parameters
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleParams = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                            $errors[$field] = 'O campo é obrigatório.';
                        }
                        break;

                    case 'min':
                        if ($value !== null && $value !== '') {
                            if (is_array($value)) {
                                if (count($value) < (int)$ruleParams[0]) {
                                    $errors[$field] = "É necessário pelo menos {$ruleParams[0]} item(s).";
                                }
                            } else if (strlen($value) < (int)$ruleParams[0]) {
                                $errors[$field] = "O campo deve ter no mínimo {$ruleParams[0]} caracteres.";
                            }
                        }
                        break;

                    case 'max':
                        if ($value !== null && $value !== '') {
                            if (is_array($value)) {
                                if (count($value) > (int)$ruleParams[0]) {
                                    $errors[$field] = "O máximo permitido é {$ruleParams[0]} item(s).";
                                }
                            } else if (strlen($value) > (int)$ruleParams[0]) {
                                $errors[$field] = "O campo deve ter no máximo {$ruleParams[0]} caracteres.";
                            }
                        }
                        break;

                    case 'numeric':
                        if ($value !== null && $value !== '' && !is_numeric($value)) {
                            $errors[$field] = 'O campo deve ser numérico.';
                        }
                        break;

                    case 'array':
                        if ($value !== null && !is_array($value)) {
                            $errors[$field] = 'O campo deve ser um array.';
                        }
                        break;

                    case 'between':
                        if ($value !== null && $value !== '') {
                            $min = (float)$ruleParams[0];
                            $max = (float)$ruleParams[1];
                            $numericValue = (float)$value;
                            
                            if ($numericValue < $min || $numericValue > $max) {
                                $errors[$field] = "O valor deve estar entre {$min} e {$max}.";
                            }
                        }
                        break;

                    case 'email':
                        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'O email é inválido.';
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    protected function validateUpload(array $file, array $allowedTypes = [], int $maxSize = 5242880): array
    {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'O arquivo excede o tamanho máximo permitido.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'O upload do arquivo foi interrompido.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'Nenhum arquivo foi enviado.';
                    break;
                default:
                    $errors[] = 'Erro no upload do arquivo.';
            }
            return $errors;
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = sprintf(
                'O arquivo excede o tamanho máximo permitido de %s MB.',
                number_format($maxSize / 1048576, 2)
            );
        }

        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = 'Tipo de arquivo não permitido.';
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', 'Erro no upload do arquivo.');
        }

        return $errors;
    }

    protected function handleFileUpload(array $file, string $destination, ?string $filename = null): array
    {
        $errors = [];
        
        // Create destination directory if it doesn't exist
        if (!is_dir($destination)) {
            if (!mkdir($destination, 0777, true)) {
                $this->setFlash('error', 'Erro ao criar diretório de destino.');
                return ['Erro ao criar diretório de destino.'];
            }
        }

        // Generate unique filename if not provided
        if ($filename === null) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
        }

        $targetPath = $destination . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors[] = 'Erro ao mover o arquivo.';
            $this->setFlash('error', 'Erro ao salvar o arquivo.');
            return $errors;
        }

        return [];
    }

    protected function checkPermission(string $module, string $action): bool
    {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlash('error', 'Você precisa estar logado para acessar esta página.');
            $this->redirect('/login');
            return false;
        }

        $userId = $_SESSION['user_id'];
        $hasPermission = $this->db->query(
            "SELECT 1 FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            JOIN modules m ON p.module_id = m.id
            WHERE ur.user_id = ? AND m.name = ? AND p.action = ?",
            [$userId, $module, $action]
        )->rowCount() > 0;

        if (!$hasPermission) {
            $this->setFlash('error', 'Você não tem permissão para realizar esta ação.');
            $this->redirect('/dashboard');
            return false;
        }

        return true;
    }

    protected function hasPermission(string $module, string $action): bool
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                return false;
            }

            $userId = $_SESSION['user_id'];
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT COUNT(*) FROM user_roles ur
                    INNER JOIN role_permissions rp ON ur.role_id = rp.role_id
                    INNER JOIN permissions p ON rp.permission_id = p.id
                    INNER JOIN modules m ON p.module_id = m.id
                    WHERE ur.user_id = :user_id
                    AND m.name = :module
                    AND p.action = :action";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':module' => $module,
                ':action' => $action
            ]);

            return (bool)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error checking permissions: " . $e->getMessage());
            return false;
        }
    }
}
