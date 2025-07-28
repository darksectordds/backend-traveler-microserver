## Детальная информация о ТЗ

<details>
# Тестовое задание для PHP-разработчика

## Задача

Разработать **бэкенд-сервис** для оценки путешественниками достопримечательностей.  
Сервис должен предоставлять REST API, позволяющее:

- управлять сущностями (создание, редактирование, удаление, получение данных)
- фильтровать и сортировать выборки
- обеспечивать хранение и обработку оценок достопримечательностей

## Сущности

### 1. Достопримечательность (`attraction`)
- `id` (уникальный идентификатор)
- `name` (название, строка)
- `distance_from_center` (удалённость от центра города, число)
- `city_id` (связь с городом)

### 2. Город (`city`)
- `id` (уникальный идентификатор)
- `name` (название, строка)

### 3. Путешественник (`traveler`)
- `id` (уникальный идентификатор)
- `name` (имя, строка)

### 4. Оценка (`rating`)
- `id` (уникальный идентификатор)
- `traveler_id` (связь с путешественником)
- `attraction_id` (связь с достопримечательностью)
- `score` (оценка, число от 1 до 5)

## API

### Пример эндпоинтов

- `/cities` – получить список городов
- `/cities` – создать город
- `/cities/{id}` – получить информацию о городе
- `/cities/{id}` – обновить данные города
- `/cities/{id}` – удалить город

## Требования к реализации

### 1. Архитектура и код
- **ООП** (чистый, структурированный код)
- **RESTful API** (чёткая структура эндпоинтов, соответствие методам HTTP)
- Чистая архитектура (разделение логики на слои)
- Обработка ошибок (валидные HTTP-ответы, JSON-формат, сообщения об ошибках)
- Читабельность кода

### 2. Технологии
- **PHP 8.0+** (использование новых возможностей)
- База данных на выбор
- Разрешены **небольшие** фреймворки для работы с базой или роутингом
- **Не использовать "громоздкие" фреймворки по типу Laravel, Yii, Symfony**
- **Composer** для управления зависимостями
- Использование **автозагрузки (PSR-4)**
</details>


### Решение: REST API для оценки достопримечательностей

В решении использовалось некоторые компоненты из `Slim - PHP-микрофреймворк`.
В данном случае только `Slim\Http\ServerRequest`, `Slim\Http\Response` и `Slim\Factory\AppFactory`. Весь остальной код
был выполнен на чистом ООП, с использованием PDO и некоторыми своими решениями под микросервисы, как например `SimpleQueryBuilder`
для создания более гибкой системы фильтрации.

**Структура проекта**:
```
app/
├── Controllers/
├── Database/
├── Models/
├── Repositories/
├── Services/
├── Database/
├── Exceptions/
├── Validations/
config/
migrations/
public/
vendor/
composer.json
.env
```

**Технологический стек**:
- PHP 8.1
- SQLite (легковесная БД)
- Slim (микро-фреймворк для роутинга)
- PDO (работа с базой данных)
- Dotenv (управление окружением)
- Composer (управление зависимостями)

#### Инструкция по запуску:

1. Установить зависимости:
```bash
composer install
```

2. Настроить окружение (файл `.env`):
```
DB_PATH=storage/database.sqlite
```

3. Запустить миграции:
```bash
php migrations/create_tables.php
```

4. Запустить встроенный сервер:
```bash
php -S localhost:8080 -t public
```
#### Routes:
```bash
# Routes: Городов
GET '/cities'
POST '/cities'
GET '/cities/{id}'
PUT '/cities/{id}'
DELETE '/cities/{id}'

# Routes: Путешественники
GET '/travelers'
POST '/travelers'
GET '/travelers/{id}'
PUT '/travelers/{id}'
DELETE '/travelers/{id}'

# Routes: Достопримечательности
GET '/attractions'
POST '/attractions'
GET '/attractions/{id}'
PUT '/attractions/{id}'
DELETE '/attractions/{id}'

# Routes: Оценка
GET '/rating'
POST '/rating'
GET '/rating/{id}'
PUT '/rating/{id}'
DELETE '/rating/{id}'
```

#### Примеры запросов:
**Pegination**: GET-коллекции имеют постраничный вывод

```bash
http://localhost:8080/attractions?city_id=2&page=1&limit=2
```


**Создание города**:
```bash
curl -X POST http://localhost:8080/cities \
  -H "Content-Type: application/json" \
  -d '{"name": "Paris"}'
```

**Добавление оценки**:
```bash
curl -X POST http://localhost:8080/ratings \
  -H "Content-Type: application/json" \
  -d '{
    "traveler_id": 1,
    "attraction_id": 5,
    "score": 4
  }'
```

**Фильтрация достопримечательностей**:
```bash
curl "http://localhost:8080/attractions?distance_from_center=>100"
```
По дистанции

| distance_from_center | Шаблон          | Пример |
|----------------------|-----------------|--------|
| меньше               | <{число}        | <100   |
| больше               | >{число}        | >100   |
| между                | {число}-{число} | 0-100  |

По городу
```bash
curl "http://localhost:8080/attractions?city_id=100"
```
**Сортировка достопримечательностей**:
```bash
curl "http://localhost:8080/attractions?sort_by={column}"
```
Возможные значения:
* `id`
* `name`
* `distance_from_center`
* `city_id`


Решение обеспечивает все требования: чистый ООП-код, RESTful API, валидацию данных, обработку ошибок и гибкую систему фильтрации с использованием только легковесных библиотек.

Чтобы сделать решение еще более чистым, то можно полностью отказаться от `Slim\Http\ServerRequest`, `Slim\Http\Response` и `Slim\Factory\AppFactory`, который используется в решении.