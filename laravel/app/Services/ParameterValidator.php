<?php

namespace App\Services;

use App\Models\Parameter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ParameterValidationService
{
    public function getDefinitionsForCategory(int $categoryId)
    {
        return Parameter::where('category_id', $categoryId)->get();
    }

    
    public function validateValues(int $categoryId, array $values): array
    {
        $parameters = $this->getDefinitionsForCategory($categoryId);

        $rules = [];
        foreach ($parameters as $param) {
            $rule = $param->is_required ? ['required'] : ['nullable'];

            switch ($param->data_type) {
                case 'string':
                    $rule[] = 'string';
                    break;
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'boolean':
                    $rule[] = 'boolean';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
            }

            $rules[$param->name] = implode('|', $rule);
        }

        $validator = Validator::make($values, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}