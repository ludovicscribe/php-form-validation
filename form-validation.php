<?php
class FormValidation {
    private $errors = array();
    private $fields = array();
	private $source = array();
	
	private $messages = array(
		'not_set' => 'Le champ "{fieldlabel}" n\'est pas renseigné.',
		'not_equals' => 'Le champ "{fieldlabel}" ne contient pas la bonne valeur.',
		'not_equals_field' => 'Le champ "{fieldlabel}" n\'est pas égal au champ "{param1}".',
		'not_valid' => 'Le champ "{fieldlabel}" n\'est pas valide.',
		'format_ipv4' => 'Le champ "{fieldlabel}" ne contient pas une IPv4 valide.',
		'format_ipv6' => 'Le champ "{fieldlabel}" ne contient pas une IPv6 valide.',
		'format_numeric' => 'Le champ "{fieldlabel}" ne contient pas un nombre valide.',
		'format_url' => 'Le champ "{fieldlabel}" ne contient pas une url valide.',
		'format_email' => 'Le champ "{fieldlabel}" ne contient pas une adresse email valide.',
		'string_short' => 'Le champ "{fieldlabel}" doit contenir au moins {param1} caractère.',
		'string_long' => 'Le champ "{fieldlabel}" doit contenir moins de {param1} caractère.',
		'numeric_high' => 'Le champ "{fieldlabel}" doit contenir un nombre inférieur ou égal à {param1}.',
		'numeric_low' => 'Le champ "{fieldlabel}" doit contenir un nombre supérieur ou égal à {param1}.'
	);
	
	########## Méthodes diverses ############
    public function setFields(array $fields) {
        $this->fields = $fields;
    }
	
	public function getFormValue($field) {
		return isset($this->source[$field]) ? $this->source[$field] : null;
	}
	
	public function getFormValues() {
		$values = array();
		
		foreach($this->fields as $field => $opt) {
			$values[$field] = isset($this->source[$field]) ? $this->source[$field] : null;
		}
		
		return $values;
	}
	
	public function resetFormValues() {
		foreach($this->source as $field => $value) $this->source[$field] = null;
	}
	
	public function setSource(array $source) {
		$this->source = $source;
	}
	
	public function getErrors() {
		return $this->errors;
	}
	
	private function getLabel($field) {
		return $this->fields[$field]['label'];
	}
	
	private function getMessage($key, $field, $params = array()) {
		$message = $this->messages[$key];
		$message = str_replace('{fieldlabel}', $this->getLabel($field), $message);
		for($i = 0; $i < count($params); $i++) $message = str_replace('{param'.($i + 1).'}', $params[$i], $message);
		return $message;
	}
	
	########## Méthodes de vérification ############
	private function filterSource() {
		// Traitement sur les valeurs
		foreach($this->fields as $field => $opt) {
			$value = $this->source[$field];			
			
			// Suppression des espaces au début et à la fin
			if (!array_key_exists('trim', $opt) || array_key_exists('trim', $opt) && $opt['trim'] == true) $value = trim($value);
			
			// Filtrage des données
			if (!array_key_exists('sanitize', $opt) || array_key_exists('sanitize', $opt) && $opt['sanitize'] == true) {
				if ($opt['type'] == 'numeric') $value = $this->sanitizeInt($value);
				else if ($opt['type'] == 'float') $value = $this->sanitizeFloat($value);
				else if ($opt['type'] == 'email') $value = $this->sanitizeEmail($value);
				else if ($opt['type'] == 'url') $value = $this->sanitizeUrl($value);
				else if ($opt['type'] == 'bool') $value = $this->sanitizeBool($value);
				else $value = $this->sanitizeString($value);
			}
			
			$this->source[$field] = $value;
		}
	}
	
	public function isValid() {	
		// Filtrage des valeurs avant la validation
		$this->filterSource();
		
		$error = false;
		
		foreach($this->fields as $field => $opt) {          	
			// On vérifie si le champ est rempli
			if ($this->isEmpty($field, $opt['required'])) {
				if ($opt['required']) $error = true;
				continue;
			}

			// On vérifie le format
			$min = array_key_exists('min', $opt) ? $opt['min'] : null;
			$max = array_key_exists('max', $opt) ? $opt['max'] : null;
			
			switch ($opt['type']) {
				case 'string':
					if (!$this->validateString($field, $min, $max)) $error = true;
					break;
					
				case 'int':
					if (!$this->validateInt($field, $min, $max)) $error = true;
					break;
					
				case 'float':
					if (!$this->validateFloat($field, $min, $max)) $error = true;
					break;
					
				case 'email':
					if (!$this->validateEmail($field)) $error = true;
					break;
					
				case 'url':
					if (!$this->validateUrl($field)) $error = true;
					break;
					
				case 'ipv4':
					if (!$this->validateIpv4($field)) $error = true;
					break;
					
				case 'ipv6':
					if (!$this->validateIpv6($field)) $error = true;
					break;
					
				case 'bool':
					if (!$this->validateBool($field)) $error = true;
					break;
				
				case 'equals':
					$case_sensitive = isset($opt['case_sensitive']) ? $opt['case_sensitive'] : true;
					if (!$this->validateEquals($field, $opt['value'], $case_sensitive)) $error = true;
					break;
					
				case 'equals_field':
					$case_sensitive = isset($opt['case_sensitive']) ? $opt['case_sensitive'] : true;
					if (!$this->validateEqualsField($field, $opt['field'], $case_sensitive)) $error = true;
					break;
				
				// Type inconnu
				default:
					$error = true;
					break;
			}
        }
		
		return !$error;
    }

