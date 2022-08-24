<?php

namespace Auth\Api\App;

class Route {

    private static array $listRoutes = [];

    public static function get(string $path, string $controller, string $function, array $middlewares = []) : void {

        self::$listRoutes[] = [
            "method" => "GET",
            "path" => $path,
            "controller" => $controller,
            "function" => $function,
            "middlewares" => $middlewares
        ];

    }

    public static function post(string $path, string $controller, string $function, array $middlewares = []) : void {

        self::$listRoutes[] = [
            "method" => "POST",
            "path" => $path,
            "controller" => $controller,
            "function" => $function,
            "middlewares" => $middlewares
        ];

    }

    public static function patch(string $path, string $controller, string $function, array $middlewares = []) : void {

        self::$listRoutes[] = [
            "method" => "PATCH",
            "path" => $path,
            "controller" => $controller,
            "function" => $function,
            "middlewares" => $middlewares
        ];

    }

    public static function put(string $path, string $controller, string $function, array $middlewares = []) : void {

        self::$listRoutes[] = [
            "method" => "PUT",
            "path" => $path,
            "controller" => $controller,
            "function" => $function,
            "middlewares" => $middlewares
        ];

    }

    public static function delete(string $path, string $controller, string $function, array $middlewares = []) : void {

        self::$listRoutes[] = [
            "method" => "DELETE",
            "path" => $path,
            "controller" => $controller,
            "function" => $function,
            "middlewares" => $middlewares
        ];

    }

    public static function run() : void {

        // Cek apakah ada path info
        if (isset($_SERVER["PATH_INFO"])) {
            $path = $_SERVER["PATH_INFO"];
        } else {
            $path = "/";
        }


        // Ambil data request method dari request user
        $reqMethod = $_SERVER["REQUEST_METHOD"];

        // Looping semua data routes
        foreach(self::$listRoutes as $route) {

            // Buat pattern untuk regex
            $pattern = "#^".$route["path"]."$#";

            // Cek apakah request path dari user tersedia di list route dan methodnya harus sesuai
            if (preg_match($pattern, $path, $variables) && $route["method"] === $reqMethod) {
                
                // Looping semua middlewares di list route
                foreach($route["middlewares"] as $middleware) {
                    // Buat objek middleware
                    $middleware = new $middleware;

                    // Jalankan function boot
                    $middleware->boot();
                }

                // Buat objek controller dan panggil class controller beserta function
                $controller = new $route["controller"];
                array_shift($variables);
                call_user_func_array([$controller, $route["function"]], $variables);
                return;
            }

        }

        // Jika request path atau request method dari user tidak valid maka kembalikan 404 page not found
        echo 404;
        // var_dump($_SERVER);

    }

}