 <?php

class UsuarioController {
    
    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }    
    private function __construct() {
    }

    public function usuarioLogeado() {
        $session = Session::getInstance();
        return ($session->usuario);
    }

 	/*
    ** LOGIN:
    */
    public function login($args = []) {
        if (!$this->usuarioLogeado()){
            $view = new Login();
            $view->show($args);
        }else{
            ResourceController::getInstance()->home();
        }
        
    }

    /*
    **	LOGIN ACTION
    */
    public function loginAction() {
        if (!$this->usuarioLogeado()){
            if ((isset($_POST['email']) AND isset($_POST['password'])) AND !empty($_POST['email']) AND !empty($_POST['password'])) {
                $email = $_POST['email'];
                $pass = $_POST['password'];
                if (Usuario::getInstance()->logear($email, $pass)){
                    ResourceController::getInstance()->home();
                }else{
                    $this->login(Message::getMessage(1));
                }
            } else {
                $this->login(Message::getMessage(5));
            }
        }else{
            ResourceController::getInstance()->home();
        }
    }

    /*
    **	REGISTRO
    */
    public function registro(){
        if (!$this->usuarioLogeado()){
            $view = new Registro();
            $view->show();
        }else{
            ResourceController::getInstance()->home($args);
        }
    }

    /*
    **	REGISTRO ACTION
    */
    public function registroAction(){  		
        if (!$this->usuarioLogeado()){
            if ( (isset($_POST['email']) AND isset($_POST['nombre']) AND isset($_POST['apellido']) AND isset($_POST['telefono']) AND isset($_POST['password'])) AND ( !empty($_POST['email']) AND !empty($_POST['nombre']) AND !empty($_POST['apellido']) AND !empty(['telefono']) AND !empty(['password'])))
            {

    			$usuario = new Usuario($_POST['nombre'], $_POST['apellido'], $_POST['email'], $_POST['password'], $_POST['telefono']);
    			$msg = $usuario->registrarUsuario($usuario);
                ResourceController::getInstance()->home($msg);
    		} else {
    			$this->registro(Message::getMessage(5));
    		}
        }else{
            ResourceController::getInstance()->home($args);
        }
    }

    /*
    ** MI CUENTA
    */
    public function miCuenta($args = []){
        if ($this->usuarioLogeado()){
            $args = array_merge($args, ['user' => $this->usuarioLogeado()]);
            $view = new MiCuenta();
            $view->show($args);
        }else{
            ResourceController::getInstance()->home();
        }

    }

    /*
    ** EDITAR CUENTA
    */
    public function editarCuenta(){
        if ($this->usuarioLogeado()){
            $user = $this->usuarioLogeado();
            if ( isset($_POST['nombre']) AND isset($_POST['apellido']) AND isset($_POST['password']) AND isset($_POST['email']) AND isset($_POST['telefono']) AND !empty($_POST['nombre']) AND !empty($_POST['apellido']) AND !empty(['password']) AND !empty($_POST['email']) AND !empty($_POST['telefono'])) {
                $userMod = new Usuario([], $_POST['nombre'], $_POST['apellido'], $_POST['email'], $_POST['password'], $_POST['telefono'], $_POST['creditos']);
                if($user->getPassword() == $userMod->getPassword()){
                    if ($user->getEmail() == $userMod->getEmail()){
                        if ($user->getCreditos() == $userMod->getCreditos()){
                            Usuario::getInstance()->modificarUsuario($userMod->getNombre(), $userMod->getApellido(), $userMod->getTelefono(), $userMod->getEmail());
                            /*Session::getInstance()->destroy();
                            ResourceController::getInstance()->home(Message::getMessage(6));*/
                            $session = Session::getInstance();
                            $session->usuario = $userMod;
                            $this->miCuenta(Message::getMessage(6));
                        }else{
                            $this->miCuenta(Message::getMessage(9));
                        }  
                    }else{
                        $this->miCuenta(Message::getMessage(7));
                    }
                }else{
                    $this->miCuenta(Message::getMessage(8));
                }
            }else{
                $this->miCuenta(Message::getMessage(5));
            }
        }else{
            ResourceController::getInstance()->home();
        }
    }

    /*
    ** DESHABILITAR CUENTA
    */
    public function deshabilitarCuenta($args = []){
        if($this->usuarioLogeado()){
            $args = array_merge($args, ['user' => $this->usuarioLogeado()]);
            $view = new deshabilitarCuenta();
            $view->show($args);
        }else{
            ResourceController::getInstance()->home();
        }
    }

    /*
    ** DESHABILITAR CUENTA ACTION
    */
    public function deshabilitarCuentaAction($args = []){
        if($this->usuarioLogeado()){
            Usuario::getInstance()->deshabilitarUsuario($this->usuarioLogeado()->getEmail());
            Session::getInstance()->destroy();
            ResourceController::getInstance()->home();
        }else{
            ResourceController::getInstance()->home();
        }
    }

    /*
    ** CREDITOS
    */
    public function creditos($args = []){
        if($this->usuarioLogeado()){
            $args = array_merge($args, ['user' => $this->usuarioLogeado()]);
            $view = new Creditos();
            $view->show($args);
        }else{
            ResourceController::getInstance()->home();
        }
    }

    /*
    ** CERRAR SESION
    */
    public function cerrarSesion(){
        Session::getInstance()->destroy();
        ResourceController::getInstance()->home();
    }
}