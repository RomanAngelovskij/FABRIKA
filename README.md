Установка
------------

Установить и настроить Yii2 advaced
```
composer create-project --prefer-dist yiisoft/yii2-app-advanced yii-application
```

В composer.json уже прописаны необходимые пакеты:
https://github.com/2amigos/yii2-transliterator-helper
https://github.com/xjflyttp/yii2-uploadify-widget

Выполнить в консоли
```
yii migrate
```

```
yii migrate --migrationPath=@yii/rbac/migrations/
```

Для папки /backend/web/uploads установить права на запись

В /frontend/config/params.php для 'backendUrl' указать адрес бекэнда

Добавление пользователей
------------

1) Зарегистрировать нового пользователя по адресу http://frontend/site/signup

2) В консоли создать групы user и admin
```
yii roles/create user
yii roles/create admin
```

3) Для нового пользователя добавить права рои admin, командой
```
yii roles/add-admin <логин_нового_пользователя>
```

4) Залогиниться в бекэнд часть
