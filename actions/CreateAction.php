<?php
/**
 * @link http://www.euqol.com/
 * @copyright Copyright (c) 2015 Su Anli
 * @license http://www.euqol.com/license/
 */

namespace anli\helper\actions;

use Yii;
use yii\base\Action;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * This is the create action class.
 *
 * @author Su Anli <anli@euqol.com>
 * @since 1.2.0
 */
class CreateAction extends Action
{
    /**
     * @var mixed
     */
    public $model;

    /**
     * @var string
     */
    public $successMsg = 'You have created a record';

    /**
     * @var string
     */
    public $errorMsg = 'You have failed to create a record';

    /**
     * @var string
     */
    public $view = 'create';

    /**
     * @var array
     */
    public $defaultValues = [];

    /**
     * @var boolean
     */
    public $isSaveAndNew = false;

    /**
     * @var string
     */
    public $saveAndNewUrl = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!empty($this->defaultValues)) {
            $this->defaultValues = call_user_func($this->defaultValues);
            $this->assignDefaultValues();
        }

    }

    /**
     * Runs the action
     *
     * @return string result content
     */
    public function run()
    {
        $model = $this->model;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {

                if ($this->isSaveAndNew) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message' => 'saveAndNew',
                        'saveAndNewUrl' => $this->saveAndNewUrl,
                    ];
                }

                Yii::$app->getSession()->setFlash('success', $this->successMsg);
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'success',
                ];
            }

            Yii::$app->getSession()->setFlash('error', $this->errorMsg);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'error',
                'error' => print_r($model->getErrors()),
            ];
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && isset($_POST['ajax'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        return $this->controller->renderAjax($this->view, [
            'model' => $model
        ]);
    }

    /**
	 * Assign default values to model.
     * @return boolean
	 */
	protected function assignDefaultValues()
	{
		foreach ($this->defaultValues as $key => $value) {
			$this->model[$key] = $value;
		}

        return true;
	}
}
