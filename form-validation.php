<?php
/**
 * PHP form validation
 *
 * This standalone class allows you to simply validate HTML forms.
 *
 * PHP version 5
 *
 * @author    Scribe Ludovic (http://ludovicscribe.fr/)
 * @link      https://github.com/ludovicscribe/php-form-validation
 * @version   1.0
 */
class FormValidation {
    // Validation errors
	private $errors = array();
	
	// Validation parameters
    private $fields = array();

	// Input values
	private $source = array();
	
	// Error messages
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
	
	//========================================================
	// 						General
	//========================================================

	/**
	 * Setting validation parameters
	 *
	 * @param array $fields Validation parameters
	 */
    public function setFields(array $fields) {
        $this->fields = $fields;
    }
	
	/**
	 * Getting specific form value
	 *
	 * @param array $field Field name
	 *
	 * @return string
	 */
	public function getFormValue($field) {
		return isset($this->source[$field]) ? $this->source[$field] : null;
	}
	
	/**
	 * Getting all form values
	 *
	 * @return array
	 */
	public function getFormValues() {
		$values = array();
		foreach($this->fields as $field => $opt) $vaues[$field] = $this->getFormValue($field);
		return $values;
	}
	
	/**
	 * Cleaning up form values
	 */
	public function resetFormValues() {
		foreach($this->source as $field => $value) $this->source[$field] = null;
	}
	
	/**
	 * Setter for form values
	 *
	 * @param array Input values
	 */
	public function setSource(array $source) {
		$this->source = $source;
	}
	
	/**
	 * Getter for error messages
	 *
	 * @return array Error messages
	 */
	public function getErrors() {
		return $this->errors;
	}
	
	/**
	 * Getting filed display label
	 *
	 * @param string $field Field name
	 *
	 * @return string Display label
	 */
	private function getLabel($field) {
		return $this->fields[$field]['label'];
	}
	
	/**
	 * Getting error message
	 *
	 * @param string $key    Message identifier
	 * @param string $field  Field name
	 * @param array  $params Additional settings
	 *
	 * @return string Error message
	 */
	private function getMessage($key, $field, $params = array()) {
		$message = $this->messages[$key];
		$message = str_replace('{fieldlabel}', $this->getLabel($field), $message);
		for($i = 0; $i < count($params); $i++) $message = str_replace('{param'.($i + 1).'}', $params[$i], $message);
		return $message;
	}
	
	//========================================================
	// 					   Validation
	//========================================================
		
	/**
	 * Form validation
	 *
	 * @return bool Result
	 */
	public function isValid() {	
		// Input filtering
		$this->filterSource();
		
		$error = false;
		
		foreach($this->fields as $field => $opt) {          	
			// If field is empty and not required, we can validate next
			// field, if field is empty and required, there are error
			if ($this->isEmpty($field, $opt['required'])) {
				if ($opt['required']) $error = true;
				continue;
			}

			// Getting optional parameters
			$min = array_key_exists('min', $opt) ? $opt['min'] : null;
			$max = array_key_exists('max', $opt) ? $opt['max'] : null;
			$case_sensitive = isset($opt['case_sensitive']) ? $opt['case_sensitive'] : true;
			
			// Format validation
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
					if (!$this->validateEquals($field, $opt['value'], $case_sensitive)) $error = true;
					break;
					
				case 'equals_field':
					if (!$this->validateEqualsField($field, $opt['field'], $case_sensitive)) $error = true;
					break;
				
				// Unknown type
				default:
					$error = true;
					break;
			}
        }
		
