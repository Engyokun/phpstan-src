<?php declare(strict_types = 1);

namespace PHPStan\Type;

use PHPStan\Reflection\ClassMemberAccessAnswerer;
use PHPStan\Reflection\ConstantReflection;
use PHPStan\Reflection\Dummy\DummyConstantReflection;
use PHPStan\Reflection\Dummy\DummyMethodReflection;
use PHPStan\Reflection\Dummy\DummyPropertyReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Traits\MaybeCallableTypeTrait;
use PHPStan\Type\Traits\MaybeIterableTypeTrait;
use PHPStan\Type\Traits\MaybeOffsetAccessibleTypeTrait;
use PHPStan\Type\Traits\TruthyBooleanTypeTrait;

class ObjectWithoutClassType implements Type
{

	use MaybeCallableTypeTrait;
	use MaybeIterableTypeTrait;
	use MaybeOffsetAccessibleTypeTrait;
	use TruthyBooleanTypeTrait;

	public function hasProperty(string $propertyName): TrinaryLogic
	{
		return TrinaryLogic::createMaybe();
	}

	public function getProperty(string $propertyName, ClassMemberAccessAnswerer $scope): PropertyReflection
	{
		return new DummyPropertyReflection();
	}

	/**
	 * @return string[]
	 */
	public function getReferencedClasses(): array
	{
		return [];
	}

	public function accepts(Type $type, bool $strictTypes): TrinaryLogic
	{
		if ($type instanceof CompoundType) {
			return CompoundTypeHelper::accepts($type, $this, $strictTypes);
		}

		return TrinaryLogic::createFromBoolean(
			$type instanceof self || $type instanceof TypeWithClassName
		);
	}

	public function isSuperTypeOf(Type $type): TrinaryLogic
	{
		if ($type instanceof CompoundType) {
			return $type->isSubTypeOf($this);
		}

		if ($type instanceof self || $type instanceof TypeWithClassName) {
			return TrinaryLogic::createYes();
		}

		if ($type instanceof ClosureType) {
			return TrinaryLogic::createYes();
		}

		return TrinaryLogic::createNo();
	}

	public function equals(Type $type): bool
	{
		return $type instanceof self;
	}

	public function describe(VerbosityLevel $level): string
	{
		return 'object';
	}

	public function canAccessProperties(): TrinaryLogic
	{
		return TrinaryLogic::createYes();
	}

	public function canCallMethods(): TrinaryLogic
	{
		return TrinaryLogic::createYes();
	}

	public function hasMethod(string $methodName): TrinaryLogic
	{
		return TrinaryLogic::createMaybe();
	}

	public function getMethod(string $methodName, ClassMemberAccessAnswerer $scope): MethodReflection
	{
		return new DummyMethodReflection($methodName);
	}

	public function canAccessConstants(): TrinaryLogic
	{
		return TrinaryLogic::createYes();
	}

	public function hasConstant(string $constantName): TrinaryLogic
	{
		return TrinaryLogic::createMaybe();
	}

	public function getConstant(string $constantName): ConstantReflection
	{
		return new DummyConstantReflection($constantName);
	}

	public function isCloneable(): TrinaryLogic
	{
		return TrinaryLogic::createYes();
	}

	public function toNumber(): Type
	{
		return new ErrorType();
	}

	public function toString(): Type
	{
		return new ErrorType();
	}

	public function toInteger(): Type
	{
		return new ErrorType();
	}

	public function toFloat(): Type
	{
		return new ErrorType();
	}

	public function toArray(): Type
	{
		return new ArrayType(new MixedType(), new MixedType());
	}

	/**
	 * @param mixed[] $properties
	 * @return Type
	 */
	public static function __set_state(array $properties): Type
	{
		return new self();
	}

}
