<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\categories */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories array */
?>


<div class="items-form">
	<?php
	$form = ActiveForm::begin([
		'id' => 'category-edit-form',
		'enableClientValidation' => false,
		'options' => [
			'class' => 'form-horizontal',
		],
		'fieldConfig' => [
			'template' => "{label}\n<div class=\"col-lg-9\">{input}{error}</div>",
			'labelOptions' => ['class' => 'col-lg-3 control-label'],
		],
	])
	?>

	<?=$form->field($model, 'name')?>

	<?php if(!$model->isNewRecord):?>
		<?=$form->field($model, 'slug')?>
	<?php endif;?>

	<?=$form->field($model, 'parentId')->dropDownList(['0' => '-'] + ArrayHelper::map($categories, 'id', 'name'))?>

	<div class="form-group">
		<?=
		Html::submitButton($model->isNewRecord ?
				'Добавить' : 'Сохранить',
			['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
		)
		?>
	</div>

	<?php
	ActiveForm::end();
	?>
</div>