<?php
defined('_JEXEC') or die;

function create_user(&$service, $email, $full_name, $password = null, $id = null) {
  $id = ($id === 0) ? null : $id;
  $first_name = explode(" ", $full_name, 2)[0];
  $family_name = explode(" ", $full_name, 2)[1];

  $user = new Google_Service_Directory_User();
  $app = JFactory::getApplication();

  $username = new Google_Service_Directory_UserName();
  $username->givenName = $first_name;
  $username->familyName = $family_name;
  $username->fullName = $full_name;

  $user->setId($id);
  $user->setPrimaryEmail($email);
  $user->setName($username);
  $user->setPassword($password);

  try {
    $service->users->insert($user);
  } catch (Google_Service_Exception $error) {
    switch ($error->getCode()) {
      case 403:
        throw new Exception('Impossibile creare l\'utente. Controlla di aver inserito un indirizzo email corretto.', 403); //TODO lingua
        break;
      case 409:
        throw new Exception('Utente già esistente.', 409);//TODO lingua
        break;
      case 400:
        throw new Exception('Errore nei dati inseriti.', 400);//TODO lingua
        break;
      default:
        throw new Exception('Errore. Se l\'errore persiste contatta un amministratore.', 400); //TODO lingua
        break;
    }
  }

  $app->enqueueMessage('Utente Google creato con successo.', 'message'); //TODO Language
}
