# BankID Sign

## Prerekvizity
1. vytvořit účet a aplikaci v https://developer.bankid.cz/dashboard
2. vytvořit digitální certifikát (`bin/jwksgen.sh`) určený k podepisování a následně jeho JWKs reprezentaci vystavit na veřejně dostupné URL
3. v nastavení aplikace:
    1. Authorization code flow “On”
    2. Implicit flow “Off”
    3. Specifikovat JWKs URI (viz 2)

## Overview

Primární zdroj informací: https://developer.bankid.cz/docs/apis#sign-api

1. Vytvořit ROS (Sign POST /ros) (`examples/views/sign_document.php`)
2. Tělo požadavku je potřeba nejdříve podepsat privátním klíčem, který přísluší certifikátu, který jsem zveřejnili ve formátu JWKs na veřejné URL (viz prerekvizity 2)
3. Digitálně podepsané tělo je potřeba zašifrovat veřejnou částí klíče BankId platformy (pro sandbox: https://oidc.sandbox.bankid.cz/.well-known/jwks)
4. Z odpovědi získáme requestUri a uploadUri
5. Na upload URI nahraji požadovaný PDF dokument (POST, multipart/form-data, soubor specifikovaný v těle pod názvem “file”
6. Následně sestavuji authUri, do kterého předávám requestUri získané z odpovědi při vytváření ROS
7. Přesměrovávám uživatele na authUri (do procesu BankID)
8. Při návratu uživatele na redirectUri (specifikované v nastavení BankId portálu) získáme z odpovědi JWT, který ve struktuře “structuredScope.documentObject.documentUri” obsahuje URL ke stažení podepsaného dokumentu.
