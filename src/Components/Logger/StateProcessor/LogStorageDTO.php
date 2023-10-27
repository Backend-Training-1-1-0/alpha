<?php

namespace Alpha\Components\Logger\StateProcessor;

class LogStorageDTO
{
    /**
     * @var string
     */
    public string $index;

    /**
     * @var string
     */
    public string $category;

    /**
     * @var string|null
     */
    public ?string $context = null;

    /**
     * @var int
     */
    public int $level;

    /**
     * @var string
     */
    public string $level_name;

    /**
     * @var string
     */
    public string $action;

    /**
     * @var string
     */
    public string $action_type;

    /**
     * @var string
     */
    public string $datetime;

    /**
     * @var string
     */
    public string $timestamp;

    /**
     * @var int|null
     */
    public ?int $userId = null;

    /**
     * @var string|null
     */
    public ?string $ip = null;

    /**
     * @var string|null
     */
    public ?string $real_ip = null;

    /**
     * @var string
     */
    public string $x_debug_tag;

    /**
     * @var string
     */
    public string $message;

    /**
     * @var mixed|null
     */
    public mixed $exception = null;

    /**
     * @var string|null
     */
    public ?string $extras = null;
}
