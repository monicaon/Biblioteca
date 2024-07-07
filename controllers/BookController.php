<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use app\models\Book;
use yii\filters\auth\HttpBearerAuth;

/**
 * Controlador para manejar las acciones CRUD de libros.
 */
class BookController extends ActiveController
{

    /**
     * @var string nombre de la clase del modelo.
     */
    public $modelClass = 'app\models\Book';

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
     * Lista todos los libros.
     * 
     * @return array|Response los libros en formato JSON
     */
    public function actionIndex()
    {
        try {
            $books = Book::find()->all();
            return $books;
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al obtener la lista de los libros',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Muestra los detalles de un libro.
     * 
     * @param int $id el ID del libro
     * @return array|Response los detalles del libro en formato JSON
     */
    public function actionView($id)
    {
        try {
            $model = Book::findOne($id);

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
                    'message' => 'Libro no encontrado',
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al obtener los detalles del libro',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crea un nuevo libro.
     * 
     * @return array|Response los detalles del libro creado en formato JSON
     */
    public function actionCreate()
    {
        try {
            $model = new Book();
            $model->scenario = 'create';
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');

            if ($model->save()) {
                Yii::$app->response->statusCode = 201;
                return [
                    'status' => 'success',
                    'message' => 'Libro creado correctamente',
                    'data' => $model,
                ];
            } else {
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 'error',
                    'message' => 'Error al crear el libro',
                    'errors' => $model->errors,
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al crear el libro',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Actualiza un libro existente.
     * 
     * @param int $id el ID del libro
     * @return array|Response los detalles del libro actualizado en formato JSON
     */
    public function actionUpdate($id)
    {
        try {
            $model = Book::findOne($id);

            if ($model !== null) {
                $model->scenario = 'update';
                $model->load(Yii::$app->getRequest()->getBodyParams(), '');
                if ($model->save()) {
                    Yii::$app->response->statusCode = 200;
                    return [
                        'status' => 'success',
                        'message' => 'Libro actualizado correctamente',
                        'data' => $model,
                    ];
                } else {
                    Yii::$app->response->statusCode = 422;
                    return [
                        'status' => 'error',
                        'message' => 'Error al actualizar el libro',
                        'errors' => $model->errors,
                    ];
                }
            } else {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 'error',
                    'message' => 'Libro no encontrado',
                ];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al actualizar el libro',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Elimina un libro existente.
     * 
     * @param int $id el ID del libro
     * @return array|Response el mensaje de éxito en formato JSON
     */
    public function actionDelete($id)
    {
        try {
            $model = Book::findOne($id);

            if ($model !== null) {
                $model->delete();
                Yii::$app->response->statusCode = 200;
                return [
                    'status' => 'success',
                    'message' => 'Libro eliminado correctamente',
                ];
            } else {
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Libro no encontrado'];
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Error interno del servidor al eliminar el libro',
                'error' => $e->getMessage(),
            ];
        }
    }
}
