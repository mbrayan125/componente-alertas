<?php

namespace App\UseCases\Process;

use App\DataResultObjects\Process\Composed\ReadBpmnUseCaseDRO as ComposedReadBpmnUseCaseDRO;
use App\DataResultObjects\Process\ReadBpmnUseCaseDRO;
use App\Models\Process;
use App\Models\TargetSystem;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Traits\Process\ElementsTypesConstantsTrait;
use App\UseCases\Models\Contracts\CreateProcessElementUseCaseInterface;
use App\UseCases\Models\Contracts\CreateProcessUseCaseInterface;
use App\UseCases\Models\Contracts\CreateUserRoleUseCaseInterface;
use App\UseCases\Process\Contracts\SaveReadedBpmnProcessUseCaseInterface;
use Illuminate\Support\Facades\DB;

class SaveReadedBpmnProcessUseCase implements SaveReadedBpmnProcessUseCaseInterface
{
    use ElementsTypesConstantsTrait;

    private ComposedReadBpmnUseCaseDRO $processInfo;
    private Process $createdProcess;
    private array $userRolesInfo = [];
    private array $createdElements = [];
    private array $processElementsRelations = [];

    public function __construct(
        private readonly CreateProcessUseCaseInterface $createProcessUseCase,
        private readonly CreateProcessElementUseCaseInterface $createProcessElementUseCase,
        private readonly CreateUserRoleUseCaseInterface $createUserRoleUseCase,
        private readonly ProcessRepositoryInterface $processRepository,
        private readonly ProcessElementRepositoryInterface $processElementRepository,
    ) { }

    /**
     * @inheritDoc
     */
    public function __invoke(ReadBpmnUseCaseDRO $readedProcess, TargetSystem $targetSystem): void
    {
        $currentProcess = $this->processRepository->findOneBy([ 'target_system_id' => $targetSystem->id ], [ 'id' => 'DESC' ]);
        $nextProcessVersion = $currentProcess ? $currentProcess->version + 1 : 1;
        
        $this->createdProcess = ($this->createProcessUseCase)([
            'target_system_id' => $targetSystem->id,
            'version'          => $nextProcessVersion
        ]);
        $this->processInfo = $readedProcess->data;

        $this->loadUserRoles();
        $this->loadEvents();
        $this->loadGateways();
        $this->loadActivities();
        $this->loadElementRelations();
    }

    /**
     * Loads user roles based on the process lanes information.
     */
    private function loadUserRoles(): void
    {
        $lanesInfo = $this->processInfo->lanes;
        foreach ($lanesInfo as $laneId => &$userRole) {
            $createdUserRole = ($this->createUserRoleUseCase)([
                'process_id' => $this->createdProcess->id,
                'name'       => $userRole['name']
            ]);

            $this->userRolesInfo[$laneId] = [
                'object_id' => $createdUserRole->id
            ];
        }
    }

    /**
     * Loads events from the process information and creates corresponding process elements.
     */
    private function loadEvents(): void
    {
        $eventsInfo = $this->processInfo->events;
        foreach ($eventsInfo as &$event) {
            $elementId = $event['id'];
            $userRoleIdProcess = $event['lane'] ?? null;
            $userRoleObject = $this->userRolesInfo[$userRoleIdProcess]['object_id'];

            $createdEvent = ($this->createProcessElementUseCase)([
                'process_id'   => $this->createdProcess->id,
                'user_role_id' => $userRoleObject,
                'name'         => $event['name'],
                'type'         => self::EVENT,
                'subtype'      => $event['type']
            ]);

            $this->createdElements[$elementId] = $createdEvent;
            $this->processElementsRelations[$elementId] = [
                'incoming' => $event['incoming'],
                'outgoing' => $event['outgoing']
            ];
        }
    }

    /**
     * Loads gateways from the process information and creates corresponding process elements.
     */
    private function loadGateways(): void
    {
        $gatewaysInfo = $this->processInfo->gateways;
        foreach ($gatewaysInfo as &$gateway) {
            $elementId = $gateway['id'];
            $userRoleIdProcess = $gateway['lane'] ?? null;
            $userRoleObject = $this->userRolesInfo[$userRoleIdProcess]['object_id'];

            $createdGateway = ($this->createProcessElementUseCase)([
                'process_id'   => $this->createdProcess->id,
                'user_role_id' => $userRoleObject,
                'name'         => $gateway['name'],
                'type'         => self::GATEWAY,
                'subtype'      => $gateway['type']
            ]);

            $this->createdElements[$elementId] = $createdGateway;
            $this->processElementsRelations[$elementId] = [
                'incoming' => $gateway['incoming'],
                'outgoing' => $gateway['outgoing']
            ];
        }
    }

    /**
     * Loads the activities.
     *
     * @return void
     */
    private function loadActivities(): void
    {
        $activitiesInfo = $this->processInfo->activities;
        foreach ($activitiesInfo as &$activity) {
            $elementId = $activity['id'];
            $userRoleIdProcess = $activity['lane'] ?? null;
            $userRoleObject = $this->userRolesInfo[$userRoleIdProcess]['object_id'];

            $createdActivity = ($this->createProcessElementUseCase)([
                'process_id'   => $this->createdProcess->id,
                'user_role_id' => $userRoleObject,
                'name'         => $activity['name'],
                'type'         => self::ACTIVITY,
                'subtype'      => $activity['type']
            ]);

            $this->createdElements[$elementId] = $createdActivity;
            $this->processElementsRelations[$elementId] = [
                'incoming' => $activity['incoming'],
                'outgoing' => $activity['outgoing']
            ];
        }
    }

    /**
     * Loads the element relations.
     *
     * @return void
     */
    private function loadElementRelations(): void
    {
        foreach ($this->processElementsRelations as $elementId => $relationsInfo)
        {
            $targetElement = $this->createdElements[$elementId];
            foreach ($relationsInfo['incoming'] as $incomingElementInfo) {
                $incomingElement = $this->createdElements[$incomingElementInfo['element_id']];
                $targetElement->incomingElements()->attach($incomingElement, [
                    'direction' => 'input',
                    'name'      => $incomingElementInfo['name']
                ]);
            }
            foreach ($relationsInfo['outgoing'] as $outgointElementInfo) {
                $outgoingElement = $this->createdElements[$outgointElementInfo['element_id']];
                $targetElement->incomingElements()->attach($outgoingElement, [
                    'direction' => 'output',
                    'name'      => $outgointElementInfo['name']
                ]);
            }
        }
    }
}