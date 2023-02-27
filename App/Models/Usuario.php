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
    public function validarCadastro() {
        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    // Recuperar um usuario por e-mail
    public function findByEmail() {
        $query = "SELECT nome, email FROM usuarios WHERE email = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Auntenticação do Usuário 
    public function autenticar() {
        
        $query = "SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_OBJ);

        if($usuario) {
            $this->__set('id', $usuario->id);
            $this->__set('nome', $usuario->nome);
        }

        return $this;
    }

    public function __get($atr) {
        return $this->$atr;
    }

    public function __set($atr, $value) {
        $this->$atr = $value;
    }

}

?>