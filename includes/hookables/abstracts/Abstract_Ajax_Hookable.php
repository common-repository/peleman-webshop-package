<?php

declare(strict_types=1);

namespace PWP\includes\hookables\abstracts;

use PWP\includes\hookables\abstracts\I_Hookable_Component;

/**
 * Abstract Ajax hookable class. Facilitates AJAX calls between wordpress and the plugin with an OOP hookable object
 */
abstract class Abstract_Ajax_Hookable implements I_Hookable_Component
{

    /**
     * name of the AJAX nonce used by this hookable
     *
     * @var string
     */
    protected string $nonceName;

    /**
     * Script handle of the Ajax method. 
     *
     * @var string
     */
    private string $scriptHandle;

    /**
     * name of the Ajax object to be used by the script
     *
     * @var string
     */
    protected string $objectName;

    /**
     * absolute path of the Javascript file.
     *
     * @var string
     */
    private string $jsFilePath;

    /**
     * execution priority
     *
     * @var integer
     */
    private int $priority;

    private bool $isAdminScript;

    private array $dependencies;

    private const CALLBACK = 'callback';
    private const CALLBACK_NOPRIV = 'callback_nopriv';

    private const OBJECT_SUFFIX = '_object';
    private const NONCE_SUFFIX = '_nonce';

    /**
     * Abstract Ajax hookable class. Facilitates AJAX calls between wordpress and the plugin with an OOP hookable object
     *
     * @param string $handle script handle to which data will be attached and unique ID of this ajax function. Will be used for all unique internal naming.
     * @param string $jsFilePath absolute path of the Javascript file.
     * @param integer $priority when executing, the priority of this hook. default `10`
     */
    public function __construct(string $handle, string $jsFilePath, int $priority = 10, array $deps = [])
    {
        $this->scriptHandle = $handle;
        $this->objectName = $handle . self::OBJECT_SUFFIX;
        $this->nonceName = $handle . self::NONCE_SUFFIX;
        $this->jsFilePath = $jsFilePath;
        $this->dependencies = array_merge(['jquery', 'wp-i18n'], $deps);

        $this->priority = $priority;
        $this->isAdminScript = false;
    }

    public function set_admin(bool $isAdminScript): void
    {
        $this->isAdminScript = $isAdminScript;
    }

    final public function register(): void
    {
        add_action(
            $this->isAdminScript ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts',
            array(
                $this,
                'enqueue_ajax'
            ),
            $this->priority
        );
        add_action(
            "wp_ajax_{$this->scriptHandle}",
            array(
                $this,
                self::CALLBACK
            ),
            $this->priority,
        );
        add_action(
            "wp_ajax_nopriv_{$this->scriptHandle}",
            array(
                $this,
                self::CALLBACK_NOPRIV
            ),
            $this->priority,
        );
    }

    /**
     * helper method to enqueue related Ajax scripts
     *
     * @return void
     */
    final public function enqueue_ajax(): void
    {
        wp_enqueue_script(
            $this->scriptHandle,
            $this->jsFilePath,
            $this->dependencies,
            wp_rand(0, 2000),
            true
        );

        $localization_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce($this->nonceName),
        );
        $localization_array += $this->object_data();
        wp_localize_script(
            $this->scriptHandle,
            $this->objectName,
            $localization_array,

        );
        wp_set_script_translations($this->scriptHandle, 'peleman-webshop-package');

        // error_log("{$this->scriptHandle} registered");
    }

    /**
     * functionality of the component for authenticated users
     *
     * @return void
     */
    public abstract function callback(): void;

    /**
     * Function to add extra data to the Ajax object, such as translated strings and other values needed for script operation.
     *
     * @return array
     */
    protected function object_data(): array
    {
        return array();
    }
    /**
     * functionality of the component for unauthenticated users
     *
     * @return void
     */
    public abstract function callback_nopriv(): void;

    /**
     * wrapper method to handle nonce validation. returns 0 if nonce is invalid, 1 or 2 if valid.
     * @param string $nonce
     * @return bool
     */
    protected function verify_nonce(string $nonce): bool
    {
        $value = wp_verify_nonce($nonce, $this->nonceName);
        return $value !== 0;
    }
}
