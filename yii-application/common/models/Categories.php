<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $parentId
 *
 * @property Categories $parent
 * @property Categories[] $categories
 * @property Items[] $items
 */
class Categories extends ActiveRecord{

	private $__subcategoriesFlat = [];

	/**
	 * @inheritdoc
	 */
	public function behaviors(){
		return [
			'slug' => [
				'class' => 'common\behaviors\Slug',
				'srcAttribute' => 'name',
				'slugAttribute' => 'slug'
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(){
		return [
			'add'  => ['name', 'slug', 'parentId'],
			'edit' => ['id', 'name', 'slug', 'parentId'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(){
		return [
			[['name'], 'required', 'on' => ['add'], 'message' => '"{attribute}" - обязательный параметр'],
			[['id', 'name', 'slug'], 'required', 'on' => ['edit'], 'message' => '"{attribute}" - обязательный параметр'],
			[['name'], 'string', 'max' => 60],
			[['slug'], 'string', 'max' => 100],
			[['slug'], 'unique'],
			['parentId', function($attribute){
				if ((int) $this->$attribute > 0){
					if(self::find()->where(['id' => $this->$attribute])->exists() === false){
						$this->addError($attribute, 'Такаой родительской категории не существует');
					}
				}
			}],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(){
		return [
			'name' => 'Название',
			'parentId' => 'Родительская категория',
			'slug' => 'SLUG'
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
			if ($this->parentId == 0){
				//If parentId set to 0, change it to NULL for correctly save in table
				$this->parentId = null;
			}

			return true;
		}
		return false;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent()
	{
		return $this->hasOne(Categories::className(), ['id' => 'parentId']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCategories()
	{
		return $this->hasMany(Categories::className(), ['parentId' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getItems()
	{
		return $this->hasMany(Items::className(), ['catId' => 'id']);
	}

	//todo: use this method to find category by full slug in URL. Example /category/subcategory/
	public function getBySlugPath($slug){
		if (empty($slug)){
			return null;
		}

		$Parts = explode('/', $slug);

		$currentSlug = array_pop($Parts);

		$Category = self::find()->where(['slug' => $currentSlug]);

		return $Category->exists() ? $Category->one() : null;

	}

	/**
	 * Recursive method for building category tree.
	 * [1] => [
	 * 			'name' => 'Category',
	 * 			'sub'  => [
	 * 						[2] => [
	 * 								'name' => 'Subcategory 1',
	 * 								'sub' => null
	 * 							   ],
	 * 						[3] => [
	 * 								'name' => 'Subcategory 2',
	 * 								'sub' => null
	 * 							   ],
	 * 					  ]
	 * 		  ]
	 *
	 * @param null $id
	 *
	 * @return array Multidimensional array
	 */
	public function subcategories($id = null){
		$subC = [];
		$sub = self::find()->where(['parentId' => $id])->all();

		if (empty($sub)){
			return;
		}

		foreach ($sub as $category){
			$subC[$category->id] = [
				'name' => $category->name,
				'sub' => $this->subcategories($category->id),
			];
		}

		return $subC;
	}

	/**
	 * Recursive method for building category tree.
	 *
	 * @param array  $tree   Array from $this->subcategories()
	 * @param string $indent Characters for creating an indent in the beginning
	 *
	 * @return array
	 */
	public function subcategoriesFlat($tree, $indent=''){
		if ($tree){
			foreach ($tree as $id => $Data) {
				if (is_array($Data['sub'])) {
					$this->__subcategoriesFlat[$id] = $indent . ' ' . $Data['name'];
					$this->subcategoriesFlat($Data['sub'], $indent . '--');
				} else {
					$this->__subcategoriesFlat[$id] = $indent . ' ' . $Data['name'];
				}
			}
		}

		return $this->__subcategoriesFlat;
	}
}