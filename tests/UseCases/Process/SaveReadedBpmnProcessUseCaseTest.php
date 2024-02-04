<?php

namespace Tests;

use App\DataResultObjects\Process\ReadBpmnUseCaseDRO;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Traits\Process\ElementsTypesConstantsTrait;
use App\UseCases\Process\Contracts\SaveReadedBpmnProcessUseCaseInterface;
use Database\Factories\ProcessFactory;
use Tests\TestCase;

class SaveReadedBpmnProcessUseCaseTest extends TestCase
{
    use ElementsTypesConstantsTrait;

    /**
     * Test case for the test_save_readed_process_success method case 1.
     */
    public function test_save_readed_process_success_case_1()
    {
        $this->test_save_readed_process_success('Process/valid_readed_process_1.json');
    }

    /**
     * Test case for the test_save_readed_process_success method case 2.
     */
    public function test_save_readed_process_success_case_2()
    {
        $this->test_save_readed_process_success('Process/valid_readed_process_2.json');
    }

    /**
     * Test case for the test_save_readed_process_success method case 1.
     */
    private function test_save_readed_process_success(string $resource)
    {
        // Prepare the test
        $mockedProcessJson = file_get_contents($this->getResourcePath($resource));
        $mockedDataArray = json_decode($mockedProcessJson, true)['data'];
        $readMockedResult = ReadBpmnUseCaseDRO::createSuccess(
            $mockedDataArray['lanes'],
            $mockedDataArray['events'],
            $mockedDataArray['activities'],
            $mockedDataArray['gateways']
        );
        [ $expectedProcessElementsParams, $expectedProcessElementsConnections ] = $this->generateExpectedCalls($readMockedResult);

        // Execute use case
        $targetProcess = ProcessFactory::new()->create();
        $saveReadedBpmnProcess = app()->make(SaveReadedBpmnProcessUseCaseInterface::class);
        $saveReadedBpmnProcess($readMockedResult, $targetProcess);

        // Validate the creation of the elements
        $createdProcesElementsIds = [];
        $processElementRepository = app()->make(ProcessElementRepositoryInterface::class);
        foreach ($expectedProcessElementsParams as $elementXmlId => $expectedProcessElementParams) {
            $expectedProcessElementParams['process_id'] = $targetProcess->id;
            $this->seeInDatabase('process_elements', $expectedProcessElementParams);
            $createdProcesElement = $processElementRepository->findOneBy($expectedProcessElementParams);
            $createdProcesElementsIds[$elementXmlId] = $createdProcesElement->id;
        }

        // Validate the creation of the connections
        foreach ($expectedProcessElementsConnections as $sourceXmlId => $expectedConnections) {
            if (empty($expectedConnections)) {
                continue;
            }
            $sourceElementId = $createdProcesElementsIds[$sourceXmlId] ?? null;;
            $this->assertNotNull($sourceElementId);
            foreach ($expectedConnections as $expectedConnection) {
                $targetElementId = $createdProcesElementsIds[$expectedConnection['xml_id']] ?? null;
                $this->assertNotNull($targetElementId);
                unset($expectedConnection['xml_id']);
                $expectedConnection['process_element_id'] = $sourceElementId;
                $expectedConnection['referenced_process_element_id'] = $targetElementId;
                $this->seeInDatabase('process_elements_relations', $expectedConnection);
            }
        }

    }

    /**
     * Generates the expected calls for the SaveReadedBpmnProcessUseCaseTest.
     *
     * @param ReadBpmnUseCaseDRO $readResult The result of the ReadBpmnUseCase.
     * 
     * @return array The array of expected calls.
     */
    private function generateExpectedCalls(ReadBpmnUseCaseDRO $readResult): array
    {
        // Create the expected creation parameters
        $expectedProcessElementsParams      = [];
        $expectedProcessElementsConnections = [];

        $types = [self::EVENT => 'events', self::GATEWAY => 'gateways', self::ACTIVITY => 'activities'];
        foreach ($types as $type => $typeKey) {
            foreach ($readResult->data->{$typeKey} as $expectedElement) {
                // Create the expected parameters
                $elementId = $expectedElement['id'];
                $expectedProcessElementsParams[$elementId] = [
                    'name'         => $expectedElement['name'],
                    'type'         => $type,
                    'subtype'      => $expectedElement['type']
                ];

                // Create the expected connections
                $createConnection = function ($connection, $direction) {
                    return [
                        'xml_id'    => $connection['element_id'],
                        'name'      => $connection['name'],
                        'direction' => $direction
                    ];
                };
                $incomingConnections = array_map(function ($connection) use ($createConnection) {
                    return $createConnection($connection, 'input');
                }, $expectedElement['incoming']);
                $outgoingConnections = array_map(function ($connection) use ($createConnection) {
                    return $createConnection($connection, 'output');
                }, $expectedElement['outgoing']);

                $expectedProcessElementsConnections[$elementId] = array_merge($incomingConnections, $outgoingConnections);
            }
        }

        return [$expectedProcessElementsParams, $expectedProcessElementsConnections];
    }

}