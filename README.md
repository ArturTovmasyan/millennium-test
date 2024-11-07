# Запуск проекта

### Github Repository
* https://github.com/ArturTovmasyan/millennium-test

### Запустите Docker на хосте ###
### Выполните команду make в корневой директории приложения ###

* make up

### Если нет Docker на хосте ###
### Выполните команду make в корневой директории приложения ###

* make local

### Для работы с базами данных
* http://0.0.0.0:8081
* username - symfony
* password - symfony
* db - symfony

### API для получения/добавления продуктов

* Метод GET
* http://0.0.0.0:8080/api/user-orders/2
* id - идентификатор пользователя


* Метод POST
* http://0.0.0.0:8080/api/product/add
* Пример POST-данных:

```json
{
  "products": [
    {
      "title": "Ноутбук",
      "price": 2700
    },
    {
      "title": "Машина",
      "price": 75000
    }
  ]
}

