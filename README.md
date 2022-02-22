# Moovity Users

## Instalación
### Añada la DATABASE_URL en el fichero .env (o .env.local). Ejemplo:
```DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"```
### Ejecute las migraciones para crear lo necesario en la BD:
```bash
php bin/console doctrine:migrations:migrate
```
[ENLACE A EJEMPLOS ENDPOINTS](https://drive.google.com/file/d/1rdNcE5Dliy0asG3HUUvWG0ayxNfvaDXb/view?usp=sharing)
