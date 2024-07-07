<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\web\Response;
use yii\base\InvalidParamException;
use app\models\User;

/**
 * Controlador para la gestión de registro de usuarios.
 *
 * Este controlador maneja la acción de registro de nuevos usuarios,
 * validando los datos proporcionados y registrando al usuario en el sistema.
 *
 * @package app\controllers
 */
class RegisterController extends Controller
{
    /**
     * Acción para registrar un nuevo usuario.
     *
     * Se espera recibir el nombre de usuario y la contraseña a través
     * de una solicitud POST. Verifica si el nombre de usuario ya existe,
     * luego crea un nuevo usuario, establece la contraseña y genera un
     * token de acceso para el nuevo usuario registrado.
     *
     * @return array Resultado del registro del usuario
     */
    public function actionRegister()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $username = \Yii::$app->request->post('username');
        $password = \Yii::$app->request->post('password');

        // Valida que se hayan proporcionado nombre de usuario y contraseña
        if (empty($username) || empty($password)) {
            \Yii::$app->response->statusCode = 400; 
            return ['error' => 'El nombre de usuario y la contraseña son requeridos'];
        }

        // Verifica si el nombre de usuario ya está en uso
        $existingUser = User::findOne(['username' => $username]);
        if ($existingUser) {
            \Yii::$app->response->statusCode = 409; 
            return ['error' => 'El nombre de usuario ya está en uso'];
        }

        // Crea un nuevo modelo de usuario
        $user = new User();
        $user->username = $username;
        $user->setPassword($password);
        $user->generateAccessToken();

        // Intenta guardar el nuevo usuario en la base de datos
        if ($user->save()) {
            return ['message' => 'Usuario registrado exitosamente', 'access_token' => $user->access_token];
        } else {
            \Yii::$app->response->statusCode = 500; 
            return ['error' => 'Hubo un problema al registrar el usuario'];
        }
    }
}
