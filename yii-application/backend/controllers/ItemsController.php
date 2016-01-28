<?php
namespace backend\controllers;

use Yii;
use common\models\Categories;
use common\models\Items;
use xj\uploadify\UploadAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ItemsController extends Controller{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => [],
						'allow' => true,
					],
					[
						'actions' => ['add', 'edit', 'delete'],
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'upload-images' => [
				'class' => UploadAction::className(),
				'basePath' => '@webroot/uploads',
				'baseUrl' => '@web/uploads',
				'enableCsrf' => true,
				'postFieldName' => 'Filedata',
				'format' => function(UploadAction $action){
					return $action->uploadfile->getBaseName() . '_' . time() . '.' . $action->uploadfile->getExtension();
				},
				'overwriteIfExist' => true,
				'validateOptions' => [
					'extensions' => ['jpg', 'png', 'jpeg', 'gif'],
					'maxSize' => 1 * 1024 * 1024, //file size
				],
				'beforeValidate' => function (UploadAction $action) {
					//throw new Exception('test error');
				},
				'afterValidate' => function (UploadAction $action) {},
				'beforeSave' => function (UploadAction $action) {},
				'afterSave' => function (UploadAction $action) {
					$action->output['fileUrl'] = $action->getWebUrl();
					$action->output['fileName'] = $action->getFilename();
					$action->getFilename();
					$action->getWebUrl();
					$action->getSavePath();
				},
			],
		];
	}

	/**
	 * Add a new Items model.
	 *
	 * If creation is successful, the browser will be redirected to the category page with items.
	 *
	 * @return mixed
	 */
	public function actionAdd(){
		$Items = new Items(['scenario' => 'add']);

		if ($Items->load(Yii::$app->request->post()) && $Items->save()){
			$Category = Categories::findOne(['id' => $Items->catId]);
			return Yii::$app->response->redirect(Url::toRoute(['site/index/' . $Category->slug]));
		}

		$Items->catId = Yii::$app->request->get('parent', 0);

		$Categories = new Categories();

		return $this->render('add', [
			'model' => $Items,
			'categoriesTree' => $Categories->subcategoriesFlat($Categories->subcategories()),
		]);

	}

	/**
	 * Updates an existing Items model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionEdit($id){
		$model = $this->_findModel($id);
		$model->scenario = 'edit';

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['edit', 'id' => $model->id]);
		} else {
			$сategories = new Categories();

			return $this->render('edit', [
				'model' => $model,
				'categoriesTree' => $сategories->subcategoriesFlat($сategories->subcategories()),
			]);
		}
	}

	/**
	 * Deletes an existing Items model.
	 * If deletion is successful, the browser will be redirected to the home page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id){
		$this->_findModel($id)->delete();

		return $this->goHome();
	}

	/**
	 * Finds the Items model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Items the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function _findModel($id){
		if (($model = Items::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('Товар не найден.');
		}
	}

}