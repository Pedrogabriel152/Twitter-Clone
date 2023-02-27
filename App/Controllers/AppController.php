<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action 
{
    public function timeline() {
        session_start();

        if(empty($_SESSION['id']) || empty($_SESSION['nome'])){
            session_destroy();
            header('Location: /?login=erro');
            exit();
        }

        $this->render('timeline');
    }

}

?>