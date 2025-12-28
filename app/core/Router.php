<?php
namespace App\Core;
class Router {
  private $routes = [];
  public function get($path, $handler) { $this->routes['GET'][$path] = $handler; }
  public function post($path, $handler) { $this->routes['POST'][$path] = $handler; }
  public function dispatch() {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
      $uri = substr($uri, strlen($scriptDir));
    }
    $uri = rtrim($uri, '/') ?: '/';
    if (isset($this->routes[$method][$uri])) {
      $handler = $this->routes[$method][$uri];
      $this->invoke($handler);
      return;
    }
    foreach ($this->routes[$method] ?? [] as $route => $handler) {
      $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([a-zA-Z0-9_-]+)', $route);
      if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
        array_shift($matches);
        $this->invoke($handler, $matches);
        return;
      }
    }
    http_response_code(404);
    echo '404';
  }
  private function invoke($handler, $params = []) {
    if (is_callable($handler)) { call_user_func_array($handler, $params); return; }
    if (is_array($handler)) {
      $class = $handler[0];
      $method = $handler[1];
      $controller = new $class;
      call_user_func_array([$controller, $method], $params);
    }
  }
}

