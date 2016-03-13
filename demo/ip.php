<?php
include('../form-validation.php');

// Liste des champs du formulaire
$form = array(
	'ipv4' => array('type' => 'ipv4', 'label' => 'IP V4', 'required' => false),
	'ipv6' => array('type' => 'ipv6', 'label' => 'IP V6', 'required' => false)
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
		<title>Adresse IP - Validez simplement vos formulaires en PHP</title>
	</head>

	<body>		
		<?php if (isset($message)) echo $message ?>
		
		<form method="post">
			<p>
				<label for="ipv4">Entrez une adresse IP v4 (exemple : 192.168.1.1)</label><br />
				<input type="text" name="ipv4" id="ipv4" value="<?=$validation->getFormValue('ipv4')?>" />
			</p>
			
			<p>
				<label for="ipv6">Entrez une adresse IP v6 (exemple : 5800:10C3:E3C3:F1AA:48E3:D923:D494:AAFF)</label><br />
				<input type="text" name="ipv6" id="ipv6" value="<?=$validation->getFormValue('ipv6')?>" />
			</p>
			
			<input type="submit" value="Envoyer" />
		</form>
	</body>
</html>