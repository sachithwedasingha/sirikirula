<?php

include_once('main.php');

class AutoNumber extends Main{

    public function NumberGenaration($id,$table,$prefix){

        $this->dbResult->query("LOCK TABLES $table WRITE");

        try{

            $sql="SELECT $id
                  FROM $table
                  WHERE $id LIKE '".$prefix."%'
                  ORDER BY LENGTH($id) DESC,$id DESC
                  LIMIT 1";

            $result=$this->dbResult->query($sql);

            if(!$result){
                throw new Exception($this->dbResult->error);
            }

            if($result->num_rows>0){

                $row=$result->fetch_assoc();

                $lastId=$row[$id];

                $numberPart=preg_replace('/[^0-9]/','',$lastId);

                $nextNumber=(int)$numberPart+1;

            }else{

                $nextNumber=1;
            }

            $newId=$prefix.str_pad($nextNumber,5,'0',STR_PAD_LEFT);

            $this->dbResult->query("UNLOCK TABLES");

            return $newId;

        }catch(Exception $e){

            $this->dbResult->query("UNLOCK TABLES");

            die($e->getMessage());
        }
    }
}
?>