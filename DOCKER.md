# Running Sellu in Docker

The whole stack runs with Docker Compose: Laravel API + Nginx, MySQL, Redis, a
queue worker, and the two Nuxt front-ends (storefront + admin).

## Services & ports

| Service    | Description                         | Host URL / port          |
|------------|-------------------------------------|--------------------------|
| `nginx`    | Serves the Laravel API (PHP-FPM)    | http://localhost:8000    |
| `backend`  | PHP-FPM (Laravel 12)                | internal                 |
| `queue`    | `php artisan queue:work`            | internal                 |
| `mainpage` | Nuxt storefront                     | http://localhost:3000    |
| `admin`    | Nuxt admin dashboard                | http://localhost:3001    |
| `mysql`    | MySQL 8                             | localhost:3306           |
| `redis`    | Redis 7                             | localhost:6379           |

## Quick start

```bash
# (optional) customise ports / credentials
cp .env.docker.example .env

# build and start everything
docker compose up -d --build

# follow logs
docker compose logs -f backend
```

On first boot the backend container automatically:
- creates `backend/.env` from `.env.example` if missing,
- generates `APP_KEY`,
- waits for MySQL, then runs `php artisan migrate`,
- runs `php artisan storage:link`.

Then open:
- Storefront → http://localhost:3000
- Admin → http://localhost:3001
- API → http://localhost:8000/api

## Common commands

```bash
# run artisan commands
docker compose exec backend php artisan migrate:fresh --seed

# open a shell in the API container
docker compose exec backend bash

# rebuild a single service after changing its Dockerfile
docker compose build admin && docker compose up -d admin

# stop everything (keeps data)
docker compose down

# stop and wipe DB/redis volumes
docker compose down -v
```

## Notes

- **Backend code** is bind-mounted (`./backend` → `/var/www/html`), so PHP changes
  are picked up live. `vendor/` lives in a named volume built into the image — run
  `docker compose exec backend composer install` if you add packages.
- **Front-end apps** are built as production bundles inside their images. After
  changing `admin/` or `mainpage/` source, rebuild that service
  (`docker compose build admin`).
- The browser-facing `NUXT_PUBLIC_API_BASE` points at `http://localhost:8000/api`
  because the API is called from the user's browser, not container-to-container.
  Override it in `.env` if you publish the API on another host/port.
