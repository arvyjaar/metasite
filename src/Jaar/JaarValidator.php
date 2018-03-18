<?php

namespace App\Jaar;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class JaarValidator {

    /**
     * @param string $email
     * @param string $name
     * @param array $categories
     */
    public function __construct($email = null, $name = null, $categories = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->categories = $categories;
    }

    /**
     * @return array|null
     */
    public function validate()
    {
        $formErrors = [];
        $validator = Validation::createValidator();
        $violations = $validator->validate($this->email, [
            new NotBlank(),
            new Email(),
        ]);
        if (0 !== count($violations)) {
            array_push($formErrors, "Email not valid");
        }
        $violations = $validator->validate($this->name, [
            new NotBlank()
        ]);
        if (0 !== count($violations)) {
            array_push($formErrors, "Name field should not be blank");
        }
        $violations = (null == $this->categories) ? 'No categories selected' : null;
        if (isset($violations)) {
            array_push($formErrors, $violations);
        }
        if (0 !== count($formErrors)) {
            return $formErrors;
        } else {
            return null;
        }
    }
}
