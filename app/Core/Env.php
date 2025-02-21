<?php

namespace App\Core;

class Env {
    private static $loaded = false;
    private static $variables = [];

    public static function load($path) {
        error_log("[Env] Iniciando carregamento do arquivo .env");
        error_log("[Env] Path: " . $path);
        
        if (self::$loaded) {
            error_log("[Env] Arquivo já foi carregado anteriormente");
            return;
        }

        if (!file_exists($path)) {
            error_log("[Env] Arquivo .env não encontrado em: " . $path);
            throw new \Exception('.env file not found at: ' . $path);
        }

        error_log("[Env] Arquivo .env encontrado, lendo conteúdo...");
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            error_log("[Env] Falha ao ler arquivo .env");
            throw new \Exception('Failed to read .env file');
        }

        error_log("[Env] Número de linhas lidas: " . count($lines));
        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Parse the line
            if (strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            }

            self::$variables[$name] = $value;
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            error_log("[Env] Carregada variável: {$name}");
        }

        self::$loaded = true;
        error_log("[Env] Variáveis carregadas com sucesso");
        error_log("[Env] Total de variáveis: " . count(self::$variables));
    }

    public static function get($key, $default = null) {
        error_log("[Env] Buscando variável: {$key}");
        
        // Primeiro tenta pegar do nosso array interno
        if (isset(self::$variables[$key])) {
            error_log("[Env] Encontrado em self::variables");
            return self::$variables[$key];
        }

        // Depois tenta pegar das variáveis de ambiente
        $value = getenv($key);
        if ($value !== false) {
            error_log("[Env] Encontrado em getenv()");
            return $value;
        }

        // Depois tenta pegar do $_ENV
        if (isset($_ENV[$key])) {
            error_log("[Env] Encontrado em \$_ENV");
            return $_ENV[$key];
        }

        // Por último tenta pegar do $_SERVER
        if (isset($_SERVER[$key])) {
            error_log("[Env] Encontrado em \$_SERVER");
            return $_SERVER[$key];
        }

        error_log("[Env] Variável não encontrada, retornando valor padrão");
        return $default;
    }

    public static function set($key, $value) {
        error_log("[Env] Definindo variável: {$key}");
        self::$variables[$key] = $value;
        putenv(sprintf('%s=%s', $key, $value));
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    public static function has($key) {
        return isset(self::$variables[$key]) || 
               getenv($key) !== false || 
               isset($_ENV[$key]) || 
               isset($_SERVER[$key]);
    }

    public static function all() {
        return self::$variables;
    }
}
