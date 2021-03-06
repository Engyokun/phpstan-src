<?php declare(strict_types = 1);

namespace PHPStan\Reflection\ReflectionProvider;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ReflectionProvider;

class ChainReflectionProvider implements ReflectionProvider
{

	/** @var \PHPStan\Reflection\ReflectionProvider[] */
	private $providers;

	/** @var string[] */
	private $universalObjectCratesClasses;

	/**
	 * @param \PHPStan\Reflection\ReflectionProvider[] $providers
	 * @param string[] $universalObjectCratesClasses
	 */
	public function __construct(
		array $providers,
		array $universalObjectCratesClasses
	)
	{
		$this->providers = $providers;
		$this->universalObjectCratesClasses = $universalObjectCratesClasses;
	}

	public function hasClass(string $className): bool
	{
		foreach ($this->providers as $provider) {
			if (!$provider->hasClass($className)) {
				continue;
			}

			return true;
		}

		return false;
	}

	public function getClass(string $className): ClassReflection
	{
		foreach ($this->providers as $provider) {
			if (!$provider->hasClass($className)) {
				continue;
			}

			return $provider->getClass($className);
		}

		throw new \PHPStan\Broker\ClassNotFoundException($className);
	}

	public function getClassName(string $className): string
	{
		foreach ($this->providers as $provider) {
			if (!$provider->hasClass($className)) {
				continue;
			}

			return $provider->getClassName($className);
		}

		throw new \PHPStan\Broker\ClassNotFoundException($className);
	}

	public function getAnonymousClassReflection(\PhpParser\Node\Stmt\Class_ $classNode, Scope $scope): ClassReflection
	{
		return $this->providers[0]->getAnonymousClassReflection($classNode, $scope);
	}

	public function hasFunction(\PhpParser\Node\Name $nameNode, ?Scope $scope): bool
	{
		foreach ($this->providers as $provider) {
			if (!$provider->hasFunction($nameNode, $scope)) {
				continue;
			}

			return true;
		}

		return false;
	}

	public function getFunction(\PhpParser\Node\Name $nameNode, ?Scope $scope): FunctionReflection
	{
		foreach ($this->providers as $provider) {
			if (!$provider->hasFunction($nameNode, $scope)) {
				continue;
			}

			return $provider->getFunction($nameNode, $scope);
		}

		throw new \PHPStan\Broker\FunctionNotFoundException((string) $nameNode);
	}

	public function resolveFunctionName(\PhpParser\Node\Name $nameNode, ?Scope $scope): ?string
	{
		foreach ($this->providers as $provider) {
			$resolvedName = $provider->resolveFunctionName($nameNode, $scope);
			if ($resolvedName === null) {
				continue;
			}

			return $resolvedName;
		}

		return null;
	}

	/**
	 * @return string[]
	 */
	public function getUniversalObjectCratesClasses(): array
	{
		return $this->universalObjectCratesClasses;
	}

}
