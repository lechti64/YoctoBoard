<?php

namespace Yocto;

class Controller
{

    /** @var Database Configuration */
    public $_configuration;

    /** @var Form Formulaire */
    private $form;

    /** @var string Layout */
    private $layout;

    /** @var array Méthodes HTTP */
    private $methods = [];

    /** @var array Notices */
    private $notices = [];

    /** @var Session Session */
    private $session;

    /** @var array Librairies */
    private $vendors = [];

    /** @var string Vue */
    private $view;

    /**
     * Constructeur de la classe
     * @param Database $_configuration
     * @throws \Exception
     */
    public function __construct(Database $_configuration)
    {
        // Ajoute les méthodes HTTP
        $this->methods = [
            'POST' => $_POST,
            'GET' => $_GET,
            'COOKIE' => $_COOKIE,
        ];
        // Transmet les données en provenance de ./index.php
        $this->_configuration = $_configuration;
        // Crée l'instance du formulaire
        $this->form = new Form($this);
        // Crée l'instance de la session
        $this->session = new Session($this);
    }

    /**
     * Recherche une clé dans les méthodes HTTP
     * @param string $key Clé à rechercher
     * @param bool $required Clé obligatoire, sinon génère une notice
     * @return mixed
     */
    public function get($key, $required = false)
    {
        // Une méthode spécifique est demandée
        if (strpos($key, ':') !== false) {
            list($method, $key) = explode(':', $key);
            if (!empty($this->methods[$method][$key])) {
                return $this->methods[$method][$key];
            }
        } // Recherche dans les méthodes
        else {
            foreach ($this->methods as $method) {
                if (!empty($method[$key])) {
                    return $method[$key];
                }
            }
        }
        // Génère une notice
        if ($required) {
            $this->notices[$key] = 'Champ requis';
            // Retourne null car cela bloque l'enregistrement des données
            return null;
        }
        return '';
    }

    /**
     * Accès au formulaire
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Accès à une ou aux notices
     * @param null $key Clé à rechercher
     * @return array|string
     */
    public function getNotices($key = null)
    {
        if ($key) {
            return isset($this->notices[$key]) ? $this->notices[$key] : '';
        } else {
            return $this->notices;
        }
    }

    /**
     * Accès à la session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Charge le layout
     */
    public function loadLayout()
    {
        require ROOT . '/layout/' . $this->layout . '/' . $this->layout . '.php';
    }

    /**
     * Charge la vue
     */
    public function loadView()
    {
        $directoryName = $this->getDirectoryName();
        require ROOT . '/application/' . $directoryName . '/view/' . $this->view . '/' . $this->view . '.php';
    }

    /**
     * Charge le CSS de la vue
     */
    public function loadViewCss()
    {
        $directoryName = $this->getDirectoryName();
        $cssPath = 'application/' . $directoryName . '/view/' . $this->view . '/' . $this->view . '.css';
        if (is_file(ROOT . '/' . $cssPath)) {
            echo '<link rel="stylesheet" href="' . $cssPath . '">';
        }
    }

    /**
     * Charge le JS de la vue
     */
    public function loadViewJs()
    {
        $directoryName = $this->getDirectoryName();
        $jsPath = 'application/' . $directoryName . '/view/' . $this->view . '/' . $this->view . '.js';
        if (is_file(ROOT . '/' . $jsPath)) {
            echo '<script src="' . $jsPath . '"></script>';
        }
    }

    /**
     * Redirection
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Configure un layout
     * @param string $layout Layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Configure une librairie
     * @param string $url Url de la librairie
     * @param string $sri SRI de la librairie (facultatif)
     */
    public function setVendor($url, $sri = '')
    {
        $this->vendors[$url] = $sri;
    }

    /**
     * Configure la vue
     * @param string $view Vue
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Accès au nom du fichier contenant le contrôleur
     * @return string
     */
    private function getDirectoryName()
    {
        $class = str_replace('Yocto\\', '', get_class($this));
        $directoryName = preg_replace_callback('/(?!^)([A-Z])/', function ($letter) {
            return strtolower('-' . $letter[1]);
        }, $class);
        return str_replace('Controller-', '', $directoryName);
    }

}