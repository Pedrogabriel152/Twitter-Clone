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

}

?>