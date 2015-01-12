#!/bin/bash

openssl genrsa -out runit-ssl-key.pem 4096 && openssl req -x509 -new -nodes -key runit-ssl-key.pem -out runit-ssl-cert.pem -days 10000 -subj "/CN=yourdomain.com"
