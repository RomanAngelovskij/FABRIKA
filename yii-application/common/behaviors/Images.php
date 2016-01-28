<?php
namespace common\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class Images extends Behavior{

	public $attribute = 'images';

	public function events(){
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => 'jsonEncode',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'jsonEncode',
			ActiveRecord::EVENT_AFTER_FIND => 'jsonDecode',
		];
	}

	public function jsonEncode($event){
		if (empty($this->owner->{$this->attribute})){
			return;
		}

		if (!is_array($this->owner->{$this->attribute})){
			$this->owner->{$this->attribute} = explode(',', $this->owner->{$this->attribute});
		}

		$this->owner->{$this->attribute} = json_encode($this->owner->{$this->attribute});
	}

	public function jsonDecode($event){
		$this->owner->{$this->attribute} = json_decode($this->owner->{$this->attribute}, true);
	}
}
