<?php
namespace App\model;

use PDO;
use App\Objet\User;

class UserModel
{
    private $db;

    public function __construct(PDO $db)
    {
       $this->db = $db;
    }
    public function add(User $user)
    {
        $q = $this->db->prepare('INSERT INTO User(nom, prenom , email, pwd, lieu, niveau , userAdd, userModif) VALUES(:nom, :prenom, :email, :pwd, :lieu, :niveau, :userAdd, :userModif)');
    
        $q->bindValue(':nom', $user->nom(), PDO::PARAM_STR);
        $q->bindValue(':prenom', $user->prenom(), PDO::PARAM_STR);
        $q->bindValue(':email', $user->email(), PDO::PARAM_STR);
        $q->bindValue(':pwd', $user->pwd(), PDO::PARAM_STR);
        $q->bindValue(':lieu', $user->lieu(), PDO::PARAM_STR);
        $q->bindValue(':niveau', $user->niveau(), PDO::PARAM_STR);
        $q->bindValue(':userAdd', $user->userAdd(), PDO::PARAM_STR);
        $q->bindValue(':userModif', $user->userModif(), PDO::PARAM_STR);

        $q->execute();
    }
    public function delete($id)
    {
        $this->db->exec('DELETE FROM User WHERE id= '.(int)$id);
    }
    public function listUser()
    {
        $sql = 'SELECT * FROM User ORDER BY id DESC';

        $q = $this->db->query($sql);

        $q->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Objet\User');
    
        $userList = $q->fetchAll();

        $q->closeCursor();

        return $userList;
    }
    public function uniqueUser($id)
    {
        $q = $this->db->prepare('SELECT * FROM User WHERE id =:id');

        $q->bindValue(':id', $id, PDO::PARAM_INT);
        $q->execute();   
        
        $q->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Objet\User');

        $user = $q->fetch();  

        $q->closeCursor();
                
        return $user;
    }
    protected function update(User $user)
    {
        $q = $this->db->prepare('UPDATE User SET nom = :nom, email = :email, lieu = :lieu, niveau = :niveau, userModif = :userModif WHERE id = :id');

        $q->bindValue(':nom', $user->nom(), PDO::PARAM_STR);
        $q->bindValue(':email', $user->email(), PDO::PARAM_STR);       
        $q->bindValue(':lieu', $user->lieu(), PDO::PARAM_STR );
        $q->bindValue(':niveau', $user->niveau(), PDO::PARAM_INT);
        $q->bindValue(':userModif', $user->userModif(),PDO::PARAM_STR);
        $q->bindValue(':id', $user->id(), PDO::PARAM_INT);

        $q->execute();
    }
    public function save(User $user)
    {
        if ($user->isValid())
        {   
            $user->isNew() ? $this->add($user) : $this->update($user);
        }
        else
        {
            throw new RuntimeException('L\'utilisateur doit être valide pour être enregistré');
        }
    }  
    public function connexion($identifiant)
    {
        $q = $this->db->prepare('SELECT * FROM User WHERE email = :identifiant');        
       
        $q->bindValue(':identifiant', $identifiant);                

        $q->execute();  
        
        $resultat = $q->fetch();

        $q->closeCursor();

        return $resultat;         
    }    
    public function nouveauPass($identifiant){
        
        $pass_hache = password_hash($_POST['pass2'], PASSWORD_DEFAULT);
         
        $q = $this->db->prepare('UPDATE User SET pwd = :pass WHERE email = :identifiant');       
        
        $q->bindValue(':pass', $pass_hache);
        $q->bindValue(':identifiant', $identifiant);

        $q->execute();

    }   
}    
