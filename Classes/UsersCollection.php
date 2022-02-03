<?php

/**
 * Автор: Ермаченя Александр
 *
 * Дата реализации: 31.01.2022 13:11
 *
 * Дата изменения: 03.02.2022 13:00
 *
 * Утилита для работы с базой данных
*/

namespace Classes;

use PDO;
use Classes\User;

/**
 * UsersCollection
 * Класс для вывода списка и удаления пользователей.
 * Получает массив id пользователей и поддерживает выражения больше, меньше, не равно.
 * Использует методы класса User.
 */
class UsersCollection extends User
{
    private $idArr = [];

    /**
     * Сохраняет массив Id пользователей в переменную $idArr.
     *
     * @param array ...$params массив параметров значение которго содержит
     *              id пользователей. Последним параметром может быть условие.
     */
    public function __construct(...$params)
    {
        $config = array(
            'host'     => 'localhost',
            'name'     => 'users',
            'user'     => 'root',
            'password' => '',
        );
        $this->db = new PDO(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';' . 'charset=utf8;',
            $config['user'],
            $config['password']
        );
        $condition = false;
        if (count($params) === 1) {
            $id = $params;
        } else {
            $conditionVal = array_pop($params);
            if (is_string($conditionVal)) {
                $condition = $conditionVal;
                $id = $params;
            } else {
                array_push($params, $conditionVal);
                $id = $params;
            }
        }
        foreach ($id as $idKey => $valueId) {
            $DataArr = $condition ? $this->userIdList($valueId, $condition) : $this->userIdList($valueId);
            foreach ($DataArr as $dataKey => $dataValue) {
                if (!in_array($dataValue['id'], $this->idArr)) {
                    array_push($this->idArr, $dataValue['id']);
                }
            }
        }
    }

    public function userIdList($id, $cond = '=')
    {
        $cond = ($cond === '=' || $cond === '<' || $cond === '>' || $cond === '!=') ? $cond : '=';
        $query = $this->db->prepare("SELECT id FROM `users` WHERE id" . $cond . ":id");
        $query->execute(array(
            'id' => $id,
        ));
        $listArr = $query->fetchAll();
        return $listArr;
    }

    public function getList()
    {
        $list = array_map(function ($id) {
            $this->id = $id;
            $this->currentUser($this->id);
            return$this->userObject();
        }, $this->idArr);
        return $list;
    }

    public function deleteList()
    {
        array_map(function ($id) {
            return $this->deleteUser($id);
        }, $this->idArr);
    }
}
