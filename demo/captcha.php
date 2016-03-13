<?php
include('../form-validation.php');

session_start();

// Génération de la chaîne de caractères
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) $randomString .= $characters[rand(0, $charactersLength - 1)];
    
	$_SESSION['captcha'] = $randomString;
}

// Liste des champs du formulaire
$form = array(
	'captcha' => array('type' => 'equals', 'value' => $_SESSION['captcha'], 'label' => 'vérification', 'required' => true),
);

$validation = new FormValidation(); // Création de l'objet
$validation->setFields($form); // Déclaration des champs

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$validation->setSource($_POST); // On fournit les valeurs à l'objet de validation
	
	if ($validation->isValid()) { // On vérifie si le formulaire est valide
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
		<title>Captcha - Validez simplement vos formulaires en PHP</title>
	</head>

	<body>		
		<?php if (isset($message)) echo $message ?>
		
		<form method="post">
			<p>
				<label for="prenom">Recopiez le texte suivant : <?=$_SESSION['captcha']?></label><br />
				<input type="text" name="captcha" id="captcha" />
			</p>
			
			<input type="submit" value="Envoyer" />
		</form>
	</body>
</html>