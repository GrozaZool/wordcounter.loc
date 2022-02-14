<?php

namespace frontend\controllers;

use common\models\File;
use common\models\FileSearch;
use common\models\FileType;
use common\models\UploadForm;
use Yii;
use yii\captcha\CaptchaAction;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $uploadModel = new UploadForm();

        $searchModel = new FileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $allowedExtensions = FileType::find()->select('extension')->column();

        return $this->render('index', [
            'uploadModel' => $uploadModel,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'allowedExtensions' => $allowedExtensions,
        ]);
    }

    /**
     * @return Response
     */
    public function actionUpload(): Response
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->textFile = UploadedFile::getInstance($model, 'textFile');

            $file = new File();

            if ($model->validate()) {
                $fileInfo = $model->getFileInfo();

                $fileType = FileType::find()
                    ->select('id')
                    ->where(['extension' => $fileInfo['extension']])
                    ->limit(1)
                    ->scalar();

                $file->name = $fileInfo['name'];
                $file->fileType = $fileType ? intval($fileType) : null;
                $file->fileGroup = $fileInfo['group'];
                $file->wordCount = $fileInfo['wordCount'];
                $file->fileSize = $fileInfo['size'];
                if ($file->validate() && $file->save()) {

                    $file->assignGroup();

                    Yii::$app->session->setFlash('success', 'Успешно');
                }
            } else {
                if ($model->errors) {
                    Yii::$app->session->setFlash('error', $model->getFirstErrors());
                }

                if ($file->errors) {
                    Yii::$app->session->setFlash('error', $file->getFirstErrors());
                }
            }
        }

        return $this->redirect(['site/index']);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return File
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): File
    {
        if (($model = File::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
