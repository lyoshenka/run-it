#!/bin/bash

openssl genrsa -out ssl-key.pem 4096 && openssl req -x509 -new -nodes -key ssl-key.pem -out ssl-cert.pem -days 10000 -subj "/CN=YOURDOMAIN.COM"
