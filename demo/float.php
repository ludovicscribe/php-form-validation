<?php
include('../form-validation.php');

// Liste des champs du formulaire
$form = array(
	'number1' => array('type' => 'float', 'label' => 'nombre 1', 'required' => false, 'sanitize' => false),
	'number2' => array('type' => 'float', 'min' => 5.5, 'label' => 'nombre 2', 'required' => false, 'sanitize' => false),
	'number3' => array('type' => 'float', 'max' => 15.3, 'label' => 'nombre 3', 'required' => false, 'sanitize' => false),
	'number4' => array('type' => 'float', 'min' => 20.8, 'max' => 35.7, 'label' => 'nombre 4', 'required' => false, 'sanitize' => false)
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
		<title>Nombre à virgule - Validez simplement vos formulaires en PHP</title>
	</head>

	<body>		
		<?php if (isset($message)) echo $message ?>
		
		<form method="post">
			<p>
				<label for="number1">Nombre 1 : Entrez un nombre</label><br />
				<input type="text" name="number1" id="number1" value="<?=$validation->getFormValue('number1')?>" />
			</p>
			
			<p>
				<label for="number2">Nombre 2 : Entrez un nombre superieur ou égal à 5.5</label><br />
				<input type="text" name="number2" id="number2" value="<?=$validation->getFormValue('number2')?>" />
			</p>
			
			<p>
				<label for="number3">Nombre 3 : Entrez un nombre inférieur ou égal à 15.3</label><br />
				<input type="text" name="number3" id="number3" value="<?=$validation->getFormValue('number3')?>" />
			</p>
			
			<p>
				<label for="number4">Nombre 4 : Entrez un nombre compris entre 20.8 et 35.7</label><br />
				<input type="text" name="number4" id="number4" value="<?=$validation->getFormValue('number4')?>" />
			</p>
			
			<input type="submit" value="Envoyer" />
		</form>
	</body>
</html>