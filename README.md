TPL Development Helper
======================

Das Tool soll bei täglichen Entwicklungsaufgaben im OXID eShop helfen, die (systembedingt) vom Shopsystem erschwert werden.

* Mailversand übers Shopframework wird blockiert __oder__
* Mails werden an alternative Mailadresse umgeleitet

- unterbindet das Löschen des Warenkorbs nach Bestellabschluss
- Thankyou-Seite ist auch ohne Bestellabschluss aufrufbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)
- Bestellbestätigungsmails und sind im Browser darstellbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)

Hinweise zur Benutzung und Konfiguration sind in der Metadata-Modulbeschreibung enthalten.
Diese können nach Installation im Backend des OXID-Shops unter "Erweiterungen -> Module" eingesehen werden.

Alle Optionen sind einzeln aktivierbar. Der Shop darf nicht im Produktivmodus laufen.

__Da das Modul einige Sicherheitsmechanismen des Shops deaktiviert, ist bei einem Einsatz in von extern erreichbaren Systemen besondere Vorsicht nötig!__