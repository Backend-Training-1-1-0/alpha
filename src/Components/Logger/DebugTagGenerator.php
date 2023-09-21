<?php

namespace Alpha\Components\Logger;

class DebugTagGenerator
{
    private function init()
    {
        if (function_exists(__NAMESPACE__ .'\getallheaders') === false) {
            function getallheaders() {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[
                        str_replace(
                            ' ',
                            '-',
                            ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                        )
                        ] = $value;
                    }
                }

                return $headers;
            }
        }
    }

    /**
     * @return void
     */
    private function createFromHeaders()
    {
        $headers = getallheaders();

        if (isset($headers['X-Debug-Tag']) === false) {
            return;
        }

        define('X_DEBUG_TAG', $headers['X-Debug-Tag']);
    }

    /**
     * @return void
     */
    public function bootstrap()
    {
        $this->init();

        $this->createFromHeaders();


        if (defined('X_DEBUG_TAG') === true) {
            return;
        }

        $key = 'x-debug-tag-' . 'ALPHA' . '-';

        $key .= uniqid();

        $key .= '-' . gethostname() . '-' . time();

        define('X_DEBUG_TAG', md5($key));
    }
}
