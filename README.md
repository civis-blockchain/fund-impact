# Fund-impact

* Install docker-compose
```
apt-get udate
apt-get install docker-compose
```
* Clone repo

```
git clone https://github.com/civis-blockchain/fund-impact.git
cd fund-impact
```

* Configuration
```
echo MYSQL_HOST=db  >> .env
echo MYSQL_ROOT_PASSWD=$(cat /dev/urandom | xxd | head -n 1 | cut -b 10-49 | sed "s/ //g")  >> .env
echo MYSQL_DATABASE=fund-impact >> .env
echo MYSQL_USERNAME=fund-impact >> .env
echo MYSQL_PASSWORD=$(cat /dev/urandom | xxd | head -n 1 | cut -b 10-49 | sed "s/ //g") >> .env
```

 * Start
```
docker-compose -f docker-compose-prod.yml up -d
```

http://localhost:7000

 * Stop
```
docker-compose stop
```

 * Reinit all
```
docker-compose down -v
```


## Release

```
VERSION=0.1.2
git tag $VERSION
git checkout ${VERSION}
make build push tag-latest -e VERSION=${VERSION}
git push origin ${VERSION}
```
