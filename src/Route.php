<?php

namespace Yocto;

class Route
{

    /** @var callable Fonction de callback */
    private $callback;

    /** @var array Liste des correspondances */
    private $matches = [];

    /** @var string Chemin de la route */
    private $path;

    /**
     * Constructeur de la classe
     * @param string $path Chemin de la route
     * @param callable $callback Fonction de callback
     */
    public function __construct($path, $callback)
    {
        $this->path = trim($path, '/');
        $this->callback = $callback;
    }

    /**
     * Appel du callback
     * @return mixed
     */
    public function call()
    {
        return call_user_func_array($this->callback, $this->matches);
    }

    /**
     * Recherche des correspondances
     * @param string $url Url
     * @return bool
     * @throws \Exception
     */
    public function match($url)
    {
        // Recherche les correspondances
        $url = trim($url, '/');
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
        $regex = "#^$path$#i";
        if(preg_match($regex, $url, $matches)){
            array_shift($matches);
            // Enregistre les correspondance
            $this->matches = $matches;
            return true;
        }
        return false;
    }

}