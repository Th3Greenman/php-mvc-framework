<?php

namespace app\core;


class Application
{
    /** @var string  */
    public static string $ROOT_DIR;
    /** @var Router  */
    public Router $router;
    /** @var Request  */
    public Request $request;
    /** @var Response  */
    public Response $response;
    /** @var Database  */
    public Database $db;
    /** @var Application  */
    public static Application $app;
    public Controller $controller;

    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);

        $this->db = new Database($config['db']);
    }

    public function run()
    {
        echo $this->router->resolve();
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }
}
