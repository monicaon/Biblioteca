<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

/**
 * Modelo para la colección de libros en MongoDB.
 */
class Book extends ActiveRecord
{
    
    /**
     * Devuelve el nombre de la colección en MongoDB.
     * 
     * @return string el nombre de la colección
     */
    public static function collectionName()
    {
        return 'books';
    }

    /**
     * Define los atributos del modelo.
     * 
     * @return array los nombres de los atributos
     */
    public function attributes()
    {
        return ['_id', 'title', 'authors', 'publication_year', 'description'];
    }

    /**
     * Define las reglas de validación para el modelo.
     * 
     * @return array las reglas de validación
     */
    public function rules()
    {
        return [
            [['title', 'authors', 'publication_year', 'description'], 'required', 'on' => 'create'],
            [['title', 'description'], 'string'],
            [['authors'], 'each', 'rule' => ['string']],
            [['publication_year'], 'integer'],
            [['authors'], 'validateAuthorsExist'],

            [['title', 'authors', 'publication_year', 'description'], 'required', 'on' => 'update'],
        ];
    }
    

    /**
     * Define los escenarios de validación para el modelo.
     * 
     * @return array los escenarios de validación
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'authors', 'publication_year', 'description'];
        $scenarios['update'] = ['title', 'authors', 'publication_year', 'description']; 
        return $scenarios;
    }

    
    /**
     * Define los campos que se devuelven en las respuestas JSON.
     * 
     * @return array los campos a devolver
     */
    public function fields()
    {
        return [
            '_id',
            'title',
            'authors' => function () {
                return $this->getAuthorInfo();
            },
            'publication_year',
            'description'
        ];
    }

    
    /**
     * Obtiene la información detallada de los autores asociados al libro.
     * 
     * @return array la información de los autores en formato estructurado
     */
    public function getAuthorInfo()
    {
        $authors = [];
        if (!empty($this->authors)) {
            foreach ($this->authors as $authorId) {
                $author = Author::findOne($authorId);
                if ($author) {
                    $authors[] = [
                        'id' => (string)$author->_id,
                        'name' => $author->name,
                        'birthdate' => $author->birthdate,
                    ];
                }
            }
        }
        return $authors;
    }

   
    /**
     * Valida la existencia de los autores asociados al libro.
     * 
     * @param string $attribute el nombre del atributo
     * @param array $params los parámetros adicionales de validación
     */
    public function validateAuthorsExist($attribute, $params)
    {
        $authors = (array) $this->$attribute;
        foreach ($authors as $authorId) {
            $author = Author::findOne($authorId);
            if (!$author) {
                $this->addError($attribute, 'El autor no existe en la base de datos.');
            }
        }
    }
}
