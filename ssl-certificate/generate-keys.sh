#! /bin/bash

# print usage

if [ -z "$1" ]; then 

    echo "USAGE: $0 [DOMAIN_NAME]"
    echo ""
    echo "This will generate a non-secure self-signed wildcard certificate for given domain."
    echo "This should only be used in a development environment."
    exit
fi

if [ ! -f rootCA.key ]; then
  echo "Error: No rootCA key found"
  echo "Usage: generate rootCA key pairs with generate-root-ca-keys.sh script"
  exit 1
fi

DOMAIN=$1

# Add wildcard
WILDCARD="*.$DOMAIN"

# Generate Private key 

openssl genrsa -out ${DOMAIN}.key 2048

# Create csr conf

cat > csr.conf <<EOF
[ req ]
default_bits = 2048
prompt = no
default_md = sha256
req_extensions = req_ext
distinguished_name = dn

[ dn ]
C = US
ST = California
L = San Fransisco
O = Local Developement
OU = Local Developement
CN = ${WILDCARD}

[ req_ext ]
subjectAltName = @alt_names

[ alt_names ]
DNS.1 = ${WILDCARD}

EOF

# create CSR request using private key

openssl req -new -key ${DOMAIN}.key -out ${DOMAIN}.csr -config csr.conf

# Create a external config file for the certificate

cat > cert.conf <<EOF

authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names

[alt_names]
DNS.1 = ${WILDCARD}

EOF

# Create SSl with self signed CA

openssl x509 -req \
    -in ${DOMAIN}.csr \
    -CA rootCA.crt -CAkey rootCA.key \
    -CAcreateserial -out ${DOMAIN}.crt \
    -days 365 \
    -sha256 -extfile cert.conf

rm "$DOMAIN.csr" && rm cert.conf && rm csr.conf

echo ""
echo "Next manual steps:"
echo "- Use $DOMAIN.crt and $DOMAIN.key to configure Apache/nginx"
echo "- Import rootCA.crt into Chrome settings: chrome://settings/certificates > tab 'Authorities'"