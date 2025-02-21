<?php

namespace App;

class Router {
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, array $callback): void {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, array $callback): void {
        $this->routes['POST'][$path] = $callback;
    }

    public function addMiddleware(string $path, callable $middleware): void {
        $this->middlewares[$path][] = $middleware;
    }

    private function getCallback(): array {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];
        
        error_log("Original Request URI: " . $path);
        error_log("Request Method: " . $method);
        
        // Remove query string
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        // Parse URL and remove base path
        $path = parse_url($path, PHP_URL_PATH);
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        
        error_log("Base Path: " . $basePath);
        error_log("Path after parse_url: " . $path);
        
        if ($basePath && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        error_log("Path after removing base: " . $path);
        
        // Remove trailing slash
        $path = rtrim($path, '/');
        if (empty($path)) {
            $path = '/';
        }
        
        error_log("Final path: " . $path);
        
        // Check if route exists
        if (!isset($this->routes[$method])) {
            error_log("Method not found: " . $method);
            return [null, null];
        }
        
        foreach ($this->routes[$method] as $routePath => $callback) {
            $pattern = $this->getPattern($routePath);
            error_log("Trying pattern: " . $pattern);
            if (preg_match($pattern, $path, $matches)) {
                error_log("Route matched: " . $routePath);
                array_shift($matches); // Remove full match
                return [$callback, $matches];
            }
        }
        
        error_log("No route found for path: " . $path);
        return [null, null];
    }
    
    private function getPattern(string $path): string {
        // Escape special regex characters first
        $path = preg_quote($path, '/');
        
        // Replace route parameters with regex pattern
        $path = str_replace('\{', '{', $path);
        $path = str_replace('\}', '}', $path);
        $pattern = preg_replace('/\{([^\/]+)\}/', '([^\/]+)', $path);
        
        return '/^' . $pattern . '$/';
    }
    
    public function resolve(): void {
        error_log("Resolving route...");
        
        [$callback, $params] = $this->getCallback();
        
        if ($callback === null) {
            error_log("Route not found");
            http_response_code(404);
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }
        
        try {
            // Get controller and method
            [$controller, $method] = $callback;
            
            error_log("Controller: " . $controller);
            error_log("Method: " . $method);
            
            // Create controller instance
            $controllerInstance = new $controller();
            
            // Check for middleware
            $this->executeMiddleware($controllerInstance);
            
            // Call controller method with parameters
            if (empty($params)) {
                $controllerInstance->$method();
            } else {
                call_user_func_array([$controllerInstance, $method], $params);
            }
            
        } catch (\Exception $e) {
            error_log("Error resolving route: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            http_response_code(500);
            require_once __DIR__ . '/../views/errors/500.php';
        }
    }
    
    private function executeMiddleware($controller): void {
        $path = $_SERVER['REQUEST_URI'];
        
        foreach ($this->middlewares as $routePath => $middlewareList) {
            if (strpos($path, $routePath) === 0) {
                foreach ($middlewareList as $middleware) {
                    $middleware($controller);
                }
            }
        }
    }

    public function registerRoutes(): void {
        // Rotas públicas
        $this->get('/public/visitor', ['App\\Controllers\\PublicController', 'visitorForm']);
        $this->post('/public/visitor/store', ['App\\Controllers\\PublicController', 'storeVisitor']);
        $this->get('/public/visitor/success', ['App\\Controllers\\PublicController', 'success']);

        // Rotas de autenticação
        $this->get('/', ['App\\Controllers\\AuthController', 'login']);
        $this->get('/login', ['App\\Controllers\\AuthController', 'login']);
        $this->post('/login', ['App\\Controllers\\AuthController', 'authenticate']);
        $this->get('/logout', ['App\\Controllers\\AuthController', 'logout']);
        $this->get('/forgot-password', ['App\\Controllers\\AuthController', 'forgotPassword']);
        $this->post('/forgot-password', ['App\\Controllers\\AuthController', 'sendResetLink']);
        $this->get('/reset-password', ['App\\Controllers\\AuthController', 'resetPassword']);
        $this->post('/reset-password', ['App\\Controllers\\AuthController', 'updatePassword']);

        // Rotas do dashboard
        $this->get('/dashboard', ['App\\Controllers\\DashboardController', 'index']);

        // Rotas de visitantes
        $this->get('/visitors', ['App\\Controllers\\VisitorController', 'index']);
        $this->get('/visitors/create', ['App\\Controllers\\VisitorController', 'create']);
        $this->post('/visitors/store', ['App\\Controllers\\VisitorController', 'store']);
        $this->get('/visitors/{id}', ['App\\Controllers\\VisitorController', 'show']);
        $this->get('/visitors/{id}/edit', ['App\\Controllers\\VisitorController', 'edit']);
        $this->post('/visitors/{id}/update', ['App\\Controllers\\VisitorController', 'update']);
        $this->get('/visitors/{id}/delete', ['App\\Controllers\\VisitorController', 'delete']);

        // Rotas de grupos
        $this->get('/groups', ['App\\Controllers\\GroupController', 'index']);
        $this->get('/groups/create', ['App\\Controllers\\GroupController', 'create']);
        $this->post('/groups/store', ['App\\Controllers\\GroupController', 'store']);
        $this->get('/groups/{id}', ['App\\Controllers\\GroupController', 'show']);
        $this->get('/groups/{id}/edit', ['App\\Controllers\\GroupController', 'edit']);
        $this->post('/groups/{id}/update', ['App\\Controllers\\GroupController', 'update']);
        $this->get('/groups/{id}/delete', ['App\\Controllers\\GroupController', 'delete']);
    }
}
