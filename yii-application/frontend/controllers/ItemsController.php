<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Items;
use common\models\Categories;

class ItemsController extends Controller{

	public function actionIndex($id){
		$item = $this->_findModel($id);

		return $this->render('index', [
					'item' => $item,
					'breadcrumbs' => $this->__makeBreadcrumbs($item->catId, $item->name),
		]);
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

	/**
	 * Build array of breadcrumbs
	 *
	 * @param int $id current category id
	 * @param string @name Items name
	 *
	 * @return array
	 */
	private function __makeBreadcrumbs($catId, $name){
		$Breadcrumbs = ['label' => $name];

		$Category = Categories::findOne($catId);
		$Breadcrumbs[] = ['label' => $Category->name, 'url' => '/site/index/' . $Category->slug];

		if ($Category->parentId === null){
			return $Breadcrumbs;
		}

		do{
			$Category = Categories::findOne($Category->parentId);
			$Breadcrumbs[] = ['label' => $Category->name, 'url' => '/site/index/' . $Category->slug];
		}while($Category->parentId != null);


		return array_reverse($Breadcrumbs);
	}
}