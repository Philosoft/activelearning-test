<?php

namespace Todo;

return [
    "doctrine" => [
        "driver" => [
            __NAMESPACE__ . "_entity" => [
                "class" => "Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver",
                "paths" => [
                    __DIR__ . "/../src/" . __NAMESPACE__ . "/Entity"
                ]
            ],
            "orm_default" => [
                "drivers" => [
                    __NAMESPACE__ . "\\Entity" => __NAMESPACE__ . "_entity"
                ]
            ]
        ]
    ],
    "router" => [
        "routes" => [
            "todo" => [
                "type" => "segment",
                "options" => [
                    "route" => "/api/todo[/:id]",
                    "constrains" => [
                        "id" => "[0-9]+"
                    ],
                    "defaults" => [
                        "controller" => "Todo\\Controller\\Todo",
                    ]
                ]
            ]
        ]
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5'
    ),
];
