<?php declare(strict_types = 1);

namespace PHPStan\Broker;

use PHPStan\DependencyInjection\Container;
use PHPStan\DependencyInjection\Reflection\ClassReflectionExtensionRegistryProvider;
use PHPStan\DependencyInjection\Type\DynamicReturnTypeExtensionRegistryProvider;
use PHPStan\File\RelativePathHelper;
use PHPStan\Parser\Parser;
use PHPStan\PhpDoc\StubPhpDocProvider;
use PHPStan\Reflection\FunctionReflectionFactory;
use PHPStan\Reflection\SignatureMap\NativeFunctionReflectionProvider;
use PHPStan\Type\FileTypeMapper;

class BrokerFactory
{

	public const PROPERTIES_CLASS_REFLECTION_EXTENSION_TAG = 'phpstan.broker.propertiesClassReflectionExtension';
	public const METHODS_CLASS_REFLECTION_EXTENSION_TAG = 'phpstan.broker.methodsClassReflectionExtension';
	public const DYNAMIC_METHOD_RETURN_TYPE_EXTENSION_TAG = 'phpstan.broker.dynamicMethodReturnTypeExtension';
	public const DYNAMIC_STATIC_METHOD_RETURN_TYPE_EXTENSION_TAG = 'phpstan.broker.dynamicStaticMethodReturnTypeExtension';
	public const DYNAMIC_FUNCTION_RETURN_TYPE_EXTENSION_TAG = 'phpstan.broker.dynamicFunctionReturnTypeExtension';
	public const OPERATOR_TYPE_SPECIFYING_EXTENSION_TAG = 'phpstan.broker.operatorTypeSpecifyingExtension';

	/** @var \PHPStan\DependencyInjection\Container */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function create(): Broker
	{
		/** @var RelativePathHelper $relativePathHelper */
		$relativePathHelper = $this->container->getService('simpleRelativePathHelper');

		return new Broker(
			$this->container->getByType(ClassReflectionExtensionRegistryProvider::class),
			$this->container->getByType(DynamicReturnTypeExtensionRegistryProvider::class),
			$this->container->getServicesByTag(self::OPERATOR_TYPE_SPECIFYING_EXTENSION_TAG),
			$this->container->getByType(FunctionReflectionFactory::class),
			$this->container->getByType(FileTypeMapper::class),
			$this->container->getByType(NativeFunctionReflectionProvider::class),
			$this->container->getByType(\PhpParser\PrettyPrinter\Standard::class),
			$this->container->getByType(AnonymousClassNameHelper::class),
			$this->container->getByType(Parser::class),
			$relativePathHelper,
			$this->container->getByType(StubPhpDocProvider::class),
			$this->container->getParameter('universalObjectCratesClasses')
		);
	}

}
