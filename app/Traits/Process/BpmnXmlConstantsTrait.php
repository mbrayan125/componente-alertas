<?php

namespace App\Traits\Process;

trait BpmnXmlConstantsTrait
{
    private const XML_ACTIVITY_IDENTIFIERS = [
        '//bpmn:task',
        '//bpmn:serviceTask',
        '//bpmn:userTask'
    ];
    
    private const XML_EVENT_IDENTIFIERS = [
        '//bpmn:startEvent',
        '//bpmn:endEvent'
    ];

    private const XML_GATEWAY_IDENTIFIER = [
        '//bpmn:exclusiveGateway',
        '//bpmn:inclusiveGateway',
        '//bpmn:parallelGateway'
    ];

    private const XML_LANES_IDENTIFIER = [
        '//bpmn:lane'
    ];

    private const XML_LANES_ELEMENTS_IDENTIFIER = [
        'bpmn:flowNodeRef'
    ];

    private const XML_SEQUENCES_IDENTIFIER = [
        '//bpmn:sequenceFlow'
    ];
}