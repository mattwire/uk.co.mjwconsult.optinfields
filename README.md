# uk.co.mjwconsult.optinfields

![Screenshot](/images/contact_summary_communication_prefs.png)

This extension adds a set of 3 "Yes/No" fields to the contact summary which can be used in public facing forms to 
allow contacts to explicitly opt-in/opt-out of different communication methods (Phone/Text, Email, Post).

Exposing the "Do not Email"/"Do not Post" fields directly is not desirable by many organisations and does not make 
it easy for them to comply with GDPR requirements.

The appropriate "Do not" fields are automatically updated whenever the new fields are changed (but not the other way round).

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM 4.7.31 (untested with earlier versions)

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl uk.co.mjwconsult.optinfields@https://github.com/FIXME/uk.co.mjwconsult.optinfields/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/uk.co.mjwconsult.optinfields.git
cv en optinfields
```

## Usage

See screenshot.
