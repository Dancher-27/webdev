<?php
/**
 * Mail API - Mailgun-achtige simulatie
 *
 * Simuleert de Mailgun REST API interface.
 * In productie zou dit een echte HTTP call naar api.mailgun.net maken.
 * Nu logt het alles naar de database en optioneel via PHP mail().
 */

class MailAPI
{
    private string $apiKey;
    private string $domain;
    private string $fromEmail;
    private string $fromName;
    private Database $db;

    public function __construct(Database $db)
    {
        $this->apiKey    = MAIL_API_KEY;
        $this->domain    = MAIL_API_DOMAIN;
        $this->fromEmail = MAIL_FROM;
        $this->fromName  = MAIL_FROM_NAME;
        $this->db        = $db;
    }

    /**
     * Verstuur een email (Mailgun-stijl interface)
     *
     * @param string $to      Ontvanger emailadres
     * @param string $subject Onderwerp
     * @param string $body    HTML of tekst body
     * @param string|null $from Afzender (optioneel, gebruikt config default)
     *
     * @return array ['success' => bool, 'id' => int, 'message' => string]
     */
    public function send(string $to, string $subject, string $body, ?string $from = null): array
    {
        $from = $from ?? "{$this->fromName} <{$this->fromEmail}>";

        // Filter: geef andere code de kans de mail te wijzigen
        $body    = apply_filters('mail_body', $body, $to, $subject);
        $subject = apply_filters('mail_subject', $subject, $to);

        // Simuleer API call - log naar database
        $logId = $this->logMail($to, $from, $subject, $body, 'queued');

        // Simuleer verwerking (in echte Mailgun zou dit een HTTP POST zijn)
        $response = $this->simulateApiCall($to, $subject, $body);

        // Update log status
        $status = $response['success'] ? 'sent' : 'failed';
        $this->db->update('mail_log', [
            'status'   => $status,
            'response' => json_encode($response),
        ], ['id' => $logId]);

        do_action('mail_sent', $to, $subject, $response['success']);

        return [
            'success' => $response['success'],
            'id'      => $logId,
            'message' => $response['message'],
        ];
    }

    /**
     * Verstuur een HTML email met tekst fallback
     */
    public function sendHtml(string $to, string $subject, string $htmlBody, string $textBody = ''): array
    {
        if (empty($textBody)) {
            $textBody = strip_tags($htmlBody);
        }

        // In een echte implementatie worden beide versies meegestuurd
        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Verstuur een template-gebaseerde email
     *
     * @param string $to
     * @param string $subject
     * @param string $template  Template naam (zonder .php)
     * @param array  $vars      Variabelen voor de template
     */
    public function sendTemplate(string $to, string $subject, string $template, array $vars = []): array
    {
        $templateFile = __DIR__ . '/../templates/' . $template . '.php';

        if (!file_exists($templateFile)) {
            return ['success' => false, 'id' => 0, 'message' => 'Template niet gevonden'];
        }

        extract($vars);
        ob_start();
        include $templateFile;
        $body = ob_get_clean();

        return $this->send($to, $subject, $body);
    }

    /**
     * Simuleert de Mailgun API call
     * In productie: curl naar https://api.mailgun.net/v3/{domain}/messages
     */
    private function simulateApiCall(string $to, string $subject, string $body): array
    {
        // Simuleer validatie zoals Mailgun zou doen
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => "Invalid recipient email: {$to}",
            ];
        }

        if (empty($subject) || empty($body)) {
            return [
                'success' => false,
                'message' => 'Subject and body are required',
            ];
        }

        // Simuleer Mailgun response format
        $messageId = '<' . uniqid('mg.') . '@' . $this->domain . '>';

        return [
            'success'    => true,
            'message'    => 'Queued. Thank you.',
            'id'         => $messageId,
            'api_domain' => $this->domain,
        ];
    }

    /**
     * Logt een mail naar de database
     */
    private function logMail(string $to, string $from, string $subject, string $body, string $status): int
    {
        return $this->db->insert('mail_log', [
            'to_email'   => $to,
            'from_email' => $from,
            'subject'    => $subject,
            'body'       => $body,
            'status'     => $status,
            'api_key'    => substr($this->apiKey, 0, 8) . '...',
            'domain'     => $this->domain,
        ]);
    }

    /**
     * Haal mail log op (voor admin panel)
     */
    public function getLog(int $limit = 50): array
    {
        return $this->db->query(
            'SELECT * FROM mail_log ORDER BY sent_at DESC LIMIT ?',
            [$limit]
        );
    }
}
