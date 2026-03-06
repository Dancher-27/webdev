# WordPress-stijl PHP Portfolio Project

Dit is een **PHP portfolio project** waarin ik enkele concepten uit WordPress heb nagebouwd zonder WordPress zelf te gebruiken.
Het doel van dit project is om te laten zien hoe systemen zoals **hooks, plugins en custom post types** werken in een webapplicatie.

## Gebruikte technologieën

* PHP (OOP)
* MySQL
* JavaScript (AJAX)
* HTML / CSS
* PDO (database verbinding)

---

## Functionaliteiten

### Frontend

* **Homepage** met projecten
* **Projecten pagina** met een overzicht van alle projecten
* **Offerte formulier** waar gebruikers een aanvraag kunnen sturen

Het offerteformulier gebruikt **AJAX** zodat de pagina niet opnieuw hoeft te laden.

---

### Admin panel

Het project bevat een simpel **admin panel** waar je data kunt beheren:

* Dashboard met statistieken
* Overzicht van offerteaanvragen
* Mail log van verzonden berichten
* Beheer van projecten (custom post types)

---

## Belangrijke concepten in dit project

Dit project demonstreert enkele concepten die ook in WordPress worden gebruikt:

**Hooks systeem**
Een eigen versie van `add_action()` en `do_action()` om functies aan events te koppelen.

**Custom Post Types**
Projecten worden opgeslagen als een eigen post type met extra metadata.

**Plugin structuur**
De functionaliteit is opgebouwd als een plugin met eigen classes.

**Mail API simulatie**
Een eenvoudige mail-API die e-mails logt in de database.

---

## Projectstructuur

```
wordpress-custom/
│
├── config/        # database configuratie
├── core/          # basis functies en hooks systeem
├── plugins/       # plugin functionaliteit
├── admin/         # admin dashboard
├── api/           # AJAX endpoints
├── templates/     # herbruikbare pagina onderdelen
├── assets/        # CSS en JavaScript
└── database/      # database schema
```

---

## Installatie

1. Plaats het project in je webserver map (bijv. XAMPP htdocs)
2. Maak een MySQL database aan
3. Importeer het SQL bestand uit de `database` map
4. Pas de database instellingen aan in `config/database.php`

Daarna kan het project lokaal worden geopend via:

```
http://localhost/wordpress-custom
```

---

## Doel van dit project

Dit schoolproject is gemaakt als **leer- en portfolio project** om mijn kennis van PHP, databases en webarchitectuur te laten zien. Het laat zien hoe je een eenvoudige CMS-achtige structuur kunt bouwen met concepten die ook in WordPress worden gebruikt.

---

