<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive;

class ItinerisPageAsPostTypeArchive
{
    /**
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Types
     *
     * @var array
     */
    protected array $types = [
        'CustomPostType',
        'Search',
        'ZeroFourZero'
    ];

    /**
     * @var string|null
     */
    protected ?string $namespace;

    /**
     * @return self|null
     */
    public static function instance(): ?self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a new Directives instance.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('after_setup_theme', function (): void {
            $this->namespace = apply_filters('itineris/page-as-post-type-archive/namespace', __NAMESPACE__);
            array_map(fn (string $class) => new $class(), $this->types());
        }, 20);
    }

    /**
     * Returns a array of types.
     *
     * @return array
     */
    protected function types(): array
    {
        if (empty($this->types)) {
            return [];
        }

        $types = [];

        foreach ($this->types as $type) {
            $class = $this->namespace . '\\Types\\' . $type;
            if (! class_exists($class)) {
                continue;
            }

            $types[$type] = $class;
        }

        return $types;
    }
}

ItinerisPageAsPostTypeArchive::instance();
