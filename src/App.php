<?php
declare(strict_types = 1);
/**
 * Weave example app for templated controller output via a custom dispatcher.
 */

namespace App;

/**
 * Weave example app for templated controller output via a custom dispatcher.
 */
class App
{
	use \Weave\Weave, \Weave\Config\Zend\Zend, \Weave\Error\Whoops\Whoops, \Weave\Container\Aura\Aura;
	// ^^ Use Zend for config, Whoops for error handling and Aura for the DIC.

	/**
	 * Helpful const to control which environment we are in.
	 */
	const ENV_DEVELOPMENT = 'development';

	/**
	 * Provide the config classes for the DIC.
	 *
	 * See the example-doublepass example for more details.
	 *
	 * @param array  $config      Optional config array as provided from loadConfig().
	 * @param string $environment Optional indication of the runtime environment.
	 *
	 * @return array
	 */
	protected function provideContainerConfigs(array $config = [], $environment = null)
	{
		return [
			new Config($config)
		];
	}

	/**
	 * Provide middleware pipeline sets for Relay Middleware.
	 *
	 * See the example-doublepass example for more details.
	 *
	 * @param string $pipelineName The name of the pipeline to return a definition for.
	 *
	 * @return mixed Whatever the chosen Middleware stack uses for a pipeline of middlewares.
	 */
	protected function provideMiddlewarePipeline($pipelineName = null)
	{
		switch ($pipelineName) {
			case 'uppercaseOwner':
				return [
					Middleware\UppercaseOwner::class,
					\Weave\Middleware\Dispatch::class
				];

			default:
				return [\Weave\Router\Router::class];
		}
	}

	/**
	 * Setup routes for the Router.
	 *
	 * See the example-doublepass example for more details.
	 *
	 * @param mixed $router The object to setup routes against.
	 *
	 * @return null
	 */
	protected function provideRouteConfiguration($router)
	{
		// Note that we call the route 'hello' and this is later used
		// in the TemplateDispatch code to pick a template to render.
		// This is just one of many ways in which a route could be
		// matched to a template.
		$router->get(
			'hello',
			'{/owner}',
			'uppercaseOwner|' . Controller\Hello::class . '->hello'
		)
		->defaults(['owner' => 'World']);
	}
}
