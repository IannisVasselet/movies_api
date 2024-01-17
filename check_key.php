<?php
require __DIR__ . '/vendor/autoload.php';

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;

// Définir les clés
$privateKeyContent = <<<EOD
-----BEGIN ENCRYPTED PRIVATE KEY-----
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
-----END ENCRYPTED PRIVATE KEY-----
EOD;

$publicKeyContent = <<<EOD
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAn9qnfveRxY0zC8r+j8Ji
s1SE1RaDOP2JreHycRprrOBsDJno2dZ1bNyHofH2LfJm74RPCT/ztXzx73D4cnob
5tNDYetQSyg/DlKuWNAit77YMVVrS9+ahaFURDUBM2ZVjNmVss+fSJiWUQW6ARSR
JwR0yuU/3watJYLwJ6s2KI/lZbUElD7e3upSZPUPYZJGCv9cj+7LlgcDZi7nuLNY
T6aIB4qxzFR0KNJBexqZ/r52VoeW+343dhh0SokLeggxEqhNVtDGlpgXQzezI2tr
LPdBXFEPLlZH/0v+llOMBc44L415K70WnS98zQXGrcPFv+1XYK3MPKFpCJBWHgWw
PfX9uDbFxWXDN0r4AWDuN87bBpnc4vnCvHfk0pPqMb/4QluPM72dReikbIv8VlLU
BIy94KVq06FeHIVJVI1l3NeKWZTz4PbY/Jsd9vkhMMpbdP3/j2uZvQ+e64FviwCo
SF7XCb1FdfpjXOa7O+lRn79SdIrgobSD05+JpVHBA+/ZPO4fZToiSvJchPSGOepl
LIS7nPgwgLXE5S3bbKovW3uYXiXvl+xjtNvcdHIg2Wycres/CpMqEmwgx1OmDzlV
HFk3YPf6yKpauFgZowQtjXyosULI2SJ2lC0i3dGggX93zwtugBOXqCCwXAzPwpaC
GPDnBmRjj7ECYF8xRrcEX00CAwEAAQ==
-----END PUBLIC KEY-----
EOD;
$passphrase='11d76450fad5c925ab6b8982f7022d8be9e1aa0a7f18e9667572480028b17cff';
// Créer une configuration avec la clé privée pour signer le token
$config = Configuration::forAsymmetricSigner(
    new Sha256(),
    InMemory::encrypted($privateKeyContent, $passphrase),
    InMemory::plainText($publicKeyContent)
);

// Créer un token
$token = $config->builder()
    ->withClaim('test', 'test')
    ->getToken($config->signer(), $config->signingKey());

try {
    // Essayer de parser et de vérifier le token
    $parsedToken = $config->parser()->parse((string) $token);
    assert($parsedToken instanceof Plain);

    $validator = new Validator();
    $constraint = new SignedWith($config->signer(), $config->verificationKey());

    if ($validator->validate($parsedToken, $constraint)) {
        echo "Les clés correspondent\n";
    } else {
        echo "Les clés ne correspondent pas\n";
    }
} catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}