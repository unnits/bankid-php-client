#!/bin/bash

# Check if certificate file path is provided as first parameter
if [ -n "$1" ] && [ -f "$1" ]; then
  cert_file="$1"
else
  # Prompt user for subject details
  echo "Enter subject details (CN): "
  read CN

  # Generate new x509 self-signed certificate
  openssl req \
    -x509 \
    -newkey rsa:4096 \
    -keyout key.private.pem \
    -out cert.pem \
    -days 3650 \
    -nodes \
    -subj "/CN=$CN" \
    -addext "extendedKeyUsage=codeSigning" \
    -addext "keyUsage=digitalSignature"

  cert_file="cert.pem"
fi

# Output public key
openssl rsa -in key.private.pem -outform PEM -pubout -out key.public.pem

# Generate JSON Web Key representation
keyMeta="$(cat key.public.pem | openssl asn1parse -noout -inform PEM -out /dev/stdout)"
e=$(echo "${keyMeta}" | tail -c +5 | head -c 3 | xxd -p)
n=$(echo "${keyMeta}" | tail -c +8 | head -c -1 | xxd -p)
x5c=$(cat cert.pem | sed '1d;$d' | tr -d '\n')
kid=$(uuidgen)
x5t=$(cat key.public.pem | openssl pkey -pubin -outform DER | openssl dgst -sha256 -binary | openssl enc -base64)

# Output JSON Web Key representation
echo "{ \"kty\": \"RSA\", \"x5t#S256\": \"$x5t\", \"e\": \"$e\", \"use\": \"sig\", \"kid\": \"$kid\", \"x5c\": [\"$x5c\"], \"n\": \"$n\" }"