	########## Méthodes de validation ############
    private function isEmpty($field, $required) {
        if (!isset($this->source[$field]) || empty($this->source[$field])) {
			if ($required) $this->errors[$field] = $this->getMessage('not_set', $field);
			return true;
		} else {
			return false;
		}
    }
		
    private function validateString($field, $minLength = null, $maxLength = null) {
        if (!is_string($this->source[$field])) {
			$this->errors[$field] = $this->getMessage('not_valid', $field);
			return false;
		} else if (isset($minLength) && strlen($this->source[$field]) < $minLength) {
            $this->errors[$field] = $this->getMessage('string_short', $field, array($minLength));
			return false;
        } else if (isset($maxLength) && strlen($this->source[$field]) > $maxLength) {
            $this->errors[$field] = $this->getMessage('string_long', $field, array($maxLength));
			return false;
        } else {
			return true;
		}
    }

    private function validateInt($field, $min = null, $max = null) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_INT) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_numeric', $field);
			return false;
        } else if (isset($min) && $this->source[$field] < $min) {
			$this->errors[$field] = $this->getMessage('numeric_low', $field, array($min));
			return false;
		} else if (isset($max) && $this->source[$field] > $max) {
			$this->errors[$field] = $this->getMessage('numeric_high', $field, array($max));
			return false;
		} else {
			return true;
		}		
    }
	
    private function validateFloat($field, $min = null, $max = null) {		
		if (filter_var($this->source[$field], FILTER_VALIDATE_FLOAT) === false) {
            $this->errors[$field] = $this->getMessage('format_numeric', $field);
			return false;
        } else if (isset($min) && $this->source[$field] < $min) {
			$this->errors[$field] = $this->getMessage('numeric_low', $field, array($min));
			return false;
		} else if (isset($max) && $this->source[$field] > $max) {
			$this->errors[$field] = $this->getMessage('numeric_high', $field, array($max));
			return false;
		}  else {
			return true;
		}
    }

	private function validateEmail($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_EMAIL) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_email', $field);
			return false;
        } else {
			return true;
		}
    }
	
    private function validateUrl($field) {
        // Si le protocole n'est pas fourni on l'ajoute
		$url = $this->source[$field];
		if ($parts = parse_url($url) && !isset($parts["scheme"])) $url = 'http://'.$url;
		
		if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_url', $field);
			return false;
        } else {
			return true;
		}
    }

    private function validateIpv4($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_ipv4', $field);
			return false;
        } else {
			return true;
		}
    }

    public function validateIpv6($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_ipv6', $field);
			return false;
        } else {
			return true;
		}
    }
	
    private function validateBool($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = $this->getMessage('not_valid', $field);
			return false;
        } else {
			return true;
		}
    }
	
	private function validateEquals($field, $value, $case_sensitive) {
        if ($case_sensitive && $this->source[$field] != $value || !$case_sensitive && strtolower($this->source[$field]) != strtolower($value)) {
            $this->errors[$field] = $this->getMessage('not_equals', $field);
			return false;
        } else {
			return true;
		}
    }
	
	private function validateEqualsField($field, $valueField, $case_sensitive) {
        if ($case_sensitive && $this->source[$field] != $this->source[$valueField] || !$case_sensitive && strtolower($this->source[$field]) != strtolower($this->source[$valueField])) {
            $this->errors[$field] = $this->getMessage('not_equals_field', $field, array($this->getLabel($valueField)));
			return false;
        } else {
			return true;
		}
    }

    ########## Méthodes de filtrage ############
    private function sanitizeString($value) {
		return (string)filter_var($value, FILTER_SANITIZE_STRING);
    }
	
    private function sanitizeInt($value) {
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    private function sanitizeFloat($value) {
		return (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
	
	private function sanitizeEmail($value) {
        $email = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $value);
        return (string)filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    private function sanitizeUrl($value) {
        return (string)filter_var($value, FILTER_SANITIZE_URL);
    }
		
	private function sanitizeBool($value) {
        return (bool)$value;
    }
}