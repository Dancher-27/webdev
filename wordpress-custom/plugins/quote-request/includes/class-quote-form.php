<?php
/**
 * Offerte formulier handler
 *
 * Verwerkt het offerte aanvraag formulier:
 * validatie, opslag in database, mail bevestiging
 */

class QuoteForm
{
    private Database $db;
    private MailAPI $mailer;

    private array $errors  = [];
    private array $data    = [];

    public function __construct(Database $db, MailAPI $mailer)
    {
        $this->db     = $db;
        $this->mailer = $mailer;
    }

    /**
     * Registreer hooks voor formulierverwerking
     */
    public function register(): void
    {
        add_action('handle_quote_form', [$this, 'process']);
    }

    /**
     * Verwerk het ingediende formulier
     * Wordt als AJAX endpoint én standaard POST handler gebruikt
     */
    public function process(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $this->sanitizeInput();
        $this->validate();

        if (!empty($this->errors)) {
            $this->sendJsonResponse(false, 'Formulier bevat fouten', $this->errors);
            return;
        }

        $quoteId = $this->saveQuote();

        if ($quoteId) {
            $this->sendConfirmationMail($quoteId);
            $this->sendAdminNotification($quoteId);

            do_action('quote_submitted', $quoteId, $this->data);

            $this->sendJsonResponse(true, 'Bedankt voor je aanvraag! We nemen zo snel mogelijk contact op.');
        } else {
            $this->sendJsonResponse(false, 'Er is een fout opgetreden. Probeer het opnieuw.');
        }
    }

    /**
     * Sanitize alle invoer
     */
    private function sanitizeInput(): void
    {
        $this->data = [
            'name'    => sanitize_text($_POST['name']    ?? ''),
            'email'   => sanitize_email($_POST['email']   ?? ''),
            'phone'   => sanitize_text($_POST['phone']   ?? ''),
            'service' => sanitize_text($_POST['service'] ?? ''),
            'budget'  => sanitize_text($_POST['budget']  ?? ''),
            'message' => sanitize_text($_POST['message'] ?? ''),
        ];

        // Filter: laat andere code de data wijzigen voor opslag
        $this->data = apply_filters('quote_form_data', $this->data);
    }

    /**
     * Valideer de invoer
     */
    private function validate(): void
    {
        if (empty($this->data['name'])) {
            $this->errors['name'] = 'Naam is verplicht';
        } elseif (strlen($this->data['name']) < 2) {
            $this->errors['name'] = 'Naam moet minimaal 2 tekens bevatten';
        }

        if (empty($this->data['email'])) {
            $this->errors['email'] = 'E-mailadres is verplicht';
        } elseif (!is_valid_email($this->data['email'])) {
            $this->errors['email'] = 'Ongeldig e-mailadres';
        }

        if (empty($this->data['message'])) {
            $this->errors['message'] = 'Omschrijving is verplicht';
        } elseif (strlen($this->data['message']) < 20) {
            $this->errors['message'] = 'Geef een uitgebreidere omschrijving (minimaal 20 tekens)';
        }

        // Actie: andere plugins kunnen extra validatie toevoegen
        do_action('validate_quote_form', $this->data, $this->errors);
    }

    /**
     * Sla de offerte op in de database
     */
    private function saveQuote(): int
    {
        return $this->db->insert('quotes', [
            'name'    => $this->data['name'],
            'email'   => $this->data['email'],
            'phone'   => $this->data['phone'],
            'service' => $this->data['service'],
            'budget'  => $this->data['budget'],
            'message' => $this->data['message'],
            'status'  => 'nieuw',
        ]);
    }

    /**
     * Stuur bevestigingsmail naar de aanvrager
     */
    private function sendConfirmationMail(int $quoteId): void
    {
        $subject = apply_filters('quote_confirmation_subject', 'Bevestiging offerte aanvraag - ' . SITE_NAME);

        $body = $this->buildConfirmationMailBody($quoteId);

        $this->mailer->send(
            $this->data['email'],
            $subject,
            $body
        );
    }

    /**
     * Stuur notificatie naar admin
     */
    private function sendAdminNotification(int $quoteId): void
    {
        $subject = "Nieuwe offerte aanvraag #{$quoteId} van {$this->data['name']}";

        $body = "<h2>Nieuwe offerte aanvraag</h2>
        <p><strong>Aanvraag #:</strong> {$quoteId}</p>
        <p><strong>Naam:</strong> {$this->data['name']}</p>
        <p><strong>Email:</strong> {$this->data['email']}</p>
        <p><strong>Telefoon:</strong> {$this->data['phone']}</p>
        <p><strong>Dienst:</strong> {$this->data['service']}</p>
        <p><strong>Budget:</strong> {$this->data['budget']}</p>
        <p><strong>Omschrijving:</strong><br>{$this->data['message']}</p>
        <p><a href='" . SITE_URL . "/admin/offertes.php'>Bekijk in admin panel</a></p>";

        $this->mailer->send(ADMIN_EMAIL, $subject, $body);
    }

    /**
     * Bouw de bevestigingsmail op
     */
    private function buildConfirmationMailBody(int $quoteId): string
    {
        $name = esc_html($this->data['name']);
        $siteName = esc_html(SITE_NAME);

        return "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
            <div style='background: #2563eb; padding: 30px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>{$siteName}</h1>
            </div>
            <div style='padding: 30px;'>
                <h2>Bedankt voor je aanvraag, {$name}!</h2>
                <p>We hebben je offerte aanvraag (#{$quoteId}) ontvangen en nemen zo snel mogelijk contact met je op.</p>
                <h3>Jouw aanvraag:</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Dienst:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>" . esc_html($this->data['service']) . "</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Budget:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>" . esc_html($this->data['budget']) . "</td></tr>
                    <tr><td style='padding: 8px;'><strong>Omschrijving:</strong></td><td style='padding: 8px;'>" . esc_html($this->data['message']) . "</td></tr>
                </table>
                <p style='margin-top: 30px; color: #666;'>Met vriendelijke groet,<br><strong>{$siteName}</strong></p>
            </div>
        </body>
        </html>";
    }

    /**
     * Stuur JSON response (voor AJAX)
     */
    private function sendJsonResponse(bool $success, string $message, array $errors = []): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'errors'  => $errors,
        ]);
        exit;
    }

    /**
     * Haal alle offertes op (voor admin)
     */
    public function getAllQuotes(string $status = ''): array
    {
        if ($status) {
            return $this->db->query(
                'SELECT * FROM quotes WHERE status = ? ORDER BY created_at DESC',
                [$status]
            );
        }
        return $this->db->query('SELECT * FROM quotes ORDER BY created_at DESC');
    }

    /**
     * Update offerte status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['nieuw', 'in_behandeling', 'afgerond', 'afgewezen'];
        if (!in_array($status, $allowed)) {
            return false;
        }

        $rows = $this->db->update('quotes', ['status' => $status], ['id' => $id]);
        return $rows > 0;
    }
}
