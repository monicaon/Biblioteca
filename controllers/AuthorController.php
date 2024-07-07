<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use app\models\Author;
use yii\filters\auth\HttpBearerAuth;

/**
 * Controlador para manejar las acciones CRUD de autores.
 */
class AuthorController extends ActiveController
{

    /**
     * @var string nombre de la clase del modelo.
     */
    public $modelClass = 'app\models\Author';

    /**
     * Configura los comportamientos del controlador.
     * 
     * Se agrega autenticación mediante token Bearer y se configura el formato de respuesta a JSON.
     * 
     * @return array los comportamientos configurados
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        
        return $behaviors;
    }

    /**
     * Configura las acciones del controlador.
     * 
     * Deshabilita las acciones CRUD por defecto excepto 'index', 'view', 'create', 'update' y 'delete'.
     * 
     * @return array las acciones configuradas
     */
    public function actions()
    {
        $actions = parent::actions();
        
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view'], $actions['index']);
        
        return $actions;
    }

    /**
     * Lista todos los autores.
     * 
     * @return array|Response los autores en formato JSON
     */
    public function actionIndex()
    {
        try {
            $authors = Author::find()->all();
            return $authors;
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al obtener la lista de los autores',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Muestra los detalles de un autor.
     * 
     * @param int $id el ID del autor
     * @return array|Response los detalles del autor en formato JSON
     */
    public function actionView($id)
    {
        try {
            $model = Author::findOne($id);

            if ($model !== null) {
                Yii::$app->response->statusCode = 200;
                return [
                    'status' => 'success',
                    'data' => $model,
                ];
            } else {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 'error',
                    'message' => 'Autor no encontrado',
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al obtener los detalles del autor',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crea un nuevo autor.
     * 
     * @return array|Response los detalles del autor creado en formato JSON
     */
    public function actionCreate()
    {
        try {
            $model = new Author();
            $model->scenario = 'create';

            $model->load(Yii::$app->getRequest()->getBodyParams(), '');

            if ($model->save()) {
                Yii::$app->response->statusCode = 201;
                return [
                    'status' => 'success',
                    'message' => 'Autor creado correctamente',
                    'data' => $model,
                ];
            } else {
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 'error',
                    'message' => 'Error al crear el autor',
                    'errors' => $model->errors,
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al crear el autor',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Actualiza un autor existente.
     * 
     * @param int $id el ID del autor
     * @return array|Response los detalles del autor actualizado en formato JSON
     */
    public function actionUpdate($id)
    {
        try {
            $model = Author::findOne($id);

            if ($model !== null) {
                $model->scenario = 'update';
                $model->load(Yii::$app->getRequest()->getBodyParams(), '');
                if ($model->save()) {
                    Yii::$app->response->statusCode = 200;
                    return [
                        'status' => 'success',
                        'message' => 'Autor actualizado correctamente',
                        'data' => $model,
                    ];
                } else {
                    Yii::$app->response->statusCode = 422;
                    return [
                        'status' => 'error',
                        'message' => 'Error al actualizar el autor',
                        'errors' => $model->errors,
                    ];
                }
            } else {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 'error',
                    'message' => 'Autor no encontrado',
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al actualizar el autor',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Elimina un autor existente.
     * 
     * @param int $id el ID del autor
     * @return array|Response el mensaje de éxito en formato JSON
     */
    public function actionDelete($id)
    {
        try {
            $model = Author::findOne($id);

            if ($model !== null) {
                $model->delete();
                Yii::$app->response->statusCode = 200;
                return [
                    'status' => 'success',
                    'message' => 'Autor eliminado correctamente',
                ];
            } else {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 'error',
                    'message' => 'Autor no encontrado',
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al eliminar el autor',
                'error' => $e->getMessage(),
            ];
        }
    }
}
