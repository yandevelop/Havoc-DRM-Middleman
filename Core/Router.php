<?php
    namespace Core;

    class Router {
        protected $routes = [];
        protected $methods = [
            'GET',
            'POST',
        ];

        public function add ($method, $uri, $controller) {
            $this->routes[] = [
                'uri' => $uri,
                'controller' => $controller,
                'method' => $method,
            ];
            
            return $this;
        }

        public function get($uri, $controller) {
            return $this->add('GET', $uri, $controller);
        }

        public function post($uri, $controller) {
            return $this->add('POST', $uri, $controller);
        }

        public function route($uri, $method) {
            if (!in_array(strtoupper($method), $this->methods)) {
                $this->abort(405); // Method Not Allowed
            }

            foreach ($this->routes as $route) {
                if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                    if (substr($uri, -5) === '.json') {
                        header("HTTP/1.1 403 Unauthorized");
                        echo "Unauthorized";
                        die();
                    }

                    if (is_callable($route['controller'])) {
                        // If the controller is a callable function, execute it
                        return call_user_func($route['controller']);
                    } else {
                        // If it's a controller file, require it
                        return require base_path($route['controller']);
                    }
                }
            } 

            $this->abort(404);
        }

        protected function abort($code = 405) {
            http_response_code($code);
            
            //View::display404();

            die();

        }
    }