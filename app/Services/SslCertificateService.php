<?php

namespace App\Services;

use Illuminate\Http\Request;

class SslCertificateService
{
    private const CERT_DIR = 'app/ssl';
    private const CERT_FILE = 'cursoplus.crt';
    private const KEY_FILE = 'cursoplus.key';

    /**
     * Obtiene el diagnóstico completo de SSL/TLS y el certificado X.509 activo.
     */
    public function obtenerEstado(?Request $request = null): array
    {
        $certLocalPath = $this->rutaCertificadoActivo();
        $certInfo = null;
        $pemPublico = null;
        $origenCert = 'Sin certificado generado aún';

        if ($certLocalPath && is_readable($certLocalPath)) {
            $contenido = @file_get_contents($certLocalPath);
            if ($contenido) {
                $certInfo = $this->parsearCertificadoPem($contenido);
                $pemPublico = trim($contenido);
                $origenCert = str_contains(str_replace('\\', '/', $certLocalPath), 'storage/app/ssl')
                    ? 'Certificado generado por Curso+ (storage/app/ssl/cursoplus.crt)'
                    : 'Certificado SSL de Apache/XAMPP (' . basename($certLocalPath) . ')';
            }
        }

        $sslDirPath = str_replace('\\', '/', storage_path(self::CERT_DIR));

        return [
            'motor' => [
                'openssl_disponible' => extension_loaded('openssl'),
                'openssl_version' => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'No disponible',
                'laravel_cipher' => config('app.cipher', 'AES-256-CBC'),
                'peticion_https' => $request ? $request->isSecure() : false,
                'puerto_actual' => $request ? $request->getPort() : 80,
                'esquema_actual' => $request ? strtoupper($request->getScheme()) : 'HTTP',
                'cookie_secure' => (bool) config('session.secure', false),
            ],
            'certificado_activo' => $certInfo,
            'origen_certificado' => $origenCert,
            'tiene_certificado_descargable' => ($certLocalPath !== null && is_readable($certLocalPath)),
            'pem_publico' => $pemPublico,
            'protecciones_tls' => [
                'Protocolos recomendados: TLSv1.2 y TLSv1.3 (SSLv3 / TLSv1.0 / TLSv1.1 deshabilitados).',
                'Cabecera HSTS (Strict-Transport-Security) para prevenir ataques SSL Stripping.',
                'Cifrado simétrico interno AES-256-CBC firmado con HMAC (Laravel Crypt).',
                'Llave privada (.key) resguardada fuera del directorio public/ (sin acceso HTTP).',
                'Cabeceras de seguridad activas en SecurityHeaders.php (CSP, X-Frame-Options, nosniff).',
            ],
            'config_apache_ssl' => "<VirtualHost *:443>\n"
                . "    ServerName cursoplus.local\n"
                . "    DocumentRoot \"" . str_replace('\\', '/', public_path()) . "\"\n"
                . "    SSLEngine on\n"
                . "    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1\n"
                . "    SSLCertificateFile \"{$sslDirPath}/cursoplus.crt\"\n"
                . "    SSLCertificateKeyFile \"{$sslDirPath}/cursoplus.key\"\n"
                . "    Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"\n"
                . "</VirtualHost>",
            'comando_certbot' => "sudo certbot --apache -d cursoplus.edu.mx -d www.cursoplus.edu.mx --redirect --hsts",
        ];
    }

    /**
     * Genera un certificado X.509 autofirmado real (RSA 2048-bit + SHA-256) con OpenSSL.
     */
    public function generarCertificadoAutofirmado(array $datos): array
    {
        if (!extension_loaded('openssl')) {
            return [
                'exito' => false,
                'mensaje' => 'La extensión PHP OpenSSL no está habilitada en el servidor.',
            ];
        }

        $opensslConfig = $this->localizarOpensslCnf();
        $configArgs = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        if ($opensslConfig) {
            $configArgs['config'] = $opensslConfig;
        }

        $dn = [
            'countryName' => strtoupper($datos['pais'] ?? 'MX'),
            'stateOrProvinceName' => 'Coahuila',
            'localityName' => 'Ramos Arizpe',
            'organizationName' => $datos['organizacion'] ?? 'Curso+ Plataforma Educativa',
            'organizationalUnitName' => $datos['unidad'] ?? 'Ingenieria DWI',
            'commonName' => strtolower(trim($datos['cn'] ?? 'cursoplus.local')),
        ];

        $dias = max(30, min(1095, (int) ($datos['dias'] ?? 365)));

        $privKey = @openssl_pkey_new($configArgs);
        if (!$privKey) {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo inicializar el par de claves RSA con OpenSSL: ' . (openssl_error_string() ?: 'error desconocido'),
            ];
        }

