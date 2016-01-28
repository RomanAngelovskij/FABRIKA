<?php
namespace console\controllers;

use Yii;
use common\models\User;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

class RolesController extends Controller{

	/**
	 * Create new role
	 *
	 * @param string $roleName
	 * @param string $description Description of role. String with spaces
	 * 							  must be taken in quotes
	 *
	 * @return int
	 * @throws \yii\console\Exception
	 */
	public function actionCreate($roleName, $description = ''){
		if (empty ($description)){
			$description = $roleName;
		}

		$role = Yii::$app->authManager->createRole($roleName);
		$role->description = $description;
		try{
			Yii::$app->authManager->add($role);
			$this->stdout("Role $roleName created\n", Console::FG_GREEN);
			self::EXIT_CODE_NORMAL;
		} catch (\Exception $e){
			throw new Exception($e->getMessage());
			return self::EXIT_CODE_ERROR;
		}
	}

	/**
	 * Add admin role for user. Example:
	 * php yii roles/add-admin username
	 *
	 * @param string $username
	 *
	 * @return int
	 * @throws \yii\console\Exception
	 */
	public function actionAddAdmin($username){
		$User = User::findByUsername($username);
		if (empty($User)){
			throw new Exception('User ' . $username . ' not found');
			return self::EXIT_CODE_ERROR;
		}

		$userRole = Yii::$app->authManager->getRole('admin');
		try{
			Yii::$app->authManager->assign($userRole, $User->getId());
			$this->stdout("Role 'admin' for user $username added\n", Console::FG_GREEN);
			self::EXIT_CODE_NORMAL;
		} catch (\Exception $e){
			throw new Exception($e->getMessage());
			return self::EXIT_CODE_ERROR;
		}
	}
}