<section class="quote-section">
    <div class="container">
        <div class="section-header">
            <h1>Offerte aanvragen</h1>
            <p>Vertel ons over je project en we sturen je binnen 24 uur een vrijblijvende offerte.</p>
        </div>

        <div class="quote-wrapper">
            <form id="quoteForm" class="quote-form" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Naam <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Je volledige naam"
                            autocomplete="name"
                        >
                        <span class="field-error" id="error-name"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mailadres <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="jouw@email.nl"
                            autocomplete="email"
                        >
                        <span class="field-error" id="error-email"></span>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefoonnummer</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="+31 6 12345678"
                            autocomplete="tel"
                        >
                    </div>

                    <div class="form-group">
                        <label for="service">Gewenste dienst</label>
                        <select id="service" name="service">
                            <option value="">Kies een dienst...</option>
                            <option value="WordPress website">WordPress website</option>
                            <option value="Webshop (WooCommerce)">Webshop (WooCommerce)</option>
                            <option value="Custom PHP applicatie">Custom PHP applicatie</option>
                            <option value="Plugin ontwikkeling">Plugin ontwikkeling</option>
                            <option value="Website onderhoud">Website onderhoud</option>
                            <option value="Anders">Anders</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Budget</label>
                        <div class="budget-options">
                            <label class="budget-option">
                                <input type="radio" name="budget" value="< €500">
                                <span>&lt; €500</span>
                            </label>
                            <label class="budget-option">
                                <input type="radio" name="budget" value="€500 - €1.500">
                                <span>€500 – €1.500</span>
                            </label>
                            <label class="budget-option">
                                <input type="radio" name="budget" value="€1.500 - €5.000">
                                <span>€1.500 – €5.000</span>
                            </label>
                            <label class="budget-option">
                                <input type="radio" name="budget" value="> €5.000">
                                <span>&gt; €5.000</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="message">Omschrijving project <span class="required">*</span></label>
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            placeholder="Beschrijf je project zo uitgebreid mogelijk. Wat moet de website doen? Heb je al een design? Wanneer wil je live gaan?"
                        ></textarea>
                        <span class="field-error" id="error-message"></span>
                        <small class="char-count"><span id="charCount">0</span> / 20 tekens minimaal</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-text">Offerte aanvragen</span>
                        <span class="btn-loading" hidden>Versturen...</span>
                    </button>
                </div>

                <div id="formMessage" class="form-message" hidden></div>
            </form>

            <aside class="quote-sidebar">
                <div class="sidebar-card">
                    <div class="sidebar-icon">&#9200;</div>
                    <h3>Snelle reactie</h3>
                    <p>We reageren binnen 24 uur op je aanvraag met een vrijblijvende offerte.</p>
                </div>
                <div class="sidebar-card">
                    <div class="sidebar-icon">&#128274;</div>
                    <h3>Vertrouwelijk</h3>
                    <p>Je gegevens worden veilig opgeslagen en nooit gedeeld met derden.</p>
                </div>
                <div class="sidebar-card">
                    <div class="sidebar-icon">&#127775;</div>
                    <h3>Kwaliteit gegarandeerd</h3>
                    <p>Al onze projecten worden opgeleverd met uitgebreide documentatie.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
