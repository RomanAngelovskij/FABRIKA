<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use xj\uploadify\Uploadify;

/* @var $this yii\web\View */
/* @var $model common\models\Items */
/* @var $form yii\widgets\ActiveForm */
/* @var $categoriesTree array */
?>


<div class="items-form">
	<?php
	$form = ActiveForm::begin([
		'id' => 'item-add-form',
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

	<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>

	<?=$form->field($model, 'catId')->dropDownList(['0' => ''] + $categoriesTree)?>

		<div>
			<div class="col-lg-3"></div>
			<div class="col-lg-9" id="imgPreview">
				<?php if(!empty($model->images)):?>
					<?php foreach($model->images as $img):?>
						<img src="/uploads/<?=$img?>">
					<?php endforeach;?>
				<?php endif;?>
			</div>
		</div>
	<?php
	echo $form->field($model, 'images')->hiddenInput(['id' => 'img', 'value' => !$model->isNewRecord && !empty($model->images) ? implode(',', $model->images) : ''])->label(false);
	echo $form->field($model, 'imgUpload')->fileInput(['id'=>'imgUpload'])->label('Изображения');
	echo Uploadify::widget([
		'url' => yii\helpers\Url::to(['upload-images']),
		'id' => 'imgUpload',
		'csrf' => true,
		'renderTag' => false,
		'jsOptions' => [
			'width' => 120,
			'height' => 40,
			'multi' => true,
			'onUploadError' => new JsExpression('
							function(file, errorCode, errorMsg, errorString) {
								console.log("The file " + file.name + " could not be uploaded: " + errorString + errorCode + errorMsg);
							}'
			),
			'onUploadSuccess' => new JsExpression('
							function(file, data, response) {
								console.log(data);
								data = JSON.parse(data);
								if (data.error) {
									console.log(data.msg);
								} else {
									console.log(data);

									var images = $("#img").val();
									images = (images === "") ? images = [] : images.split(",");

									images.push(data.fileName)
									$("#img").val(images);

									var img = $("<img>");
									img.attr("src", "/uploads/" + data.fileName);
									$("#imgPreview").append(img);
								}
							}'
			),
		]
	]);
	?>

	<?=$form->field($model, 'description')->textarea(['rows' => 6])?>

	<?=$form->field($model, 'price')->textInput()?>

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