        $csr = @openssl_csr_new($dn, $privKey, $configArgs);
        if (!$csr) {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo generar la solicitud de firma CSR: ' . (openssl_error_string() ?: 'error desconocido'),
            ];
        }

        $x509 = @openssl_csr_sign($csr, null, $privKey, $dias, $configArgs, random_int(100000, 99999999));
        if (!$x509) {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo firmar el certificado X.509: ' . (openssl_error_string() ?: 'error desconocido'),
            ];
        }

        $certOut = '';
        $keyOut = '';
        openssl_x509_export($x509, $certOut);
        openssl_pkey_export($privKey, $keyOut, null, $configArgs);

        $dir = storage_path(self::CERT_DIR);
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }

        // Guardar certificado público y llave privada fuera de public/
        file_put_contents($dir . DIRECTORY_SEPARATOR . self::CERT_FILE, $certOut);
        file_put_contents($dir . DIRECTORY_SEPARATOR . self::KEY_FILE, $keyOut);

        return [
            'exito' => true,
            'mensaje' => "Certificado X.509 RSA-2048 (SHA-256) generado correctamente para {$dn['commonName']} ({$dias} días).",
            'certificado' => $this->parsearCertificadoPem($certOut),
            'pem_publico' => trim($certOut),
        ];
    }

    /**
     * Inspecciona en vivo el certificado SSL/TLS de un servidor remoto o local.
     */
    public function inspeccionarHostTls(string $host, int $puerto = 443): array
    {
        $host = strtolower(trim($host));
        $puerto = max(1, min(65535, $puerto));
        $inicio = microtime(true);

        $contexto = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'SNI_enabled' => true,
                'peer_name' => $host,
            ],
        ]);

        $errno = 0;
        $errstr = '';
        $cliente = @stream_socket_client(
            "ssl://{$host}:{$puerto}",
            $errno,
            $errstr,
            4,
            STREAM_CLIENT_CONNECT,
            $contexto
        );

        $latenciaMs = round((microtime(true) - $inicio) * 1000, 2);

        if (!$cliente) {
            return [
                'exito' => false,
                'host' => $host,
                'puerto' => $puerto,
                'latencia_ms' => $latenciaMs,
                'mensaje' => "No se pudo establecer handshake TLS con {$host}:{$puerto} ({$errstr}).",
            ];
        }

        $params = stream_context_get_params($cliente);
        $meta = stream_get_meta_data($cliente);
        fclose($cliente);

        $peerCert = $params['options']['ssl']['peer_certificate'] ?? null;
        if (!$peerCert) {
            return [
                'exito' => false,
                'host' => $host,
                'puerto' => $puerto,
                'latencia_ms' => $latenciaMs,
                'mensaje' => "El servidor {$host}:{$puerto} no devolvió un certificado X.509.",
            ];
        }

        $pem = '';
        openssl_x509_export($peerCert, $pem);
        $parsed = $this->parsearCertificadoPem($pem);

        return [
            'exito' => true,
            'host' => $host,
            'puerto' => $puerto,
            'latencia_ms' => $latenciaMs,
            'protocolo_negociado' => $meta['crypto']['protocol'] ?? 'TLS',
            'cifrado_negociado' => $meta['crypto']['cipher_name'] ?? 'N/D',
            'certificado' => $parsed,
            'mensaje' => "Handshake TLS completado con {$host}:{$puerto} en {$latenciaMs} ms.",
        ];
    }

    /**
     * Devuelve la ruta del certificado público activo para descarga o lectura.
     */
    public function rutaCertificadoActivo(): ?string
    {
        $custom = storage_path(self::CERT_DIR . DIRECTORY_SEPARATOR . self::CERT_FILE);
        if (file_exists($custom) && is_readable($custom)) {
            return $custom;
        }

        $xamppCert = 'C:\\xampp\\apache\\conf\\ssl.crt\\server.crt';
        if (file_exists($xamppCert) && is_readable($xamppCert)) {
            return $xamppCert;
        }

        return null;
    }

    private function parsearCertificadoPem(string $pem): ?array
    {
        $parsed = @openssl_x509_parse($pem);
        if (!is_array($parsed)) {
            return null;
        }

        $validoDesde = $parsed['validFrom_time_t'] ?? time();
        $validoHasta = $parsed['validTo_time_t'] ?? time();
        $ahora = time();
        $diasRestantes = (int) floor(($validoHasta - $ahora) / 86400);

        $estado = 'VIGENTE';
        if ($diasRestantes < 0) {
            $estado = 'EXPIRADO';
        } elseif ($diasRestantes <= 30) {
            $estado = 'PRÓXIMO A VENCER';
        }

        $fp = @openssl_x509_fingerprint($pem, 'sha256') ?: '';
        $fpFormateado = $fp ? strtoupper(implode(':', str_split($fp, 2))) : 'N/D';

        $subject = $parsed['subject'] ?? [];
        $issuer = $parsed['issuer'] ?? [];

        return [
            'cn' => is_array($subject['CN'] ?? null) ? implode(', ', $subject['CN']) : ($subject['CN'] ?? 'N/D'),
            'organizacion' => $subject['O'] ?? 'No especificada',
            'unidad' => $subject['OU'] ?? '—',
            'pais' => $subject['C'] ?? '—',
            'emisor_cn' => is_array($issuer['CN'] ?? null) ? implode(', ', $issuer['CN']) : ($issuer['CN'] ?? ($issuer['O'] ?? 'Autofirmado')),
            'emisor_o' => $issuer['O'] ?? 'Emisor Local',
            'serie' => $parsed['serialNumberHex'] ?? ($parsed['serialNumber'] ?? 'N/D'),
            'algoritmo_firma' => $parsed['signatureTypeSN'] ?? 'RSA-SHA256',
            'version_x509' => 'v' . (($parsed['version'] ?? 2) + 1),
            'valido_desde' => date('d/m/Y H:i', $validoDesde),
            'valido_hasta' => date('d/m/Y H:i', $validoHasta),
            'dias_restantes' => $diasRestantes,
            'estado' => $estado,
            'huella_sha256' => $fpFormateado,
        ];
    }

    private function localizarOpensslCnf(): string
    {
        $candidatos = [
            'C:\\xampp\\apache\\conf\\openssl.cnf',
            'C:\\xampp\\php\\extras\\ssl\\openssl.cnf',
            'C:\\xampp\\php\\extras\\openssl\\openssl.cnf',
        ];

        foreach ($candidatos as $path) {
            if (@file_exists($path) && @is_readable($path)) {
                return $path;
            }
        }

        // Generar configuración mínima local en storage/app/ssl/openssl.cnf para garantizar portabilidad en Windows
        $dir = storage_path(self::CERT_DIR);
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }

        $localCnf = $dir . DIRECTORY_SEPARATOR . 'openssl.cnf';
        if (!file_exists($localCnf)) {
            $cnfContent = "[req]\n"
                . "default_bits = 2048\n"
                . "distinguished_name = req_distinguished_name\n"
                . "x509_extensions = v3_req\n"
                . "prompt = no\n\n"
                . "[req_distinguished_name]\n"
                . "CN = cursoplus.local\n\n"
                . "[v3_req]\n"
                . "basicConstraints = CA:FALSE\n"
                . "keyUsage = digitalSignature, keyEncipherment\n";
            file_put_contents($localCnf, $cnfContent);
        }

        return $localCnf;
    }
}
