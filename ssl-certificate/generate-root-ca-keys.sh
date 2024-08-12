#!/bin/bash

if [ "$#" -ne 1 ]
then
  echo "Error: No domain name argument provided"
  echo "Usage: Provide a domain name as an argument"
  exit 1
fi

DOMAIN=$1

if [ ! -f rootCA.key ]; then

# Create root CA & Private key

  openssl req -x509 \
    -sha256 -days 356 \
    -nodes \
    -newkey rsa:2048 \
    -subj "/CN=${DOMAIN}/C=US/L=San Fransisco" \
    -keyout rootCA.key -out rootCA.crt 
fi