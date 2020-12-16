```
docker-compose up -d
```

## Nginx + PHP-FPM

### 201

```
--- ~ » curl -H 'Host: testheader.lh' -D - 'http://127.0.0.1:8888/index.php?code=201'
HTTP/1.1 201 ololo
Server: nginx/1.19.6
Date: Wed, 16 Dec 2020 23:05:34 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/7.4.13

content
```

### 200

```
--- ~ » curl -H 'Host: testheader.lh' -D - 'http://127.0.0.1:8888/index.php?code=200'
HTTP/1.1 200 OK
Server: nginx/1.19.6
Date: Wed, 16 Dec 2020 23:06:11 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/7.4.13

content
```

## php -S

### 201

```
--- ~ » curl -D - 'http://127.0.0.1:8889/index.php?code=201'
HTTP/1.1 201 ololo
Host: 127.0.0.1:8889
Date: Wed, 16 Dec 2020 23:07:14 GMT
Connection: close
X-Powered-By: PHP/7.4.13
Content-type: text/html; charset=UTF-8

content
```

### 200

```
--- ~ » curl -D - 'http://127.0.0.1:8889/index.php?code=200'
HTTP/1.1 200 ololo
Host: 127.0.0.1:8889
Date: Wed, 16 Dec 2020 23:07:27 GMT
Connection: close
X-Powered-By: PHP/7.4.13
Content-type: text/html; charset=UTF-8

content
```

## Additional evidence for nginx + php-fpm

#### excerpt from tcpdump

##### ad-hoc installation of tcpdump

```
docker-compose exec backend sh
apk update
apk add tcpdump
tcpdump -i eth0 port 9000 -X
```

##### part of actual transmission for 200 response
```
22:46:29.803511 IP 2bd3a7110946.9000 > 200-header_frontend_1.200-header_default.55310: Flags [P.], seq 1:105, ack 569, win 505, options [nop,nop,TS val 1049935503 ecr 708172859], length 104
        0x0000:  4500 009c d398 4000 4006 0e88 ac1b 0002  E.....@.@.......
        0x0010:  ac1b 0003 2328 d80e 097e ef14 4cfb f412  ....#(...~..L...
        0x0020:  8018 01f9 58ca 0000 0101 080a 3e94 be8f  ....X.......>...
        0x0030:  2a35 dc3b 0106 0001 004c 0400 582d 506f  *5.;.....L..X-Po < no cgi status field
        0x0040:  7765 7265 642d 4279 3a20 5048 502f 372e  wered-By:.PHP/7.
        0x0050:  342e 3133 0d0a 436f 6e74 656e 742d 7479  4.13..Content-ty
        0x0060:  7065 3a20 7465 7874 2f68 746d 6c3b 2063  pe:.text/html;.c
        0x0070:  6861 7273 6574 3d55 5446 2d38 0d0a 0d0a  harset=UTF-8....
        0x0080:  636f 6e74 656e 740a 0000 0000 0103 0001  content.........
        0x0090:  0008 0000 0000 0000 0074 0a00            .........t..  
```

the same from debug log of nginx point of view (times are inconsistent, that's ok - commands were performed at different time)

```
backend_1    | 172.27.0.3 -  16/Dec/2020:23:20:51 +0000 "GET /index.php" 200
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 06
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 00
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 01
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 00
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 4C
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 04
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record byte: 00
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi record length: 76
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi parser: 0
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi header: "X-Powered-By: PHP/7.4.13"
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi parser: 0
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi header: "Content-type: text/html; charset=UTF-8"
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi parser: 1
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 http fastcgi header done
frontend_1   | 2020/12/16 23:20:51 [debug] 21#21: *1 HTTP/1.1 200 OK
frontend_1   | Server: nginx/1.19.6
frontend_1   | Date: Wed, 16 Dec 2020 23:20:51 GMT
frontend_1   | Content-Type: text/html; charset=UTF-8
frontend_1   | Transfer-Encoding: chunked
frontend_1   | Connection: keep-alive
frontend_1   | X-Powered-By: PHP/7.4.13
```

##### part of actual transmission for 201 response

```
22:46:42.600117 IP 2bd3a7110946.9000 > 200-header_frontend_1.200-header_default.55318: Flags [P.], seq 1:121, ack 569, win 505, options [nop,nop,TS val 1049948299 ecr 708185655], length 120
        0x0000:  4500 00ac 49ca 4000 4006 9846 ac1b 0002  E...I.@.@..F....
        0x0010:  ac1b 0003 2328 d816 f151 fb79 7ec7 ce87  ....#(...Q.y~...
        0x0020:  8018 01f9 58da 0000 0101 080a 3e94 f08b  ....X.......>...
        0x0030:  2a36 0e37 0106 0001 005f 0100 5374 6174  *6.7....._..Stat  < actual cgi status field
        0x0040:  7573 3a20 3230 3120 6f6c 6f6c 6f0d 0a58  us:.201.ololo..X
        0x0050:  2d50 6f77 6572 6564 2d42 793a 2050 4850  -Powered-By:.PHP
        0x0060:  2f37 2e34 2e31 330d 0a43 6f6e 7465 6e74  /7.4.13..Content
        0x0070:  2d74 7970 653a 2074 6578 742f 6874 6d6c  -type:.text/html
        0x0080:  3b20 6368 6172 7365 743d 5554 462d 380d  ;.charset=UTF-8.
        0x0090:  0a0d 0a63 6f6e 7465 6e74 0a00 0103 0001  ...content......
        0x00a0:  0008 0000 0000 0000 0000 0000            ............
```

the same from nginx debug log point of view
```
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http upstream request: "/index.php?code=201"
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http upstream process header
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 malloc: 00005615BD68D100:4096
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 recv: eof:0, avail:-1
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 recv: fd:21 120 of 4096
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 01
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 06
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 00
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 01
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 00
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 5F
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 01
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record byte: 00
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi record length: 95
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi parser: 0
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi header: "Status: 201 ololo"
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi parser: 0
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi header: "X-Powered-By: PHP/7.4.13"
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi parser: 0
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi header: "Content-type: text/html; charset=UTF-8"
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi parser: 1
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 http fastcgi header done
frontend_1   | 2020/12/16 23:24:34 [debug] 21#21: *3 HTTP/1.1 201 ololo
frontend_1   | Server: nginx/1.19.6
frontend_1   | Date: Wed, 16 Dec 2020 23:24:34 GMT
frontend_1   | Content-Type: text/html; charset=UTF-8
frontend_1   | Transfer-Encoding: chunked
frontend_1   | Connection: keep-alive
frontend_1   | X-Powered-By: PHP/7.4.13
frontend_1   | 

```
