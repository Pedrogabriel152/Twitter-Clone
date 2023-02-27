<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    // Salvar
    public function salvar() {
        $query = "INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)";

        $nome = $this->__get('nome');
        $email = $this->__get('email');
        $senha = $this->__get('senha');

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha);  //md5() -> hash 32 caracteres

        $stmt->execute();

        return $this;

    }


    // Fazer a validacao

    // Recuperar um usuario por e-mail

    public function __get($atr) {
        return $this->$atr;
    }

    public function __set($atr, $value) {
        $this->$atr = $value;
    }


}

?>