<?php
declare(strict_types = 1);
/**
 * Weave example app Aura.Di config class.
 *
 * Look to the bottom of the define method for the custom dispatcher override.
 */

namespace App;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

/**
 * Weave example app Aura.Di config class.
 */
class Config extends ContainerConfig
{
	/**
	 * The config loaded in this case via Zend Config.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param array $config The config.
	 */
	public function __construct(array $config = [])
	{
		$this->config = $config;
	}

	/**
	 * Define params, setters, and services before the Container is locked.
	 *
	 * @param Container $container The DI container.
	 *
	 * @return null
	 */
	public function define(Container $container)
	{
		// Specify we want to use Relay for our Middleware
		$container->types[\Weave\Middleware\MiddlewareAdaptorInterface::class] = $container->lazyNew(
			\Weave\Middleware\Relay\Relay::class
		);

		// Specify we want to use Zend Diactoros for our PSR7 stuff
		$container->types[\Weave\Http\ResponseEmitterInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\responseEmitter::class
		);

		$container->types[\Weave\Http\RequestFactoryInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\RequestFactory::class
		);

		$container->types[\Weave\Http\ResponseFactoryInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\ResponseFactory::class
		);

		// Specify we want to use Aura for our router
		$container->types[\Weave\Router\RouterAdaptorInterface::class] = $container->lazyNew(
			\Weave\Router\Aura\Aura::class
		);

		// Override the default dispatcher with our custom one that handles templating
		$container->types[\Weave\Dispatch\DispatchAdaptorInterface::class] = $container->lazyNew(
			TemplateDispatch::class
		);

		// Setup the directory in which Plates will look for temlates
		// You could do this with a config value if you need to change the template location
		// depending on which environment you are running in (but this is rare).
		$container->params[\League\Plates\Engine::class] = ['directory' => realpath(__DIR__ . '/templates')];

		// Setup a parameter for our Hello Controller based on the content of the config.
		$container->params[\App\Controller\Hello::class] = ['message' => $this->config['HelloMessage']];
	}
}
