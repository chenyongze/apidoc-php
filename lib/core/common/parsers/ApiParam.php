<?php
namespace api\parsers;

use core\common\Util;
use core\Config;

$groupFunc = function () {
    return '';
};

return [
    // Search: group, type, optional, fieldname, defaultValue, size, description
    // Example: {String{1..4}} [user.name='John Doe'] Users fullname.
    //
    // Naming convention:
    //     b -> begin
    //     e -> end
    //     name -> the field value
    //     oName -> wrapper for optional field
    //     wName -> wrapper for field
    'parse'         => function($content, $source, $defaultGroup = '') {
        // replace Linebreak with Unicode
        $content = preg_replace('/\n/', WRAP, trim($content));
        $parseRegExp = DS . Util::objectValuesToString(Config::get('api_param_reg')) . DS;

        preg_match($parseRegExp, $content, $matches);

        if (empty($matches)) {
            return null;
        }

        // todo match[4]
        $allowedValues = $matches[4];

        // Replace Unicode Linebreaks in description
        if ($matches[10]) {
            $matches[10] = preg_replace('/' . WRAP . '/', "\n", $matches[10]);
        }

        // Set global group variable
        $group = $matches[1] ?: ($defaultGroup ?: 'Parameter');

        return [
            'group'        => $group,
            'type'         => $matches[2],
            'size'         => $matches[3],
            'allowedValues'=> $allowedValues,
            'optional'     => ($matches[5] && $matches[5][0] === '[') ? true : false,
            'field'        => $matches[6],
            'defaultValue' => $matches[7] || $matches[8] || $matches[9],
            'description'  => Util::unindent($matches[10] ?: '')
        ];
    },

    'path'          => function() use ($groupFunc) {
        return 'local.parameter.fields.' . $groupFunc();
    },
    'method'        => 'push',
    'getGroup'      => $groupFunc,
    'markdownFields'=> ['description', 'type'],
    'markdownRemovePTags'=> ['type']
];