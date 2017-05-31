TPL Development Helper
======================

Das Tool soll bei täglichen Entwicklungsaufgaben im OXID eShop helfen, die (systembedingt) vom Shopsystem erschwert werden.

* Mailversand (übers Shopframework) übers Shopframework wird blockiert __oder__
* Mails (übers Shopframework) werden an alternative Mailadresse umgeleitet
    (Das Tool setzt direkt an der oxemail::_sendMail()-Methode an und kann damit __jeden__ Mailversand kontrollieren, der übers Framework läuft. Man muss nicht X verschiedene Module überwachen und hat auch Kontrolle über Mailerweiterungen, die keinen Stage-Einsatz vorsehen.)

- unterbindet das Löschen des Warenkorbs nach Bestellabschluss
- Thankyou-Seite ist auch ohne Bestellabschluss aufrufbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)
- Bestellbestätigungsmails und sind im Browser darstellbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)

Hinweise zur Benutzung und Konfiguration sind in der Metadata-Modulbeschreibung enthalten.
Diese können nach Installation im Backend des OXID-Shops unter "Erweiterungen -> Module" eingesehen werden.

Alle Optionen sind einzeln aktivierbar. Der Shop darf nicht im Produktivmodus laufen.

__Da das Modul einige Sicherheitsmechanismen des Shops deaktiviert, ist bei einem Einsatz in von extern erreichbaren Systemen besondere Vorsicht nötig!__