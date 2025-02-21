<?php

namespace App\Core;

class Router {
    private static array $routes = [];
    private static array $middlewares = [];
    private static string $basePath = '';

    public static function setBasePath($path) {
        self::$basePath = '/' . trim($path, '/');
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
        $uri = '/' . trim($uri, '/');
        $uri = str_replace('//', '/', $uri);
        
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
            error_log("Starting route dispatch...");
            
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Verifica se é um método PUT ou DELETE via POST
            if ($requestMethod === 'POST' && isset($_POST['_method'])) {
                $requestMethod = strtoupper($_POST['_method']);
            }
            
            error_log("Original Request Method: {$requestMethod}");
            error_log("Original Request URI: {$requestUri}");
            
            // Normaliza a URI
            $requestUri = '/' . trim($requestUri, '/');
            error_log("Normalized Request URI: {$requestUri}");
            
            // Adiciona log para todas as rotas registradas
            error_log("Registered routes:");
            foreach (self::$routes as $route) {
                error_log("  {$route['method']} {$route['uri']} -> {$route['handler']['controller']}::{$route['handler']['method']}");
            }
            
            foreach (self::$routes as $route) {
                if ($route['method'] !== $requestMethod) {
                    continue;
                }

                $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $route['uri']);
                $pattern = "@^" . $pattern . "$@D";
                
                error_log("Checking route pattern: {$pattern} against URI: {$requestUri}");
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    error_log("Route matched!");
                    array_shift($matches);
                    
                    // Processa os middlewares
                    foreach ($route['middlewares'] as $middleware) {
                        $middlewareClass = "App\\Middlewares\\" . ucfirst($middleware) . "Middleware";
                        error_log("Processing middleware: {$middlewareClass}");
                        
                        if (!class_exists($middlewareClass)) {
                            error_log("Middleware not found: {$middlewareClass}");
                            continue;
                        }
                        
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle()) {
                            error_log("Middleware check failed");
                            return;
                        }
                    }
                    
                    // Executa o controller
                    $controllerClass = $route['handler']['controller'];
                    $method = $route['handler']['method'];
                    
                    error_log("Executing controller: {$controllerClass}->{$method}");
                    
                    if (!class_exists($controllerClass)) {
                        throw new \Exception("Controller not found: {$controllerClass}");
                    }
                    
                    $controller = new $controllerClass();
                    if (!method_exists($controller, $method)) {
                        throw new \Exception("Method not found: {$method} in controller {$controllerClass}");
                    }
                    
                    call_user_func_array([$controller, $method], $matches);
                    return;
                }
            }
            
            error_log("No route found for: {$requestUri}");
            http_response_code(404);
            View::render('errors/404');
            
        } catch (\Exception $e) {
            error_log("Router error: " . $e->getMessage());
            http_response_code(500);
            if (APP_DEBUG) {
                echo "Error: " . $e->getMessage();
            } else {
                View::render('errors/500');
            }
        }
    }
}
