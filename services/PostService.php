<?php

namespace app\services;

use app\models\forms\PostForm;
use app\models\PostTag;
use app\models\response\PostResponse;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class PostService
{
    public function create(PostResponse $model, PostForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(PostResponse $model, PostForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(PostResponse $model, PostForm $form)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->assignAttributes($model, $form);
            $model->image = UploadedFile::getInstancesByName('image');

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }
            $this->handleTags($model, $form);

            $transaction->commit();
            return PostResponse::find()
                ->where(['id' => $model->id])
                ->with(['comments', 'ratings', 'media', 'tags'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(PostResponse $model, PostForm $form): void
    {
        $attributes = array_filter(
            $form->getAttributes([
                'category_id',
                'title',
                'content',
                'status',
                'description',
            ]),
            fn($value) => $value !== null
        );

        $model->setAttributes($attributes, false);

        if ($form->removed_image !== null && is_array($form->removed_image)) {
            $model->removed_image = $form->removed_image;
        }
    }

    public function delete(PostResponse $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete post.');
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    public function updateStatus($model, $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->setAttributes($form->getAttributes(['status']), false);

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return PostResponse::find()
                ->where(['id' => $model->id])
                ->with(['comments', 'ratings', 'media'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    protected function handleTags(PostResponse $model, PostForm $form): void
    {


        if ($form->removed_tag !== null && is_array($form->removed_tag)) {
            $removedTagIds = [];
            foreach ($form->removed_tag as $tagId) {
                $removedTagIds[] = $tagId;
            }
            PostTag::deleteAll(['post_id' => $model->id, 'tag_id' => $removedTagIds]);
        }

        if ($form->add_tag !== null && is_array($form->add_tag)) {

            $existingTagIds = PostTag::find()
                ->select(['tag_id'])
                ->where(['post_id' => $model->id])
                ->column();

            $newTagIds = array_diff($form->add_tag, $existingTagIds);

            $rows = [];

            foreach ($newTagIds as $tagId) {
                $rows[] = [$model->id, $tagId];
            }

            if (!empty($rows)) {
                Yii::$app->db->createCommand()
                    ->batchInsert(PostTag::tableName(), ['post_id', 'tag_id'], $rows)
                    ->execute();
            }
        }
    }
}
