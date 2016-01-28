<?php
namespace backend\controllers;

use Yii;
use common\models\Categories;
use common\models\Items;
use common\models\ItemsSearch;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'add', 'edit', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

	/**
	 * Lists all categories or subcategories. If category is selected,
	 * it shows the items
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */
    public function actionIndex($slug = '')
    {
		$categoriesModel = new Categories();

		$currentCategory = null;
		$parentId = null;
		$subcategoriesIds = [];

		// If category is selected...
		if (!empty($slug)){
			$currentCategory = $this->_findModel($slug);

			$parentId = !empty($currentCategory) ? $currentCategory->id : null;

			//...build array of subcategories ids
			$subcategoriesIds = array_keys(
				$categoriesModel->subcategoriesFlat(
					$categoriesModel->subcategories(!empty($currentCategory) ? $currentCategory->id : null)
				)
			);

			//add parent category id
			$subcategoriesIds = array_merge([$currentCategory->id], $subcategoriesIds);
		}

		//List of subcategories
		$categoriesList = Categories::find()
								->where(['parentId' => $parentId])
								->orderBy('name')
								->all();

		$searchModel = new ItemsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $subcategoriesIds);
		$dataProvider->pagination->pageSize = Yii::$app->params['itemsOnPage'];

		return $this->render('index', [
			'categories' => $categoriesList,
			'breadcrumbs' => !empty($currentCategory) ? $this->__makeBreadcrumbs($currentCategory->id) : [],
			'CurrentCategory' => $currentCategory,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }

	/**
	 * Creates a new category.
	 * If creation is successful, the browser will be redirected to the home page.
	 *
	 * @return mixed
	 */
	public function actionAdd(){
		$category = new Categories(['scenario' => 'add']);

		if ($category->load(Yii::$app->request->post()) && $category->validate() === true){
			if ($category->save()){
				return $this->goHome();
			}
		}

		//setup parent category if selected, for default select in dropdown
		$category->parentId = Yii::$app->request->get('parent', 0);

		return $this->render('/categories/add', [
			'model' => $category,
			'categories' => Categories::find()->orderBy('name')->indexBy('id')->all(),
		]);
	}

	/**
	 * Updates an existing category.
	 * If update is successful, the browser will be redirected to the home page.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */
	public function actionEdit($slug = ''){
		$Category = Categories::findOne(['slug' => $slug]);
		$Category->scenario = 'edit';

		if ($Category->load(Yii::$app->request->post()) && $Category->validate() === true){
			if ($Category->save()){
				return $this->goHome();
			}
		}

		return $this->render('/categories/edit', [
			'model' => $Category,
			'categories' => Categories::find()->orderBy('name')->all(),
		]);
	}

	/**
	 * Deletes an existing category with it subcategories and items
	 * If deletion is successful, the browser will be redirected to the home page.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */
	public function actionDelete($slug){
		$this->_findModel($slug)->delete();

		return $this->goHome();
	}

	/**
	 * Login user into backend part. Uses RBAC authentication.
	 * For get access to backend user must have 'admin' privelegies.
	 *
	 * For setup privelegies use console application:
	 * @see  /console/controllers/RolesController.php
	 *
	 * @return mixed
	 */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

	/**
	 * Build array of breadcrumbs
	 *
	 * @param int $id current category id
	 *
	 * @return array
	 */
	private function __makeBreadcrumbs($id){
		$Breadcrumbs = [];

		$Category = Categories::findOne($id);
		$Breadcrumbs[] = $Category->name;

		if ($Category->parentId === null){
			return $Breadcrumbs;
		}

		do{
			$Category = Categories::findOne($Category->parentId);
			$Breadcrumbs[] = ['label' => $Category->name, 'url' => $Category->slug];
		}while($Category->parentId != null);


		return array_reverse($Breadcrumbs);
	}

	/**
	 * Finds the Items model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param string $slug
	 *
	 * @return Category the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function _findModel($slug){
		if (($model = Categories::findOne(['slug' => $slug])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('Категория не найдена.');
		}
	}
}
