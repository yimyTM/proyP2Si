<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regla de validación: contraseña segura.
 *
 * Requisitos obligatorios:
 *   ① Mínimo 8 caracteres
 *   ② Al menos 1 letra mayúscula (A-Z)
 *   ③ Al menos 1 letra minúscula (a-z)
 *   ④ Al menos 1 dígito (0-9)
 *   ⑤ Al menos 1 carácter especial (!@#$%^&*-_=+?)
 */
class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('La contraseña debe tener al menos 8 caracteres.');
            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('La contraseña debe contener al menos una letra mayúscula.');
            return;
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('La contraseña debe contener al menos una letra minúscula.');
            return;
        }

        if (! preg_match('/[0-9]/', $value)) {
            $fail('La contraseña debe contener al menos un número.');
            return;
        }

        if (! preg_match('/[!@#$%^&*\-_=+?]/', $value)) {
            $fail('La contraseña debe contener al menos un carácter especial (!@#$%^&*-_=+?).');
        }
    }
}
