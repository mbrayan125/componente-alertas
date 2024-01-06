<?php

namespace App\UseCases\Process;

use App\DataResultObjects\Process\ReadBpmnUseCaseDRO;
use App\Traits\Process\BpmnXmlConstantsTrait;
use App\Traits\Process\ElementsTypesConstantsTrait;
use App\UseCases\Process\Contracts\ReadBpmnProcessUseCaseInterface;
use Exception;
use SimpleXMLElement;

/**
 * Use case to read a BPMN process.
 * 
 * This use case is responsible for reading a BPMN process and returning the
 * information of the process elements, lanes and sequence flows.
 */
class ReadBpmnProcessUseCase implements ReadBpmnProcessUseCaseInterface
{
    use BpmnXmlConstantsTrait;
    use ElementsTypesConstantsTrait;

    private SimpleXMLElement $bpmnXml;

    private array $validationErrors   = [];
    private array $validationWarnings = [];
    private array $lanes              = [];
    private array $sequenceFlows      = [];
    private array $processElements    = [];

    /**
     * @inheritDoc
     */
    public function __invoke(string $bpmnPath): ReadBpmnUseCaseDRO
    {
        if (!$this->loadBpmnXml($bpmnPath)) {
            return ReadBpmnUseCaseDRO::createFailure(['Error en la lectura del proceso BPMN']);
        }

        $this->loadAllElements();

        if (sizeof($this->validationErrors) > 0) {
            return ReadBpmnUseCaseDRO::createFailure($this->validationErrors, $this->validationWarnings);
        }

        return ReadBpmnUseCaseDRO::createSuccess(
            $this->lanes,
            $this->processElements[self::EVENT],
            $this->processElements[self::ACTIVITY],
            $this->processElements[self::GATEWAY]
        )->setWarnings($this->validationWarnings);
    }

