<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

/**
 * Modelo para la colección de autores en MongoDB.
 */
class Author extends ActiveRecord
{
    
    /**
     * Devuelve el nombre de la colección en MongoDB.
     * 
     * @return string el nombre de la colección
     */
    public static function collectionName()
    {
        return 'authors';
    }


    /**
     * Define los atributos del modelo.
     * 
     * @return array los nombres de los atributos
     */
    public function attributes()
    {
        return ['_id', 'name', 'birthdate', 'books'];
    }

    /**
     * Define las reglas de validación para el modelo.
     * 
     * @return array las reglas de validación
     */
    public function rules()
    {
        return [
            [['name', 'birthdate'], 'required', 'on' => ['create']],
            [['name'], 'string'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
            [['books'], 'each', 'rule' => ['string']],
            [['books'], 'validateBooksExist'],
            [['name', 'birthdate'], 'required', 'on' => ['update']],
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
        $scenarios['create'] = ['name', 'birthdate', 'books'];
        $scenarios['update'] = ['name', 'birthdate', 'books'];
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
            'name',
            'birthdate',
            'books' => function () {
                return $this->getBookInfo();
            }
        ];
    }

    /**
     * Obtiene la información detallada de los libros asociados al autor.
     * 
     * @return array la información de los libros en formato estructurado
     */
    public function getBookInfo()
    {
        $books = [];
        if (!empty($this->books)) {
            foreach ($this->books as $bookId) {
                $book = Book::findOne($bookId);
                if ($book) {
                    $books[] = [
                        'id' => (string)$book->_id,
                        'title' => $book->title,
                        'publication_year' => $book->publication_year,
                        'description' => $book->description,
                    ];
                }
            }
        }
        return $books;
    }

    /**
     * Valida la existencia de los libros asociados al autor.
     * 
     * @param string $attribute el nombre del atributo
     * @param array $params los parámetros adicionales de validación
     */
    public function validateBooksExist($attribute, $params)
    {
        $books = (array) $this->$attribute;
        foreach ($books as $bookId) {
            $book = Book::findOne($bookId);
            if (!$book) {
                $this->addError($attribute, 'El libro no existe en la base de datos');
            }
        }
    }

}
