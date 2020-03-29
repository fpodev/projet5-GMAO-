<?php
namespace App\Routeur;

require 'vendor/autoload.php';


use Twig\Environment;
use App\Routeur\Route;
use Twig\Loader\FilesystemLoader;
use App\Controller\UserController;

class Routeur{

     private $ctrlUser; 
     private $routes=[];
     private $namedRoutes = [];
     private $url;      

    public function __construct($url)
    {
        $this->ctrlUser = new UserController();
        $this->loader = new \Twig\Loader\FilesystemLoader(['Librairies/View', 'Librairies/Templates']);
        $this->twig = new \Twig\Environment($this->loader, ['debug' => true,]); 
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());  

        $this->url = $url;
      }
      
      public function get($path, $callable, $name = null){
        return $this->add($path, $callable, $name, 'GET');
    }

    public function post($path, $callable, $name = null){
        return $this->add($path, $callable, $name, 'POST');
    }

    private function add($path, $callable, $name, $method){
        $route = new Route($path, $callable);
        $this->routes[$method][] = $route;
        if(is_string($callable) && $name === null){
            $name = $callable;
        }
        if($name){
            $this->namedRoutes[$name] = $route;
        }
        
        return $route;
    }

    public function run(){
        if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new RouterException('REQUEST_METHOD does not exist');
        }
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }
        throw new RouterException('No matching routes');
    }

    public function url($name, $params = []){
        if(!isset($this->namedRoutes[$name])){
            throw new RouterException('No route matches this name');
        }        
        return $this->namedRoutes[$name]->getUrl($params);
    }

}

      
       /*if(isset($_GET['connexion']))
            { 
              //si une session existe ce connect directement
              
              {
                $this->ctrlUser->listUser();                             
              }
              //si session non existante, detruit la session dans le cache et renvoie sur la page pour se connecter
              else{ 
                session_destroy();                 
               include ('Librairies/View/userList.php');               
              }
            } 
        
        elseif(isset($_POST['connexion']))       
        {            
            $this->ctrlUser->connexion();              
        }    
        elseif(isset($_POST['valider']))
        {           
            $this->ctrlUser->addUser();
        }
        elseif(isset($_GET['modifier']))
        {
            $this->ctrlUser->changeUser();
        }
        elseif(isset($_GET['deleteUser']))
        {
            $this->ctrlUser->deleteUser();
        }
        else
        {
          $this->ctrlUser->home();      
        }*/
      
    