# sos-users
# Info
`docker-compose up` per l'esecuzione dell'ambiente locale di *SOS*.
Lo stack comprende un container `web`, contenente `php7.2`, `nginx`, `joomla` e `simpleSAMLphp`, e un `db` mysql.
# Istruzioni
- `cp .env.template .env` (ed eventuale modifica delle variabili d'ambiente)
- avviare l'ambiente di sviluppo con `npm run start`

# Note
Il container di sviluppo risponde a `localhost:8000`
Le credenziali di amministratore (`localhost:8000/administrator`) dell'ambiente locale sono:
- user: `admin`
- password: `admin`

# SAML Login
Container docker sample-scuola integrato con SimpleSAMLphp
che permette ai siti delle scuole di svolgere la funzionalità
di Identity Provider (IDp) SAML per i servizi Google.  
##

### Componenti:
- Modulo Joomla! `SAML Login`:
    - Mostra una pagina di login agli utenti che si stanno autenticando
    con SAML, e ne gestisce il logout.
- Authentication source di SimpleSAMLphp `JoomlaAuth`:  
    - Dirige l'utente alla pagina di login del modulo `SAML Login` e
     alla pagina di Google che stava cercando di visitare.
     All'untente verrà richiesto di inserire le credenziali di Joomla!
     per poter proseguire.
##

### Installazione:
Per eseguire questa applicazione sono necessari `Docker` e `docker-compose`:
  - https://docs.docker.com/install/
  - https://docs.docker.com/compose/install/

Configurare le variabili d'ambiente del container:  
  1. `$ cp .env.template .env` e modificare `.env`
##

### Testare la soluzione localmente:  
  1. Aggiungere la seguente configurazione al proprio file hosts:
    
         127.0.0.1 localhost       #(Solo se non già presente)
         127.0.0.1 saml.localhost
    
  2. Avviare i container con `$ docker-compose up`
  3. Joomla!, Joomla! Admin e SimpleSAMLphp saranno accessibili rispettivamente a:
     - http://localhost/
     - http://localhost/administrator
     - http://saml.localhost/simplesaml
  4. Installare il componente `SAML Login` accedendo al pannello
  admin di Joomla! e cliccando su:  
  `Extensions > Manage > Install > Install from folder > Check and install`
  5. Rinominare il file `simplesamlphp/config/authsources.php.sample` a `simplesamlphp/config/authsources.php`
  6. Modificare il campo `redirect_url` nel file `simplesamlphp/config/authsources.php`.  
    `redirect_url => http://localhost/index.php?option=com_samllogin`  
    (localhost è il dominio dell'installazione di Joomla!)
  7. È possibile testare il componente `JoomlaAuth` accedendo al pannello admin
  di SimpleSAMLphp (credenziali: admin@admin) e cliccando su:
  `Autenticazione > Prova le fonti di autenticazione configurate > joomlamodule:JoomlaAuth`
##

### Utilizzo con Google Suite:
  1. Generare i certificati RSA per SAML:  
    `sh generate-cert.sh`  
    Verranno generati due file nella cartella `cert`:  
    `googleappsidp.pem` e `googleappsidp.crt`
  2. Configurare autenticazione con Joomla!:  
    Modificare i campi `redirect_url` e `verify_url` nel file `simplesamlphp/config/authsources.php`.  
    `redirect_url`: Url in cui si trova la pagina di login del modulo Joomla! `SAML Login`.
    (Sarà necessario sostituire a DOMAIN_NAME il dominio dell'account Google da configurare).  
    `verify_url`: Url chiamato da SimpleSAMLphp per ottenere gli attributi di un
    utente, dopo che questo si è loggato su Joomla!. (Può essere necessario cambiare l'hostname
    se SimpleSAMLphp e Joomla! non sono hostati sulla stessa macchina).
  3. Sostituire a DOMAIN_NAME il dominio dell'account Google da configurare nel file
  `simplesamlphp/metadata/saml20-sp-remote.php`
  4. Configurare l'account Google:
      1. Collegarsi ad `admin.google.com` e accedere alla sezione "Sicurezza"
      2. Cliccare su "Imposta single-sign-on (SSO)"
  5. Abilitare la spunta "Configura SSO con provider di identità di terze parti"
  6. Inserire i seguenti valori (Sostituendo a DOMAIN_NAME il valore appropriato):   
      `URL pagina di accesso` -> `http://saml.DOMAIN_NAME/simplesaml/saml2/idp/SSOService.php`  
      `URL della pagina di uscita` -> `http://saml.DOMAIN_NAME/simplesaml/module.php/core/authenticate.php?as=joomlamodule%3AJoomlaAuth&logout`  
  7. Caricare il certificato `googleappsidp.crt` generato in precedenza e salvare.
  8. Avviare i container con `$ docker-compose up`
  9. Per ulteriori informazioni, consultare: https://simplesamlphp.org/docs/stable/simplesamlphp-googleapps
