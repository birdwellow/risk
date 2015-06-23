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

	"accepted"             => ":attribute muss akzeptiert werden.",
	"active_url"           => ":attribute ist keine g&uuml;tige URL.",
	"after"                => ":attribute muss ein Datum nach :date sein.",
	"alpha"                => ":attribute darf nur Buchstaben enthalten.",
	"alpha_dash"           => ":attribute darf nur Buchstaben, Nummern und Schr&auml;gstriche enthalten.",
	"alpha_num"            => ":attribute darf nur Buchstaben und Nummern enthalten.",
	"array"                => ":attribute muss ein Feld (Array) sein.",
	"before"               => ":attribute muss ein Datum vor :date sein.",
	"between"              => [
		"numeric" => ":attribute muss zwischen :min und :max sein.",
		"file"    => ":attribute muss zwischen :min und :max kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss zwischen :min und :max Zeichen lang sein.",
		"array"   => ":attribute muss zwischen :min und :max Elemente enthalten.",
	],
	"boolean"              => ":attribute muss 'wahr' oder 'falsch' sein.",
	"confirmed"            => ":attribute Best&auml;tigung weicht ab.",
	"date"                 => ":attribute ist kein g&uuml;tiges Datum.",
	"date_format"          => ":attribute hat nicht das Format :format.",
	"different"            => ":attribute und :other m&uuml;ssen unterschiedlich sein.",
	"digits"               => ":attribute muss :digits Ziffern lang sein enthalten.",
	"digits_between"       => ":attribute muss zwischen :min und :max Ziffern lang sein.",
	"email"                => ":attribute muss eine g&uuml;tige E-Mail-Adresse sein.",
	"filled"               => ":attribute wird ben&ouml;tigt.",
	"exists"               => "Die Auswahl f&uuml;r :attribute ung&uuml;tig.",
	"image"                => ":attribute muss ein Bild sein.",
	"in"                   => "Die Auswahl f&uuml;r :attribute ung&uuml;tig.",
	"integer"              => ":attribute muss eine ganze Zahl sein.",
	"ip"                   => ":attribute muss eine g&uuml;tige IP-Adresse sein.",
	"max"                  => [
		"numeric" => ":attribute darf nicht gr&uuml;&szlig;er als :max sein.",
		"file"    => ":attribute darf nicht gr&uuml;&szlig;er als :max kilobytes sein.",
		"string"  => ":attribute darf nicht mehr als :max Zeichen lang sein.",
		"array"   => ":attribute darf nicht mehr als :max Elemente haben.",
	],
	"mimes"                => ":attribute muss eine Datei vom Typ :values sein.",
	"min"                  => [
		"numeric" => ":attribute muss mindestens :min sein.",
		"file"    => ":attribute muss be at least :min kilobytes gro&szlig; sein.",
		"string"  => ":attribute muss mindestens :min Zeichen lang sein.",
		"array"   => ":attribute muss mindestens :min Elemente haben.",
	],
	"not_in"               => "Die Auswahl f&uuml;r :attribute ist ung&uuml;ltig.",
	"numeric"              => ":attribute muss eine Zahl sein.",
	"regex"                => ":attribute ist ung&uuml;ltig.",
	"required"             => ":attribute wird ben&ouml;tigt.",
	"required_if"          => ":attribute wird ben&ouml;tigt, wenn :other :value ist.",
	"required_with"        => ":attribute wird ben&ouml;tigt, wenn :values gesetzt ist.",
	"required_with_all"    => ":attribute wird ben&ouml;tigt, wenn :values gesetzt ist.",
	"required_without"     => ":attribute wird ben&ouml;tigt, wenn :values nicht gesetzt ist.",
	"required_without_all" => ":attribute wird ben&ouml;tigt, wenn keiner von :values are present.",
	"same"                 => ":attribute und m&uuml;ssen :other &uuml;bereinstimmen.",
	"size"                 => [
		"numeric" => ":attribute muss :size gro&szlig; sein.",
		"file"    => ":attribute muss :size kilobyte gro&szlig; sein.",
		"string"  => ":attribute muss :size Zeichen lang sein.",
		"array"   => ":attribute muss :size Elemente enthalten.",
	],
	"unique"               => ":attribute ist leider schon in Benutzung.",
	"url"                  => ":attribute ist keine g&uuml;ltige URL.",
	"timezone"             => ":attribute muss eine g&uuml;ltige Zeitzone sein.",

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
            'username' => "Username",
            'email' => "E-Mail",
        ],

];
