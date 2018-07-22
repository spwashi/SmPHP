<?php


namespace Sm\Data\Property;


interface PropertyContainerInstance extends \JsonSerializable {
	public function getChanged(): array;
	public function getAll();
	public function resolve(): ?PropertyInstance;
	public function getProperties($search = []): PropertyContainerInstance;
}