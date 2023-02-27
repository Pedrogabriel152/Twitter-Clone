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

    public function getAllByName() {
        $query = "SELECT u.id,
                         u.nome,
                         u.email,
                         (
                            SELECT 
                                count(*)
                            FROM 
                                usuarios_seguidores as us
                            WHERE
                                us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id
                         ) as seguindo_sn
                    FROM usuarios as u
                    WHERE nome like :nome AND id != :id_usuario
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function seguirUsuario($id) {
        $query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguindo) VALUES(:id, :id_seguindo)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->bindValue(':id_seguindo', $id);
        $stmt->execute();

    }

    public function deixarDeSeguirUsuario($id) {
        $query = "DELETE FROM usuarios_seguidores WHERE id_usuario = :id AND id_usuario_seguindo = :id_seguindo";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->bindValue(':id_seguindo', $id);
        $stmt->execute();
    }

    // Informoces do Usuario
    public function getInfoUsuarios() {
        $query = "SELECT nome from usuarios WHERE id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    // Total de Tweets
    public function getTotalTweets() {
        $query = "SELECT COUNT(*) as total_tweets from tweets WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    // Total de usuario que estamos seguindo
    public function getTotalSeguindo() {
        $query = "SELECT COUNT(*) as total_seguindo from usuarios_seguidores WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    // Total deseguidore
    public function getTotalSeguidores() {
        $query = "SELECT COUNT(*) as total_seguindores from usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function __get($atr) {
        return $this->$atr;
    }

    public function __set($atr, $value) {
        $this->$atr = $value;
    }

}

?>