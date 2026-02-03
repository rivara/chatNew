**Proyecto Laravel/Vue y soketi con eventos** 

Este proyecto carece de bd y para hacerlos funcionar tienes que hacer los siguientes pasos

1.- primero despliegalo en un wsl o consola linux en var/www/

2.- abrir una consola y compilar 

a) rutas

docker exec -it chatnew-app-1 php artisan optimize

b)npm 

docker exec -it chatnew-app-1 npm run build

c)colas

docker exec -it chatnew-app-1 php artisan queue:work
