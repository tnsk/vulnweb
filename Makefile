.PHONY: up down reset logs ps nuke rebuild

up:        ## build + başlat
	docker compose up -d --build

down:      ## durdur
	docker compose down

rebuild:   ## image'i yeniden build et + başlat
	docker compose up -d --build --force-recreate

reset:     ## lab DB seed reset (CLI)
	docker compose exec app php bin/reset.php

logs:      ## app loglarını izle
	docker compose logs -f app

ps:        ## container durumu
	docker compose ps

nuke:      ## her şeyi sil (volume dahil) — pristine
	docker compose down -v
