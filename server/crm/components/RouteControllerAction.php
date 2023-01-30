<?php
/**
 * Created by PhpStorm.
 * User: Brooke
 * Date: 21-01-8
 * Time: 11:19
 */

namespace app\components;

use app\util\PhpParser;
use yii\base\Action;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;

class RouteControllerAction extends Action
{
    public $methodName;

    public function run ()
    {
        $methodName = $this->methodName;

        $reqMethodName = strtolower(\Yii::$app->request->method) . ucfirst($methodName);

        if (method_exists($this->controller, $reqMethodName)) {
            $methodName = $reqMethodName;
        }

        if (!method_exists($this->controller, $methodName)) {
            $reflection = new \ReflectionClass($this->controller);

            $allowed = [];
            foreach ($reflection->getMethods() as $classMethod) {
                preg_match('/^(get|post|put|patch|delete)?' . ucfirst($methodName) . '$/', $classMethod->name, $matches);
                if (empty($matches))
                    continue;
                $allowed[] = $matches[1];
            }
            throw new MethodNotAllowedHttpException('Method Not Allowed. This URL can only handle the following request methods: ' . implode(', ', $allowed) . '.');
        }

        $reflection = new \ReflectionMethod($this->controller, $methodName);

        list($route, $params) = \Yii::$app->request->resolve();

//			if ($comment = $reflection->getDocComment()) {
//				$tags = static::parseDocCommentTags($comment);
//
//				if ($this->methodName === $methodName && isset($tags['method'])) {
//					$methods = array_map('strtoupper', explode('|', $tags['method']));
//
//					if (!in_array(\Yii::$app->request->method, $methods)) {
//						throw new MethodNotAllowedHttpException('Method Not Allowed. This URL can only handle the following request methods: ' . implode(', ', $methods) . '.');
//					}
//				}
//
//				if (isset($tags['param'])) {
//					$params = array_merge($tags['param'], $params);
//				}
//			}
//
//			$phpParser = new PhpParser();
//
//			$classes = $phpParser->parseClass(
//				new \ReflectionClass($this->controller)
//			);

//			$params = array_map(function ($param) use ($classes) {
//				$value = strtolower($param);
//				if (isset($classes[$value]))
//					return \Yii::$container->get($classes[$value]);
//				try {
//					$class = \Yii::$container->get($param);
//				} catch (\yii\base\InvalidConfigException $exception) {
//					return $param;
//				}
//
//				return $class;
//			}, $params);

        $args = static::resolveCallableDependencies([$this->controller, $methodName], $params);

        $response = \Yii::$container->invoke([$this->controller, $methodName], $args);

        return $response;
    }

    public static function parseDocCommentTags (string $doc)
    {
        if (preg_match_all('#^\s*\*(.*)#m', $doc, $lines) === false)
            return [];

        $lines = array_filter(
            array_map('trim', $lines[1]),
            function ($line) {
                return strpos($line, '@') === 0;
            }
        );

        $tags = [];
        foreach ($lines as $line) {
            $parts = preg_split('/^\s*@/m', $line, -1, PREG_SPLIT_NO_EMPTY)[0];

            $param = substr($parts, 0, strpos($parts, ' '));

            $value = trim(substr($parts, strlen($param) + 1));

            $type = '';
            if (strpos($value, ' ') !== false) {
                $type  = substr($value, 0, strpos($value, ' '));
                $value = substr($value, strlen($type) + 2);
                $value = ltrim(trim($value), '$');
            }

            if (isset($tags[$param])) {
                if ($type) {
                    $tags[$param][$value] = $type;
                } else {
                    $tags[$param][] = $value;
                }
            } else {
                $tags[$param] = $type ? [$value => $type] : $value;
            }
        }

        return $tags;
    }

    public static function resolveCallableDependencies (callable $callback, $params = [])
    {
        if (is_array($callback)) {
            $method = new \ReflectionMethod($callback[0], $callback[1]);
        } else {
            $method = new \ReflectionFunction($callback);
        }

        $args = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $params)) {
                $isValid = true;
                if ($param->isArray()) {
                    $params[$name] = (array) $params[$name];
                } elseif (is_array($params[$name])) {
                    $isValid = false;
                } elseif (
                    PHP_VERSION_ID >= 70000 &&
                    ($type = $param->getType()) !== NULL &&
                    $type->isBuiltin() &&
                    ($params[$name] !== NULL || !$type->allowsNull())
                ) {
                    $typeName = PHP_VERSION_ID >= 70100 ? $type->getName() : (string) $type;
                    switch ($typeName) {
                        case 'int':
                            $params[$name] = filter_var($params[$name], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                            break;
                        case 'float':
                            $params[$name] = filter_var($params[$name], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
                            break;
                        case 'bool':
                            $params[$name] = filter_var($params[$name], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                            break;
                    }
                    if ($params[$name] === NULL) {
                        $isValid = false;
                    }
                }
                if (!$isValid)
                    continue;
                $args[] = $params[$name];
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }

        return $args;
    }

    public static function getActions (Controller $controller)
    {
        $ref = new \ReflectionClass($controller);

        $methods = [];
        foreach ($ref->getMethods() as $classMethod) {
            preg_match('/^(get|post|put|patch|delete)?([a|A]ction(([A-Z][a-z]*([0-9]+)?)+))$/', $classMethod->name, $matches);
            if (empty($matches))
                continue;
            $method           = strtolower(ltrim(preg_replace('/([A-Z]){1}/', '-$0', $matches[3]), '-'));
            $methods[$method] = lcfirst($matches[2]);
        }

        return array_combine(array_keys($methods), array_map(function ($method) use ($methods) {
            return [
                'class'      => static::class,
                'methodName' => $methods[$method],
            ];
        }, array_keys($methods)));
    }
}