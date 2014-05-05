<?php

/**
 * DbTable_Articles
 * 
 * Работа с статьями
 * 
 * @author Александр Махомет aka San для http://zendframework.ru
 */
class DbTable_Articles extends Zend_Db_Table_Abstract 
{

    /**
     * Имя таблицы
     * @var string 
     */        
    protected $_name = 'articles';
    
    /**
     * Получить все статьи или одну
     *
     * @param int $articleId Идентификатор статьи
     * @return array
     */
    public function getArticles($articleId = null) 
    {

        // Создаем объект Zend_Db_Select
        $select = $this->getAdapter()->select()
            // Таблица из которой делается выборка
            ->from($this->_name)
            // Добавление таблицы с помощью join, указывается поле связи
            ->join('users', 'users.id = articles.author_id', array('name'))
            // Порядок сортировки
            ->order('id DESC')
            // Количество возвращаемых записей
            ->limit(2)
            ;

        if (!is_null($articleId)) {
            
            // Условие на выборку
            $select->where("articles.id = ?", $articleId); 
            // Выполнение запроса
            $stmt = $this->getAdapter()->query($select);
            // Получение данных
            $result = $stmt->fetch();


        }
        else {

            $stmt = $this->getAdapter()->query($select);
            // Получение массива данных
            $result = $stmt->fetchAll();    

        }

        return $result;

    }

}