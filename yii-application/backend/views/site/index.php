<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\grid\GridView;

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
			<div class="panel panel-default">
				<div class="panel-body">
					<?php if (!empty($CurrentCategory)):?>
						<?=Html::a('<em class="glyphicon glyphicon-plus-sign"></em>',
							['items/add/', 'parent' => $CurrentCategory->id],
							['class' => 'btn btn-default', 'title' => 'Добавить товар'])
						?>

						<?=Html::a('<em class="glyphicon glyphicon-pencil"></em>',
							['site/edit/' . $CurrentCategory->slug],
							['class' => 'btn btn-default', 'title' => 'Редактировать категорию'])
						?>

						<?=Html::a('<em class="glyphicon glyphicon-trash"></em>',
							['site/delete/' . $CurrentCategory->slug],
							[
								'class' => 'btn btn-danger',
								'title' => 'Удалить категорию',
								'data-confirm' => 'Также будут удалены все вложенные категории и товары. Продолжить?'
							])
						?>
					<?php endif;?>

					<?=Html::a('<em class="glyphicon glyphicon-indent-right"></em>',
						['site/add/', 'parent' => !empty($CurrentCategory) ? $CurrentCategory->id : 0],
						['class' => 'btn btn-default', 'title' => 'Добавить подкатегорию'])
					?>
				</div>
			</div>

			<?php if (!empty($CurrentCategory)):?>
			<?= GridView::widget([
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],
						'name',
						'price',
						[
							'class' => 'yii\grid\ActionColumn',
							'headerOptions' => ['width' => '50'],
							'template' => '{edit} {delete}',
							'buttons' => [
								'edit' => function ($url, $model) {
									return Html::a(
										'<span class="glyphicon glyphicon-pencil"></span>',
										'/items/edit/' . $model->id);
								},
								'delete' => function ($url, $model){
									return Html::a(
										'<span class="glyphicon glyphicon-trash"></span>',
										'/items/delete/' . $model->id,
										[
											'data-confirm' => 'Удалить товар?',
										]
									);
								}
							],
						],
					],
			]); ?>


		<?php else:?>
			Если вы хотите отредактировать категорию, добавить подкатегорию или товар, сначала откройте ее, сверху появится панель управления.
			</p>
		<?php endif;?>
	</div>
</div>
