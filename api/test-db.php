<?php
// Prevent unauthorized access, but keep it simple for testing
if (isset($_GET['secret']) && $_GET['secret'] === 'check123') {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: '4000';
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');
    
    echo "<h1>Database Connection Test</h1>";
    echo "<p>Host: " . htmlspecialchars($host) . "</p>";
    echo "<p>Port: " . htmlspecialchars($port) . "</p>";
    echo "<p>Database: " . htmlspecialchars($database) . "</p>";
    echo "<p>Username: " . htmlspecialchars($username) . "</p>";
    
    $variations = [
        'No options' => [],
        'Verify Server Cert = true' => [
            1014 => true
        ],
        'Verify Server Cert = false' => [
            1014 => false
        ],
        'Verify Server Cert = true, CA = empty' => [
            1014 => true,
            1007 => ''
        ],
        'Verify Server Cert = false, CA = empty' => [
            1014 => false,
            1007 => ''
        ],
        'Verify Server Cert = true, CA = system bundle' => [
            1014 => true,
            1007 => '/etc/pki/tls/certs/ca-bundle.crt'
        ],
        'Verify Server Cert = false, CA = system bundle' => [
            1014 => false,
            1007 => '/etc/pki/tls/certs/ca-bundle.crt'
        ],
        'Verify Server Cert = true, CA = alt system bundle' => [
            1014 => true,
            1007 => '/etc/ssl/certs/ca-certificates.crt'
        ],
        'Verify Server Cert = false, CA = alt system bundle' => [
            1014 => false,
            1007 => '/etc/ssl/certs/ca-certificates.crt'
        ],
        'Verify Server Cert = true, CA = temp ISRG cert' => [
            1014 => true,
            1007 => sys_get_temp_dir() . '/isrgrootx1.pem'
        ],
        'Verify Server Cert = false, CA = temp ISRG cert' => [
            1014 => false,
            1007 => sys_get_temp_dir() . '/isrgrootx1.pem'
        ],
        'Verify Server Cert = false, CA = dev/null' => [
            1014 => false,
            1007 => '/dev/null'
        ],
    ];
    
    // Ensure ISRG cert exists in tmp for testing
    $certPath = sys_get_temp_dir() . '/isrgrootx1.pem';
    if (!file_exists($certPath)) {
        $certContent = "-----BEGIN CERTIFICATE-----\n" .
            "MIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n" .
            "TzELMAkGA1UEBhMCVVMxKTAnBgNVBAoTIEludGVybmV0IFNlY3VyaXR5IFJlc2Vh\n" .
            "cmNoIEdyb3VwMRUwEwYDVQQDEwxJU1JHIFJvb3QgWDEwHhcNMTUwNjA0MTEwNDM4\n" .
            "WhcNMzUwNjA0MTEwNDM4WjBPMQswCQYDVQQGEwJVUzEpMCcGA1UEChMgSW50ZXJu\n" .
            "ZXQgU2VjdXJpdHkgUmVzZWFyY2ggR3JvdXAxFTATBgNVBAMTDElTUkcgUm9vdCBY\n" .
            "MTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBAK3oJHP0FDfzm54rVygc\n" .
            "h77ct984kIxuPOZXoHj3dcKi/vVqbvYATyjb3miGbESTtrFj/RQSa78f0uoxmyF+\n" .
            "0TM8ukj13Xnfs7j/EvEhmkvBioZxaUpmZmyPfjxwv60pIgbz5MDmgK7iS4+3mX6U\n" .
            "A5/TR5d8mUgjU+g4rk8Kb4Mu0UlXjIB0ttov0DiNewNwIRt18jA8+o+u3dpjq+sW\n" .
            "T8KOEUt+zwvo/7V3LvSye0rgTBIlDHCNAymg4VMk7BPZ7hm/ELNKjD+Jo2FR3qyH\n" .
            "B5T0Y3HsLuJvW5iB4YlcNHlsdu87kGJ55tukmi8mxdAQ4Q7e2RCOFvu396j3x+UC\n" .
            "B5iPNgiV5+I3lg02dZ77DnKxHZu8A/lJBdiB3QW0KtZB6awBdpUKD9jf1b0SHzUv\n" .
            "KBds0pjBqAlkd25HN7rOrFleaJ1/ctaJxQZBKT5ZPt0m9STJEadao0xAH0ahmbWn\n" .
            "OlFuhjuefXKnEgV4We0+UXgVCwOPjdAvBbI+e0ocS3MFEvzG6uBQE3xDk3SzynTn\n" .
            "jh8BCNAw1FtxNrQHusEwMFxIt4I7mKZ9YIqioymCzLq9gwQbooMDQaHWBfEbwrbw\n" .
            "qHyGO0aoSCqI3Haadr8faqU9GY/rOPNk3sgrDQoo//fb4hVC1CLQJ13hef4Y53CI\n" .
            "rU7m2Ys6xt0nUW7/vGT1M0NPAgMBAAGjQjBAMA4GA1UdDwEB/wQEAwIBBjAPBgNV\n" .
            "HRMBAf8EBTADAQH/MB0GA1UdDgQWBBR5tFnme7bl5AFzgAiIyBpY9umbbjANBgkq\n" .
            "hkiG9w0BAQsFAAOCAgEAVR9YqbyyqFDQDLHYGmkgJykIrGF1XIpu+ILlaS/V9lZL\n" .
            "ubhzEFnTIZd+50xx+7LSYK05qAvqFyFWhfFQDlnrzuBZ6brJFe+GnY+EgPbk6ZGQ\n" .
            "3BebYhtF8GaV0nxvwuo77x/Py9auJ/GpsMiu/X1+mvoiBOv/2X/qkSsisRcOj/KK\n" .
            "NFtY2PwByVS5uCbMiogziUwthDyC3+6WVwW6LLv3xLfHTjuCvjHIInNzktHCgKQ5\n" .
            "ORAzI4JMPJ+GslWYHb4phowim57iaztXOoJwTdwJx4nLCgdNbOhdjsnvzqvHu7Ur\n" .
            "TkXWStAmzOVyyghqpZXjFaH3pO3JLF+l+/+sKAIuvtd7u+Nxe5AW0wdeRlN8NwdC\n" .
            "jNPElpzVmbUq4JUagEiuTDkHzsxHpFKVK7q4+63SM1N95R1NbdWhscdCb+ZAJzVc\n" .
            "oyi3B43njTOQ5yOf+1CceWxG1bQVs5ZufpsMljq4Ui0/1lvh+wjChP4kqKOJ2qxq\n" .
            "4RgqsahDYVvTH9w7jXbyLeiNdd8XM2w9U/t7y0Ff/9yi0GE44Za4rF2LN9d11TPA\n" .
            "mRGunUHBcnWEvgJBQl9nJEiU0Zsnvgc/ubhPgXRR4Xq37Z0j4r7g1SgEEzwxA57d\n" .
            "emyPxgcYxn/eR44/KJ4EBs+lVDR3veyJm+kXQ99b21/+jh5Xos1AnX5iItreGCc=\n" .
            "-----END CERTIFICATE-----";
        file_put_contents($certPath, $certContent);
    }
    
    foreach ($variations as $name => $opts) {
        echo "<h3>Testing: $name</h3>";
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, $opts);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT 1");
            $result = $stmt->fetchColumn();
            echo "<p style='color:green;'><b>SUCCESS!</b> Result: $result</p>";
        } catch (\Exception $e) {
            echo "<p style='color:red;'><b>FAILED:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
            $ssl_errors = [];
            while ($ssl_err = openssl_error_string()) {
                $ssl_errors[] = $ssl_err;
            }
            if (!empty($ssl_errors)) {
                echo "<p style='color:orange;'><b>SSL Errors:</b> " . htmlspecialchars(implode(' | ', $ssl_errors)) . "</p>";
            }
        }
    }
} else {
    echo "Access denied.";
}
