<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action 
{
    public function timeline() {
        session_start();

        $this->validaAutenticacao();

        // recuperando os tweets
        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        $this->view->tweets = $tweet->getAll();

        $this->render('timeline');
    }

    public function tweet() {

        session_start();

        $this->validaAutenticacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header('Location: /timeline');

    }

    public function validaAutenticacao() {

        if(empty($_SESSION['id']) || empty($_SESSION['nome'])){
            session_destroy();
            header('Location: /?login=erro');
            exit();
        }

        return true;
    }

    public function quemSeguir() {
        session_start();
        $this->validaAutenticacao();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        $usuario->__set('nome', $pesquisarPor);

        $this->view->usuarios = $usuario->getAllByName();

        $this->render('quemSeguir');
    }

    public function acao() {
        session_start();
        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao === 'seguir') {
            $usuario->seguirUsuario($id_usuario_seguindo);
        }

        if($acao === 'deixar_de_seguir') {
            $usuario->deixarDeSeguirUsuario($id_usuario_seguindo);
        }

        header('Location: /quem_seguir');
    }

}

?>