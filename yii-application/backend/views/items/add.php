<?php
/*
 * @var $this yii\web\View
 * @var model common\models\Items
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Alert;

$this->title = 'Добавление товара';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-about">
	<h1><?= Html::encode($this->title) ?></h1>

	<?php if ($model->hasErrors()){
		echo Alert::widget([
			'options' => [
				'class' => 'alert-danger',
			],
			'body' => '<b>Ошибка!</b><ul><li>' . implode('<li>', ArrayHelper::getColumn($model->getErrors(), 0)) . '</ul>'
		]);
	}
	?>

	<?= $this->render('_form', [
		'model' => $model,
		'categoriesTree' => $categoriesTree,
	]) ?>

</div>