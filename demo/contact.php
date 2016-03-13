<?php
include('../form-validation.php');

// Liste des champs du formulaire
$form = array(
	'nom' => array('type' => 'string', 'label' => 'nom', 'required' => true),
	'prenom' => array('type' => 'string', 'label' => 'prénom', 'required' => true),
	'email' => array('type' => 'email', 'label' => 'adresse email', 'required' => true),
	'site' => array('type' => 'url', 'label' => 'site web', 'required' => false),
	'message' => array('type' => 'string', 'label' => 'message', 'required' => true)
);

$validation = new FormValidation(); // Création de l'objet
$validation->setFields($form); // Déclaration des champs

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$validation->setSource($_POST); // On fournit les valeurs à l'objet de validation
	
	if ($validation->isValid()) { // On vérifie si le formulaire est valide
		$content = 'Nom : '.$validation->getFormValue('nom')."\r\n";
		$content .= 'Prénom : '.$validation->getFormValue('prenom')."\r\n";
		$content .= 'Adresse email : '.$validation->getFormValue('email')."\r\n";
		$content .= 'Site web : '.$validation->getFormValue('site')."\r\n";
		$content .= 'Message : '.$validation->getFormValue('message');
		
		if (@mail('email@domaine.com', 'Message de '.$validation->getFormValue('prenom'), $message)) {
			$message = '<p>Votre message a bien été envoyé.</p>';
			$validation->resetFormValues(); // Réinitialisation des valeurs pour ne pas qu'elles soient réaffichées
		} else {
			$message = '<p>Une erreur s\'est produite lors de l\'envoi du message.</p>';
		}
	} else {
		$message = '<p>Vous avez fait des erreurs :</p><ul><li>'.implode('</li><li>', $validation->getErrors()).'</li></ul>'; // Affichage des erreurs
	}
}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>formulaire de contact - Validez simplement vos formulaires en PHP</title>
	</head>

	<body>		
		<?php if (isset($message)) echo $message ?>
		
		<form method="post">
			<p>
				<label for="nom">Nom</label><br />
				<input type="text" name="nom" id="nom" value="<?=$validation->getFormValue('nom')?>" />
			</p>
			
			<p>
				<label for="prenom">Prénom</label><br />
				<input type="text" name="prenom" id="prenom" value="<?=$validation->getFormValue('prenom')?>"  />
			</p>
			
			<p>
				<label for="email">Email</label><br />
				<input type="text" name="email" id="email" value="<?=$validation->getFormValue('email')?>"  />
			</p>
			
			<p>
				<label for="site">Site web</label><br />
				<input type="text" name="site" id="site" value="<?=$validation->getFormValue('site')?>"  />
			</p>
			
			<p>
				<label for="message">Message</label><br />
				<textarea name="message" id="message"><?=$validation->getFormValue('message')?></textarea>
			</p>
			
			<input type="submit" value="Envoyer" />
		</form>
	</body>
</html>