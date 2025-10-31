# Тестовое задание

## Установить зависимости
```bash
composer install
```

## Применить миграции
```bash
php arisan migrate --seed
```

## Запустить сервер (порт:8000)
```bash
php artisan serve
```

## Запрос доступных слотов:
```bash
curl http://localhost:8000/api/slots/availability
```

## Создание холда
```bash
curl -X POST http://localhost:8000/api/slots/1/hold -H 'Idempotency-Key: c9006f2e-7cce-4efb-abb2-08a61091d469'
```
Повторный запрос будет отдавать холд с тем же id

## Подтверждение холда
```bash
curl -X POST http://localhost:8000/api/holds/1/confirm
```

## Отмена подтверждёного холда
```bash
curl -X DELETE http://localhost:8000/api/holds/1
```
