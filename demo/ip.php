<?php
include('../form-validation.php');

// Fields list
$form = array(
	'ipv4' => array('type' => 'ipv4', 'label' => 'IP V4', 'required' => false),
	'ipv6' => array('type' => 'ipv6', 'label' => 'IP V6', 'required' => false)
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