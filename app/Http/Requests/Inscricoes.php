<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Models\User;

class Inscricoes extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'lowercase', 'cpf', 'max:255', 'unique:'.User::class],
        ];
    }

    public function withValidator($validator){
        $validator->after(function($validator){
            $year = date('Y');
            $email = $this->input('email');
            $cpf = $this->input('cpf');

            if(User::select('ano')->whereAnd('email',$email)->where('cpf',$cpf)->where('ano',$year)){
                $validator->errors()->add('errors','Sua Inscrição ja foi Realizada!');
            }

        });
    }

}
