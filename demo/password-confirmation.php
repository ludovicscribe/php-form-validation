<?php
include('../form-validation.php');

// Fields list
$form = array(
	'password' => array('type' => 'string', 'label' => 'mot de passe', 'required' => true),
	'passwordconf' => array('type' => 'equals_field', 'field' => 'password', 'label' => 'confirmation du mot de passe', 'required' => true)
);

// Object creation and parameters definition
$validation = new FormValidation();
$validation->setFields($form);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Form values setting
	$validation->setSource($_POST);
	
	// Form validation
	if ($validation->isValid()) {
		$message = '<p>Le formulaire est valide.</p>';
	} else {
		$message = '<p>Vous avez fait des erreurs :</p><ul><li>'.implode('</li><li>', $validation->getErrors()).'</li></ul>'; // Affichage des erreurs
	}
}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Confirmation du mot de passe - Validez simplement vos formulaires en PHP</title>
	</head>

	<body>		
		<?php if (isset($message)) echo $message ?>

		<form method="post">
			<p>
				<label for="nom">Mot de passe</label><br />
				<input type="password" name="password" id="password" />
			</p>
			
			<p>
				<label for="prenom">Confirmation du mot de passe</label><br />
				<input type="password" name="passwordconf" id="passwordconf"  />
			</p>
			
			<input type="submit" value="Envoyer" />
		</form>
	</body>
</html>