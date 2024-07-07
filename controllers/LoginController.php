<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;

/**
 * Controlador para la gestión de inicio de sesión de usuarios.
 *
 * Este controlador maneja las acciones relacionadas con el inicio de sesión
 * de usuarios, como autenticar credenciales y generar tokens de acceso.
 *
 * @package app\controllers
 */
class LoginController extends Controller
{
    /**
     * Configura los comportamientos del controlador.
     *
     * Establece el formato de respuesta a JSON para todas las acciones.
     *
     * @return array Configuración de comportamientos
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    /**
     * Acción para realizar el inicio de sesión.
     *
     * Intenta autenticar al usuario con las credenciales proporcionadas
     * (nombre de usuario y contraseña). Si las credenciales son válidas,
     * se genera un nuevo token de acceso para el usuario.
     *
     * @return array Resultado del inicio de sesión
     */
    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        // Busca al usuario por nombre de usuario
        $user = User::findOne(['username' => $username]);

        // Valida las credenciales del usuario
        if ($user && $user->validatePassword($password)) {
            // Genera un nuevo token de acceso para el usuario
            $user->generateAccessToken();

            // Guarda el usuario con el nuevo token de acceso
            if ($user->save()) {
                return ['message' => 'Ingreso correcto', 'access_token' => $user->access_token];
            }
        }

        // Si las credenciales no son válidas, devuelve un error de acceso no autorizado
        Yii::$app->response->statusCode = 401;
        return ['error' => 'Datos incorrectos'];
    }
}
