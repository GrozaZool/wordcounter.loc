<?php

use common\models\File;
use common\models\FileGroup;
use common\models\UploadForm;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $uploadModel UploadForm */
/* @var $searchModel common\models\FileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $allowedExtensions array */


$this->title = 'Word counter';
?>
<div class="site-index">

    <div class="body-content">

        <h5>
            Приложение на yii 2, которое позволяет через веб загружать файл любого размера в формате txt
            (и другие форматы), считает в нём количество слов, и результаты записывает в БД.
        </h5>

        <p class="font-weight-bold mt-4">
            Разрешенные типы файлов: <?= implode(', ', $allowedExtensions) ?>
        </p>

        <?php $form = ActiveForm::begin([
            'action' => '/site/upload',
            'options' => ['enctype' => 'multipart/form-data']
        ]) ?>
        <div class="row">
            <div class="col-8">
                <?= $form->field($uploadModel, 'textFile')->fileInput()->label('') ?>
            </div>
            <div class="col-4">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary btn-block', 'name' => 'submit-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>

        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'name',
                        [
                            'attribute' => 'fileType',
                            'value' => static function (File $model) {
                                return $model->type->extension;
                            }
                        ],
                        [
                            'label' => 'Группы',
                            'format' => 'html',
                            'attribute' => 'fileGroup',
                            'value' => static function (File $model) {
                                $result = [];
                                /** @var FileGroup $group */
                                foreach ($model->getGroups()->all() as $group) {
                                    $result[] = "<p class='small'>— {$group->name}</p>";
                                }

                                return implode('', array_filter($result));
                            }
                        ],
                        'wordCount',
                        [
                            'attribute' => 'fileSize',
                            'value' => static function (File $model) {
                                if ($model->fileSize > 0 && $model->fileSize < 1024) {
                                    return round($model->fileSize, 4) . ' Б.';
                                } elseif ($model->fileSize > 1024 && $model->fileSize < 1024*1024) {
                                    return round($model->fileSize / 1024, 4) . ' КБ.';
                                } else {
                                    return round($model->fileSize / 1024 / 1024, 4) . ' МБ.';
                                }
                            }
                        ],

                        [
                            'attribute' => 'createdAt',
                            'value' => static function ($model) {
                                return Yii::$app->formatter->asDatetime($model->createdAt);
                            }
                        ],
                        [
                            'class' => ActionColumn::class,
                            'template' => '{delete}',
                            'urlCreator' => function ($action, File $model) {
                                if(in_array($action, ['delete'])) {
                                    return Url::toRoute([$action, 'id' => $model->id]);
                                }
                                return false;
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
