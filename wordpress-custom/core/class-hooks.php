<?php
/**
 * WordPress-stijl Hooks systeem
 *
 * Simuleert het WordPress hooks/filters systeem:
 * add_action, do_action, add_filter, apply_filters
 */

class WP_Hooks
{
    private static ?WP_Hooks $instance = null;

    /** @var array<string, array<int, array<callable>>> */
    private array $actions = [];

    /** @var array<string, array<int, array<callable>>> */
    private array $filters = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registreert een actie (vergelijkbaar met WordPress add_action)
     */
    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    /**
     * Voert alle callbacks uit voor een hook (vergelijkbaar met WordPress do_action)
     */
    public function doAction(string $hook, mixed ...$args): void
    {
        if (!isset($this->actions[$hook])) {
            return;
        }

        ksort($this->actions[$hook]);

        foreach ($this->actions[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Registreert een filter (vergelijkbaar met WordPress add_filter)
     */
    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
    }

    /**
     * Past filters toe op een waarde (vergelijkbaar met WordPress apply_filters)
     */
    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        if (!isset($this->filters[$hook])) {
            return $value;
        }

        ksort($this->filters[$hook]);

        foreach ($this->filters[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $value = call_user_func_array($callback, array_merge([$value], $args));
            }
        }

        return $value;
    }

    /**
     * Geeft alle geregistreerde actions terug (voor debugging)
     */
    public function getActions(): array
    {
        return $this->actions;
    }
}
