<?php
/**
 * Plugin Loader
 *
 * Koppelt plugin hooks aan de juiste methodes.
 * Vergelijkbaar met de Loader klasse in WordPress plugin boilerplates.
 */

class Loader
{
    private Database   $db;
    private PostTypes  $postTypes;
    private QuoteForm  $quoteForm;
    private MailAPI    $mailer;

    public function __construct()
    {
        $this->db        = Database::getInstance();
        $this->mailer    = new MailAPI($this->db);
        $this->postTypes = new PostTypes($this->db);
        $this->quoteForm = new QuoteForm($this->db, $this->mailer);
    }

    /**
     * Initialiseer alle plugin componenten
     */
    public function init(): void
    {
        // Registreer custom post types op 'init' hook
        $this->postTypes->register();

        // Registreer quote form handler op hook
        $this->quoteForm->register();

        // Trigger init hook (zodat custom post types geregistreerd worden)
        do_action('init');

        // Extra filter: pas de post excerpt aan
        add_filter('the_excerpt', function (string $excerpt): string {
            return strlen($excerpt) > 120
                ? substr($excerpt, 0, 120) . '...'
                : $excerpt;
        });
    }

    public function getPostTypes(): PostTypes
    {
        return $this->postTypes;
    }

    public function getQuoteForm(): QuoteForm
    {
        return $this->quoteForm;
    }

    public function getMailer(): MailAPI
    {
        return $this->mailer;
    }

    public function getDb(): Database
    {
        return $this->db;
    }
}
