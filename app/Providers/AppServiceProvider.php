<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $mainModels = [ 'Process', 'ProcessElement', 'ProcessInstance', 'ProcessInstanceHistory', 'TargetSystem', 'UserAlert', 'UserRole' ];
        $this->bindUseCasesGroup($mainModels, 'Models', '', 'Create');
        $this->bindUseCasesGroup($mainModels, 'ModelValidators', 'AttributesValidator');
        $this->bindRepositoriesGroup($mainModels, 'Default');
        $this->bindUseCasesGroup([ 'Read', 'SaveReaded' ], 'Process', 'BpmnProcess');
        $this->bindUseCasesGroup([ 'Move', 'Remove' ], 'Files', 'File');
        $this->bindUseCasesGroup([ 'Create' ], 'Folders', 'Folder');
    }

    /**
     * Binds the repositories group to the selected abstract.
     *
     * @param array $abstracts The array of abstracts.
     * @param string $selected The selected abstract.
     * 
     * @return void
     */
    private function bindRepositoriesGroup(array $abstracts, string $selected)
    {
        $path = 'App\Repositories';
        $this->bindAbstractsGroup($abstracts, $path, '', 'Repository', $selected);
    }

    /**
     * Binds a group of use cases to the container.
     *
     * @param array $abstracts The list of abstracts to bind.
     * @param string $folder The folder where the use cases are located.
     * @param string $suffix The suffix to append to the use case class names.
     * @param string $prefix The prefix to prepend to the use case class names.
     * 
     * @return void
     */
    private function bindUseCasesGroup(array $abstracts, string $folder,  string $suffix = '', string $prefix = '')
    {
        $path = 'App\UseCases\\' . $folder;
        $this->bindAbstractsGroup($abstracts, $path, $prefix, ($suffix . 'UseCase'));
    }

    /**
     * Binds a group of abstracts to their corresponding implementations.
     *
     * @param array $abstracts The array of abstracts to bind.
     * @param string $path The path to the implementations.
     * @param string $prefix The prefix for the implementation class names.
     * @param string $suffix The suffix for the implementation class names.
     * @param string $selected The selected implementation.
     * 
     * @return void
     */
    private function bindAbstractsGroup(array $abstracts, string $path, string $prefix, string $suffix, string $selected = '')
    {
        foreach ($abstracts as $abstract) {
            $this->bindAbstract($abstract, $path, $prefix, $suffix, $selected);
        }
    }

    /**
     * Binds an abstract class or interface to a concrete implementation.
     *
     * @param string $abstract The abstract class or interface to bind.
     * @param string $path The path to the concrete implementation.
     * @param string $prefix The prefix to prepend to the concrete implementation.
     * @param string $suffix The suffix to append to the concrete implementation.
     * @param string $selected The selected implementation, if any.
     * 
     * @return void
     */
    private function bindAbstract(string $abstract, string $path, string $prefix, string $suffix, string $selected = '')
    {
        $abstractClass = sprintf('%s\Contracts\%s%s%sInterface', $path, $prefix, $abstract, $suffix);
        $concreteClass = sprintf('%s\%s%s%s%s', $path, $prefix, $abstract, $selected, $suffix);
        $this->app->bind($abstractClass, $concreteClass);
    }
}