		return !$error;
    }

	/**
	 * Check if field is not empty
	 *
	 * @param string $field Field name
	 * @param bool $required Defines if field is required
	 *
	 * @return bool Validation result
	 */
    private function isEmpty($field, $required) {
        if (!isset($this->source[$field]) || empty($this->source[$field])) {
			if ($required) $this->errors[$field] = $this->getMessage('not_set', $field);
			return true;
		} else {
			return false;
		}
    }
		
	/**
	 * Check if string field is valid
	 *
	 * @param string $field Field name
	 * @param int $min_length Minimum content length
	 * @param int $max_length Maximum content length
	 *
	 * @return bool Validation result
	 */
    private function validateString($field, $min_length = null, $max_length = null) {
        if (!is_string($this->source[$field])) {
			$this->errors[$field] = $this->getMessage('not_valid', $field);
			return false;
		} else if (isset($min_length) && strlen($this->source[$field]) < $min_length) {
            $this->errors[$field] = $this->getMessage('string_short', $field, array($min_length));
			return false;
        } else if (isset($max_length) && strlen($this->source[$field]) > $max_length) {
            $this->errors[$field] = $this->getMessage('string_long', $field, array($max_length));
			return false;
        } else {
			return true;
		}
    }

	/**
	 * Check if int field is valid
	 *
	 * @param string $field Field name
	 * @param int $min Minimum field value
	 * @param int $max Minimum field value
	 *
	 * @return bool Validation result
	 */
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
	
	/**
	 * Check if float field is valid
	 *
	 * @param string $field Field name
	 * @param int $min Minimum field value
	 * @param int $max Maximum field value
	 *
	 * @return bool Validation result
	 */
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

	/**
	 * Check if email field is valid
	 *
	 * @param string $field Field name
	 *
	 * @return bool Validation result
	 */
	private function validateEmail($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_EMAIL) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_email', $field);
			return false;
        } else {
			return true;
		}
    }
	
	/**
	 * Check if URL field is valid
	 *
	 * @param string $field Field name
	 *
	 * @return bool Validation result
	 */
    private function validateUrl($field) {
        // Adding the protocol if it's not set
		$url = $this->source[$field];
		if ($parts = parse_url($url) && !isset($parts["scheme"])) $url = 'http://'.$url;
		
		if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_url', $field);
			return false;
        } else {
			return true;
		}
    }

	/**
	 * Check if IP v4 field is valid
	 *
	 * @param string $field Field name
	 *
	 * @return bool Validation result
	 */
    private function validateIpv4($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_ipv4', $field);
			return false;
        } else {
			return true;
		}
    }

	/**
	 * Check if IP v6 field is valid
	 *
	 * @param string $field Field name
	 *
	 * @return bool Validation result
	 */
    public function validateIpv6($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) {
            $this->errors[$field] = $this->getMessage('format_ipv6', $field);
			return false;
        } else {
			return true;
		}
    }
	
	/**
	 * Check if bool field is valid
	 *
	 * @param string $field Field name
	 *
	 * @return bool Validation result
	 */
    private function validateBool($field) {
        if (filter_var($this->source[$field], FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = $this->getMessage('not_valid', $field);
			return false;
        } else {
			return true;
		}
    }
	
	/**
	 * Check if field match specified value
	 *
	 * @param string $field Field name
	 * @param string $value Value
	 * @param string $case_sensitive Defines if verification is case sensitive
	 *
	 * @return bool Validation result
	 */
	private function validateEquals($field, $value, $case_sensitive) {
        if ($case_sensitive && $this->source[$field] != $value || !$case_sensitive && strtolower($this->source[$field]) != strtolower($value)) {
            $this->errors[$field] = $this->getMessage('not_equals', $field);
			return false;
        } else {
			return true;
		}
    }
	
	/**
	 * Check if field match specified field
	 *
	 * @param string $field Field name
	 * @param string $value Other field name
	 * @param string $case_sensitive Defines if verification is case sensitive
	 *
	 * @return bool Validation result
	 */
	private function validateEqualsField($field, $valueField, $case_sensitive) {
        if ($case_sensitive && $this->source[$field] != $this->source[$valueField] || !$case_sensitive && strtolower($this->source[$field]) != strtolower($this->source[$valueField])) {
            $this->errors[$field] = $this->getMessage('not_equals_field', $field, array($this->getLabel($valueField)));
			return false;
        } else {
			return true;
		}
    }

	//========================================================
	// 					  Filtering
	//========================================================
	
	/**
	 * Filtering input values
	 */
	private function filterSource() {
		foreach($this->fields as $field => $opt) {
			$value = $this->source[$field];			
			
			// Removing beginning and ending spaces
			if (!array_key_exists('trim', $opt) || array_key_exists('trim', $opt) && $opt['trim'] == true) $value = trim($value);
			
			// Removing unauthorized characters
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
	
	/**
	 * Filtering unauthorized characters for string field type
	 *
	 * @param string $value Input value
	 *
	 * @return string Filtered value
	 */
    private function sanitizeString($value) {
		return (string)filter_var($value, FILTER_SANITIZE_STRING);
    }
	
	/**
	 * Filtering unauthorized characters for int field type
	 *
	 * @param string $value Input value
	 *
	 * @return int Filtered value
	 */
    private function sanitizeInt($value) {
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

	/**
	 * Filtering unauthorized characters for float field type
	 *
	 * @param string $value Input value
	 *
	 * @return float Filtered value
	 */
    private function sanitizeFloat($value) {
		return (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
	
	/**
	 * Filtering unauthorized characters for email field type
	 *
	 * @param string $value Input value
	 *
	 * @return string Filtered value
	 */
	private function sanitizeEmail($value) {
        $email = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $value);
        return (string)filter_var($email, FILTER_SANITIZE_EMAIL);
    }

	/**
	 * Filtering unauthorized characters for URL field type
	 *
	 * @param string $value Input value
	 *
	 * @return string Filtered value
	 */
    private function sanitizeUrl($value) {
        return (string)filter_var($value, FILTER_SANITIZE_URL);
    }
		
	/**
	 * Filtering unauthorized characters for bool field type
	 *
	 * @param string $value Input value
	 *
	 * @return bool Filtered value
	 */
	private function sanitizeBool($value) {
        return (bool)$value;
    }
}