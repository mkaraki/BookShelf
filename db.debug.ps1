$docker_pwd = pwd.exe
docker run --rm -it -e MYSQL_ROOT_PASSWORD=password -e MYSQL_DATABASE=bookshelf -v "$docker_pwd/_debugtmp/db:/var/lib/mysql" -p 3306:3306 mariadb:10.6