<?php

use app\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\search\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'pager' => [
            'class' => \yii\widgets\LinkPager::class,

            'maxButtonCount' => 5,

            'firstPageLabel' => '<<',
            'lastPageLabel' => '>>',

            'prevPageLabel' => '<',
            'nextPageLabel' => '>',

            'options' => [
                'class' => 'pagination justify-content-center',
            ],

            'linkOptions' => [
                'class' => 'page-link',
            ],

            'pageCssClass' => 'page-item',
            'activePageCssClass' => 'active',
            'disabledPageCssClass' => 'disabled',
        ],
        
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'slug',
            'img' => [
                'label' => 'Ảnh',
                'format' => 'raw',
                'value' => function ($model) {
                    $media = $model->getMedia()->all();
                    $output = '';
                    foreach ($media as $item) {
                        $output .= Html::img(Url::to('@web/' . $item->filepath), ['width' => '50', 'style' => 'margin-right: 10px;']);
                    }
                    return $output;
                },
            ],
            'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Category $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

</div>
