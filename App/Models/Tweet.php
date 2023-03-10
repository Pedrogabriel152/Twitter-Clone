<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model
{
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __get($atr) {
        return $this->$atr;
    }

    public function __set($atr, $value) {
        $this->$atr = $value;
    }

    // salvar
    public function salvar() {
        
        $query = "INSERT INTO tweets(id_usuario, tweet) VALUES(:id_usuario, :tweet)";

        $stmt= $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;

    }

    // recuperar

    public function getAll() {
        $query = "SELECT 
                    t.id, 
                    t.id_usuario, 
                    t.tweet, 
                    DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data, 
                    u.nome 
                FROM tweets as t 
                INNER JOIN usuarios as u 
                ON(t.id_usuario = u.id) 
                WHERE t.id_usuario = :id_usuario 
                        OR t.id_usuario in(
                            SELECT id_usuario_seguindo FROM usuarios_seguidores
                            WHERE id_usuario = :id_usuario
                        ) 
                ORDER BY t.data desc
        ";

        $stmt= $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function delete() {
        $query = "DELETE FROM tweets WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();
    }

    public function getPorPagina($limit, $offset) {
        $query = "SELECT 
                    t.id, 
                    t.id_usuario, 
                    t.tweet, 
                    DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data, 
                    u.nome 
                FROM tweets as t 
                INNER JOIN usuarios as u 
                ON(t.id_usuario = u.id) 
                WHERE t.id_usuario = :id_usuario 
                        OR t.id_usuario in(
                            SELECT id_usuario_seguindo FROM usuarios_seguidores
                            WHERE id_usuario = :id_usuario
                        ) 
                ORDER BY t.data desc
                LIMIT $limit
                OFFSET $offset
        ";

        $stmt= $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Recuperar total de tweets
    public function getTotalRegistros() {
        $query = "SELECT 
                    COUNT(*) as total
                FROM tweets as t 
                INNER JOIN usuarios as u 
                ON(t.id_usuario = u.id) 
                WHERE t.id_usuario = :id_usuario 
                    OR t.id_usuario in(
                        SELECT id_usuario_seguindo FROM usuarios_seguidores
                        WHERE id_usuario = :id_usuario
                    )
        ";

        $stmt= $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
}

?>