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

use Classes\User;
use Classes\UsersCollection;

spl_autoload_register(function ($class) {
    $pach = str_replace('\\', '/', $class . '.php');
    if (file_exists($pach)) {
        require $pach;
    }
});

if (class_exists('Classes\User')) {
    //выводи конкретного пользователя по id
    $runUsers = new User(1);
    print_r($runUsers->userObject());

     //добавляет пользователя
    $newUsers = new User('Сергей', 'Попов', '1965-01-12', 1, 'Минск');

    //добавляет список пользователей с поддержкой условия
    $runUsers = new UsersCollection(2, 3, ">");
    print_r($runUsers->getList());

    // удаляет список
    $UsersCollection = new UsersCollection(5, 6);
    $UsersCollection->deleteList();
}
