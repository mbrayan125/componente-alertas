<?php

namespace App\Traits\ProcessElement;

use App\Models\ProcessElement;
use App\Traits\Process\ElementsTypesConstantsTrait;

trait ProcessElementAssertsTrait
{
    use ElementsTypesConstantsTrait;

    /**
     * Checks if the given ProcessElement is a gateway.
     *
     * @param ProcessElement $element The ProcessElement to check.
     * @return bool Returns true if the ProcessElement is a gateway, false otherwise.
     */
    private function isGateway(ProcessElement $element): bool
    {
        return $element->type === self::GATEWAY;
    }

    /**
     * Checks if the given ProcessElement is an activity.
     *
     * @param ProcessElement $element The ProcessElement to check.
     * @return bool Returns true if the ProcessElement is an activity, false otherwise.
     */
    private function isActivity(ProcessElement $element): bool
    {
        return $element->type === self::ACTIVITY;
    }

    /**
     * Checks if the given ProcessElement is an event.
     *
     * @param ProcessElement $element The ProcessElement to check.
     * @return bool Returns true if the ProcessElement is an event, false otherwise.
     */
    private function isEvent(ProcessElement $element): bool
    {
        return $element->type === self::EVENT;
    }

    /**
     * Checks if a given ProcessElement is a join gateway.
     *
     * A join gateway is a type of gateway in a BPMN process that has multiple incoming elements and a single outgoing element.
     *
     * @param ProcessElement $gateway The ProcessElement to check.
     * @return bool Returns true if the ProcessElement is a join gateway, false otherwise.
     */
    private function isJoinGateway(ProcessElement $gateway): bool
    {
        if (!$this->isGateway($gateway)) {
            return false;
        }
        $incomingElements = $gateway->incomingElements;
        $outgoingElements = $gateway->outgoingElements;
        return count($incomingElements) > 1 && count($outgoingElements) === 1;
    }

    /**
     * Checks if a given ProcessElement is a split gateway.
     *
     * @param ProcessElement $gateway The ProcessElement to check.
     * @return bool Returns true if the ProcessElement is a split gateway, false otherwise.
     */
    private function isSplitGateway(ProcessElement $gateway): bool
    {
        if (!$this->isGateway($gateway)) {
            return false;
        }
        $incomingElements = $gateway->incomingElements;
        $outgoingElements = $gateway->outgoingElements;
        return count($incomingElements) === 1 && count($outgoingElements) > 1;
    }

    /**
     * Verifies if a process element is an end event.
     *
     * @param ProcessElement $element The process element to verify.
     * 
     * @return bool
     */
    private function isEndEvent(ProcessElement $element): bool
    {
        return $this->isEvent($element) && $element->subtype === 'endEvent';
    }

    /**
     * Checks if the given ProcessElement is an end activity.
     *
     * @param ProcessElement $element The ProcessElement to check.
     * 
     * @return bool Returns true if the ProcessElement is an end activity, false otherwise.
     */
    private function isEndActivity(ProcessElement $element): bool
    {
        if (!$this->isActivity($element)) {
            return false;
        }

        $outgoingElements = $element->outgoingElements;
        if (empty($outgoingElements) || count($outgoingElements) > 1) {
            return false;
        }

        $nextElement = $outgoingElements[0];
        return $this->isEndEvent($nextElement);
    }

    /**
     * Checks if the given ProcessElement is a start event.
     *
     * @param ProcessElement $element The ProcessElement to check.
     * @return bool Returns true if the ProcessElement is a start event, false otherwise.
     */
    private function isStartEvent(ProcessElement $element): bool
    {
        return $this->isEvent($element) && $element->subtype === 'startEvent';
    }
}