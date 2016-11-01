<?php
return [
    'wrap' => '##{WRAP}##',

    'parser_prefix_reg' => '/^(api)/g',

    'api_param_reg' => [
        'b' =>               '^',                                   // start
        'oGroup' => [                                               // optional 'group'=> (404)
            'b' =>               '\\s*(?:\\(\\s*',                  // starting with '(', optional surrounding spaces
            'group' =>              '(.+?)',                        // 1
            'e' =>               '\\s*\\)\\s*)?'                    // ending with ')', optional surrounding spaces
        ],
        'oType' => [                                                // optional 'type'=> {string}
            'b' =>               '\\s*(?:\\{\\s*',                  // starting with '{', optional surrounding spaces
            'type' =>                '([a-zA-Z0-9\(\)#:\\.\\/\\\\\\[\\]_-]+)', // 2
            'oSize' => [                                            // optional size within 'type'=> {string{1..4}}
                'b' =>               '\\s*(?:\\{\\s*',              // starting with '{', optional surrounding spaces
                'size' =>                '(.+?)',                   // 3
                'e' =>               '\\s*\\}\\s*)?'                // ending with '}', optional surrounding spaces
            ],
            'oAllowedValues' => [                                   // optional allowed values within 'type'=> {string='abc','def'}
                'b' =>               '\\s*(?:=\\s*',                // starting with '=', optional surrounding spaces
                'possibleValues' =>      '(.+?)',                   // 4
                'e' =>               '(?=\\s*\\}\\s*))?'            // ending with '}', optional surrounding spaces
            ],
            'e' =>               '\\s*\\}\\s*)?'                    // ending with '}', optional surrounding spaces
        ],
        'wName' => [
            'b' =>               '(\\[?\\s*',                       // 5 optional optional-marker
            'name' =>                '([a-zA-Z0-9\\:\\.\\/\\\\_-]+',   // 6
            'withArray' =>           '(?:\\[[a-zA-Z0-9\\.\\/\\\\_-]*\\])?)', // https://github.com/apidoc/apidoc-core/pull/4
            'oDefaultValue' => [                                    // optional defaultValue
                'b' =>               '(?:\\s*=\\s*(?:',             // starting with '=', optional surrounding spaces
                'withDoubleQuote' =>     '"([^"]*)"',               // 7
                'withQuote' =>           '|\'([^\']*)\'',           // 8
                'withoutQuote' =>        '|(.*?)(?:\\s|\\]|$)',     // 9
                'e' =>               '))?'
            ],
            'e' =>               '\\s*\\]?\\s*)'
        ],
        'description' =>         '(.*)?',                           // 10
        'e' =>               '$|@'
    ]
];