# Тестовое задание

## Установить зависимости
composer install

## Применить миграции 
php arisan migrate --seed

## Запустить сервер (порт:8000)
php artisan serve

## Запрос доступных слотов:
```bash
curl http://localhost:8000/api/slots/availability
```

## Создание холда
```bash
curl -X POST http://localhost:8000/api/slots/2/hold -H 'Idempotency-Key: c9006f2e-7cce-4efb-abb2-08a61091d469'
```
Повторный запрос будет отдавать холд с тем же id

## Подтверждение холда
```bash
curl -X POST http://localhost:8000/api/holds/2/confirm
```

## Отмена подтверждёного холда
```bash
curl -X DELETED http://localhost:8000/api/holds/2
```
