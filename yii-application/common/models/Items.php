<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "items".
 *
 * @property integer $id
 * @property integer $catId
 * @property string $name
 * @property string $images
 * @property string $description
 * @property double $price
 *
 * @property Categories $cat
 */

class Items extends ActiveRecord{

	public $imgUpload;

	/**
	 * @inheritdoc
	 */
	public static function tableName(){
		return 'items';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors(){
		return [
			'images' => [
				'class' => 'common\behaviors\Images',
				'attribute' => 'images',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(){
		return [
			'add'  => ['catId', 'name', 'images', 'description', 'price'],
			'edit' => ['id', 'catId', 'name', 'images', 'description', 'price'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(){
		return [
			[['name', 'catId', 'price'], 'required', 'on' => ['add', 'edit'], 'message' => '"{attribute}" - обязательный параметр'],
			[['name'], 'string', 'max' => 250],
			[['price'], 'number', 'min' => 0.1, 'on' => ['add']],
			[['id', 'name', 'catId'], 'required', 'on' => ['edit'], 'message' => '"{attribute}" - обязательный параметр'],
			['catId', 'exist', 'targetClass' => Categories::className(), 'targetAttribute' => 'id', 'message' => 'Вы пытаетесь добавить товар в несуществующую категорию'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(){
		return [
			'name' => 'Название',
			'catId' => 'Категория',
			'images' => 'Изображения',
			'description' => 'Описание',
			'price' => 'Цена',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCat(){
		return $this->hasOne(Categories::className(), ['id' => 'catId']);
	}
}