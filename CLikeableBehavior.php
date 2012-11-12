<?php

/**
 * Updates database 'items_users' table when users vote for items.
 * This solution uses a 'direct' database access via transactions
 * as the ActiveRecord as it is cannot do it.
 * @return boolean
 * 
 * @author Philipp Galichkin <p.galitchkin@gmail.com>
 */
class CLikeableBehavior extends CActiveRecordBehavior {

    /**
     * @var string ID of a user who likes items
     */
    protected $user_id = "";
    protected $item_id = "";
    /**
     * @var string A name of the relational table where all liked will be stored
     */
    public $items_users_table = "";
    public $user_id_column_name = "";
    public $item_id_column_name = "";

    public function like($item_id = "") {       
        $this->item_id = $item_id;
        $sql = sprintf('INSERT INTO %s(%s, %s) VALUES(:iid, :uid);', 
                $this->items_users_table, 
                $this->item_id_column_name, 
                $this->user_id_column_name
        );
        return $this->execute($sql);
    }

    public function dislike($item_id) {        
        $this->item_id = $item_id;
        $sql = sprintf('DELETE FROM %s WHERE %s = :iid AND %s = :uid;', 
                $this->items_users_table, 
                $this->item_id_column_name, 
                $this->user_id_column_name
                );
        
        return $this->execute($sql);
    }

    protected function execute($sql) {
        
        if (!Yii::app()->user->isGuest) {
            $this->user_id = User::model()->findByAttributes(
                    array('username' => Yii::app()->user->id)
                    )->pk_uid;
        } else
            return false;

        $connection = Yii::app()->db;
        $transaction = false; //transaction variable

        //start transaction only if another one is not  running already
        if (!$connection->currentTransaction) {
            $transaction = $connection->beginTransaction();
        }
        try {
            $command = $connection->createCommand($sql);
            $command->bindParam(':iid', $this->item_id, PDO::PARAM_INT);
            $command->bindParam(':uid', $this->user_id, PDO::PARAM_INT);
            $command->execute();
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }
    }

}

?>