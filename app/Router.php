<?php
declare(strict_types=1);

final class Router
{
    private array $routes = [];
    public function get(string $path, callable|array $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, callable|array $handler): void { $this->add('POST', $path, $handler); }
    public function put(string $path, callable|array $handler): void { $this->add('PUT', $path, $handler); }
    public function patch(string $path, callable|array $handler): void { $this->add('PATCH', $path, $handler); }
    public function delete(string $path, callable|array $handler): void { $this->add('DELETE', $path, $handler); }
    public function any(string $path, callable|array $handler): void { $this->add('*', $path, $handler); }
    private function add(string $method, string $path, callable|array $handler): void { $this->routes[] = compact('method','path','handler'); }
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        foreach ($this->routes as $route) {

            if ($route['method'] !== '*' && $route['method'] !== $method) continue;
            $pattern = preg_replace_callback('#\{([A-Za-z_][A-Za-z0-9_]*)\}#', static fn(array $match): string => '(?P<'.$match[1].'>'.($match[1] === 'path' ? '.+' : '[^/]+').')', $route['path']);
            if (!preg_match('#^'.$pattern.'$#', $path, $matches)) continue;
            call_user_func($route['handler'], array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
            return;
        }
        http_response_code(404); require __DIR__.'/View.php'; render('Not found','<p class="bg-primary-subtle opacity-75 rounded p-4 text-primary-emphasis">The requested page does not exist.</p>',false);
    }
}
