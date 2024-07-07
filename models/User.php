<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Modelo para la gestión de usuarios.
 *
 * Este modelo maneja la autenticación y la gestión de usuarios,
 * implementando la interfaz IdentityInterface.
 *
 * @property string $_id ID del usuario
 * @property string $username Nombre de usuario
 * @property string $auth_key Clave de autenticación
 * @property string $access_token Token de acceso
 * @property string $password_hash Hash de la contraseña
 * @property integer $token_expiration Tiempo de expiración del token de acceso
 *
 * @package app\models
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Devuelve el nombre de la colección MongoDB utilizada para los usuarios.
     *
     * @return string Nombre de la colección
     */
    public static function collectionName()
    {
        return 'users';
    }

    /**
     * Define los atributos del modelo.
     *
     * @return array Lista de atributos del modelo
     */
    public function attributes()
    {
        return ['_id', 'username', 'auth_key', 'access_token', 'password_hash', 'token_expiration'];
    }

    /**
     * Define las reglas de validación para los atributos del modelo.
     *
     * @return array Reglas de validación
     */
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['auth_key', 'access_token'], 'string'],
            [['token_expiration'], 'integer'],
        ];
    }

    /**
     * Encuentra una identidad de usuario por su ID.
     *
     * @param string $id ID del usuario
     * @return User|null Usuario encontrado o null si no existe
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Encuentra una identidad de usuario por su token de acceso.
     *
     * @param string $token Token de acceso
     * @param mixed $type Tipo de token (no utilizado aquí)
     * @return User|null Usuario encontrado o null si no existe o el token ha expirado
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::findOne(['access_token' => $token]);
        if ($user && $user->token_expiration > time()) {
            return $user;
        }
        return null;
    }

    /**
     * Devuelve el ID del usuario.
     *
     * @return string ID del usuario
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Devuelve la clave de autenticación del usuario.
     *
     * @return string Clave de autenticación
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Valida la clave de autenticación.
     *
     * @param string $authKey Clave de autenticación a validar
     * @return bool Si la clave de autenticación es válida
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Valida la contraseña del usuario.
     *
     * @param string $password Contraseña a validar
     * @return bool Si la contraseña es válida
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Establece la contraseña del usuario.
     *
     * @param string $password Nueva contraseña a establecer
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Genera una nueva clave de autenticación para el usuario.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Genera un nuevo token de acceso para el usuario.
     * Establece el tiempo de expiración a 30 minutos.
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->token_expiration = time() + 1800; // Expira en 30 minutos 
    }
}
