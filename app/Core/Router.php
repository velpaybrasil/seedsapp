<?php

namespace App\Core;

class Router {
    private static array $routes = [];
    private static array $middlewares = [];
    private static string $basePath = '';

    public static function setBasePath($path) {
        self::$basePath = '/' . trim($path, '/');
        if (self::$basePath === '/') {
            self::$basePath = '';
        }
        error_log("Router Base Path set to: " . self::$basePath);
    }

    public static function getBasePath() {
        error_log("Router Base Path is: " . self::$basePath);
        return self::$basePath;
    }

    public static function get(string $uri, $handler): void {
        self::addRoute('GET', $uri, $handler);
    }

    public static function post(string $uri, $handler): void {
        self::addRoute('POST', $uri, $handler);
    }

    public static function put(string $uri, $handler): void {
        self::addRoute('PUT', $uri, $handler);
    }

    public static function delete(string $uri, $handler): void {
        self::addRoute('DELETE', $uri, $handler);
    }

    public static function group(array $attributes, callable $callback): void {
        $previousMiddlewares = self::$middlewares;
        
        if (isset($attributes['middleware'])) {
            if (is_string($attributes['middleware'])) {
                self::$middlewares[] = $attributes['middleware'];
            } else {
                self::$middlewares = array_merge(self::$middlewares, $attributes['middleware']);
            }
        }
        
        call_user_func($callback);
        
        self::$middlewares = $previousMiddlewares;
    }

    private static function addRoute(string $method, string $uri, $handler): void {
        // Normaliza a URI removendo a barra no final
        $uri = '/' . trim($uri, '/');
        
        // Adiciona o basePath à URI se ele existir
        if (self::$basePath) {
            $uri = self::$basePath . $uri;
        }
        
        if (is_string($handler)) {
            $parts = explode('@', $handler);
            $handler = [
                'controller' => "App\\Controllers\\" . $parts[0],
                'method' => $parts[1]
            ];
        } elseif (is_array($handler) && count($handler) === 2) {
            $handler = [
                'controller' => $handler[0],
                'method' => $handler[1]
            ];
        }

        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'middlewares' => self::$middlewares
        ];
        
        error_log("Added Route - Method: {$method}, URI: {$uri}, Controller: {$handler['controller']}, Method: {$handler['method']}");
    }

    public static function dispatch(): void {
        try {
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Normaliza a URI removendo a barra no final se existir
            $requestUri = rtrim($requestUri, '/');
            if (empty($requestUri)) {
                $requestUri = '/';
            }
            
            error_log("Processing request - Method: {$requestMethod}, URI: {$requestUri}");
            
            // Verifica se é um método PUT ou DELETE via POST
            if ($requestMethod === 'POST' && isset($_POST['_method'])) {
                $requestMethod = strtoupper($_POST['_method']);
            }
            
            foreach (self::$routes as $route) {
                error_log("Checking route: {$route['method']} {$route['uri']}");
                
                if ($route['method'] !== $requestMethod) {
                    error_log("Method mismatch: {$route['method']} != {$requestMethod}");
                    continue;
                }

                $routeUri = $route['uri'];
                $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $routeUri);
                $pattern = "@^" . $pattern . "$@D";
                
                error_log("Route pattern: {$pattern}");
                error_log("Request URI: {$requestUri}");
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    error_log("Route matched: {$routeUri}");
                    array_shift($matches);
                    
                    // Processa os middlewares
                    foreach ($route['middlewares'] as $middleware) {
                        $middlewareClass = "App\\Middleware\\" . ucfirst($middleware) . "Middleware";
                        error_log("Processing middleware: {$middlewareClass}");
                        
                        if (!class_exists($middlewareClass)) {
                            error_log("Middleware not found: {$middlewareClass}");
                            throw new \Exception("Middleware not found: {$middlewareClass}");
                        }
                        
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle()) {
                            error_log("Middleware {$middlewareClass} blocked request");
                            return;
                        }
                    }
                    
                    // Executa o controller
                    $controllerClass = $route['handler']['controller'];
                    $method = $route['handler']['method'];
                    
                    error_log("Executing controller: {$controllerClass}::{$method}");
                    
                    if (!class_exists($controllerClass)) {
                        error_log("Controller not found: {$controllerClass}");
                        throw new \Exception("Controller not found: {$controllerClass}");
                    }
                    
                    $controller = new $controllerClass();
                    if (!method_exists($controller, $method)) {
                        error_log("Method not found: {$method} in controller {$controllerClass}");
                        throw new \Exception("Method not found: {$method} in controller {$controllerClass}");
                    }
                    
                    call_user_func_array([$controller, $method], $matches);
                    error_log("Request dispatched successfully");
                    return;
                } else {
                    error_log("Route did not match");
                }
            }

            // Nenhuma rota encontrada
            error_log("No route found for {$requestMethod} {$requestUri}");
            http_response_code(404);
            View::render('errors/404');
        } catch (\Exception $e) {
            error_log("Router error: " . $e->getMessage());
            error_log($e->getTraceAsString());
            http_response_code(500);
            View::render('errors/500', ['error' => $e->getMessage()]);
        }
    }
    
    public static function add($method, $uri, $handler) {
        self::addRoute($method, $uri, $handler);
    }
    
    public static function run() {
        // Rotas para Grupos
        self::add('GET', '/groups', ['App\Controllers\GroupController', 'index']);
        self::add('GET', '/groups/create', ['App\Controllers\GroupController', 'create']);
        self::add('POST', '/groups', ['App\Controllers\GroupController', 'store']);
        self::add('GET', '/groups/{id}', ['App\Controllers\GroupController', 'viewGroup']);
        self::add('GET', '/groups/{id}/edit', ['App\Controllers\GroupController', 'edit']);
        self::add('POST', '/groups/{id}', ['App\Controllers\GroupController', 'update']);
        self::add('POST', '/groups/{id}/delete', ['App\Controllers\GroupController', 'delete']);
        
        // Rotas para Participantes de Grupos
        self::add('POST', '/groups/{id}/participants', ['App\Controllers\GroupController', 'addParticipant']);
        self::add('POST', '/groups/{groupId}/participants/{participantId}/remove', ['App\Controllers\GroupController', 'removeParticipant']);
        
        // Rotas para Pré-inscrição em Grupos
        self::add('GET', '/groups/{id}/pre-register', ['App\Controllers\GroupMemberController', 'preRegister']);
        self::add('POST', '/groups/{id}/pre-register', ['App\Controllers\GroupMemberController', 'submitPreRegistration']);
        self::add('GET', '/groups/{id}/pending-members', ['App\Controllers\GroupMemberController', 'pendingMembers']);
        self::add('POST', '/groups/members/{id}/status', ['App\Controllers\GroupMemberController', 'updateMemberStatus']);
        self::add('GET', '/groups/members/{id}/history', ['App\Controllers\GroupMemberController', 'memberHistory']);
        
        // Rotas para Reuniões e Presenças
        self::add('POST', '/groups/{id}/meetings', ['App\Controllers\GroupController', 'addMeeting']);
        self::add('GET', '/groups/meetings/{id}/attendance', ['App\Controllers\GroupController', 'getMeetingAttendance']);
        self::add('POST', '/groups/meetings/{id}/attendance', ['App\Controllers\GroupController', 'updateMeetingAttendance']);
        
        self::dispatch();
    }
}
