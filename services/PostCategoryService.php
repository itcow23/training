<?php

namespace app\services;

use RuntimeException;
use Throwable;
use Yii;

class PostCategoryService
{
    public function create($model, $postData)
    {
        $transaction = Yii::$app->db->begintransaction();
        try{
            if(!$model->load($postData,'')){
                Throw new RuntimeException('Load data error');
            }

            if(!$model->save()){
               Throw new RuntimeException('Save error');
            }

            $transaction->commit();
            return true;
        }catch(Throwable $e){
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    public function update($model, $postData)
    {
        $transaction = Yii::$app->db->begintransaction();
        try{
            if(!$model->load($postData,'')){
                Throw new RuntimeException('Load data error');
            }

            if(!$model->save()){
               Throw new RuntimeException('update error');
            }

            $transaction->commit();
            return true;
        }catch(Throwable $e){
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    public function delete($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete.');
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
             Yii::error($e->getMessage());
            return false;
        }
    }
}
