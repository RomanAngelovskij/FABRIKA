<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Items;

/**
 * ItemsSearch represents the model behind the search form about `common\models\Items`.
 */
class ItemsSearch extends Items
{

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'catId'], 'integer'],
			[['name', 'images', 'description'], 'safe'],
			[['price'], 'number'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 * @param array $categories
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params, $categories = [])
	{
		$query = Items::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'price' => $this->price,
		]);

		$query->andFilterWhere(['in', 'catId', $categories]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'images', $this->images])
			->andFilterWhere(['like', 'description', $this->description]);

		return $dataProvider;
	}
}