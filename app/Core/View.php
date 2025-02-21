<?php

namespace App\Core;

class View {
    private static string $viewsPath;
    private static string $extends = '';
    private static array $sections = [];
    private static string $currentSection = '';
    private static array $data = [];
    
    public static function init(string $viewsPath): void {
        self::$viewsPath = $viewsPath;
    }
    
    public static function render(string $view, array $data = [], ?string $layout = 'app'): void {
        try {
            // Merge data with existing data
            self::$data = array_merge(self::$data, $data);
            
            // Extract data for view
            extract(self::$data);
            
            // Start output buffering
            ob_start();
            
            // Include the view
            $viewFile = self::$viewsPath . '/' . str_replace('.', '/', $view) . '.php';
            if (!file_exists($viewFile)) {
                error_log("[View] View file not found: {$viewFile}");
                throw new \Exception("View não encontrada: {$view}");
            }
            require $viewFile;
            
            // Get the view content
            $content = ob_get_clean();
            
            // If we have a layout, render it with the content
            if ($layout && self::$extends) {
                $layout = self::$extends;
            }
            
            if ($layout) {
                // Store the content in a section if not already set
                if (!isset(self::$sections['content'])) {
                    self::$sections['content'] = $content;
                }
                
                // Render the layout
                $layoutFile = self::$viewsPath . '/layouts/' . $layout . '.php';
                if (!file_exists($layoutFile)) {
                    error_log("[View] Layout file not found: {$layoutFile}");
                    throw new \Exception("Layout não encontrado: {$layout}");
                }
                require $layoutFile;
            } else {
                // No layout, just output the content
                echo $content;
            }
        } catch (\Exception $e) {
            error_log('[View] Erro ao renderizar view: ' . $e->getMessage());
            error_log('[View] Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
    
    public static function section(string $name): void {
        self::$currentSection = $name;
        ob_start();
    }
    
    public static function endSection(): void {
        if (self::$currentSection) {
            $content = ob_get_clean();
            if ($content === false) {
                error_log("Error getting buffer content for section: " . self::$currentSection);
                $content = '';
            }
            self::$sections[self::$currentSection] = $content;
            self::$currentSection = '';
        }
    }
    
    public static function renderSection(string $name): void {
        if (isset(self::$sections[$name])) {
            echo self::$sections[$name];
        }
    }
    
    // Alias para section()
    public static function sectionStart(string $name): void {
        self::section($name);
    }
    
    // Alias para endSection()
    public static function sectionEnd(): void {
        self::endSection();
    }
    
    // Alias para renderSection()
    public static function yield(string $name): void {
        self::renderSection($name);
    }
    
    public static function extends(string $layout): void {
        self::$extends = $layout;
    }
    
    public static function renderPartial(string $view, array $data = []): void {
        try {
            // Extract data for partial
            $mergedData = array_merge(self::$data, $data);
            extract($mergedData);
            
            // Include the partial view
            $viewFile = self::$viewsPath . '/' . str_replace('.', '/', $view) . '.php';
            if (!file_exists($viewFile)) {
                error_log("[View] Partial view file not found: {$viewFile}");
                throw new \Exception("Partial view não encontrada: {$view}");
            }
            require $viewFile;
        } catch (\Exception $e) {
            error_log('[View] Erro ao renderizar partial view: ' . $e->getMessage());
            error_log('[View] Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
    
    public static function escape(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function asset(string $path): string {
        return APP_URL . '/public/' . ltrim($path, '/');
    }
    
    public static function url(string $path): string {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return APP_URL . '/' . ltrim($path, '/');
    }
    
    public static function old(string $key, $default = ''): mixed {
        return $_SESSION['old'][$key] ?? $default;
    }
    
    public static function csrf(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf_token" value="' . self::escape($_SESSION['csrf_token']) . '">';
    }
    
    public static function csrf_token(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function hasFlash(): bool {
        return isset($_SESSION['flash']) || !empty($_SESSION['flash_messages']);
    }
    
    public static function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
    
    public static function getFlashMessages(): array {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    
    public static function isLoggedIn(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public static function user(): ?array {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }

    public static function formatDate(?string $date, string $format = 'd/m/Y'): string {
        if (!$date) {
            return '';
        }
        return date($format, strtotime($date));
    }
    
    public static function isAdmin(): bool {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
    }
    
    public static function formatDateTime(string $date, string $format = 'd/m/Y H:i'): string {
        return date($format, strtotime($date));
    }
    
    public static function formatMoney(float $value): string {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}
