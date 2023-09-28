<?php

namespace Alpha\Components\Logger;

class DebugTagGenerator
{
    private string $projectIndex = 'ALPHA';

    private function init(): void
    {
        if (function_exists(__NAMESPACE__ .'\getallheaders') === false) {
            function getAllHeaders(): array
            {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (str_starts_with($name, 'HTTP_')) {
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
    private function createFromHeaders(): void
    {
        $headers = getAllHeaders();

        if (isset($headers['X-Debug-Tag']) === false) {
            return;
        }

        define('X_DEBUG_TAG', $headers['X-Debug-Tag']);
    }

    /**
     * @return void
     */
    public function bootstrap(): void
    {
        $this->init();
        $this->createFromHeaders();

        if (defined('X_DEBUG_TAG') === true) {
            return;
        }

        if (empty(getenv('PROJECT_INDEX') === false)) {
            $this->projectIndex = getenv('PROJECT_INDEX');
        }

        $key = 'x-debug-tag-' . $this->projectIndex . '-';
        $key .= uniqid();
        $key .= '-' . gethostname() . '-' . time();

        define('X_DEBUG_TAG', md5($key));
    }
}
