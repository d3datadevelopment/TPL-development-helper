# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased](https://git.d3data.de/D3Public/devhelper/compare/4.0.0.0...rel_4.x)

## [4.0.0.0](https://git.d3data.de/D3Public/devhelper/compare/3.0.3.0...4.0.0.0) (2024-09-04)
### Added
- Modul verwendbar in OXID 7.x

### Removed
- Modul verwendbar in OXID < 7

## [3.0.3.0](https://git.d3data.de/D3Public/devhelper/compare/3.0.2.0...3.0.3.0) (2023-05-12)
### Changed
- Modul verwendbar in OXID 6.5.2

## [3.0.2.0](https://git.d3data.de/D3Public/devhelper/compare/3.0.0.1...3.0.2.0) (2021-08-19)
### Changed
- Modul verwendbar in OXID 6.3.1

## [3.0.0.1](https://git.d3data.de/D3Public/devhelper/compare/3.0.0.0...3.0.0.1) (2021-04-28)
### Changed
- Modul verwendbar in OXID 6.3
### Fixed
- falsche prepared statement Kombination korrigiert

## [3.0.0.0](https://git.d3data.de/D3Public/devhelper/compare/2.0.1.0...3.0.0.0) (2020-11-11)
### Changed
- Modul verwendbar in OXID 6.2, deprecated Code bestmöglich entfernt
- Anzeige von Anfragemails entfernt, da dazugehöriges Modul nicht weiter entwickelt wird

## [2.0.1.0](https://git.d3data.de/D3Public/devhelper/compare/2.0.0.0...2.0.1.0) (2019-10-21)
### Changed
- Mails können auch Dreingabeartikel regenerieren und darstellen
- weitere Mailinformationen werden für Mailumleitung geändert
### Fixed
- verhindert Thankyou Redirect, wenn keine Order geladen wurde
- Debugging von Mails mit Dreingabebestellungen löscht diese Discounts in der gesamten Bestellung

## [2.0.0.0](https://git.d3data.de/D3Public/devhelper/compare/1.2.0.0...2.0.0.0) (2018-02-23)
### Added
- verfügbar für OXID 6
- Installation via Composer

## [1.2.0.0](https://git.d3data.de/D3Public/devhelper/compare/1.1.0.0...1.2.0.0) (2017-11-21)
### Added
- Mail-Anzeige fordert zusätzlich Authentfikation mit einem Shopadmin-Konto
- Seitenencoding definiert
### Changed
- Dokumentation ergänzt

## [1.1.0.0](https://git.d3data.de/D3Public/devhelper/compare/1.0.0.0...1.1.0.0) (2017-05-31)
### Added
- Mailversand übers Shopframework wird blockiert oder
- Mails werden an alternative Mailadresse umgeleitet

## [1.0.0.0](https://git.d3data.de/D3Public/devhelper/releases/tag/1.0.0.0) (2015-12-16)
### Added
- unterbindet das Löschen des Warenkorbs nach Bestellabschluss
- Thankyou ist ohne Bestellabschluss aufrufbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)
- Bestellbestätigungsmails und (sofern D3-Modul installiert) Anfragebestätigungsmails sind im Browser darstellbar (unter Angabe der Bestellnummer auch für eine bestimmte Bestellung)
