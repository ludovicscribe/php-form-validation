<?php
include('../form-validation.php');

session_start();

// Random string generation
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) $randomString .= $characters[rand(0, $charactersLength - 1)];
    
	$_SESSION['captcha'] = $randomString;
}

// Fields list
$form = array(
	'captcha' => array('type' => 'equals', 'value' => $_SESSION['captcha'], 'label' => 'vérification', 'required' => true),
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