/**
 * WordPress Custom - Front-end JavaScript
 * AJAX formulier verwerking + validatie
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('quoteForm');
    if (!form) return;

    const submitBtn  = document.getElementById('submitBtn');
    const btnText    = submitBtn?.querySelector('.btn-text');
    const btnLoading = submitBtn?.querySelector('.btn-loading');
    const formMsg    = document.getElementById('formMessage');
    const charCount  = document.getElementById('charCount');
    const messageEl  = document.getElementById('message');

    // Live teken teller voor omschrijving veld
    messageEl?.addEventListener('input', () => {
        const count = messageEl.value.length;
        charCount.textContent = count;
        charCount.style.color = count >= 20 ? '#16a34a' : '#9ca3af';
    });

    // Budget radio buttons: klik op het span element
    document.querySelectorAll('.budget-option span').forEach(span => {
        span.addEventListener('click', () => {
            const radio = span.previousElementSibling;
            if (radio) radio.checked = true;
        });
    });

    // Formulier submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        clearErrors();

        const data = new FormData(form);

        // Client-side validatie (extra laag bovenop server validatie)
        const errors = validateForm(data);
        if (Object.keys(errors).length > 0) {
            showErrors(errors);
            return;
        }

        setLoading(true);

        try {
            // AJAX POST naar de API endpoint (WordPress-stijl admin-ajax.php equivalent)
            const response = await fetch(window.WP?.ajaxUrl || 'api/quote.php', {
                method: 'POST',
                body: data,
            });

            const result = await response.json();

            if (result.success) {
                showFormMessage(result.message, 'success');
                form.reset();
                charCount.textContent = '0';
                // Scroll naar bevestiging
                formMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                if (result.errors && Object.keys(result.errors).length > 0) {
                    showErrors(result.errors);
                } else {
                    showFormMessage(result.message || 'Er is een fout opgetreden.', 'error');
                }
            }
        } catch (err) {
            showFormMessage('Verbindingsfout. Controleer je internetverbinding.', 'error');
            console.error('Quote form error:', err);
        } finally {
            setLoading(false);
        }
    });

    /**
     * Client-side validatie
     * @returns {Object} errors object { fieldName: 'foutmelding' }
     */
    function validateForm(data) {
        const errors = {};
        const name    = data.get('name')?.trim();
        const email   = data.get('email')?.trim();
        const message = data.get('message')?.trim();

        if (!name || name.length < 2) {
            errors.name = 'Naam moet minimaal 2 tekens bevatten';
        }

        if (!email) {
            errors.email = 'E-mailadres is verplicht';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = 'Ongeldig e-mailadres';
        }

        if (!message || message.length < 20) {
            errors.message = 'Geef een uitgebreidere omschrijving (minimaal 20 tekens)';
        }

        return errors;
    }

    /**
     * Toon veldfoutmeldingen
     */
    function showErrors(errors) {
        Object.entries(errors).forEach(([field, msg]) => {
            const errorEl = document.getElementById(`error-${field}`);
            const inputEl = document.getElementById(field);

            if (errorEl) errorEl.textContent = msg;
            if (inputEl) inputEl.classList.add('error');
        });

        // Focus eerste fout veld
        const firstErrorField = Object.keys(errors)[0];
        document.getElementById(firstErrorField)?.focus();
    }

    /**
     * Wis alle foutmeldingen
     */
    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        if (formMsg) formMsg.hidden = true;
    }

    /**
     * Toon algemene formbericht
     */
    function showFormMessage(msg, type) {
        if (!formMsg) return;
        formMsg.textContent = msg;
        formMsg.className = `form-message ${type}`;
        formMsg.hidden = false;
    }

    /**
     * Toggle laadstatus van de submit knop
     */
    function setLoading(loading) {
        if (!submitBtn) return;
        submitBtn.disabled = loading;
        if (btnText)    btnText.hidden    = loading;
        if (btnLoading) btnLoading.hidden = !loading;
    }
});