    /**
     * Loads the BPMN XML file.
     *
     * @param string $bpmnPath The path to the BPMN XML file.
     * 
     * @return bool Returns true if the BPMN XML file was successfully loaded, false otherwise.
     */
    private function loadBpmnXml(string $bpmnPath): bool
    {
        try {
            $this->bpmnXml = simplexml_load_file($bpmnPath);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Loads all elements.
     *
     * @return void
     */
    private function loadAllElements(): void
    {
        $this->loadLanes();
        $this->loadSequenceFlows();
        $this->loadProcessElements(self::EVENT, self::XML_EVENT_IDENTIFIERS);
        $this->loadProcessElements(self::ACTIVITY, self::XML_ACTIVITY_IDENTIFIERS);
        $this->loadProcessElements(self::GATEWAY, self::XML_GATEWAY_IDENTIFIER);
    }

    /**
     * Loads the lanes for the BPMN process.
     *
     * This method is responsible for loading the lanes of the BPMN process.
     * It does not return any value.
     */
    private function loadLanes(): void
    {
        $lanesXml = $this->bpmnXml->xpath($this->createXpathQuery(self::XML_LANES_IDENTIFIER));
        $allLanes = $this->getElementsFromXmlArray(
            $lanesXml, 
            'id', 
            ['name' => 'name'], 
            [ 'associations' => self::XML_LANES_ELEMENTS_IDENTIFIER ]
        );

        foreach ($allLanes as $laneId => $lane) {
            $elementsInLane = $this->getElementsFromXmlArray(
                $lane['associations'], 
                'element_id', 
                ['element_id' => 'element']
            );
            $this->lanes[$laneId] = [
                'name'     => $lane['name'],
                'elements' => $elementsInLane
            ];

        }
    }

    /**
     * Loads the sequence flows.
     *
     * @return void
     */
    private function loadSequenceFlows(): void
    {
        $sequenceFlowsXml = $this->bpmnXml->xpath($this->createXpathQuery(self::XML_SEQUENCES_IDENTIFIER));
        $this->sequenceFlows = $this->getElementsFromXmlArray(
            $sequenceFlowsXml,
            'id',
            [
                'target' => 'targetRef',
                'source' => 'sourceRef',
                'name'   => 'name'
            ]
        );
    }

    /**
     * Loads the process elements of a specific type from the BPMN process.
     *
     * @param string $elementType The type of process element to load.
     * @param array $bpmnTypes An array of BPMN types to filter the process elements.
     * 
     * @return void
     */
    private function loadProcessElements(string $elementType, array $bpmnTypes)
    {
        $xmlElements = $this->bpmnXml->xpath($this->createXpathQuery($bpmnTypes));
        $arrayElements = $this->getElementsFromXmlArray(
            $xmlElements,
            'id',
            [
                'id'   => 'id',
                'name' => 'name',
                'type' => 'type',
            ],
            [
                'incoming_xml' => 'bpmn:incoming',
                'outgoing_xml' => 'bpmn:outgoing'
            ]
        );

        $arrayElements = array_map(function($element) {
            $elementsIncoming = [];
            $elementsOutgoing = [];

            foreach ($element['incoming_xml'] as $incoming) {
                $elementIncoming = $this->sequenceFlows[(string) $incoming];
                $elementsIncoming[] = [
                    'element_id' => $elementIncoming['source'],
                    'name'       => $elementIncoming['name']
                ];
            }
            foreach ($element['outgoing_xml'] as $outgoing) {
                $elementOutgoing = $this->sequenceFlows[(string) $outgoing];
                $elementsOutgoing[] = [
                    'element_id' => $elementOutgoing['target'],
                    'name'       => $elementOutgoing['name']
                ];
            }

            unset($element['incoming_xml']);
            unset($element['outgoing_xml']);

            $elementLane = $this->getLaneFromElementId($element['id']);
            if (is_null($elementLane)) {
                $this->validationErrors[] = sprintf(
                    'El elemento %s no pertenece a ningún lane',
                    $element['name']
                );
            }

            $element['lane'] = $elementLane;
            $element['incoming'] = $elementsIncoming;
            $element['outgoing'] = $elementsOutgoing;

            return $element;
        }, $arrayElements);

        $this->processElements[$elementType] = $arrayElements;
    }

    /**
     * Creates an XPath query based on the given identifiers.
     *
     * @param array $identifiers The identifiers to be used in the XPath query.
     * 
     * @return string The generated XPath query.
     */
    private function createXpathQuery($identifiers): string
    {
        if (is_array($identifiers)) {
            return implode(' | ', $identifiers);
        }

        if (is_string($identifiers)) {
            return $identifiers;
        }
    }

    /**
     * Retrieves the lane associated with the given element ID.
     *
     * @param string $elementId The ID of the element.
     * 
     * @return string|null The lane associated with the element ID, or null if not found.
     */
    private function getLaneFromElementId(string $elementId): ?string
    {
        foreach ($this->lanes as $laneId => $lane) {
            if (array_key_exists($elementId, $lane['elements'])) {
                return $laneId;
            }
        }
        return null;
    }

    /**
     * Retrieves elements from an XML array based on specified criteria.
     *
     * @param array $xmlElements The XML elements to retrieve from.
     * @param string|null $identifier The identifier used to match elements in the $elementsProperties array.
     * @param array $elementsProperties The properties of the elements to retrieve.
     * @param array $xmlChildrens The XML children elements to retrieve for each element.
     * 
     * @return array The retrieved elements.
     */
    private function getElementsFromXmlArray(
        array $xmlElements,
        ?string $identifier,
        array $elementsProperties, 
        array $xmlChildrens = []
    ): array {

        $autoIdentifier = false;
        $identifierAdded = false;
        $elements = [];

        if (is_null($identifier)) {
            $autoIdentifier = true;
        } else if (!array_key_exists($identifier, $elementsProperties)) {
            $elementsProperties['id'] = $identifier;
            $identifierAdded = true;
        }

        $elementIndex = 0;
        foreach ($xmlElements as $xmlElement) {
            $element = $this->getElementProperties($xmlElement, $elementsProperties);
            $elementId = $autoIdentifier ? $elementIndex : $element[$identifier];
            if ($identifierAdded) {
                unset($element['id']);
            }
            $childElements = [];
            foreach ($xmlChildrens as $arrayName => $xmlChild) {
                $childElements = $xmlElement->xpath($this->createXpathQuery($xmlChild));
                $element[$arrayName] = $childElements;
            }
            $elements[$elementId] = $element;
            $elementIndex++;
        }

        return $elements;
    }

    /**
     * Retrieves the properties of an XML element.
     *
     * @param SimpleXMLElement $xmlElement The XML element to retrieve properties from.
     * @param array $elementProperties An array containing the properties of the element.
     * 
     * @return array The properties of the XML element.
     */
    private function getElementProperties(SimpleXMLElement $xmlElement, array $elementProperties): array
    {
        $arrayElement = [];
        foreach ($elementProperties as $arrayName => $elementProperty) {
            switch ($elementProperty) {
                case 'element':
                    $arrayElement[$arrayName] = (string) $xmlElement;
                    break;
                case 'type':
                    $arrayElement[$arrayName] = $xmlElement->getName();
                    break;
                default:
                    $arrayElement[$arrayName] = (string) $xmlElement[$elementProperty];
                    break;
            }
        }
        return $arrayElement;
    }
}