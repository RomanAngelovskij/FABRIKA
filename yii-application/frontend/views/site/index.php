<?php
/* @var $this yii\web\View */

use yii\bootstrap\Html;
use yii\bootstrap\Alert;
use yii\widgets\LinkPager;

$this->title = 'Категории';

$this->params['breadcrumbs'] = $breadcrumbs;
?>
<div class="row">
	<div class="col-md-3 col-sm-4">
		<?php if (!empty($categories)):?>
			<div class="list-group">
				<?php
				foreach ($categories as $category) {
					$label = '<i class="glyphicon glyphicon-chevron-right"></i>' . Html::encode($category->name);
					echo Html::a($label, '/site/index/' . $category->slug, [
						'class' => 'list-group-item',
					]);
				}
				?>
			</div>
		<?php else:?>
			Категорий нет
		<?php endif; ?>
	</div>

	<div class="col-md-9 col-sm-8">
		<?php if (!empty($CurrentCategory)):?>
			<?php if (!empty($items)):?>
				<?php foreach ($items as $item):?>
					<div class="item-wrap">
						<div class="img-wrap">
							<?=Html::img(!(empty($item->images)) ? rtrim(Yii::$app->params['backendUrl'], '/') . '/uploads/' . $item->images[0] : '/img/noimg.png')?>
						</div>
						<?=Html::a($item->name, '/items/' . $item->id)?>
						<span class="description">
							<?=!empty($item->description) ? substr(nl2br($item->description), 0, 200) : ''?>
						</span>
					</div>
				<?php endforeach;?>

				<div class="clearfix"></div>

				<?=LinkPager::widget(['pagination' => $pages])?>

			<?php else:?>
				<?=Alert::widget([
					'options' => [
						'class' => 'alert-warning',
					],
					'body' => 'В этой категории нет товаров'
				]);
				?>
			<?php endif;?>

		<?php else:?>
			<p>
				<a href="https://github.com/RomanAngelovskij/FABRIKA/" target="_blank">https://github.com/RomanAngelovskij/FABRIKA/</a>
			</p>
		<?php endif;?>
	</div>
</div>
