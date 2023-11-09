<?php 
namespace Todoist\Application\UseCases;

interface UseCase
{
    /**
     * Execute UseCase
     *
     * @param array $input
     * @return array $output
     */
    public function execute(array $input): array;
}