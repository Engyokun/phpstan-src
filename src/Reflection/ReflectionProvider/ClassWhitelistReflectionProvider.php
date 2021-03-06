<?php declare(strict_types = 1);

namespace PHPStan\Reflection\ReflectionProvider;

use Nette\Utils\Strings;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ReflectionProvider;

class ClassWhitelistReflectionProvider implements ReflectionProvider
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var string[] */
	private $universalObjectCratesClasses;

	/** @var string[] */
	private $patterns;

	/**
	 * @param \PHPStan\Reflection\ReflectionProvider $reflectionProvider
	 * @param string[] $universalObjectCratesClasses
	 * @param string[] $patterns
	 */
	public function __construct(
		ReflectionProvider $reflectionProvider,
		array $universalObjectCratesClasses,
		array $patterns
	)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->universalObjectCratesClasses = $universalObjectCratesClasses;
		$this->patterns = $patterns;
	}

	public function hasClass(string $className): bool
	{
		foreach ($this->patterns as $pattern) {
			if (Strings::match($className, $pattern) === null) {
				continue;
			}

			return $this->reflectionProvider->hasClass($className);
		}

		return false;
	}

	public function getClass(string $className): ClassReflection
	{
		if (!$this->hasClass($className)) {
			throw new \PHPStan\Broker\ClassNotFoundException($className);
		}

		return $this->reflectionProvider->getClass($className);
	}

	public function getClassName(string $className): string
	{
		if (!$this->hasClass($className)) {
			throw new \PHPStan\Broker\ClassNotFoundException($className);
		}

		return $this->reflectionProvider->getClassName($className);
	}

	public function getAnonymousClassReflection(\PhpParser\Node\Stmt\Class_ $classNode, Scope $scope): ClassReflection
	{
		return $this->reflectionProvider->getAnonymousClassReflection($classNode, $scope);
	}

	public function hasFunction(\PhpParser\Node\Name $nameNode, ?Scope $scope): bool
	{
		return false;
	}

	public function getFunction(\PhpParser\Node\Name $nameNode, ?Scope $scope): FunctionReflection
	{
		throw new \PHPStan\Broker\FunctionNotFoundException((string) $nameNode);
	}

	public function resolveFunctionName(\PhpParser\Node\Name $nameNode, ?Scope $scope): ?string
	{
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
