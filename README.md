# PHP form validation

This standalone class allows you to simply validate HTML forms. French speaking people can visit my website for usage description, I think my french is better than my english : http://ludovicscribe.fr/blog/validation-formulaires-php.

## Getting started

First, you need to download the project archive and put the "form-validation.php" file in your project directory. Then, just include it : 

```php
include('form-validation.php');
```

## Setting parameters

Next, you need to declare the fields you want to validate with few parameters. Below, you can see a list of required and optionnal parameters and how to declare them.

### Required parameters

**For all verification types :**

- **Field name :** It's the associative array key.
- **Type (string) :** Validation type, choose among "string", "int", "float", "email", "url", "ipv4", "ipv6", "bool", "equals" and "equals_field".
- **Label :** Field label displayed in errors.
- **Required (bool) :** Defines if the field is required or not.

**For "equals" verification type :**

- **Value (string) :** The value with which the field must match.

**For "equals_field" verification type :**

- **Field (string) :** The field name with which the field must match.

### Optional parameters

**For all verification types :**

- **Trim (bool, default true) :** Defines whether beginning and ending spaces must be deleted.

- **Sanitize (bool, default true) :** Specifies whether field content must be cleaned prior validation (removing unauthorized characters).

**For "string", "int" and "float" verification types :**

- **Min (int / float) :** If verification type is "string", this parameter specifies the minimum field content length. If verification type is "int" or "float", it defines the minimum value entered in field.
- **Max (int / float) :** If verification type is "string", this parameter specifies the maximum field content length. If verification type is "int" or "float", it defines the maximum value entered in field.

**For "equals" and "equals_field" verification type :**

- **Case_sensitive (bool, default true) :** Defines if value checking must be case sensitive. 

### Object creation and parameters setting

```php
$params = array(
	'firstname' => array('type' => 'string',
'label' => 'first name', 'required' => true),
	'lastname' => array('type' => 'string', 'label' => 'last name', 'required' => true),
	'email' => array('type' => 'email', 'label' => 'email', 'required' => true)
);

$validation = new FormValidation();
$validation->setFields($params);
```

## Validate your form

**Give form values to validation object :**

```php
$validation->setSource($_POST);
````

**Validate your form and show result :**

```php
if ($validation->isValid()) {
    echo 'Form is valid.';
} else {
    echo '<p>There are errors :</p><ul><li>'.implode('</li><li>', $validation->getErrors()).'</li></ul>';
}
```

**If there are errors, you can print input values in fields :**

```php
<p>
	<label for="firstname">First name</label>
	<input type="text" name="firstname" id="firstname" value="<?=$validation->getFormValue('firstname')?>" />
</p>
			
<p>
	<label for="lastname">Last name</label>
	<input type="text" name="lastname" id="lastname" value="<?=$validation->getFormValue('lastname')?>"  />
</p>
```

**If there are no errors, you can reset form values to ensure that the visitor not repost them :**

```php
$validation->resetFormValues();
```

You can see examples for all validation types in "demo" folder.

## Advanced configuration

Default messages are written in french but you can edit them, there are specified in the "$messages" array in the "form-validation.php" file.