<?php
/**
 * Created by IntelliJ IDEA.
 * User: flo
 * Date: 02/12/14
 * Time: 11:24
 */

class Database{
    private $server = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbname = 'mycroblog';
    private $db;

    public function __construct(){
        try{
            $this->db = new PDO('mysql:host='.$this->server.';dbname='.$this->dbname.'', ''.$this->user.'', ''.$this->password.'');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(Exception $e){
            die('Erreur:'.$e->getMessage());
        }

    }

}