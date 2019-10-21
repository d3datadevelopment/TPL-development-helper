# Changelog
All notable changes to this project will be documented in this file.

---

## 2.0.1.0 (2019-10-21)
### Changed
- Mails können auch Dreingabeartikel regenieren und darstellen
- weitere Mailinformationen werden für Mailumleitung geändert
### Fixed
- verhindert Thankyou Redirect, wenn keine Order geladen wurde
- Debugging von Mails mit Dreingabebestellungen löscht diese Discounts in der gesamten Bestellung

---

## 2.0.0.0 (2018-02-23)
### Added
- verfügbar für OXID 6
- Installation via Composer

---

## 1.2.0.0 (2017-11-21)
### Added
- Mail-Anzeige fordert zusätzlich Authentfikation mit einem Shopadmin-Konto
- Seitenencoding definiert
### Changed
- Dokumentation ergänzt

---

## 1.1.0.0 (2017-05-31)
### Added
- Mailversand übers Shopframework wird blockiert oder
- Mails werden an alternative Mailadresse umgeleitet

---

## 1.0.0.0 (2015-12-16)
### Added
- unterbindet das Löschen des Warenkorbs nach Bestellabschluss
- Thankyou ist ohne Bestellabschluss aufrufbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)
- Bestellbestätigungsmails und (sofern D3-Modul installiert) Anfragebestätigungsmails sind im Browser darstellbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)
