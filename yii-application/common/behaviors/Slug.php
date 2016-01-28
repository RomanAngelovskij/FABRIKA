<?php
namespace common\behaviors;

use dosamigos\transliterator\TransliteratorHelper;
use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

class Slug extends Behavior{

	/**
	 * @var string The attribute name of which will be created slug
	 */
	public $srcAttribute = 'name';

	/**
	 * @var string The attribute name for slug
	 */
	public $slugAttribute = 'slug';

	public function events(){
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'getSlug'
		];
	}

	/**
	 * Setup slug attribute in model
	 *
	 * @param $event
	 */
	public function getSlug($event){
		if ( empty( $this->owner->{$this->slugAttribute} ) ) {
			$this->owner->{$this->slugAttribute} = $this->__generateSlug( $this->owner->{$this->srcAttribute} );
		} else {
			$this->owner->{$this->slugAttribute} = $this->__generateSlug( $this->owner->{$this->slugAttribute} );
		}
	}

	/**
	 * @param string $slug The string from which is obtained an slug
	 *
	 * @return string
	 */
	private function __generateSlug($slug){
		$slug = Inflector::slug( TransliteratorHelper::process( $slug ), '-', true );
		if ( $this->__checkUniqueSlug( $slug ) ) {
			return $slug;
		} else {
			//If newly generated slug not unique, add numeric suffix
			for ( $suffix = 2; !$this->__checkUniqueSlug( $newSlug = $slug . '-' . $suffix ); $suffix++ ) {}
			return $newSlug;
		}
	}

	private function __checkUniqueSlug($slug){
		$pk = $this->owner->primaryKey();
		$pk = $pk[0];

		$condition = $this->slugAttribute . ' = :out_attribute';
		$params = [ ':out_attribute' => $slug ];
		if ( !$this->owner->isNewRecord ) {
			$condition .= ' and ' . $pk . ' != :pk';
			$params[':pk'] = $this->owner->{$pk};
		}

		return !$this->owner->find()
			->where( $condition, $params )
			->one();
	}
}
