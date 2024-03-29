<?php

namespace Yocto;

use Yocto\Exception\NotFoundException;

class Router
{

    /** @var Route[][] Liste des routes */
    private $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /** @var string Url */
    private $url;

    /**
     * Constructeur de la classe
     * @param string $url Url
     */
    public function __construct($url)
    {
        $this->url = trim($url, '/');
    }

    /**
     * Crée une route
     * @param string $method Méthode HTTP
     * @param string $path Chemin de la route
     * @param callable $callback Fonction de callback
     * @throws \Exception
     */
    public function map($method, $path, $callback)
    {
        // Méthode introuvable
        if (!isset($this->routes[$method])) {
            throw new \Exception('Method "' . $method . '" does not exist');
        }
        // Crée la route
        $route = new Route($path, $callback);
        // Ajout de la route à la propriété $this->routes
        $this->routes[$method][] = $route;
    }

    /**
     * Exécute le routeur
     * @throws NotFoundException
     */
    public function run()
    {
        // Méthode introuvable
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new NotFoundException('Method "' . $_SERVER['REQUEST_METHOD'] . '" does not exist');
        }
        // Recherche la route dans la propriété $this->routes
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }
        // Route introuvable
        throw new NotFoundException('Route not found');
    }

}