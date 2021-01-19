<?php
namespace classes\db;
use PDO;
/**
 * the idea of this class is provide a interface for a mysql database.
 * @author António Lira Fernandes
 * @version 1.2
 * @updated 2021-01-19
 */

//changes:
//      warnings are now active

class Database{
    private $_dbh;
    private $_stmt;
    private $_queryCounter = 0;
    private $_errors;

  
  
    //class constructor 
    public function __construct($user, $pass, $dbname, $host="localhost"){
      
      //echo "aqui";
        $dsn = 'mysql:host='. $host .';dbname=' . $dbname;
        //$dsn = 'sqlite:myDatabase.sq3';
        //$dsn = 'sqlite::memory:';
        $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
          
                    );
        try {
            $this->_dbh = new PDO($dsn, $user, $pass, $options);
        }
        catch (PDOException $e) {
            $this->_errors=$e->getMessage();
            //exit();
        }
    }

    //#########################################################################################################################################
    // begin transaction // must be innoDatabase table
    public function beginTransaction(){
        return $this->_dbh->beginTransaction();
    }
    
    //#########################################################################################################################################
    // assing values to a sql paramteter
    public function bind($pos, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->_stmt->bindValue($pos, $value, $type);
    }
  
    //#########################################################################################################################################
    // cancel transaction
    public function cancelTransaction(){
        return $this->_dbh->rollBack();
    }
  
    //#########################################################################################################################################
    // cancel transaction
    public function rollBack(){
        return $this->cancelTransaction();
    }
  
    //#########################################################################################################################################
    // show informations
    public function debugDumpParams(){
        return $this->_stmt->debugDumpParams();
    }
  
    //#########################################################################################################################################
    // end transaction
    public function endTransaction(){
        return $this->_dbh->commit();
    }

    //#########################################################################################################################################
    public function execute(){
        $this->_queryCounter++;
        //echo $this->_stmt->querie();
        return $this->_stmt->execute();
    }
  
    //#########################################################################################################################################
    public function getError(){
      //return $this->debugDumpParams();
      return $this->_errors;
    }
  
    //#########################################################################################################################################
    // returns last insert ID
    //!!!! if called inside a transaction, must call it before closing the transaction!!!!!!
    public function lastInsertId(){
        return $this->_dbh->lastInsertId();
    }
  
    //#########################################################################################################################################
    public function query($query){
        $this->_stmt = $this->_dbh->prepare($query);
        //echo "<br><br>$sql<br><bR>";
        //$this->_errors=$this->_dbh->errorInfo();
        //print_r($this->_dbh->errorInfo());
    }

    //#########################################################################################################################################
    // returns number of queries executed
    public function queryCounter(){
        return $this->_queryCounter;
    }

    //#########################################################################################################################################
    public function resultset(){
        $this->execute();
        //$this->_errors=$this->_dbh->errorCode();
        return $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
      
    //#########################################################################################################################################
    // returns number of rows updated, deleted, or inserted
    public function rowCount(){
        return $this->_stmt->rowCount();
    }
  
    //#########################################################################################################################################
    public function single(){
        $this->execute();
        return $this->_stmt->fetch(PDO::FETCH_ASSOC);
    }

  
}
