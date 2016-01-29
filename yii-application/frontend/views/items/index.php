<?php
/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = $item->name;

$this->params['breadcrumbs'] = $breadcrumbs;
?>

<div class="row item-view">
	<h1><?=$item->name?></h1>

	<div class="col-md-8">
		<span class="label label-primary price"><?=number_format($item->price, 2, '.', ' ')?> руб.</span>
		<div class="description">
			<?=nl2br($item->description)?>
		</div>
	</div>

	<div class="col-md-4 img-wrap">
		<?php if (!empty($item->images)):?>
			<div class="col-md-8 preview">
				<?=Html::img(rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/' . $item->images[0])?>
			</div>
			<div class="col-md-4 min">
				<?php foreach ($item->images as $img):?>
					<a href="" data-src="<?=rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/' . $img?>">
						<?=Html::img(rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/' . $img)?>
					</a>
				<?php endforeach;?>
			</div>
		<?php else:?>
			<?=Html::img('/img/noimg.png')?>
		<?php endif?>
	</div>
</div>

<?php
$this->registerJs('
	$(".item-view .img-wrap .min a").on("click", function(e){
		e.preventDefault();

		var src = $(this).data("src");
		$(".item-view .img-wrap .preview img").attr("src", src);
	})
');
?>
