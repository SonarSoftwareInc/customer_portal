<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Le :attribute doit être accepté.',
    'active_url'           => 'Le :attribute n’est pas une URL valide.',
    'after'                => 'Le :attribute doit être une date postérieure : date.',
    'alpha'                => 'Le :attribute peut contenir uniquement des lettres.',
    'alpha_dash'           => 'Le :attribute peut contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num'            => 'Le :attribute peut contenir uniquement des lettres et des chiffres.',
    'array'                => 'Le :attribute doit être un tableau.',
    'before'               => 'Le :attribute doit être une date avant :date.',
    'between'              => [
        'numeric' => 'Le :attribute doit être comprise entre :min et :max.',
        'file'    => 'Le :attribute doit être comprise entre :min et :max kilobytes.',
        'string'  => 'Le :attribute doit être comprise entre :min and :max characters.',
        'array'   => 'Le :attribute doit être comprise entre :min and :max items.',
    ],
    'boolean'              => 'Le :attribute domaine doit être vrai ou faux.',
    'confirmed'            => 'Le :attribute confirmation ne correspond pas à.',
    'date'                 => 'Le :attribute n’est pas une date valide.',
    'date_format'          => 'Le :attribute ne correspond pas au format :format.',
    'different'            => 'Le :attribute et :other doit être différent.',
    'digits'               => 'Le :attribute doit être :digits digits.',
    'digits_between'       => 'Le :attribute doit être comprise entre :min et :max digits.',
    'distinct'             => 'Le :attribute champ a une valeur en double.',
    'email'                => 'Le :attribute doit être une adresse email valide.',
    'exists'               => 'Le selected :attribute n’est pas valide.',
    'filled'               => 'Le :attribute le champ est requis.',
    'image'                => 'Le :attribute doit être une image.',
    'in'                   => 'Le sélectionné :attribute n’est pas valide.',
    'in_array'             => 'Le :attribute champ n’existe pas dans :other.',
    'integer'              => 'Le :attribute doit être un entier.',
    'ip'                   => 'Le :attribute doit être une adresse IP valide.',
    'json'                 => 'Le :attribute doit être une chaîne JSON valide.',
    'max'                  => [
        'numeric' => 'Le :attribute ne peut pas être supérieure à :max.',
        'file'    => 'Le :attribute ne peut pas être supérieure à :max kilobytes.',
        'string'  => 'Le :attribute ne peut pas être supérieure à :max characters.',
        'array'   => 'Le :attribute ne peut pas avoir plus de :max éléments.',
    ],
    'mimes'                => 'Le :attribute type de doit être un fichier de: :values.',
    'min'                  => [
        'numeric' => 'Le :attribute doit être au moins :min.',
        'file'    => 'Le :attribute doit être au moins :min kilobytes.',
        'string'  => 'Le :attribute doit être au moins :min characters.',
        'array'   => 'Le :attribute doit être au moins :min items.',
    ],
    'not_in'               => 'Le sélectionné :attribute n’est pas valide.',
    'numeric'              => 'Le :attribute\ doit être un numéro.',
    'present'              => 'Le :attribute domaine doit être présent.',
    'regex'                => 'Le :attribute format n’est pas valide.',
    'required'             => 'Le :attribute  le champ est requis.',
    'required_if'          => 'Le :attribute le champ est requis :other est :value.',
    'required_unless'      => 'Le :attribute le champ est requis à moins que :other est en :values.',
    'required_with'        => 'Le :attribute le champ est requis quand :values is present.',
    'required_with_all'    => 'Le :attribute le champ est requis :values is present.',
    'required_without'     => 'Le :attribute le champ est requis :values is not present.',
    'required_without_all' => 'Le :attribute le champ est requis lorsque aucune des :values sont présents.',
    'same'                 => 'Le :attribute et :autres doivent correspondre.',
    'size'                 => [
        'numeric' => 'Le :attribute doit être :size.',
        'file'    => 'Le :attribute doit être :size kilobytes.',
        'string'  => 'Le :attribute doit être :size characters.',
        'array'   => 'Le :attribute doit contenir :size items.',
    ],
    'string'               => 'The :attribute doit être une chaîne.',
    'timezone'             => 'The :attribute doit être une zone valide.',
    'unique'               => 'The :attribute a déjà été pris.',
    'url'                  => 'The :attribute format n’est pas valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'cc-number' => 'Numéro de carte de crédit',
        'name' => 'nom',
        'expirationDate' => 'date d’expiration',
        'role' => 'rôle',
        'email_address' => 'adresse de courriel',
        'work_phone' => 'Téléphone de travail',
        'mobile_phone' => 'Téléphone mobile',
        'home_phone' => 'Téléphone à la maison',
        'fax' => 'faxe',
        'current_password' => 'mot de passe actuel',
        'new_password' => 'nouveau mot de passe',
        'new_card' => 'nouvelle carte',
        'payment_method' => 'mode de paiement',
        'paypal' => 'PayPal',
        'subject' => 'Objet',
        'description' => 'description',
    ],

];
