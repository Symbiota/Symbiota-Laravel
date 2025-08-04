<?php

class MySQLiConnectionFactory {
    public static function getCon($type) {
        $SERVERS = [
            [
                'type' => 'readonly',
                'host' => getenv('DB_HOST'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'database' => getenv('DB_DATABASE'),
                'port' => getenv('DB_PORT'),
                'charset' => getenv('DB_CHARSET') ?? 'utf8',
            ],
            [
                'type' => 'write',
                'host' => getenv('DB_HOST'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'database' => getenv('DB_DATABASE'),
                'port' => getenv('DB_PORT'),
                'charset' => getenv('DB_CHARSET') ?? 'utf8',
            ],
        ];

        // Figure out which connections are open, automatically opening any connections
        // which are failed or not yet opened but can be (re)established.
        for ($i = 0, $n = count($SERVERS); $i < $n; $i++) {
            $server = $SERVERS[$i];
            if ($server['type'] == $type) {
                $connection = new mysqli($server['host'], $server['username'], $server['password'], $server['database'], $server['port']);
                if (mysqli_connect_errno()) {
                    throw new Exception('Could not connect to any databases! Please try again later.');
                }
                if (isset($server['charset']) && $server['charset']) {
                    if (! $connection->set_charset($server['charset'])) {
                        throw new Exception('Error loading character set ' . $server['charset'] . ': ' . $connection->error);
                    }
                }

                return $connection;
            }
        }
    }
}
